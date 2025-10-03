<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipmentId = $_POST['shipment_id'];

    if (empty($shipmentId)) {
        echo json_encode(['status' => 'error', 'message' => 'Shipment ID is required.']);
        http_response_code(400);
        exit;
    }

    if (!isset($_FILES['attachments'])) {
        echo json_encode(['status' => 'error', 'message' => 'No files uploaded.']);
        http_response_code(400);
        exit;
    }

    $uploadDir = '../uploads/';
    $uploadedFiles = [];

    foreach ($_FILES['attachments']['name'] as $key => $filename) {
        $fileTmpPath = $_FILES['attachments']['tmp_name'][$key];
        $filePath = $uploadDir . basename($filename);

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $attachmentPath = basename($filename);
            $attachmentSql = "INSERT INTO shipment_attachments (shipment_id, attachment_path) VALUES ('$shipmentId', '$attachmentPath')";

            if ($conn->query($attachmentSql)) {
                $uploadedFiles[] = $attachmentPath;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
                http_response_code(500);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file: ' . $filename]);
            http_response_code(500);
            exit;
        }
    }

    echo json_encode(['status' => 'success', 'uploadedFiles' => $uploadedFiles]);
    http_response_code(200);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    http_response_code(405);
}
?>