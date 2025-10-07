<?php
// Test script for PaymentService implementation
require_once 'conn.php';
require_once 'services/PaymentServices.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PaymentService Test Script</h1>";

try {
    $paymentService = new PaymentService($conn);
    
    // Test 1: Get customer details
    echo "<h2>Test 1: Get Customer Details</h2>";
    $customer = $paymentService->getCustomerDetails(1); // Assuming customer ID 1 exists
    echo "<pre>";
    print_r($customer);
    echo "</pre>";
    
    // Test 2: Get invoice total
    echo "<h2>Test 2: Test Invoice Methods</h2>";
    // We'll need an actual invoice number for this test
    $testInvoiceNumber = "INV-2024-001"; // Replace with actual invoice number
    
    echo "<h3>Invoice Total for $testInvoiceNumber:</h3>";
    $invoiceTotal = $paymentService->getInvoiceTotal($testInvoiceNumber);
    echo "Invoice Total: " . $invoiceTotal . "<br>";
    
    $totalPaid = $paymentService->getTotalPaidAmount($testInvoiceNumber);
    echo "Total Paid: " . $totalPaid . "<br>";
    
    // Test 3: Get customer credit balance
    echo "<h2>Test 3: Customer Credit Balance</h2>";
    $creditBalance = $paymentService->getCustomerCreditBalance(1);
    echo "Credit Balance for Customer 1: " . $creditBalance . "<br>";
    
    // Test 4: Get customer invoices summary
    echo "<h2>Test 4: Customer Invoices Summary</h2>";
    $invoiceSummary = $paymentService->getCustomerInvoicesSummary(1);
    echo "<pre>";
    print_r($invoiceSummary);
    echo "</pre>";
    
    echo "<h2>✅ All basic tests completed successfully!</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Test Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>