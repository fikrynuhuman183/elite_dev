<?php
  include 'conn.php';

  $userName = $_REQUEST['username'];
  $uPassword = $_REQUEST['password'];

  $sql = "SELECT * FROM tbl_users WHERE u_name='$userName' AND u_pass='$uPassword'";
  $rs = $conn->query($sql);



  if($rs->num_rows == 1){

    $rowUser = $rs->fetch_assoc();

    $_SESSION['uid'] = $rowUser['u_id'];
    $uid=$_SESSION['uid'];
    if($rowUser['u_id']==1){
        header('location:../index.php');
        exit();
    }else{
        header('location:../add_shipment.php');
        exit();
    }
  }
  else {
    $_SESSION['error'] = true;
    header('location:../login.php');
    exit();
  }
 ?>
