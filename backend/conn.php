<?php
date_default_timezone_set('Asia/Dubai');

session_start();

$servername = "localhost";
$username = "root";
$password = "#Kali183";
$dbname = "elitelink_new";



// $servername = "localhost";
// $username = "posfkpop_db_frieght_admin";
// $password = "X,]YR&gniDfg";
// $dbname = "posfkpop_db_frieght";
//
// $username1 = "posfkpop_world_admin";
// $password1 = "%o;g{b(wT(v3";
// $dbname1 = "posfkpop_world";

$current_date_time = date('Y/m/d H:i:s');

$conn = new mysqli($servername,$username,$password,$dbname);


if(isset($_SESSION['uid'])){
 $u_id = $_SESSION['uid'];
}else{
  header("location: ./login.php");
}

function getUserProgressCount($conn,$u_id,$qst){
 $sql = "SELECT * FROM tbl_quote WHERE u_id='$u_id' AND q_status='$qst'";
 $rs=$conn->query($sql);

 return $rs->num_rows;
}

function customRound($value) {
   // Multiply by 2, round up to the nearest integer, and then divide by 2
   $roundedValue = ceil($value * 2) / 2;
   // Format the rounded value with two decimal places
   $formattedValue = number_format($roundedValue, 2);
   return $formattedValue;
}




function uploadImage($fileName,$filePath,$allowedList,$errorLocation){

 $img = $_FILES[$fileName];
 $imgName =$_FILES[$fileName]['name'];
 $imgTempName = $_FILES[$fileName]['tmp_name'];
 $imgSize = $_FILES[$fileName]['size'];
 $imgError= $_FILES[$fileName]['error'];

 $fileExt = explode(".",$imgName);
 $fileActualExt = strtolower(end($fileExt));

 $allowed = $allowedList;

 if(in_array($fileActualExt, $allowed)){
   if($imgError == 0){
     $GLOBALS['fileNameNew']='prime'.uniqid('',true).".".$fileActualExt;
       $fileDestination = $filePath.$GLOBALS['fileNameNew'];

       $resultsImage = move_uploaded_file($imgTempName,$fileDestination);

     }
     else{
       header('location:'.$errorLocation.'?imgerror');
       exit();
     }
 }
 else{
   header('location:'.$errorLocation.'?extensionError&'.$fileActualExt);
   exit();
 }
}

function time_elapsed_string($datetime, $full = false) {
   $now = new DateTime;
   $ago = new DateTime($datetime);
   $diff = $now->diff($ago);

   $diff->w = floor($diff->d / 7);
   $diff->d -= $diff->w * 7;

   $string = array(
       'y' => 'year',
       'm' => 'month',
       'w' => 'week',
       'd' => 'day',
       'h' => 'hour',
       'i' => 'minute',
       's' => 'second',
   );
   foreach ($string as $k => &$v) {
       if ($diff->$k) {
           $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
       } else {
           unset($string[$k]);
       }
   }

   if (!$full) $string = array_slice($string, 0, 1);
   return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function formatTimeDifference($date_time) {
   $date_time_now = date('Y-m-d H:i:s');

   // Convert string dates to DateTime objects
   $datetime1 = new DateTime($date_time);
   $datetime2 = new DateTime($date_time_now);

   // Calculate the difference between two dates
   $interval = $datetime1->diff($datetime2);

   // Format the result
   if ($interval->days == 0) {
       // Within the same day
       if ($interval->h > 0) {
           return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
       } elseif ($interval->i > 0) {
           return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
       } else {
           return 'Just now';
       }
   } else {
       // More than 24 hours ago, show the original date and time
       return $datetime1->format('Y-m-d H:i:s');
   }
}

function getCustomerCreditBalance(mysqli $conn, $customerId)
{
  if ($customerId === null || $customerId === '') {
    return 0.0;
  }

  $customerId = $conn->real_escape_string($customerId);

  $credit = 0.0;
  $creditSql = "SELECT
      SUM(CASE
          WHEN invoice_number = '0' THEN payment_amount
          WHEN invoice_number != '0' AND payment_amount < 0 THEN payment_amount
        END) AS credit
    FROM payment_receipts
    WHERE customer_id = '$customerId'
      AND (invoice_number = '0' OR payment_amount < 0)";

  if ($result = $conn->query($creditSql)) {
    if ($row = $result->fetch_assoc()) {
      $credit = isset($row['credit']) ? (float) $row['credit'] : 0.0;
    }
    $result->free();
  }

  return round($credit, 2);
}
?>
