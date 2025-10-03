<?php
// Include your database connection file (e.g., conn.php)
include 'conn.php';

// Assuming you have received JSON data from the frontend
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Extract the data
$name = $data['name'];

// Insert the shipping mode data into the database
$sql = "INSERT INTO shipping_modes (shipping_mode_name) VALUES ('$name')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Shipping mode added successfully.']);
} else {
    // Handle error if needed
    echo json_encode(['status' => 'error', 'message' => 'Error adding shipping mode: ' . $conn->error ]);
    http_response_code(500); // Internal Server Error
}

$conn->close();
?>
