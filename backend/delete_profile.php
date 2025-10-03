<?php
// Include database connection
include 'conn.php';

// Get product code from POST request
$productCode = $_POST["product_code"];

// Delete the product from the database
if($productCode !=1){
$sql = "DELETE FROM tbl_users WHERE u_id='$productCode'";
$result = $conn->query($sql);

// Check if deletion was successful
if ($result) {
    // Return success message
    echo "Profile deleted successfully.";
} else {
    // Return error message
    echo "Error deleting profile.";
}}else{
  echo "Can not delete main profile.";
}
?>
