<?php
include 'conn.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $roe = $_POST['roe'];

    // Update the currency in the database
    $sql = "UPDATE currencies SET roe='$roe' WHERE id='$id'";
    $result = $conn->query($sql);

    // Check if the update was successful
    if ($result) {
        echo "Currency updated successfully.";
    } else {
        echo "Error updating currency.";
    }

    // Close the database connection
    $conn->close();
}
?>
