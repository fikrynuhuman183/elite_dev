<?php
// Include database connection
include 'conn.php';

// Get receipt ID from GET request
$receiptId = $_GET['id'] ?? null;

if (!$receiptId) {
    http_response_code(400);
    echo "Missing receipt ID.";
    exit;
}

// Escape the input just to be slightly safe
$receiptId = $conn->real_escape_string($receiptId);

// Run SQL query to fetch both html_content and receipt_no
$sql = "SELECT receipt_id, html_content FROM saved_receipts WHERE receipt_id = '$receiptId'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['receipt_id' => $row['receipt_id'], 'html_content' => $row['html_content']]);
} else {
    http_response_code(404);
    echo "Receipt not found.";
}
?>
