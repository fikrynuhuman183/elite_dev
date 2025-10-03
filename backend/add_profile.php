<?php

include 'conn.php';


$item_id = $_POST["profile_name"];
$item_name = $_POST["profile_pass"];




// Insert into returns table
$sql = "INSERT INTO tbl_users (u_name, U_pass) VALUES ('$item_id', '$item_name')";
$rsAdd = $conn->query($sql);

if ($rsAdd>0) {

  $_SESSION['add_profile'] = 'New Profile Successfully Added';
  header("location: ../add_profiles.php");
  exit();
}



 ?>
