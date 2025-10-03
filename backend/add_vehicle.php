<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Assuming you have received JSON data from the frontend
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Extract the data
$name = $data['name'];

// Insert the supplier data into the database
$sql = "INSERT INTO vehicles (vehicle_name) VALUES ('$name')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Vehicle added successfully.']);
} else {
    // Handle error if needed
    echo json_encode(['status' => 'error', 'message' => 'Error adding vehicle: ' . $conn->error ]);
    http_response_code(500); // Internal Server Error
}

$conn->close();
?>
