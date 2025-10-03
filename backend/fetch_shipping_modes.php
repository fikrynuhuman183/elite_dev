<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Fetch customers from the database
$sql = "SELECT shipping_mode_id, shipping_mode_name FROM shipping_modes";
$result = $conn->query($sql);

$shippingModes = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shippingModes[] = $row;
    }
}

echo json_encode($shippingModes);

$conn->close();
?>
