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
$check_sql = "SELECT * FROM receipts WHERE receipt_no='$receipt_id'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0 || $receipt_id == '') {
    $row = $result->fetch_assoc();
    $deleted_id = $row['id']; // Store the deleted receipt's ID

    // Delete receipt and its charges
    $sql_clear = "DELETE FROM receipts WHERE id='$deleted_id'";
    $conn->query($sql_clear);
    $sql_clear = "DELETE FROM receipt_charges WHERE receipt_id='$deleted_id'";
    $conn->query($sql_clear);
}

if ($receipt_id == '') {
    echo json_encode(['status' => 'success', 'message' => 'Empty receipt ID']);
    http_response_code(200);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Prepare the main receipt insert statement with the deleted ID (if exists)
    $stmt = $conn->prepare("
        INSERT INTO receipts (
            id, date, receipt_no, customer, address, salesperson, job,
            mode_of_payment, due_date, special_note, total_amount, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    // If there was a deleted receipt, reuse its ID; otherwise, use NULL for auto-increment
    $id_to_insert = $deleted_id ?? NULL;

    $stmt->bind_param(
        "isssssssssd",
        $id_to_insert,
        $data['date'],
        $data['receipt_no'],
        $data['customer'],
        $data['to'],
        $data['salesperson'],
        $data['job'],
        $data['mode_of_payment'],
        $data['due_date'],
        $data['special_note'],
        $data['total']
    );

    // Execute the main receipt insert
    if (!$stmt->execute()) {
        throw new Exception("Error inserting receipt: " . $stmt->error);
    }

    // Get the new receipt ID (either reused or auto-generated)
    $receipt_id = $id_to_insert ?? $conn->insert_id;
    $stmt->close();

    // Prepare the charges insert statement
    $chargeStmt = $conn->prepare("
        INSERT INTO receipt_charges (
            receipt_id, quantity, description, currency, unit_price, total_amount
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($data['borderCharges'] as $charge) {
        $chargeStmt->bind_param(
            "idssdd",
            $receipt_id,
            $charge['quantity'],
            $charge['description'],
            $charge['currency'],
            $charge['unit_price'],
            $charge['totalAmount']
        );

        if (!$chargeStmt->execute()) {
            throw new Exception("Error inserting charge: " . $chargeStmt->error);
        }
    }

    $chargeStmt->close();

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
