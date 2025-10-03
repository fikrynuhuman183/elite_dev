<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Create a directory for attachments if it doesn't exist
$target_dir = "supplier_attachments/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Extract the data from the POST request
$name = $_POST['name'];
$location = $_POST['location'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$join_date = $_POST['join_date'];
$expiry_date = $_POST['expiry_date'];

// Handle the file upload
$attachment = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
    $file_ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
    $unique_name = uniqid() . '.' . $file_ext;
    $attachment = $target_dir . $unique_name;
    if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment)) {
        echo json_encode(['status' => 'error', 'message' => 'Error uploading file.']);
        http_response_code(500); // Internal Server Error
        exit;
    }
}

// Insert the supplier data into the database
$sql = "INSERT INTO suppliers (name, location, phone, email, join_date, expiry_date, attachment) VALUES ('$name', '$location', '$phone', '$email', '$join_date', '$expiry_date', '$attachment')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully.']);
} else {
    // Handle error if needed
    echo json_encode(['status' => 'error', 'message' => 'Error adding supplier: ' . $conn->error]);
    http_response_code(500); // Internal Server Error
}

$conn->close();
?>
