<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Fetch suppliers from the database
$sql = "SELECT supplier_id, name FROM suppliers";
$result = $conn->query($sql);

$suppliers = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

echo json_encode($suppliers);

$conn->close();
?>
