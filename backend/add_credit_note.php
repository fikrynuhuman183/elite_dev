<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Get the JSON data from the request body
$json_data = file_get_contents("php://input");
error_log($json_data);
$data = json_decode($json_data, true);

if ($data === null) {
    // Handle the case where JSON decoding fails
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    http_response_code(400); // Bad Request
    exit;
}


$shipmentId = mysqli_real_escape_string($conn, $data['shipmentId']);
$credit_note_number = mysqli_real_escape_string($conn, $data['credit_note_number']);
$border_charges = $data['borderCharges'];


if($shipmentId==''){
  echo json_encode(['status' => 'success', 'message' => 'Invalid SHipment ID']);
  http_response_code(500); // OK
  exit();
}

try {


    foreach ($border_charges as $charge) {
        $border_charge_sql = "INSERT INTO credit_notes (shipment_id, credit_note_number, charge_details, unit_rate, currency, quantity, taxable, amount, amount_AED, amount_USD, total_amount)
            VALUES ('$shipmentId','$credit_note_number', '" . $charge['description'] . "', '" . $charge['rate'] . "', '" . $charge['currency'] . "', '" . $charge['quantity'] . "',
                    '" . $charge['taxable'] . "', '" . $charge['amount'] . "', '" . $charge['amountAED'] . "', '" . $charge['amountUSD'] . "', '" . $charge['totalAmount'] . "')";

        if ($conn->query($border_charge_sql) !== TRUE) {
            throw new Exception("Error inserting border charge: " . $conn->error);
        }
           
        // Get the last inserted credit_note ID
        $credit_note_id = $conn->insert_id;

        // Multiply amounts by -1 for shipment_charges
        $amount = $charge['amount'] * -1;
        $amountAED = $charge['amountAED'] * -1;
        $amountUSD = $charge['amountUSD'] * -1;
        $totalAmount = $charge['totalAmount'] * -1;

        // Insert into shipment_charges table (negative amounts)
        $shipment_charge_sql = "INSERT INTO shipment_charges (shipment_id, credit_note_id, charge_details, unit_rate, currency, quantity, taxable, amount, amount_AED, amount_USD, total_amount)
        VALUES ('$shipmentId','$credit_note_id', '" . $charge['description'] . "', '" . $charge['rate'] . "', '" . $charge['currency'] . "', '" . $charge['quantity'] . "',
                '" . $charge['taxable'] . "', '$amount', '$amountAED', '$amountUSD', '$totalAmount')";

        if ($conn->query($shipment_charge_sql) !== TRUE) {
            throw new Exception("Error inserting border charge into shipment_charges: " . $conn->error);
        }

        
    }


    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Shipment and related data inserted successfully']);
    http_response_code(200); // OK

} catch (Exception $e) {

    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500); // Internal Server Error
}

// Close the connection
$conn->close();
?>
