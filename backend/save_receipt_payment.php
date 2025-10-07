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

    // Prepare payment data for PaymentService
    $payment_amount = floatval($data['total']);
    
    // Handle credit deduction (negative amount for credit deduction)
    $is_credit_deduction = !empty($data['deduct_credit']);
    if ($is_credit_deduction) {
        $payment_amount = abs($payment_amount); // Keep positive, let PaymentService handle the logic
    }

    $paymentData = [
        'customer_id' => $customer_id,
        'invoice_number' => $invoice_no,
        'receipt_no' => $receipt_id,
        'payment_amount' => $payment_amount,
        'use_credit' => !empty($data['use_existing_credit']) || $is_credit_deduction,
        'payment_date' => $data['date'],
        'mode_of_payment' => $data['mode_of_payment'] ?? 'cash',
        'salesperson' => $data['salesperson'] ?? '',
        'note' => $data['special_note'] ?? '',
        'description' => $data['description'] ?? '',
        'currency_name' => $data['currency_name'] ?? 'AED',
        'currency_roe' => floatval($data['currency_roe'] ?? 1.0)
    ];

    // For credit deduction, we need special handling
    if ($is_credit_deduction) {
        // This is a credit deduction, not a regular payment
        $result = $paymentService->updateCustomerCreditBalance($customer_id, $payment_amount, 'deduct');
        
        if ($result['status'] !== 'success') {
            throw new Exception($result['message']);
        }
        
        // Insert the credit deduction record
        $creditDeductionData = [
            'customer_id' => $customer_id,
            'invoice_number' => $invoice_no,
            'receipt_no' => $receipt_id,
            'payment_amount' => $payment_amount,
            'payment_type' => 'credit_deduction',
            'full_payment' => 0,
            'payment_date' => $data['date'],
            'mode_of_payment' => 'credit',
            'note' => $data['special_note'] ?? ''
        ];
        
        // Use reflection to access private method or create a public wrapper
        $insertResult = $paymentService->insertPaymentRecord($creditDeductionData);
        
    } else {
        // Regular payment processing
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
