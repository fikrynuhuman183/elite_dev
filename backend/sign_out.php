<?php
  include 'conn.php';


    session_destroy();
    header('location:../login.php');
    exit();
 ?>
