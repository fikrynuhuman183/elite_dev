<?php
include 'conn.php'; // Assuming you have a file to handle DB connection

if (isset($_GET['driver_id'])) {
    $driver_id = $_GET['driver_id'];

    $query = "SELECT * FROM drivers WHERE driver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();

    echo json_encode($driver);
}
?>
