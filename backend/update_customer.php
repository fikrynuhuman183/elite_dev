<?php
include 'conn.php';

// Create a directory for attachments if it doesn't exist
$target_dir = "customer_attachments/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Extract data from POST request
$customer_id = $_POST['customer_id'];  // Hidden input or passed via form data attribute
$name = $_POST['name'];
$location = $_POST['location'];
$phone = $_POST['phone'];
$phone_optional = $_POST['phone_optional'];
$email = $_POST['email'];
$join_date = $_POST['join_date'];
$expiry_date = $_POST['expiry_date'];
$vat_number = $_POST['vat_number'];

if (!empty($_FILES['attachment']['name'][0])) {
    $total_files = count($_FILES['attachment']['name']);

    // Loop through each file
    for ($i = 0; $i < $total_files; $i++) {
        $file_name = $_FILES['attachment']['name'][$i];
        $file_tmp = $_FILES['attachment']['tmp_name'][$i];
        $file_type = $_FILES['attachment']['type'][$i];
        $file_size = $_FILES['attachment']['size'][$i];

        // You can customize this part to store the file, e.g., move it to a specific directory
        $upload_directory = 'customer_attachments/';
        $target_file = $upload_directory . basename($file_name);

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($file_tmp, $target_file)) {
            // Insert file details into the customer_attachments table
            $sql = "INSERT INTO customer_attachments (customer_id, attachment) VALUES ('$customer_id', '$file_name')";
            if ($conn->query($sql) !== TRUE) {
                echo json_encode(['status' => 'error', 'message' => 'Error adding attachment: ' . $conn->error]);
                http_response_code(500); // Internal Server Error
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading file: ' . $file_name]);
            http_response_code(500);
            exit();
        }
    }
    echo json_encode(['status' => 'success', 'message' => 'Customer added successfully.']);
}

// Update the customer record in the database
$sql = "UPDATE customers SET
        name = '$name',
        location = '$location',
        phone = '$phone',
        phone_optional = '$phone_optional',
        email = '$email',
        vat_number = '$vat_number',
        join_date = '$join_date',
        expiry_date = '$expiry_date',
        attachment = IF('$attachment' != '', '$attachment', attachment)
        WHERE customer_id = '$customer_id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Customer updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating customer: ' . $conn->error]);
}

$conn->close();
?>
