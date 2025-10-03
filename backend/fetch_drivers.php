<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Fetch drivers from the database
$sql = "SELECT driver_id, name FROM drivers";
$result = $conn->query($sql);

$drivers = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }
}

echo json_encode($drivers);

$conn->close();
?>
