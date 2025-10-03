<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

$target_dir = "driver_attachments/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Extract the data from the POST request
$supplier_id = $_POST['supplier_id'];
$driver_type = $_POST['driver_type'];
$name = $_POST['name'];
$license = $_POST['license'];
$phone = $_POST['phone'];
$email = $_POST['email'];

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

// Insert the driver data into the database
$sql = "INSERT INTO drivers (name, driver_type, license, phone, email, supplier_id, attachment) VALUES ('$name','$driver_type', '$license', '$phone', '$email', '$supplier_id', '$attachment')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Driver added successfully.']);
} else {
    // Handle error if needed
    echo json_encode(['status' => 'error', 'message' => 'Error adding driver: ' . $conn->error]);
    http_response_code(500); // Internal Server Error
}

$conn->close();
?>
