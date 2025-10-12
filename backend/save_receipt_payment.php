<?php
include 'conn.php';
require_once 'services/PaymentServices.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Initialize PaymentService
$paymentService = new PaymentService($conn);

$invoice_no = $data['invoice_no'] ?? '';

// Check if shipment is already marked as paid using PaymentService
$invoiceTotal = $paymentService->getInvoiceTotal($invoice_no);
if ($invoiceTotal === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invoice not found or has no charges']);
    exit;
}

$totalPaid = $paymentService->getTotalPaidAmount($invoice_no);
$remainingBalance = round($invoiceTotal - $totalPaid, 3);

if (round($remainingBalance, 0) <= 0) {
    http_response_code(400);
    echo json_encode(['error' => "This invoice is already marked as paid. No more payments can be added."]);
    exit;
}

$receipt_id = $data['receipt_no'];

if ($receipt_id == '') {
    // Generate random receipt ID using timestamp and random number
    $timestamp = time(); // Current Unix timestamp
    $random_num = mt_rand(1000, 9999); // Random 4-digit number
    $receipt_id = 'RCPT-INV' . date('Ymd-His', $timestamp) . '-' . $random_num;
}

// Check if receipt already exists and delete if found
$check_sql = "SELECT * FROM payment_receipts WHERE receipt_no = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $receipt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $deleted_id = $row['id'];
    $conn->query("DELETE FROM payment_receipts WHERE id = '$deleted_id'");
}
$stmt->close();

try {
    // Get customer_id from customer name
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE name = ?");
    $stmt->bind_param("s", $data['customer']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Customer not found: " . $data['customer']);
    }
    $customer_id = $result->fetch_assoc()['customer_id'];
    $stmt->close();

    // Debug: Log the received data
    error_log("Payment data received: " . json_encode($data));

    // Prepare payment data for PaymentService
    $total_payment_amount = 0; // This will be in AED
    $total_payment_amount_original_currency = 0; // This will be in original currency
    $payment_methods = [];
    $cash_amount = 0;
    $cheque_amount = 0;
    $bank_transfer_amount = 0;
    $mode_of_payment = 'cash'; // Default
    
    // Use AED amount for backend processing if available
    if (isset($data['total_aed']) && $data['total_aed'] > 0) {
        $total_payment_amount = floatval($data['total_aed']); // AED amount
        $total_payment_amount_original_currency = floatval($data['total'] ?? $data['total_aed']);
    } else {
        // Fallback to original total if AED not provided
        $total_payment_amount = floatval($data['total'] ?? 0);
        $total_payment_amount_original_currency = $total_payment_amount;
    }
    
    // Handle multiple payment methods
    if (isset($data['payment_methods']) && count($data['payment_methods']) > 0) {
        foreach ($data['payment_methods'] as $method) {
            $amount_key = $method . '_amount';
            $description_key = $method . '_description';
            
            if (isset($data[$amount_key]) && $data[$amount_key] > 0) {
                $amount = floatval($data[$amount_key]); // Keep in original currency
                
                // Map to specific amount columns (in original currency)
                switch($method) {
                    case 'cash':
                        $cash_amount = $amount;
                        break;
                    case 'cheque':
                        $cheque_amount = $amount;
                        break;
                    case 'bank_transfer':
                        $bank_transfer_amount = $amount;
                        break;
                }
                
                $payment_methods[] = [
                    'method' => $method,
                    'amount' => $amount,
                    'description' => $data[$description_key] ?? $method . ' payment'
                ];
            }
        }
        
        // Set mode of payment
        if (count($payment_methods) > 1) {
            $mode_of_payment = 'multiple';
        } else if (count($payment_methods) == 1) {
            $mode_of_payment = $payment_methods[0]['method'];
        }
    }
    
    // Fallback to single payment if no payment methods specified
    if (empty($payment_methods) && isset($data['total']) && $data['total'] > 0) {
        $cash_amount = floatval($data['total']); // In original currency
        $mode_of_payment = $data['mode_of_payment'] ?? 'cash';
        $payment_methods[] = [
            'method' => $mode_of_payment,
            'amount' => $cash_amount,
            'description' => $data['cash_description'] ?? 'Payment'
        ];
    }
    
    if ($total_payment_amount <= 0) {
        throw new Exception("No valid payment amount provided");
    }
    
    // Validate that breakdown matches total in original currency (frontend validation check)
    $breakdown_total = $cash_amount + $cheque_amount + $bank_transfer_amount;
    if (abs($breakdown_total - $total_payment_amount_original_currency) > 0.01) {
        throw new Exception("Payment breakdown ($breakdown_total) doesn't match total amount ($total_payment_amount_original_currency)");
    }
    
    // Handle credit deduction (negative amount for credit deduction)
    $is_credit_deduction = !empty($data['deduct_credit']);
    if ($is_credit_deduction) {
        $total_payment_amount = abs($total_payment_amount); // Keep positive, let PaymentService handle the logic
    }

    $paymentData = [
        'customer_id' => $customer_id,
        'invoice_number' => $invoice_no,
        'receipt_no' => $receipt_id,
        'payment_amount' => $total_payment_amount, // This is now in AED
        'cash_amount' => $cash_amount, // Individual amounts in original currency
        'cheque_amount' => $cheque_amount,
        'bank_transfer_amount' => $bank_transfer_amount,
        'payment_type' => 'payment', // Keep this for database consistency
        'use_credit' => !empty($data['use_existing_credit']) || $is_credit_deduction,
        'payment_date' => $data['date'],
        'mode_of_payment' => $mode_of_payment,
        'salesperson' => $data['salesperson'] ?? '',
        'note' => $data['special_note'] ?? '',
        'description_cash' => $data['cash_description'] ?? '',
        'description_cheque' => $data['cheque_description'] ?? '',
        'description_bank_transfer' => $data['bank_transfer_description'] ?? '',
        'bank_details' => $data['bank_transfer_details'] ?? '',
        'cheque_details' => $data['cheque_details'] ?? '',
        'currency_name' => $data['currency_name'] ?? 'AED',
        'currency_roe' => floatval($data['currency_roe'] ?? 1.0)
    ];

    // For credit deduction, we need special handling
    if ($is_credit_deduction) {
        // This is a credit deduction, not a regular payment
        $result = $paymentService->updateCustomerCreditBalance($customer_id, $total_payment_amount, 'deduct');
        
        if ($result['status'] !== 'success') {
            throw new Exception($result['message']);
        }
        
        // Insert the credit deduction record
        $creditDeductionData = [
            'customer_id' => $customer_id,
            'invoice_number' => $invoice_no,
            'receipt_no' => $receipt_id,
            'payment_amount' => $total_payment_amount,
            'cash_amount' => 0.00,
            'cheque_amount' => 0.00,
            'bank_transfer_amount' => 0.00,
            'payment_type' => 'credit_deduction',
            'full_payment' => 0,
            'payment_date' => $data['date'],
            'mode_of_payment' => 'credit',
            'salesperson' => $data['salesperson'] ?? '',
            'note' => $data['special_note'] ?? '',
            'description_cash' => 'Credit deduction',
            'description_cheque' => '',
            'description_bank_transfer' => '',
            'bank_details' => '',
            'cheque_details' => '',
            'currency_name' => $data['currency_name'] ?? 'AED',
            'currency_roe' => floatval($data['currency_roe'] ?? 1.0)
        ];
        
        // Use reflection to access private method or create a public wrapper
        $insertResult = $paymentService->insertPaymentRecord($creditDeductionData);
        
    } else {
        // Process single payment with breakdown information
        $result = $paymentService->processPayment($paymentData);
        
        if ($result['status'] !== 'success') {
            throw new Exception($result['message']);
        }
    }

    // Send success response
    http_response_code(200);
    echo json_encode(['success' => true, 'receipt_id' => $receipt_id, 'message' => $result['message'] ?? 'Payment processed successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
