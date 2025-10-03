<?php
include 'conn.php';

// Get the customer ID from the POST request
$customer_id = $_POST['id'];

// Fetch the customer data from the database
$sql = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();

    // Fetch customer attachments
    $attachments_sql = "SELECT * FROM customer_attachments WHERE customer_id = '$customer_id'";
    $attachments_result = $conn->query($attachments_sql);

    $attachments = [];
    if ($attachments_result->num_rows > 0) {
        while ($attachment = $attachments_result->fetch_assoc()) {
            $attachments[] = [
                'name' => $attachment['attachment'],  // The file name
                'url' => './backend/customer_attachments/' . $attachment['attachment']  // Full URL to the file
            ];
        }
    }

    $customer['attachments'] = $attachments;  // Add the attachments array to the customer data
    echo json_encode($customer);  // Send the customer data back as JSON
} else {
    echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
}

$conn->close();
?>
