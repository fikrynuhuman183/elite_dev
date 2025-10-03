<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Check if data is properly decoded
if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$receipt_id = $data['receipt_no'];
$deleted_id = null; // Store the deleted ID

// Check if the receipt exists
$check_sql = "SELECT * FROM payment_receipts WHERE receipt_no='$receipt_id'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $deleted_id = $row['id']; // Store the deleted receipt's ID

    // Delete receipt and its charges
    $sql_clear = "DELETE FROM payment_receipts WHERE id='$deleted_id'";
    $conn->query($sql_clear);
}
if ($receipt_id == '') {
    // Generate random receipt ID using timestamp and random number
    $timestamp = time(); // Current Unix timestamp
    $random_num = mt_rand(1000, 9999); // Random 4-digit number
    $receipt_id = 'RCPT-CUS' . date('Ymd-His', $timestamp) . '-' . $random_num;
    
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Step 1: Get customer_id from customer name
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE name = ?");
    $stmt->bind_param("s", $data['customer']);  // Assuming $data['customer'] contains the name
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Customer not found: " . $data['customer']);
    }

    $customer_row = $result->fetch_assoc();
    $customer_id = $customer_row['customer_id'];
    $stmt->close();

    // Step 2: Insert into payment_receipts
    $stmt = $conn->prepare("
        INSERT INTO payment_receipts (
            id, payment_date, receipt_no, customer_id, 
            mode_of_payment, note, payment_amount, description, currency_name, currency_roe, salesperson
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $id_to_insert = $deleted_id ?? NULL;

    $stmt->bind_param(
        "ississdssds",
        $id_to_insert,
        $data['date'],
        $receipt_id,
        $customer_id,
        $data['mode_of_payment'],
        $data['special_note'],
        $data['total'],
        $data['description'],
        $data['currency_name'],
        $data['currency_roe'],
        $data['salesperson']
    );

    if (!$stmt->execute()) {
        throw new Exception("Error inserting receipt: " . $stmt->error);
    }

    
    $stmt->close();

    // Commit transaction
    $conn->commit();

    // Send success response
    http_response_code(200);
    echo json_encode(['success' => true, 'receipt_id' => $receipt_id]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
