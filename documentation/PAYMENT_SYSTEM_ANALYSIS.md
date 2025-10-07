# Payment System Analysis & Restructuring Guide

## Current System Overview

### Database Structure

#### `payment_receipts` Table
```sql
- id (PK, AUTO_INCREMENT)
- customer_id (FK to customers)
- invoice_number (varchar, '0' for credit top-ups)
- receipt_no (unique identifier for receipt)
- payment_amount (double, negative for credit deductions)
- full_payment (int, 1=full payment, 0=partial)
- created_at (timestamp)
- payment_date (datetime)
- note (varchar)
- mode_of_payment (varchar)
- description (varchar)
- currency_name (varchar)
- currency_roe (float - rate of exchange)
- salesperson (varchar)
```

#### `shipments` Table
```sql
- shipment_id (PK)
- invoice_number (unique)
- customer_id (FK)
- status (varchar: 'pending', 'generated', 'submitted', 'paid')
- ... other shipment fields
```

#### `shipment_charges` Table
```sql
- id (PK)
- shipment_id (FK)
- charge_details
- amount
- total_amount
- ... other charge fields
```

---

## Current Payment Flow Analysis

### 1. **Payment Types Handled**

#### A. Regular Invoice Payments
- **Partial Payments**: `full_payment = 0`, positive `payment_amount`
- **Full Payments**: `full_payment = 1`, positive `payment_amount`
- **Overpayments**: Excess amount stored as credit (invoice_number = '0')

#### B. Credit Transactions
- **Credit Top-ups**: `invoice_number = '0'`, positive `payment_amount`
- **Credit Deductions**: `invoice_number != '0'`, negative `payment_amount`

### 2. **Payment Logic in `save_receipt_payment.php`**

```
1. Validate shipment is not already marked as 'paid'
2. Get customer_id from customer name
3. Calculate invoice total from shipment_charges
4. Get previous partial payments sum
5. Determine payment type:
   
   IF (full_payment OR total_paid >= invoice_total):
      - Split payment into: payable amount + credit
      - Insert payable as full_payment = 1
      - Mark shipment status = 'paid'
      - If excess: Insert credit top-up (invoice_number = '0')
   
   ELSE:
      - Insert as partial payment (full_payment = 0)
      - Keep shipment status unchanged
```

---

## Issues & Risks Identified

### 🔴 Critical Issues

1. **Decimal Precision Issue (FIXED)** ⚠️ **HIGH PRIORITY**
   - **Problem**: Using `DOUBLE` and `DECIMAL(10,2)` causes floating-point errors
   - Fully paid invoices (e.g., 17614.30 = 17614.30) marked as "partially paid"
   - Values stored as 17614.299999 vs 17614.300001 fail exact comparisons
   - **Fix Applied**: Changed to `DECIMAL(10,3)` with tolerance-based comparison
   - See "Decimal Precision Fix" section below for details

2. **No Transaction Atomicity for Multi-Table Updates**
   - Credit balance calculation happens across multiple queries
   - Risk of inconsistent state if transaction fails midway

3. **Invoice Status Logic Fragility**
   - Status is only updated when `full_payment = 1`
   - Doesn't account for sum of partial payments reaching invoice total
   - Manual status changes in UI bypass payment validation

4. **Negative Amount Handling**
   - Credit deductions use negative amounts (confusing)
   - Mixed with positive payments in same table
   - Complex queries needed to separate types

5. **Duplicate Receipt Handling**
   - Receipt deletion by receipt_no before re-insert
   - Loses payment history if receipt re-generated
   - No audit trail

6. **Customer Credit Balance Calculation**
   - Function `getCustomerCreditBalance()` in conn.php
   - Calculates on-the-fly every time (no cached balance)
   - Performance issue with many transactions

### ⚠️ Medium Issues

1. **No Payment Validation**
   - Can pay more than invoice amount
   - No check for negative payment amounts (except credit deduction flag)
   - Currency conversion not validated

2. **Receipt Number Generation**
   - Random timestamp-based (collision risk)
   - Not sequential or meaningful
   - Hard to search/sort

3. **Mixed Concerns**
   - `payment_receipts` table stores both:
     - Payment transactions
     - Credit balance adjustments
   - Should be separated for clarity




## Recommended Restructure

### Phase 1: Database Schema Improvements (Safe Migration)

#### Step 1A: Add New Columns to Existing Tables

```sql
-- Add to payment_receipts (without breaking existing data)
ALTER TABLE payment_receipts 
ADD COLUMN payment_type ENUM('payment', 'credit_topup', 'credit_deduction') DEFAULT 'payment',
ADD COLUMN transaction_status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
ADD COLUMN related_payment_id INT NULL COMMENT 'For refunds/adjustments',
ADD COLUMN created_by INT NULL COMMENT 'User ID who created this',
ADD INDEX idx_customer_invoice (customer_id, invoice_number),
ADD INDEX idx_receipt_no (receipt_no),
ADD INDEX idx_payment_date (payment_date);

-- Add to shipments
ALTER TABLE shipments
ADD COLUMN total_charges FLOAT DEFAULT 0 COMMENT 'Cached total from shipment_charges',
ADD COLUMN total_paid FLOAT DEFAULT 0 COMMENT 'Cached total from payments',
ADD COLUMN balance_due FLOAT DEFAULT 0 COMMENT 'total_charges - total_paid',
ADD INDEX idx_customer_status (customer_id, status);
```

#### Step 1B: Create New Support Tables

```sql
-- Create customer_credits table (separate from payment_receipts)
CREATE TABLE customer_credits (
  id INT PRIMARY KEY AUTO_INCREMENT,
  customer_id INT NOT NULL,
  credit_balance FLOAT DEFAULT 0,
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create payment_audit_log
CREATE TABLE payment_audit_log (
  id INT PRIMARY KEY AUTO_INCREMENT,
  payment_id INT NOT NULL,
  action ENUM('created', 'updated', 'deleted', 'cancelled') NOT NULL,
  old_values JSON,
  new_values JSON,
  changed_by INT,
  changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  reason TEXT,
  INDEX idx_payment_id (payment_id),
  INDEX idx_changed_at (changed_at)
) ENGINE=InnoDB;

-- Create invoice_payment_summary (materialized view alternative)
CREATE TABLE invoice_payment_summary (
  invoice_number VARCHAR(100) PRIMARY KEY,
  shipment_id VARCHAR(100),
  customer_id INT,
  invoice_total FLOAT DEFAULT 0,
  total_paid FLOAT DEFAULT 0,
  total_credits_used FLOAT DEFAULT 0,
  balance_due FLOAT DEFAULT 0,
  payment_status ENUM('unpaid', 'partial', 'paid', 'overpaid') DEFAULT 'unpaid',
  last_payment_date DATETIME NULL,
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
) ENGINE=InnoDB;
```

---

### Phase 2: Create Helper Functions/Stored Procedures

#### Step 2A: Update Customer Credit Balance

```sql
DELIMITER //

CREATE PROCEDURE update_customer_credit_balance(
    IN p_customer_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_transaction_type ENUM('add', 'deduct')
)
BEGIN
    DECLARE current_balance DECIMAL(10,2);
    
    START TRANSACTION;
    
    -- Get or create customer credit record
    INSERT INTO customer_credits (customer_id, credit_balance)
    VALUES (p_customer_id, 0)
    ON DUPLICATE KEY UPDATE credit_balance = credit_balance;
    
    -- Update balance
    IF p_transaction_type = 'add' THEN
        UPDATE customer_credits 
        SET credit_balance = credit_balance + p_amount
        WHERE customer_id = p_customer_id;
    ELSE
        UPDATE customer_credits 
        SET credit_balance = credit_balance - p_amount
        WHERE customer_id = p_customer_id;
    END IF;
    
    COMMIT;
END //

DELIMITER ;
```

#### Step 2B: Process Payment Transaction

**Note:** This stored procedure includes all fields from the current implementation:
- `p_note` - Special notes about the payment
- `p_description` - Payment description
- `p_currency_name` - Currency used (e.g., AED, USD)
- `p_currency_roe` - Rate of Exchange for currency conversion

```sql
DELIMITER //

CREATE PROCEDURE process_payment(
    IN p_customer_id INT,
    IN p_invoice_number VARCHAR(100),
    IN p_receipt_no VARCHAR(50),
    IN p_payment_amount DECIMAL(10,2),
    IN p_use_credit BOOLEAN,
    IN p_payment_date DATETIME,
    IN p_mode_of_payment VARCHAR(20),
    IN p_salesperson VARCHAR(50),
    IN p_note TEXT,
    IN p_description TEXT,
    IN p_currency_name VARCHAR(50),
    IN p_currency_roe FLOAT,
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_invoice_total DECIMAL(10,2);
    DECLARE v_total_paid DECIMAL(10,2);
    DECLARE v_credit_balance DECIMAL(10,2);
    DECLARE v_amount_from_credit DECIMAL(10,2) DEFAULT 0;
    DECLARE v_amount_from_payment DECIMAL(10,2);
    DECLARE v_remaining_balance DECIMAL(10,2);
    DECLARE v_excess_amount DECIMAL(10,2) DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'error';
        SET p_message = 'Database error occurred';
    END;
    
    START TRANSACTION;
    
    -- Get invoice total
    SELECT COALESCE(SUM(total_amount), 0) INTO v_invoice_total
    FROM shipment_charges sc
    JOIN shipments s ON sc.shipment_id = s.shipment_id
    WHERE s.invoice_number = p_invoice_number;
    
    IF v_invoice_total = 0 THEN
        SET p_status = 'error';
        SET p_message = 'Invoice not found or has no charges';
        ROLLBACK;
    ELSE
        -- Get already paid amount
        SELECT COALESCE(SUM(ABS(payment_amount)), 0) INTO v_total_paid
        FROM payment_receipts
        WHERE invoice_number = p_invoice_number 
        AND payment_type = 'payment';
        
        -- Calculate remaining
        SET v_remaining_balance = v_invoice_total - v_total_paid;
        
        -- Check if already paid
        IF v_remaining_balance <= 0 THEN
            SET p_status = 'error';
            SET p_message = 'Invoice is already fully paid';
            ROLLBACK;
        ELSE
            -- Get customer credit balance
            SELECT COALESCE(credit_balance, 0) INTO v_credit_balance
            FROM customer_credits
            WHERE customer_id = p_customer_id;
            
            -- Calculate payment split
            IF p_use_credit AND v_credit_balance > 0 THEN
                SET v_amount_from_credit = LEAST(v_credit_balance, v_remaining_balance);
                SET v_remaining_balance = v_remaining_balance - v_amount_from_credit;
            END IF;
            
            SET v_amount_from_payment = LEAST(p_payment_amount, v_remaining_balance);
            SET v_excess_amount = p_payment_amount - v_amount_from_payment;
            
            -- Insert payment record
            INSERT INTO payment_receipts (
                customer_id, invoice_number, receipt_no,
                payment_amount, payment_type, full_payment,
                payment_date, mode_of_payment, salesperson,
                note, description, currency_name, currency_roe
            ) VALUES (
                p_customer_id, p_invoice_number, p_receipt_no,
                v_amount_from_payment, 'payment',
                IF(v_amount_from_payment + v_amount_from_credit >= v_invoice_total - v_total_paid, 1, 0),
                p_payment_date, p_mode_of_payment, p_salesperson,
                p_note, p_description, p_currency_name, p_currency_roe
            );
            
            -- Deduct credit if used
            IF v_amount_from_credit > 0 THEN
                INSERT INTO payment_receipts (
                    customer_id, invoice_number, receipt_no,
                    payment_amount, payment_type, full_payment,
                    payment_date, mode_of_payment, note
                ) VALUES (
                    p_customer_id, p_invoice_number, CONCAT(p_receipt_no, '-CRD'),
                    v_amount_from_credit, 'credit_deduction', 0,
                    p_payment_date, 'credit', p_note
                );
                
                CALL update_customer_credit_balance(p_customer_id, v_amount_from_credit, 'deduct');
            END IF;
            
            -- Add excess to credit
            IF v_excess_amount > 0 THEN
                CALL update_customer_credit_balance(p_customer_id, v_excess_amount, 'add');
            END IF;
            
            -- Update shipment status
            IF (v_total_paid + v_amount_from_payment + v_amount_from_credit) >= v_invoice_total THEN
                UPDATE shipments SET status = 'paid' WHERE invoice_number = p_invoice_number;
            END IF;
            
            -- Update summary table
            CALL update_invoice_payment_summary(p_invoice_number);
            
            SET p_status = 'success';
            SET p_message = 'Payment processed successfully';
            COMMIT;
        END IF;
    END IF;
END //

DELIMITER ;
```

---

### Phase 3: Migration Script for Existing Data

```sql
-- Populate customer_credits from existing payment_receipts
INSERT INTO customer_credits (customer_id, credit_balance)
SELECT 
    customer_id,
    COALESCE(SUM(
        CASE 
            WHEN invoice_number = '0' THEN payment_amount
            WHEN invoice_number != '0' AND payment_amount < 0 THEN payment_amount
            ELSE 0
        END
    ), 0) AS credit_balance
FROM payment_receipts
GROUP BY customer_id
ON DUPLICATE KEY UPDATE credit_balance = VALUES(credit_balance);

-- Set payment_type for existing records
UPDATE payment_receipts
SET payment_type = CASE
    WHEN invoice_number = '0' THEN 'credit_topup'
    WHEN payment_amount < 0 THEN 'credit_deduction'
    ELSE 'payment'
END;

-- Populate invoice_payment_summary
INSERT INTO invoice_payment_summary (
    invoice_number, shipment_id, customer_id,
    invoice_total, total_paid, balance_due, payment_status
)
SELECT 
    s.invoice_number,
    s.shipment_id,
    s.customer_id,
    ROUND(COALESCE(charges.invoice_total, 0), 3) AS invoice_total,
    ROUND(COALESCE(payments.total_paid, 0), 3) AS total_paid,
    GREATEST(0, ROUND(COALESCE(charges.invoice_total, 0), 3) - ROUND(COALESCE(payments.total_paid, 0), 3)) AS balance_due,
    CASE 
        WHEN ROUND(COALESCE(payments.total_paid, 0), 0) >= ROUND(COALESCE(charges.invoice_total, 0), 0) THEN 'paid'
        WHEN ROUND(COALESCE(payments.total_paid, 0), 0) > 0 THEN 'partial'
        ELSE 'unpaid'
    END AS payment_status
FROM shipments s
LEFT JOIN (
    -- Pre-aggregate charges by invoice with 3 decimal precision
    SELECT 
        s2.invoice_number,
        ROUND(SUM(sc.total_amount), 3) AS invoice_total
    FROM shipments s2
    LEFT JOIN shipment_charges sc ON s2.shipment_id = sc.shipment_id
    GROUP BY s2.invoice_number
) charges ON s.invoice_number = charges.invoice_number
LEFT JOIN (
    -- Pre-aggregate payments by invoice (matching PHP exactly) with 3 decimal precision
    SELECT 
        invoice_number,
        ROUND(SUM(ABS(payment_amount)), 3) AS total_paid
    FROM payment_receipts
    GROUP BY invoice_number
) payments ON s.invoice_number = payments.invoice_number;
```

---

### Phase 4: Refactor PHP Code

#### Step 4A: Create Payment Service Class

```php
// /home/fikry/dev/elite/backend/PaymentService.php

<?php
class PaymentService {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get customer credit balance from cache table
     */
    public function getCustomerCreditBalance($customerId) {
        $stmt = $this->conn->prepare(
            "SELECT credit_balance FROM customer_credits WHERE customer_id = ?"
        );
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return (float) $row['credit_balance'];
        }
        return 0.0;
    }
    
    /**
     * Get invoice payment summary
     */
    public function getInvoicePaymentSummary($invoiceNumber) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM invoice_payment_summary WHERE invoice_number = ?"
        );
        $stmt->bind_param("s", $invoiceNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Process a payment using stored procedure
     */
    public function processPayment($paymentData) {
        $stmt = $this->conn->prepare(
            "CALL process_payment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @status, @message)"
        );
        
        $stmt->bind_param(
            "issdissssssd",
            $paymentData['customer_id'],
            $paymentData['invoice_number'],
            $paymentData['receipt_no'],
            $paymentData['payment_amount'],
            $paymentData['use_credit'],
            $paymentData['payment_date'],
            $paymentData['mode_of_payment'],
            $paymentData['salesperson'],
            $paymentData['note'],
            $paymentData['description'],
            $paymentData['currency_name'],
            $paymentData['currency_roe']
        );
        
        $stmt->execute();
        $stmt->close();
        
        // Get output parameters
        $result = $this->conn->query("SELECT @status AS status, @message AS message");
        return $result->fetch_assoc();
    }
    
    /**
     * Add credit top-up
     */
    public function addCreditTopup($customerId, $amount, $note = '') {
        $this->conn->begin_transaction();
        
        try {
            // Insert credit topup record
            $stmt = $this->conn->prepare(
                "INSERT INTO payment_receipts (
                    customer_id, invoice_number, receipt_no,
                    payment_amount, payment_type, full_payment,
                    payment_date, note
                ) VALUES (?, '0', ?, ?, 'credit_topup', 0, NOW(), ?)"
            );
            
            $receiptNo = 'CRD-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
            $stmt->bind_param("isds", $customerId, $receiptNo, $amount, $note);
            $stmt->execute();
            
            // Update customer credit balance
            $stmt = $this->conn->prepare(
                "INSERT INTO customer_credits (customer_id, credit_balance)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE credit_balance = credit_balance + ?"
            );
            $stmt->bind_param("idd", $customerId, $amount, $amount);
            $stmt->execute();
            
            $this->conn->commit();
            return ['status' => 'success', 'receipt_no' => $receiptNo];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get customer payment history with proper categorization
     */
    public function getCustomerPaymentHistory($customerId) {
        $query = "
            SELECT 
                s.invoice_number,
                s.invoice_date,
                ips.invoice_total,
                ips.total_paid,
                ips.balance_due,
                ips.payment_status,
                s.status AS shipment_status
            FROM shipments s
            LEFT JOIN invoice_payment_summary ips ON s.invoice_number = ips.invoice_number
            WHERE s.customer_id = ?
            ORDER BY 
                FIELD(ips.payment_status, 'partial', 'unpaid', 'paid'),
                s.invoice_date DESC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $invoices = [
            'partial' => [],
            'unpaid' => [],
            'paid' => []
        ];
        
        while ($row = $result->fetch_assoc()) {
            $status = $row['payment_status'] ?? 'unpaid';
            $invoices[$status][] = $row;
        }
        
        return $invoices;
    }
}
```

#### Step 4B: Example Usage of PaymentService

```php
// Example: Processing a payment with all fields
require_once 'backend/PaymentService.php';

$paymentService = new PaymentService($conn);

// Prepare payment data from frontend
$paymentData = [
    'customer_id' => 123,
    'invoice_number' => 'INV-2024-001',
    'receipt_no' => 'RCPT-20241004-1234',
    'payment_amount' => 5000.00,
    'use_credit' => true,  // Whether to use customer credit
    'payment_date' => '2024-10-04 14:30:00',
    'mode_of_payment' => 'bank_transfer',
    'salesperson' => 'John Doe',
    'note' => 'Partial payment for services rendered',
    'description' => 'Payment via wire transfer',
    'currency_name' => 'AED',
    'currency_roe' => 1.0
];

// Process the payment
$result = $paymentService->processPayment($paymentData);

if ($result['status'] === 'success') {
    echo "Payment processed successfully!";
    echo $result['message'];
} else {
    echo "Payment failed: " . $result['message'];
}
```

---

## Implementation Roadmap

### 🟢 Phase 1: Preparation (1-2 days)
1. **Backup existing database**
2. **Test migration script on copy**
3. **Document current payment flows**
4. **Create rollback plan**

### 🟡 Phase 2: Database Migration (1 day)
1. **Add new columns to existing tables** (non-breaking)
2. **Create new support tables**
3. **Run data migration for customer_credits**
4. **Populate invoice_payment_summary**
5. **Verify data integrity**

### 🟠 Phase 3: Create New Code (2-3 days)
1. **Implement PaymentService class**
2. **Create stored procedures**
3. **Write unit tests for payment logic**
4. **Create API endpoints using PaymentService**

### 🔴 Phase 4: Gradual Cutover (1 week)
1. **Deploy new code alongside old (feature flag)**
2. **Test new payment flow in staging**
3. **Monitor both systems in parallel**
4. **Gradually switch traffic to new system**
5. **Deprecate old endpoints**

### ⚪ Phase 5: Cleanup (1-2 days)
1. **Remove old payment code**
2. **Add indexes for performance**
3. **Document new system**
4. **Train users on changes**

---

## Benefits of New Structure

### ✅ Performance
- Cached credit balances (no real-time calculation)
- Indexed queries for faster searches
- Summary table reduces join complexity

### ✅ Data Integrity
- Stored procedures enforce business logic
- Transaction safety with proper rollbacks
- Audit trail for all changes

### ✅ Maintainability
- Separation of concerns (payments vs credits)
- Clear payment types
- Easier to debug and extend

### ✅ Reporting
- Pre-calculated summaries
- Easy to generate reports
- Clear payment status tracking

---

## Quick Wins (Can Implement Immediately)

### 1. Add Indexes
```sql
ALTER TABLE payment_receipts 
ADD INDEX idx_customer_invoice (customer_id, invoice_number),
ADD INDEX idx_payment_date (payment_date);
```

### 2. Create View for Quick Queries
```sql
-- Create view based on actual view_invoices.php logic
CREATE OR REPLACE VIEW v_invoice_payments AS
SELECT 
    s.shipment_id,
    s.invoice_number,
    s.customer_id,
    c.name AS customer_name,
    -- Get total from shipment_charges (matching view_invoices.php line 116)
    COALESCE(SUM(sc.total_amount), 0) AS invoice_total,
    -- Get paid amount using ABS() like view_invoices.php line 128
    COALESCE(SUM(ABS(pr.payment_amount)), 0) AS total_paid,
    -- Calculate due amount with GREATEST to prevent negative (matching PHP max(0, $total - $paid))
    GREATEST(0, COALESCE(SUM(sc.total_amount), 0) - COALESCE(SUM(ABS(pr.payment_amount)), 0)) AS balance_due,
    s.status,
    s.invoice_date,
    CASE 
        WHEN s.status = 'paid' THEN 'paid'
        WHEN COALESCE(SUM(ABS(pr.payment_amount)), 0) >= COALESCE(SUM(sc.total_amount), 0) THEN 'paid'
        WHEN COALESCE(SUM(ABS(pr.payment_amount)), 0) > 0 THEN 'partial'
        ELSE 'unpaid'
    END AS payment_status
FROM shipments s
LEFT JOIN customers c ON s.customer_id = c.customer_id
LEFT JOIN shipment_charges sc ON s.shipment_id = sc.shipment_id
LEFT JOIN payment_receipts pr ON s.invoice_number = pr.invoice_number
GROUP BY s.shipment_id, s.invoice_number, s.customer_id, c.name, s.status, s.invoice_date
ORDER BY s.id DESC;
```

### 3. Update customer_payment_history.php to use view
```php
// Simpler query using the view
$invoices_sql = "
    SELECT * FROM v_invoice_payments
    WHERE customer_id = ?
    ORDER BY 
        CASE 
            WHEN balance_due > 0 AND total_paid > 0 THEN 1  -- Partial
            WHEN balance_due > 0 THEN 2                      -- Due
            ELSE 3                                            -- Paid
        END,
        invoice_date DESC
";
```

---

## Next Steps for You

1. **Review this analysis** and decide which phases to implement
2. **Test migration script** on a database copy first
3. **Implement quick wins** (indexes + view) - zero risk, immediate benefit
4. **Plan full migration** if you want the complete restructure
5. **Let me know** if you want me to help implement any specific phase

Would you like me to proceed with implementing any of these phases?
