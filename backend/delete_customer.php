<?php
// Include database connection
include 'conn.php';

// Get product code from POST request
$customerId = $_POST["product_code"];

// First, check if the customer ID is used in any shipments
$checkQuery = "SELECT invoice_number FROM shipments WHERE customer_id = '$customerId'";
$checkResult = $conn->query($checkQuery);

// If found in shipments, prevent deletion
if ($checkResult->num_rows > 0) {
    $invoiceNumbers = [];
    while ($row = $checkResult->fetch_assoc()) {
        $invoiceNumbers[] = $row['invoice_number'];
    }
    
    // Create comma-separated invoice numbers
    $invoiceList = implode(', ', $invoiceNumbers);
    echo "Cannot delete customer. This customer is associated with the following invoice(s): $invoiceList. Please reassign these invoices to another customer before deleting.";
} else {
    // If not used in shipments, proceed to delete
    $sql = "DELETE FROM customers WHERE customer_id='$customerId'";
    $result = $conn->query($sql);

    if ($result) {
        echo "Customer deleted successfully.";
    } else {
        echo "Error deleting customer.";
    }
}
?>
