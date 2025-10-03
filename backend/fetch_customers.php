<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Fetch customers from the database
$sql = "SELECT customer_id, name FROM customers";
$result = $conn->query($sql);

$customers = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

echo json_encode($customers);

$conn->close();
?>
