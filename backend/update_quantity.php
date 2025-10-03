<?php

include 'conn.php';

$item_id = $_POST["item_id"];
$quantity_to_add = $_POST["quantity"];
$cost = $_POST["cost"];
$selling_price = $_POST["selling_price"];

$sql_validate = "SELECT * FROM tbl_products WHERE product_code='$item_id'";
$rs_validate = $conn->query($sql_validate);
if($rs_validate->num_rows == 1){
  $sql_update_quantity = "UPDATE tbl_products SET product_quantity = product_quantity + $quantity_to_add, product_cost=$cost,product_selling_price=$selling_price   WHERE product_code = '$item_id'";

  if ($conn->query($sql_update_quantity) === TRUE) {
    $sql_h = "INSERT INTO tbl_stock_history (product_code,product_quantity,product_cost,product_selling_price) VALUES ('$item_id', '$quantity_to_add','$cost','$selling_price')";
    $rsAdd_h = $conn->query($sql_h);

    $_SESSION['update_product'] = 'Product Quantity updated successfully';
    header("location: ../add_products.php");
    exit();
  } else {
    $_SESSION['update_product'] = 'Unknown error';
    header("location: ../add_products.php");
    exit();
  }


}else{
  $_SESSION['update_product'] = 'Product Not found';
  header("location: ../add_products.php");
  exit();

}






 ?>
