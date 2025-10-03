<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Fetch customers from the database
$sql = "SELECT vehicle_id, vehicle_name FROM vehicles";
$result = $conn->query($sql);

$vehicles = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

echo json_encode($vehicles);

$conn->close();
?>
