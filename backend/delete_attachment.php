<?php
include 'conn.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

if (isset($data['id'])) {
    $attachmentId = $data['id'];

    // Get the file path
    $sql = "SELECT attachment_path FROM shipment_attachments WHERE id='$attachmentId'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = '../uploads/' . $row['attachment_path'];

        // Delete the file
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the record from the database
        $deleteSql = "DELETE FROM shipment_attachments WHERE id='$attachmentId'";
        if ($conn->query($deleteSql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Attachment not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>