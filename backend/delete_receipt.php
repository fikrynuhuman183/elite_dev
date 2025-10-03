<?php
// Include database connection
include 'conn.php';

// Get invoice ID from POST request
$invoiceId = $_POST["receipt_id"];

// Start a transaction
$conn->begin_transaction();

try {
    // Delete the invoice from the tbl_invoices
    $sql = "DELETE FROM payment_receipts WHERE id='$invoiceId'";
    if (!$conn->query($sql)) {
        throw new Exception("Error deleting invoice.");
    }


    // Commit the transaction
    $conn->commit();

    // Return success message
    echo "Receipt deleted successfully.";
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    // Return error message
    echo $e->getMessage();
}
?>
