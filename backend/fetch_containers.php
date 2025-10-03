<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Fetch customers from the database
$sql = "SELECT container_id, container_name FROM containers";
$result = $conn->query($sql);

$containers = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $containers[] = $row;
    }
}

echo json_encode($containers);

$conn->close();
?>
