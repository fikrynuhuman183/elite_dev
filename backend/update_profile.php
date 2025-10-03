<?php

include 'conn.php';


$profile_id = $_POST["u_id"];
$profile_name = $_POST["profile_name"];
$profile_pass = $_POST["profile_pass"];



// Update the product in the database
$sql = "UPDATE tbl_users SET u_name='$profile_name', u_pass='$profile_pass' where u_id='$profile_id'";
$result = $conn->query($sql);

if ($result) {
    $_SESSION['update_product'] = 'Profile Successfully Updated';
    header("location: ../user_profiles.php");
    exit();
} else {
    $_SESSION['update_product'] = 'Error updating profile';
    header("location: ../user_profiles.php");
    exit();
}

?>
