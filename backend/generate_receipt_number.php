<?php
require_once 'conn.php';
require_once 'services/PaymentServices.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$invoiceNumber = $input['invoice_number'] ?? null;

if (!$invoiceNumber) {
    echo json_encode(['status' => 'error', 'message' => 'Invoice number is required']);
    exit;
}

try {
    // Initialize PaymentService
    $paymentService = new PaymentService($conn);
    
    // Generate next receipt number
    $receiptNumber = $paymentService->generateNextReceiptNumber($invoiceNumber);
    
    echo json_encode([
        'status' => 'success',
        'receipt_number' => $receiptNumber
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error generating receipt number: ' . $e->getMessage()
    ]);
}

$conn->close();
?>