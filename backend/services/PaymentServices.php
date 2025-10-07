<?php

class PaymentService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Update customer credit balance with transaction type
    public function updateCustomerCreditBalance($customerId, $amount, $transactionType) {
        try {
            $this->conn->begin_transaction();
            
            // Get or create customer credit record
            $stmt = $this->conn->prepare("
                INSERT INTO customer_credits (customer_id, credit_balance)
                VALUES (?, 0)
                ON DUPLICATE KEY UPDATE credit_balance = credit_balance
            ");
            $stmt->bind_param("i", $customerId);
            $stmt->execute();
            
            // Update balance based on transaction type
            if ($transactionType === 'add') {
                $sql = "UPDATE customer_credits SET credit_balance = credit_balance + ? WHERE customer_id = ?";
            } else {
                $sql = "UPDATE customer_credits SET credit_balance = credit_balance - ? WHERE customer_id = ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("di", $amount, $customerId);
            $stmt->execute();
            
            $this->conn->commit();
            return ['status' => 'success', 'message' => 'Credit balance updated successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'message' => 'Failed to update credit balance: ' . $e->getMessage()];
        }
    }
    
    // Process payment transaction with credit support
    public function processPayment($params) {
        $customerId = $params['customer_id'];
        $invoiceNumber = $params['invoice_number'];
        $receiptNo = $params['receipt_no'];
        $paymentAmount = round($params['payment_amount'], 3);
        $useCredit = $params['use_credit'] ?? false;
        $paymentDate = $params['payment_date'];
        $modeOfPayment = $params['mode_of_payment'];
        $salesperson = $params['salesperson'] ?? '';
        $note = $params['note'] ?? '';
        $description = $params['description'] ?? '';
        $currencyName = $params['currency_name'] ?? 'AED';
        $currencyRoe = $params['currency_roe'] ?? 1.0;
        
        try {
            $this->conn->begin_transaction();
            
            // Get invoice total
            $invoiceTotal = $this->getInvoiceTotal($invoiceNumber);
            if ($invoiceTotal === 0) {
                throw new Exception('Invoice not found or has no charges');
            }
            
            // Get already paid amount
            $totalPaid = $this->getTotalPaidAmount($invoiceNumber);
            $remainingBalance = round($invoiceTotal - $totalPaid, 3);
            
            // Check if already fully paid (using whole number comparison)
            if (round($remainingBalance, 0) <= 0) {
                throw new Exception('Invoice is already fully paid');
            }
            
            // Get customer credit balance
            $creditBalance = $this->getCustomerCreditBalance($customerId);
            
            // Calculate payment split
            $amountFromCredit = 0;
            if ($useCredit && $creditBalance > 0) {
                $amountFromCredit = min($creditBalance, $remainingBalance);
                $remainingBalance -= $amountFromCredit;
            }
            
            $amountFromPayment = min($paymentAmount, $remainingBalance);
            $excessAmount = $paymentAmount - $amountFromPayment;
            
            // Insert main payment record
            $this->insertPaymentRecord([
                'customer_id' => $customerId,
                'invoice_number' => $invoiceNumber,
                'receipt_no' => $receiptNo,
                'payment_amount' => $amountFromPayment,
                'payment_type' => 'payment',
                'full_payment' => (round($amountFromPayment + $amountFromCredit, 0) >= round($remainingBalance + $amountFromPayment, 0)),
                'payment_date' => $paymentDate,
                'mode_of_payment' => $modeOfPayment,
                'salesperson' => $salesperson,
                'note' => $note,
                'description' => $description,
                'currency_name' => $currencyName,
                'currency_roe' => $currencyRoe
            ]);
            
            // Process credit deduction if used
            if ($amountFromCredit > 0) {
                $this->insertPaymentRecord([
                    'customer_id' => $customerId,
                    'invoice_number' => $invoiceNumber,
                    'receipt_no' => $receiptNo . '-CRD',
                    'payment_amount' => $amountFromCredit,
                    'payment_type' => 'credit_deduction',
                    'full_payment' => 0,
                    'payment_date' => $paymentDate,
                    'mode_of_payment' => 'credit',
                    'salesperson' => $salesperson,
                    'note' => $note,
                    'description' => $description,
                    'currency_name' => $currencyName,
                    'currency_roe' => $currencyRoe
                ]);
                
                $this->updateCustomerCreditBalance($customerId, $amountFromCredit, 'deduct');
            }
            
            // Add excess amount to credit
            if ($excessAmount > 0) {
                $this->updateCustomerCreditBalance($customerId, $excessAmount, 'add');
            }
            
            // Update shipment status if fully paid (using whole number comparison)
            $newTotalPaid = $totalPaid + $amountFromPayment + $amountFromCredit;
            if (round($newTotalPaid, 0) >= round($invoiceTotal, 0)) {
                $this->updateShipmentStatus($invoiceNumber, 'paid');
            }
            
            // Update invoice payment summary
            $this->updateInvoicePaymentSummary($invoiceNumber);
            
            $this->conn->commit();
            return ['status' => 'success', 'message' => 'Payment processed successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    // Get invoice total from shipment charges
    public function getInvoiceTotal($invoiceNumber) {
        $stmt = $this->conn->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total
            FROM shipment_charges sc
            JOIN shipments s ON sc.shipment_id = s.shipment_id
            WHERE s.invoice_number = ?
        ");
        $stmt->bind_param("s", $invoiceNumber);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return round($result['total'], 3);
    }
    
    // Get total paid amount for an invoice
    public function getTotalPaidAmount($invoiceNumber) {
        $stmt = $this->conn->prepare("
            SELECT COALESCE(SUM(ABS(payment_amount)), 0) as total_paid
            FROM payment_receipts
            WHERE invoice_number = ? AND invoice_number != '0'
        ");
        $stmt->bind_param("s", $invoiceNumber);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return round($result['total_paid'], 3);
    }
    
    // Get customer credit balance
    public function getCustomerCreditBalance($customerId) {
        // Try the new customer_credits table first
        $stmt = $this->conn->prepare("
            SELECT COALESCE(credit_balance, 0) as balance
            FROM customer_credits
            WHERE customer_id = ?
        ");
        $stmt->bind_param("i", $customerId);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_assoc();
            if ($result) {
                return round($result['balance'] ?? 0, 3);
            }
        }
        
        // Fallback to old calculation method if customer_credits table doesn't exist or has no data
        $stmt = $this->conn->prepare("
            SELECT COALESCE(SUM(
                CASE
                    WHEN invoice_number = '0' THEN payment_amount
                    WHEN invoice_number != '0' AND payment_amount < 0 THEN payment_amount
                    ELSE 0
                END
            ), 0) AS credit
            FROM payment_receipts
            WHERE customer_id = ?
            AND (invoice_number = '0' OR payment_amount < 0)
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return round($result['credit'] ?? 0, 3);
    }
    
    // Insert payment record with all fields (made public for flexibility)
    public function insertPaymentRecord($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO payment_receipts (
                customer_id, invoice_number, receipt_no, payment_amount, 
                payment_type, full_payment, payment_date, mode_of_payment, 
                salesperson, note, description, currency_name, currency_roe
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Ensure all variables are properly set for bind_param
        $salesperson = $data['salesperson'] ?? '';
        $note = $data['note'] ?? '';
        $description = $data['description'] ?? '';
        $currency_name = $data['currency_name'] ?? 'AED';
        $currency_roe = $data['currency_roe'] ?? 1.0;
        
        $stmt->bind_param(
            "issdsissssssd",
            $data['customer_id'],
            $data['invoice_number'],
            $data['receipt_no'],
            $data['payment_amount'],
            $data['payment_type'],
            $data['full_payment'],
            $data['payment_date'],
            $data['mode_of_payment'],
            $salesperson,
            $note,
            $description,
            $currency_name,
            $currency_roe
        );
        
        return $stmt->execute();
    }
    
    // Update shipment status
    private function updateShipmentStatus($invoiceNumber, $status) {
        $stmt = $this->conn->prepare("UPDATE shipments SET status = ? WHERE invoice_number = ?");
        $stmt->bind_param("ss", $status, $invoiceNumber);
        return $stmt->execute();
    }
    
    // Update invoice payment summary table
    public function updateInvoicePaymentSummary($invoiceNumber) {
        // Get basic invoice info first
        $infoStmt = $this->conn->prepare("
            SELECT shipment_id, customer_id 
            FROM shipments 
            WHERE invoice_number = ?
        ");
        $infoStmt->bind_param("s", $invoiceNumber);
        $infoStmt->execute();
        $invoiceInfo = $infoStmt->get_result()->fetch_assoc();
        
        if (!$invoiceInfo) {
            return false;
        }
        
        // Pre-aggregate charges by invoice with 3 decimal precision (matching analysis document)
        $chargesStmt = $this->conn->prepare("
            SELECT ROUND(COALESCE(SUM(sc.total_amount), 0), 3) AS invoice_total
            FROM shipments s
            LEFT JOIN shipment_charges sc ON s.shipment_id = sc.shipment_id
            WHERE s.invoice_number = ?
        ");
        $chargesStmt->bind_param("s", $invoiceNumber);
        $chargesStmt->execute();
        $chargesResult = $chargesStmt->get_result()->fetch_assoc();
        $invoiceTotal = $chargesResult['invoice_total'];
        
        // Pre-aggregate payments by invoice (matching PHP logic exactly) with 3 decimal precision
        $paymentsStmt = $this->conn->prepare("
            SELECT 
                ROUND(COALESCE(SUM(ABS(payment_amount)), 0), 3) AS total_paid
            FROM payment_receipts
            WHERE invoice_number = ? AND invoice_number != '0'
        ");
        $paymentsStmt->bind_param("s", $invoiceNumber);
        $paymentsStmt->execute();
        $paymentsResult = $paymentsStmt->get_result()->fetch_assoc();
        $totalPaid = $paymentsResult['total_paid'];
        
        // Get credit deductions separately for tracking
        $creditsStmt = $this->conn->prepare("
            SELECT 
                ROUND(COALESCE(SUM(ABS(payment_amount)), 0), 3) AS total_credits_used
            FROM payment_receipts
            WHERE invoice_number = ? AND payment_type = 'credit_deduction'
        ");
        $creditsStmt->bind_param("s", $invoiceNumber);
        $creditsStmt->execute();
        $creditsResult = $creditsStmt->get_result()->fetch_assoc();
        $totalCreditsUsed = $creditsResult['total_credits_used'];
        
        // Calculate balance due using the same logic as the analysis document
        $balanceDue = round(max(0, $invoiceTotal - $totalPaid), 3);
        
        // Determine payment status using whole number comparison (matching analysis document)
        if (round($totalPaid, 0) >= round($invoiceTotal, 0)) {
            $paymentStatus = 'paid';
        } elseif (round($totalPaid, 0) > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'unpaid';
        }
        
        // Get last payment date
        $lastPaymentStmt = $this->conn->prepare("
            SELECT MAX(payment_date) as last_payment_date
            FROM payment_receipts
            WHERE invoice_number = ? AND invoice_number != '0'
        ");
        $lastPaymentStmt->bind_param("s", $invoiceNumber);
        $lastPaymentStmt->execute();
        $lastPaymentResult = $lastPaymentStmt->get_result()->fetch_assoc();
        
        // Insert or update summary
        $summaryStmt = $this->conn->prepare("
            INSERT INTO invoice_payment_summary (
                invoice_number, shipment_id, customer_id, invoice_total,
                total_paid, total_credits_used, balance_due, payment_status, last_payment_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                invoice_total = VALUES(invoice_total),
                total_paid = VALUES(total_paid),
                total_credits_used = VALUES(total_credits_used),
                balance_due = VALUES(balance_due),
                payment_status = VALUES(payment_status),
                last_payment_date = VALUES(last_payment_date)
        ");
        
        $summaryStmt->bind_param(
            "sisddddss",
            $invoiceNumber,
            $invoiceInfo['shipment_id'],
            $invoiceInfo['customer_id'],
            $invoiceTotal,
            $totalPaid,
            $totalCreditsUsed,
            $balanceDue,
            $paymentStatus,
            $lastPaymentResult['last_payment_date']
        );
        
        return $summaryStmt->execute();
    }
    
    // Get customer invoices summary with payment status
    public function getCustomerInvoicesSummary($customerId, $limit = null, $offset = 0) {
        $limitClause = $limit ? "LIMIT ? OFFSET ?" : "";
        
        // Get invoice data with fallback for missing invoice_payment_summary table
        $stmt = $this->conn->prepare("
            SELECT 
                s.invoice_number,
                s.shipment_id,
                s.invoice_date,
                s.port_of_origin,
                s.port_of_destination,
                ROUND(COALESCE(charges.invoice_total, 0), 3) AS total_charges,
                ROUND(COALESCE(payments.total_paid, 0), 3) AS total_paid,
                GREATEST(0, ROUND(COALESCE(charges.invoice_total, 0), 3) - ROUND(COALESCE(payments.total_paid, 0), 3)) AS due_amount,
                CASE 
                    WHEN ROUND(COALESCE(payments.total_paid, 0), 0) >= ROUND(COALESCE(charges.invoice_total, 0), 0) THEN 'paid'
                    WHEN ROUND(COALESCE(payments.total_paid, 0), 0) > 0 THEN 'partial'
                    ELSE 'unpaid'
                END AS payment_status
            FROM shipments s
            LEFT JOIN (
                SELECT 
                    s2.invoice_number,
                    ROUND(SUM(sc.total_amount), 3) AS invoice_total
                FROM shipments s2
                LEFT JOIN shipment_charges sc ON s2.shipment_id = sc.shipment_id
                GROUP BY s2.invoice_number
            ) charges ON s.invoice_number = charges.invoice_number
            LEFT JOIN (
                SELECT 
                    invoice_number,
                    ROUND(SUM(ABS(payment_amount)), 3) AS total_paid
                FROM payment_receipts
                WHERE invoice_number != '0'
                GROUP BY invoice_number
            ) payments ON s.invoice_number = payments.invoice_number
            WHERE s.customer_id = ?
            ORDER BY s.invoice_date DESC
            $limitClause
        ");
        
        if ($limit) {
            $stmt->bind_param("iii", $customerId, $limit, $offset);
        } else {
            $stmt->bind_param("i", $customerId);
        }
        
        $stmt->execute();
        $invoices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Organize invoices by payment status
        $organized = [
            'paid' => [],
            'partial' => [],
            'unpaid' => []
        ];
        
        $totals = [
            'total_jobs' => 0,
            'closed_jobs' => 0,
            'due_jobs' => 0,
            'total_paid_amount' => 0,
            'total_due_amount' => 0
        ];
        
        foreach ($invoices as $invoice) {
            $status = $invoice['payment_status'];
            $organized[$status][] = $invoice;
            
            $totals['total_jobs']++;
            $totals['total_paid_amount'] += $invoice['total_paid'];
            $totals['total_due_amount'] += $invoice['due_amount'];
            
            if ($status === 'paid') {
                $totals['closed_jobs']++;
            } else {
                $totals['due_jobs']++;
            }
        }
        
        return [
            'invoices' => $organized,
            'totals' => $totals
        ];
    }
    
    // Get customer payment history
    public function getCustomerPaymentHistory($customerId, $limit = null, $offset = 0) {
        $limitClause = $limit ? "LIMIT ? OFFSET ?" : "";
        
        $stmt = $this->conn->prepare("
            SELECT 
                pr.receipt_no,
                pr.invoice_number,
                pr.payment_amount,
                pr.payment_type,
                pr.payment_date,
                pr.mode_of_payment,
                pr.note,
                pr.description,
                pr.currency_name,
                pr.currency_roe
            FROM payment_receipts pr
            WHERE pr.customer_id = ?
            ORDER BY pr.payment_date DESC
            $limitClause
        ");
        
        if ($limit) {
            $stmt->bind_param("iii", $customerId, $limit, $offset);
        } else {
            $stmt->bind_param("i", $customerId);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get invoice payment details
    public function getInvoicePaymentDetails($invoiceNumber) {
        $stmt = $this->conn->prepare("
            SELECT 
                ips.*,
                s.invoice_date,
                s.port_of_origin,
                s.port_of_destination,
                c.customer_name
            FROM invoice_payment_summary ips
            JOIN shipments s ON ips.invoice_number = s.invoice_number
            JOIN customers c ON ips.customer_id = c.customer_id
            WHERE ips.invoice_number = ?
        ");
        
        $stmt->bind_param("s", $invoiceNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Add credit to customer account
    public function addCustomerCredit($customerId, $amount, $receiptNo, $note = '', $description = '') {
        try {
            $this->conn->begin_transaction();
            
            // Insert credit topup record
            $this->insertPaymentRecord([
                'customer_id' => $customerId,
                'invoice_number' => '0',
                'receipt_no' => $receiptNo,
                'payment_amount' => $amount,
                'payment_type' => 'credit_topup',
                'full_payment' => 0,
                'payment_date' => date('Y-m-d H:i:s'),
                'mode_of_payment' => 'credit',
                'salesperson' => '',
                'note' => $note,
                'description' => $description,
                'currency_name' => 'AED',
                'currency_roe' => 1.0
            ]);
            
            // Update credit balance
            $result = $this->updateCustomerCreditBalance($customerId, $amount, 'add');
            
            if ($result['status'] === 'success') {
                $this->conn->commit();
                return ['status' => 'success', 'message' => 'Credit added successfully'];
            } else {
                throw new Exception($result['message']);
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'message' => 'Failed to add credit: ' . $e->getMessage()];
        }
    }
    
    // Calculate payment status for multiple invoices
    public function calculatePaymentStatuses($invoiceNumbers) {
        $placeholders = str_repeat('?,', count($invoiceNumbers) - 1) . '?';
        
        $stmt = $this->conn->prepare("
            SELECT 
                s.invoice_number,
                ROUND(COALESCE(SUM(sc.total_amount), 0), 3) as invoice_total,
                ROUND(COALESCE(SUM(CASE WHEN pr.invoice_number != '0' THEN ABS(pr.payment_amount) ELSE 0 END), 0), 3) as total_paid,
                ROUND(COALESCE(SUM(CASE WHEN pr.payment_type = 'credit_deduction' THEN ABS(pr.payment_amount) ELSE 0 END), 0), 3) as total_credits_used
            FROM shipments s
            LEFT JOIN shipment_charges sc ON s.shipment_id = sc.shipment_id
            LEFT JOIN payment_receipts pr ON s.invoice_number = pr.invoice_number
            WHERE s.invoice_number IN ($placeholders)
            GROUP BY s.invoice_number
        ");
        
        $stmt->bind_param(str_repeat('s', count($invoiceNumbers)), ...$invoiceNumbers);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $statuses = [];
        foreach ($results as $row) {
            $invoiceTotal = $row['invoice_total'];
            $totalPaid = $row['total_paid'];
            $totalCreditsUsed = $row['total_credits_used'];
            $balanceDue = round($invoiceTotal - $totalPaid - $totalCreditsUsed, 3);
            
            if ($balanceDue < 0) {
                $balanceDue = 0;
            }
            
            // Determine payment status using whole number comparison
            if (round($balanceDue, 0) === 0) {
                $paymentStatus = 'paid';
            } elseif (round($totalPaid, 0) > 0 || round($totalCreditsUsed, 0) > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'unpaid';
            }
            
            $statuses[$row['invoice_number']] = [
                'invoice_total' => $invoiceTotal,
                'total_paid' => $totalPaid,
                'total_credits_used' => $totalCreditsUsed,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus
            ];
        }
        
        return $statuses;
    }
    
    // Get customer details
    public function getCustomerDetails($customerId) {
        $stmt = $this->conn->prepare("
            SELECT customer_id, name, email, phone, location
            FROM customers
            WHERE customer_id = ?
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Get customer credit summary with transactions
    public function getCustomerCreditSummary($customerId) {
        // Get credit transactions
        $stmt = $this->conn->prepare("
            SELECT 
                payment_amount as amount,
                payment_date,
                payment_type as type,
                note,
                CASE 
                    WHEN payment_type = 'credit_topup' THEN 'Credit Top-up'
                    WHEN payment_type = 'credit_deduction' THEN 'Credit Used'
                    ELSE 'Other'
                END as type_label
            FROM payment_receipts
            WHERE customer_id = ? 
            AND (payment_type = 'credit_topup' OR payment_type = 'credit_deduction')
            ORDER BY payment_date DESC
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Calculate totals
        $topups = 0;
        $deductions = 0;
        
        foreach ($transactions as $transaction) {
            if ($transaction['type'] === 'credit_topup') {
                $topups += abs($transaction['amount']);
            } elseif ($transaction['type'] === 'credit_deduction') {
                $deductions += abs($transaction['amount']);
            }
        }
        
        $remaining_credit = $this->getCustomerCreditBalance($customerId);
        
        return [
            'transactions' => $transactions,
            'topups' => round($topups, 3),
            'deductions' => round($deductions, 3),
            'remaining_credit' => $remaining_credit
        ];
    }
}
