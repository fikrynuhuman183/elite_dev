<?php

include 'conn.php';

$target_dir = "customer_attachments/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$name = $_POST['name'];
$location = $_POST['location'];
$phone = $_POST['phone'];
$phone_optional = $_POST['phone_optional'];
$email = $_POST['email'];
$join_date = $_POST['join_date'];
$expiry_date = $_POST['expiry_date'];
$vat_number = $_POST['vat_number'];
$role = isset($_POST['role']) ? $_POST['role'] : 'customer'; // New line

$sql = "INSERT INTO customers (name, location, phone, phone_optional, email, vat_number, join_date, expiry_date, role)
        VALUES ('$name', '$location', '$phone', '$phone_optional', '$email', '$vat_number', '$join_date', '$expiry_date', '$role')";

if ($conn->query($sql) === TRUE) {
    $customer_id = $conn->insert_id;

    if (!empty($_FILES['attachment']['name'][0])) {
        $total_files = count($_FILES['attachment']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['attachment']['name'][$i];
            $file_tmp = $_FILES['attachment']['tmp_name'][$i];
            $target_file = $target_dir . basename($file_name);

            if (move_uploaded_file($file_tmp, $target_file)) {
                $sql = "INSERT INTO customer_attachments (customer_id, attachment) VALUES ('$customer_id', '$file_name')";
                if ($conn->query($sql) !== TRUE) {
                    echo json_encode(['status' => 'error', 'message' => 'Error adding attachment: ' . $conn->error]);
                    http_response_code(500);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error uploading file: ' . $file_name]);
                http_response_code(500);
                exit();
            }
        }
    }
    echo json_encode(['status' => 'success', 'message' => 'Customer added successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error adding customer: ' . $conn->error]);
    http_response_code(500);
}
?>