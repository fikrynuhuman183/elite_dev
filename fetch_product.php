<?php
// Include your database connection file (e.g., conn.php)
include './backend/conn.php';

// Get item code from the request
$itemCode = $_GET['itemCode'];

// Perform a database query to fetch product details based on the item code
$sql = "SELECT * FROM tbl_products WHERE product_code = '$itemCode'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch product details
    $row = $result->fetch_assoc();
    $itemName = $row['product_name'];
    $sellingPrice = $row['product_selling_price'];

    // Create an associative array with product details
    $productData = array(
        'itemName' => $itemName,
        'sellingPrice' => $sellingPrice
    );

    // Convert the array to JSON and send the response
    header('Content-Type: application/json');
    echo json_encode($productData);
} else {
    // If no matching product is found, you can customize the response accordingly
    echo json_encode(['error' => 'Product not found']);
}

// Close the database connection
$conn->close();
?>
