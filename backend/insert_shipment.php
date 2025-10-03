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


// Extract data from JSON
$shipmentId = $data['shipmentId'];
$handled_by = $data['handled_by'];
$job_date = $data['job_date'];
$invoice_date = $data['invoice_date'];
$payment_date = $data['payment_date'];
$invoice_number = $data['invoice_number'];
$job_number = $data['job_number'];
$bl_number = $data['bl_number'];
$bl_type = $data['bl_type'];
$house_bl_number = $data['house_bl_number'];
$bill_of_entry = $data['bill_of_entry'];
$note = mysqli_real_escape_string($conn, $data['note']);
$special_note = mysqli_real_escape_string($conn, $data['special_note']);
$customer_id = $data['customer_id'];
$consignee = $data['consignee'];
$supplier_id = $data['supplier_id'];
$shipper_reference = $data['shipper_reference'];
$vessel = $data['vessel'];
$voyage_number = $data['voyage_number'];
$item_description = $data['item_desc'];
$weight = $data['weight'];
$height = $data['height'];
$width = $data['width'];
$length = $data['length'];
$equipments = $data['equipments'];
$units = $data['units'];
$loading_country = mysqli_real_escape_string($conn, $data['loadingCountry']);
$loading_region = mysqli_real_escape_string($conn, $data['loadingRegion']);
$loading_street = mysqli_real_escape_string($conn, $data['loading_street']);
$port_of_origin = $data['port_origin'];
$warehouse = $data['warehouse'];
$etd_departure = $data['etdDeparture'];
$etd_departure_2 = $data['etdDeparture_2'];
$etd_departure_3 = $data['etdDeparture_3'];
$gate_in = $data['gate_in'];
$gate_out = $data['gate_out'];
$unloading_country = mysqli_real_escape_string($conn, $data['unloadingCountry']);
$unloading_region = mysqli_real_escape_string($conn, $data['unloadingRegion']);
$unloading_street = $data['unloading_street'];
$port_of_destination = $data['port_destination'];
$etd_arrival = $data['etd_arrival'];
$border_charges = $data['borderCharges'];



$check_sql = "SELECT * FROM shipments WHERE shipment_id='$shipmentId'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0 || $shipmentId=='' ) {
    $row = $result->fetch_assoc();
    if (($row['status'] !== 'pending' || $row['status'] !== 'generated') && $u_id !=1) {
        // If the shipment exists and its status is not 'pending', return success without making changes
        echo json_encode(['status' => 'success', 'message' => 'Shipment already exists and is not in pending status. No changes made.']);
        http_response_code(200); // OK
        exit();
    } else {
        // If the shipment exists and its status is 'pending', clear existing data
        $sql_clear = "DELETE FROM shipments WHERE shipment_id='$shipmentId'";
        $conn->query($sql_clear);
        $sql_clear = "DELETE FROM shipment_charges WHERE shipment_id='$shipmentId'";
        $conn->query($sql_clear);
        $sql_clear = "DELETE FROM shipment_equipments WHERE shipment_id='$shipmentId'";
        $conn->query($sql_clear);
    }
}



if($shipmentId==''){
  echo json_encode(['status' => 'success', 'message' => 'Empty test Shipment']);
  http_response_code(200); // OK
  exit();
}

try {


  $parts = explode('-', $shipmentId);

  // Step 2: Get the last part after the dash
  $lastPart = end($parts);

  // Step 3: Remove the first 4 characters from the last part


  // Step 4: Convert the remaining string to an integer
  $number = $lastPart;
    // Start a transaction
    $conn->begin_transaction();

    // Insert data into shipments table
    $sql = "INSERT INTO shipments (shipment_number,shipment_id, handled_by, job_date, invoice_date, payment_date, invoice_number, job_number, bl_number, bl_type,
            house_bl_number, bill_of_entry, note, special_note,supplier_id, customer_id, consignee, shipper_reference, vessel, voyage_number,
            loading_country, loading_region, loading_street, port_of_origin, unloading_country, unloading_region, unloading_street,
            port_of_destination, etd_departure,etd_departure_2, etd_departure_3, gate_in, gate_out, etd_arrival, warehouse, item_description, weight, height, width, length,units, status)

            VALUES ('$number','$shipmentId', '$handled_by', '$job_date', '$invoice_date', '$payment_date', '$invoice_number', '$job_number', '$bl_number', '$bl_type',
                    '$house_bl_number', '$bill_of_entry', '$note','$special_note', '$supplier_id', '$customer_id', '$consignee', '$shipper_reference', '$vessel', '$voyage_number',
                    '$loading_country', '$loading_region', '$loading_street', '$port_of_origin', '$unloading_country', '$unloading_region', '$unloading_street',
                    '$port_of_destination', '$etd_departure','$etd_departure_2', '$etd_departure_3','$gate_in','$gate_out','$etd_arrival', '$warehouse', '$item_description', '$weight', '$height', '$width', '$length', '$units', 'pending')";

    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting shipment: " . $conn->error);
    }

    // Get the inserted shipment id
    $shipmentInsertId = $conn->insert_id;

    // Insert data into border_charges table
    foreach ($border_charges as $charge) {
        $border_charge_sql = "INSERT INTO shipment_charges (shipment_id, charge_details, unit_rate, currency, quantity, taxable, amount, amount_AED, amount_USD, total_amount)
            VALUES ('$shipmentId', '" . $charge['description'] . "', '" . $charge['rate'] . "', '" . $charge['currency'] . "', '" . $charge['quantity'] . "',
                    '" . $charge['taxable'] . "', '" . $charge['amount'] . "', '" . $charge['amountAED'] . "', '" . $charge['amountUSD'] . "', '" . $charge['totalAmount'] . "')";

        if ($conn->query($border_charge_sql) !== TRUE) {
            throw new Exception("Error inserting border charge: " . $conn->error);
        }
    }

    // Insert data into equipments table
    foreach ($equipments as $equipment) {
        $equipment_sql = "INSERT INTO shipment_equipments (
            shipment_id, equipment_id, equipment_number, description, weight, packs
        ) VALUES (
            '$shipmentId',
            '" . mysqli_real_escape_string($conn, $equipment['equipment']) . "',
            '" . mysqli_real_escape_string($conn, $equipment['eq_number']) . "',
            '" . mysqli_real_escape_string($conn, $equipment['description']) . "',
            '" . mysqli_real_escape_string($conn, $equipment['weight']) . "',
            '" . mysqli_real_escape_string($conn, $equipment['packs']) . "'
        )";
        if ($conn->query($equipment_sql) !== TRUE) {
            throw new Exception("Error inserting equipment: " . $conn->error);
        }
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Shipment and related data inserted successfully']);
    http_response_code(200); // OK

} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500); // Internal Server Error
}

// Close the connection
$conn->close();
?>
