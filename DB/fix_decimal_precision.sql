-- Fix Decimal Precision Issue in Payment System
-- This script updates all monetary columns to use DECIMAL(10,3) instead of DOUBLE or DECIMAL(10,2)
-- Run this script to fix the floating point comparison issues

-- Backup reminder
-- IMPORTANT: Backup your database before running this script!

-- 1. Fix payment_receipts table
ALTER TABLE payment_receipts 
MODIFY COLUMN payment_amount DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN currency_roe DECIMAL(10,3) DEFAULT 1.0;

-- 2. Fix shipment_charges table
ALTER TABLE shipment_charges 
MODIFY COLUMN amount DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN total_amount DECIMAL(10,3) DEFAULT 0;

-- 3. Fix customer_credits table (if exists)
ALTER TABLE customer_credits 
MODIFY COLUMN credit_balance DECIMAL(10,3) DEFAULT 0;

-- 4. Fix invoice_payment_summary table (if exists)
ALTER TABLE invoice_payment_summary 
MODIFY COLUMN invoice_total DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN total_paid DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN total_credits_used DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN balance_due DECIMAL(10,3) DEFAULT 0;

-- 5. Fix shipments table (if it has monetary columns)
ALTER TABLE shipments 
MODIFY COLUMN total_charges DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN total_paid DECIMAL(10,3) DEFAULT 0,
MODIFY COLUMN balance_due DECIMAL(10,3) DEFAULT 0;

-- 6. Update existing stored procedure to use 3 decimal places
DROP PROCEDURE IF EXISTS update_invoice_payment_summary;

DELIMITER //

CREATE PROCEDURE update_invoice_payment_summary(
    IN p_invoice_number VARCHAR(100)
)
BEGIN
    DECLARE v_invoice_total DECIMAL(10,3);
    DECLARE v_total_paid DECIMAL(10,3);
    DECLARE v_balance_due DECIMAL(10,3);
    DECLARE v_payment_status VARCHAR(20);
    DECLARE v_shipment_id VARCHAR(100);
    DECLARE v_customer_id INT;
    
    -- Get shipment details
    SELECT shipment_id, customer_id INTO v_shipment_id, v_customer_id
    FROM shipments
    WHERE invoice_number = p_invoice_number
    LIMIT 1;
    
    -- Calculate invoice total with 3 decimal precision
    SELECT ROUND(COALESCE(SUM(total_amount), 0), 3) INTO v_invoice_total
    FROM shipment_charges sc
    JOIN shipments s ON sc.shipment_id = s.shipment_id
    WHERE s.invoice_number = p_invoice_number;
    
    -- Calculate total paid with 3 decimal precision
    SELECT ROUND(COALESCE(SUM(ABS(payment_amount)), 0), 3) INTO v_total_paid
    FROM payment_receipts
    WHERE invoice_number = p_invoice_number
    AND (payment_type = 'payment' OR payment_type IS NULL OR payment_type = '');
    
    -- Calculate balance with 3 decimal precision
    SET v_balance_due = ROUND(v_invoice_total - v_total_paid, 3);
    
    -- Ensure balance is not negative
    IF v_balance_due < 0 THEN
        SET v_balance_due = 0;
    END IF;
    
    -- Determine payment status based on rounded balance
    IF v_invoice_total = 0 THEN
        SET v_payment_status = 'unpaid';
    ELSEIF v_balance_due = 0 THEN
        SET v_payment_status = 'paid';
    ELSEIF v_balance_due < 0 THEN
        SET v_payment_status = 'overpaid';
    ELSEIF v_total_paid > 0 THEN
        SET v_payment_status = 'partial';
    ELSE
        SET v_payment_status = 'unpaid';
    END IF;
    
    -- Update or insert summary
    INSERT INTO invoice_payment_summary (
        invoice_number, shipment_id, customer_id,
        invoice_total, total_paid, balance_due,
        payment_status, last_payment_date
    ) VALUES (
        p_invoice_number, v_shipment_id, v_customer_id,
        v_invoice_total, v_total_paid, v_balance_due,
        v_payment_status, NOW()
    )
    ON DUPLICATE KEY UPDATE
        invoice_total = v_invoice_total,
        total_paid = v_total_paid,
        balance_due = v_balance_due,
        payment_status = v_payment_status,
        last_payment_date = NOW();
        
    -- Update shipment status if fully paid
    IF v_payment_status = 'paid' THEN
        UPDATE shipments 
        SET status = 'paid',
            total_charges = v_invoice_total,
            total_paid = v_total_paid,
            balance_due = 0
        WHERE invoice_number = p_invoice_number;
    ELSE
        UPDATE shipments 
        SET total_charges = v_invoice_total,
            total_paid = v_total_paid,
            balance_due = v_balance_due
        WHERE invoice_number = p_invoice_number;
    END IF;
END //

DELIMITER ;

-- 7. Update process_payment procedure to use 3 decimal places
DROP PROCEDURE IF EXISTS process_payment;

DELIMITER //

CREATE PROCEDURE process_payment(
    IN p_customer_id INT,
    IN p_invoice_number VARCHAR(100),
    IN p_receipt_no VARCHAR(50),
    IN p_payment_amount DECIMAL(10,3),
    IN p_use_credit BOOLEAN,
    IN p_payment_date DATETIME,
    IN p_mode_of_payment VARCHAR(20),
    IN p_salesperson VARCHAR(50),
    IN p_note TEXT,
    IN p_description TEXT,
    IN p_currency_name VARCHAR(50),
    IN p_currency_roe DECIMAL(10,3),
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_invoice_total DECIMAL(10,3);
    DECLARE v_total_paid DECIMAL(10,3);
    DECLARE v_credit_balance DECIMAL(10,3);
    DECLARE v_amount_from_credit DECIMAL(10,3) DEFAULT 0;
    DECLARE v_amount_from_payment DECIMAL(10,3);
    DECLARE v_remaining_balance DECIMAL(10,3);
    DECLARE v_excess_amount DECIMAL(10,3) DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'error';
        SET p_message = 'Database error occurred';
    END;
    
    START TRANSACTION;
    
    -- Get invoice total with 3 decimal precision
    SELECT ROUND(COALESCE(SUM(total_amount), 0), 3) INTO v_invoice_total
    FROM shipment_charges sc
    JOIN shipments s ON sc.shipment_id = s.shipment_id
    WHERE s.invoice_number = p_invoice_number;
    
    IF v_invoice_total = 0 THEN
        SET p_status = 'error';
        SET p_message = 'Invoice not found or has no charges';
        ROLLBACK;
    ELSE
        -- Get already paid amount with 3 decimal precision
        SELECT ROUND(COALESCE(SUM(ABS(payment_amount)), 0), 3) INTO v_total_paid
        FROM payment_receipts
        WHERE invoice_number = p_invoice_number 
        AND (payment_type = 'payment' OR payment_type IS NULL OR payment_type = '');
        
        -- Calculate remaining with 3 decimal precision
        SET v_remaining_balance = ROUND(v_invoice_total - v_total_paid, 3);
        
        -- Check if already paid (exact comparison after rounding)
        IF v_remaining_balance <= 0 THEN
            SET p_status = 'error';
            SET p_message = 'Invoice is already fully paid';
            ROLLBACK;
        ELSE
            -- Get customer credit balance with 3 decimal precision
            SELECT ROUND(COALESCE(credit_balance, 0), 3) INTO v_credit_balance
            FROM customer_credits
            WHERE customer_id = p_customer_id;
            
            -- Calculate payment split with 3 decimal precision
            IF p_use_credit AND v_credit_balance > 0 THEN
                SET v_amount_from_credit = ROUND(LEAST(v_credit_balance, v_remaining_balance), 3);
                SET v_remaining_balance = ROUND(v_remaining_balance - v_amount_from_credit, 3);
            END IF;
            
            SET v_amount_from_payment = ROUND(LEAST(p_payment_amount, v_remaining_balance), 3);
            SET v_excess_amount = ROUND(p_payment_amount - v_amount_from_payment, 3);
            
            -- Insert payment record
            INSERT INTO payment_receipts (
                customer_id, invoice_number, receipt_no,
                payment_amount, payment_type, full_payment,
                payment_date, mode_of_payment, salesperson,
                note, description, currency_name, currency_roe
            ) VALUES (
                p_customer_id, p_invoice_number, p_receipt_no,
                v_amount_from_payment, 'payment',
                IF(ROUND(v_amount_from_payment + v_amount_from_credit - v_remaining_balance, 3) >= -0.01, 1, 0),
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
                
                UPDATE customer_credits 
                SET credit_balance = ROUND(credit_balance - v_amount_from_credit, 3)
                WHERE customer_id = p_customer_id;
            END IF;
            
            -- Add excess to credit
            IF v_excess_amount > 0 THEN
                INSERT INTO customer_credits (customer_id, credit_balance)
                VALUES (p_customer_id, v_excess_amount)
                ON DUPLICATE KEY UPDATE credit_balance = ROUND(credit_balance + v_excess_amount, 3);
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

-- 8. Recalculate all invoice payment summaries with new precision
-- This will fix any existing records with wrong status
DELETE FROM invoice_payment_summary;

INSERT INTO invoice_payment_summary (
    invoice_number, shipment_id, customer_id,
    invoice_total, total_paid, balance_due, payment_status
)
SELECT 
    s.invoice_number,
    s.shipment_id,
    s.customer_id,
    ROUND(COALESCE(SUM(sc.total_amount), 0), 3) AS invoice_total,
    ROUND(COALESCE(SUM(ABS(pr.payment_amount)), 0), 3) AS total_paid,
    ROUND(COALESCE(SUM(sc.total_amount), 0) - COALESCE(SUM(ABS(pr.payment_amount)), 0), 3) AS balance_due,
    CASE 
        WHEN ROUND(COALESCE(SUM(sc.total_amount), 0) - COALESCE(SUM(ABS(pr.payment_amount)), 0), 3) = 0 THEN 'paid'
        WHEN COALESCE(SUM(ABS(pr.payment_amount)), 0) > 0 THEN 'partial'
        ELSE 'unpaid'
    END AS payment_status
FROM shipments s
LEFT JOIN shipment_charges sc ON s.shipment_id = sc.shipment_id
LEFT JOIN payment_receipts pr ON CAST(s.invoice_number AS CHAR) = CAST(pr.invoice_number AS CHAR)
    AND (pr.payment_type = 'payment' OR pr.payment_type IS NULL OR pr.payment_type = '')
GROUP BY s.invoice_number, s.shipment_id, s.customer_id;

-- 9. Update shipment statuses based on corrected summaries
UPDATE shipments s
JOIN invoice_payment_summary ips ON CAST(s.invoice_number AS CHAR) = CAST(ips.invoice_number AS CHAR)
SET s.status = 'paid',
    s.total_charges = ips.invoice_total,
    s.total_paid = ips.total_paid,
    s.balance_due = 0
WHERE ips.payment_status = 'paid' AND s.status != 'paid';

-- Done!
SELECT 'Decimal precision fix completed successfully!' AS message;
