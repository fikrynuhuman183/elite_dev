<?php
include 'conn.php';

// Check if customer_id is set in the POST data
if(isset($_POST['customer_id'])) {
    $id = $_POST['customer_id'];
    $vat_number = '0';
    $id = $conn->real_escape_string($id); // Escape special characters to prevent SQL injection
    $sql = "SELECT vat_number FROM customers WHERE customer_id = '$id'";
    $result = $conn->query($sql);

    $vat_number = '';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $vat_number = $row['vat_number'];
    }

    

    echo json_encode(['vat_number' => $vat_number]);
} else {
    echo json_encode(['error' => 'No customer ID provided']);
}
?>
