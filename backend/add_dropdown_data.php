<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Get the JSON data from the request body
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Extract data from JSON

$loading_street = $data['loading_street'];
$port_origin = $data['port_origin'];
$warehouse = $data['warehouse'];
$unloading_street = $data['unloading_street'];
$port_destination = $data['port_destination'];
$item_desc = $data['item_desc'];
$chargeDescription = $data['chargeDescription'];
$vehicle_num = $data['vehicle_num'];
$taxes = $data['taxableValues'];

$check_sql = "SELECT * FROM item_desc WHERE item_desc='$item_desc'";
$result = $conn->query($check_sql);

if ($result->num_rows <= 0) {
    $sql = "INSERT INTO item_desc (item_desc) VALUES ('$item_desc')";
    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting port into ports: " . $conn->error);
    }
}

$check_sql = "SELECT * FROM places WHERE place='$loading_street'";
$result = $conn->query($check_sql);

if ($result->num_rows <= 0) {
    $sql = "INSERT INTO places (place) VALUES ('$loading_street')";
    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting port into ports: " . $conn->error);
    }
}

$check_sql = "SELECT * FROM places WHERE place='$unloading_street'";
$result = $conn->query($check_sql);

if ($result->num_rows <= 0) {
    $sql = "INSERT INTO places (place) VALUES ('$unloading_street')";
    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting port into ports: " . $conn->error);
    }
}

$check_sql = "SELECT * FROM warehouses WHERE warehouse='$warehouse'";
$result = $conn->query($check_sql);

if ($result->num_rows <= 0) {
    $sql = "INSERT INTO warehouses (warehouse) VALUES ('$warehouse')";
    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting port into ports: " . $conn->error);
    }
}

$check_sql = "SELECT * FROM ports WHERE port='$port_origin'";
$result = $conn->query($check_sql);

if ($result->num_rows <= 0) {
    $sql = "INSERT INTO ports (port) VALUES ('$port_origin')";
    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting port into ports: " . $conn->error);
    }
}
$check_sql = "SELECT * FROM ports WHERE port='$port_destination'";
$result = $conn->query($check_sql);

if ($result->num_rows <= 0) {
    $sql = "INSERT INTO ports (port) VALUES ('$port_destination')";
    if ($conn->query($sql) !== TRUE) {
        throw new Exception("Error inserting port into ports: " . $conn->error);
    }
}

foreach ($chargeDescription as $desc) {
    $check_sql = "SELECT * FROM charge_details WHERE detail='$desc'";
    $result = $conn->query($check_sql);
    if ($result->num_rows <= 0) {
        $sql = "INSERT INTO charge_details (detail) VALUES ('$desc')";
        if ($conn->query($sql) !== TRUE) {
            throw new Exception("Error inserting port into ports: " . $conn->error);
        }
    }
}

foreach ($vehicle_num as $num) {
    $check_sql = "SELECT * FROM equipment_numbers WHERE eq_number='$num'";
    $result = $conn->query($check_sql);
    if ($result->num_rows <= 0) {
        $sql = "INSERT INTO equipment_numbers (eq_number) VALUES ('$num')";
        if ($conn->query($sql) !== TRUE) {
            throw new Exception("Error inserting port into ports: " . $conn->error);
        }
    }
}

foreach ($taxes as $value) {
    $check_sql = "SELECT * FROM tax_percentages WHERE value='$value'";
    $result = $conn->query($check_sql);
    if ($result->num_rows <= 0) {
        $sql = "INSERT INTO tax_percentages (value) VALUES ('$value')";
        if ($conn->query($sql) !== TRUE) {
            throw new Exception("Error inserting port into ports: " . $conn->error);
        }
    }
}

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Customer added successfully.']);
} else {
    // Handle error if needed
    echo json_encode(['status' => 'error', 'message' => 'Error adding customer: ' . $conn->error]);
    http_response_code(500); // Internal Server Error
}

$conn->close();
?>
