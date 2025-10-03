<?php
include 'conn.php';
$result = $conn->query("SELECT id, currency, roe FROM currencies");
$currencies = [];
while ($row = $result->fetch_assoc()) {
    $currencies[] = [
        'id' => $row['id'],
        'currency' => $row['currency'],
        'roe' => $row['roe']
    ];
}
echo json_encode($currencies);
?>