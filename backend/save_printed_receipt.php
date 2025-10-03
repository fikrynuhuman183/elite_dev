<?php
include 'conn.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$receipt_id = $data['receipt_id'] ?? '';
$html = $data['html_content'] ?? '';

if (!$receipt_id || !$html) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing receipt_id or html_content']);
    exit;
}

// Optional: sanitize/escape before storing
$stmt = $conn->prepare("REPLACE INTO saved_receipts (receipt_id, html_content) VALUES (?, ?)");
$stmt->bind_param("ss", $receipt_id, $html);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
