<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Assuming you have received JSON data from the frontend
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Generate a unique invoice id using the current date and time
$invoiceId = date('ymdHis');

// Insert the invoice id and sold items data into the database
foreach ($data['items'] as $item) {
    $itemCode = $item['product_code'];
    $quantity = $item['quantity'];
    $price = $item['price'];

    // Update the sold_items table (replace with your actual query)
    $sql = "INSERT INTO tbl_sale_history (invoice_id, product_code, quantity, price)
            VALUES ('$invoiceId', '$itemCode', '$quantity', '$price')";

    if ($conn->query($sql) !== TRUE) {

        // Handle error if needed
        echo json_encode(['error' => 'Error updating database']);
        http_response_code(500); // Internal Server Error
        exit();
    }
    $sql_update_quantity = "UPDATE tbl_products SET product_quantity = product_quantity - $quantity   WHERE product_code = '$itemCode'";
    $rsAdd_h = $conn->query($sql_update_quantity);
}

// Return the generated invoice id
echo json_encode(['invoice_id' => $invoiceId]);

?>
