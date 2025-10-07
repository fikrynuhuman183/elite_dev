<?php
/**
 * Test script for the optimized fetch_customer_soa.php
 * Run this to verify Phase 2 implementation
 */

require_once 'backend/conn.php';

echo "=== Testing Optimized Customer SOA (Phase 2) ===\n\n";

// Test 1: Get a customer to test with
echo "1. Finding a customer to test with...\n";
$customer_query = "SELECT customer_id, name FROM customers LIMIT 1";
$result = $conn->query($customer_query);
$customer = $result->fetch_assoc();

if (!$customer) {
    echo "❌ No customers found in database\n";
    exit;
}

echo "✅ Found customer: {$customer['name']} (ID: {$customer['customer_id']})\n\n";

// Test 2: Test the optimized SOA endpoint
echo "2. Testing optimized SOA endpoint...\n";

$test_data = [
    'customer_id' => $customer['customer_id'],
    'soa_date' => date('Y-m-d') // Today's date
];

$json_data = json_encode($test_data);

// Simulate a POST request to the SOA endpoint
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $json_data
    ]
]);

$start_time = microtime(true);

// Make the request
$url = 'http://localhost/elite/backend/fetch_customer_soa.php';
$response = @file_get_contents($url, false, $context);

$end_time = microtime(true);
$execution_time = ($end_time - $start_time) * 1000;

if ($response === false) {
    echo "❌ Failed to call SOA endpoint. Testing with direct include instead...\n";
    
    // Test with direct include
    $_POST = $test_data;
    $input = $test_data;
    
    ob_start();
    try {
        include 'backend/fetch_customer_soa.php';
        $response = ob_get_contents();
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        ob_end_clean();
        exit;
    }
    ob_end_clean();
}

$result = json_decode($response, true);

if ($result === null) {
    echo "❌ Invalid JSON response\n";
    echo "Raw response: " . substr($response, 0, 500) . "\n";
    exit;
}

// Test 3: Verify response structure
echo "3. Verifying response structure...\n";

$expected_fields = [
    'status', 'customer_info', 'selected_date', 'data', 
    'total_amount_due', 'customer_credit_balance', 'performance'
];

$missing_fields = [];
foreach ($expected_fields as $field) {
    if (!isset($result[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo "❌ Missing fields: " . implode(', ', $missing_fields) . "\n";
} else {
    echo "✅ All expected fields present\n";
}

// Test 4: Check performance improvements
echo "\n4. Performance Analysis:\n";
if (isset($result['performance'])) {
    $perf = $result['performance'];
    echo "   • Execution time: {$perf['execution_time_ms']}ms\n";
    echo "   • Invoice count: {$perf['invoice_count']}\n";
    echo "   • Query optimized: " . ($perf['query_optimized'] ? 'Yes' : 'No') . "\n";
    
    if ($perf['execution_time_ms'] < 100) {
        echo "   ✅ Good performance (< 100ms)\n";
    } elseif ($perf['execution_time_ms'] < 500) {
        echo "   ⚠️  Acceptable performance (< 500ms)\n";
    } else {
        echo "   ❌ Slow performance (> 500ms)\n";
    }
} else {
    echo "   ❌ Performance metrics not available\n";
}

// Test 5: Data quality checks
echo "\n5. Data Quality Checks:\n";
if ($result['status'] === 'success') {
    echo "   ✅ Status: Success\n";
    
    if (isset($result['customer_info']['name'])) {
        echo "   ✅ Customer info: {$result['customer_info']['name']}\n";
    }
    
    if (isset($result['customer_credit_balance'])) {
        echo "   ✅ Credit balance: {$result['customer_credit_balance']}\n";
    }
    
    if (isset($result['customer_summary'])) {
        $summary = $result['customer_summary'];
        echo "   ✅ Customer summary available:\n";
        echo "      - Total invoices: {$summary['total_invoices']}\n";
        echo "      - Total outstanding: {$summary['total_outstanding']}\n";
    }
    
    echo "   ✅ Invoice data count: " . count($result['data']) . "\n";
    
} else {
    echo "   ❌ Status: " . $result['status'] . "\n";
    if (isset($result['message'])) {
        echo "   ❌ Error: " . $result['message'] . "\n";
    }
}

echo "\n=== Test Complete ===\n";

if ($result['status'] === 'success' && isset($result['performance']['query_optimized']) && $result['performance']['query_optimized']) {
    echo "🎉 Phase 2 SOA Optimization: PASSED\n";
} else {
    echo "❌ Phase 2 SOA Optimization: FAILED\n";
}

$conn->close();
?>