<?php

include 'conn.php';
$shipment_id = $_GET['shipment_id'];
$result = $conn->query("SELECT * FROM shipment_costs WHERE shipment_id='$shipment_id'");
$costs = [];
while ($row = $result->fetch_assoc()) {
    $costs[] = $row;
}
echo json_encode($costs);
?>