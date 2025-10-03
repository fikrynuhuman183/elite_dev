<?php
include 'conn.php';
$data = json_decode(file_get_contents('php://input'), true);
$shipment_id = $data['shipment_id'];
$costs = $data['costs'];

// Remove old costs for this shipment
$conn->query("DELETE FROM shipment_costs WHERE shipment_id='$shipment_id'");

// Insert new costs
$stmt = $conn->prepare("INSERT INTO shipment_costs (shipment_id, supplier_id, description, tag, currency, unit_rate, quantity, taxable, amount, amount_AED, amount_USD, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($costs as $cost) {
    $stmt->bind_param(
        "sisssddddddd",
        $shipment_id,
        $cost['supplier_id'],
        $cost['description'],
        $cost['tag'],
        $cost['currency'],
        $cost['unit_rate'],
        $cost['quantity'],
        $cost['taxable'],
        $cost['amount'],
        $cost['amount_AED'],
        $cost['amount_USD'],
        $cost['total_amount']
    );
    $stmt->execute();
}
echo json_encode(['status' => 'success']);
?>