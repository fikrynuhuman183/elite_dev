<?php
include 'conn.php';

// Get the last shipment_id from the database
$sql = "SELECT MAX(shipment_number) AS max_value FROM shipments";
$result = $conn->query($sql);

$lastId = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Extract the numeric part after the second dash

    // Increment the number part, assuming the format is always 'IMP-AIR-xxx'
    $lastId = $row['max_value'];
} else {
    // No records found, start with 1
    $lastId = 0;
}

$conn->close();

echo json_encode(['lastId' => $lastId]);
?>
