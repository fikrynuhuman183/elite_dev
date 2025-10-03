<?php
include 'conn.php';

$query = "SELECT currency, roe FROM currencies";
$result = $conn->query($query);

$rates = array();
while ($row = $result->fetch_assoc()) {
    $rates[$row['currency']] = $row['roe'];
}

echo json_encode($rates);
?>
