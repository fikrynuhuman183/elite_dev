<?php
require_once 'conn.php';
require_once 'services/PaymentServices.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$customer_id = $input['customer_id'] ?? null;
$selected_date = $input['soa_date'] ?? null;

// Input validation
if (!$customer_id || !is_numeric($customer_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing customer ID.']);
    exit;
}

if (!$selected_date) {
    echo json_encode(['status' => 'error', 'message' => 'Missing selected date.']);
    exit;
}

// Validate and sanitize the selected date
$selected_date_obj = DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($selected_date)));
if (!$selected_date_obj) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format.']);
    exit;
}
$selected_date = $selected_date_obj->format('Y-m-d');

$response = [];
$cumulative_balance = 0;

try {
    // Initialize PaymentService
    $paymentService = new PaymentService($conn);
    
    // Start performance monitoring
    $start_time = microtime(true);
    
    // Fetch customer details
    $stmt = $conn->prepare("SELECT name, email, phone, vat_number FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $customer_result = $stmt->get_result();
    $customer_info = $customer_result->fetch_assoc();
    $stmt->close();

    if (!$customer_info) {
        throw new Exception("Customer not found.");
    }

    $response['customer_info'] = $customer_info;
    $response['selected_date'] = $selected_date;

    // Get customer invoice summaries with payment details up to selected date
    // Using optimized query that handles payments in a single operation
    $sql = "
        SELECT 
            s.shipment_id,
            s.invoice_number,
            s.job_date,
            s.invoice_date,
            s.payment_date,
            s.status,
            ROUND(COALESCE(charges.invoice_total, 0), 3) AS invoice_total,
            ROUND(COALESCE(payments.total_paid, 0), 3) AS total_paid,
            GREATEST(0, ROUND(COALESCE(charges.invoice_total, 0), 3) - ROUND(COALESCE(payments.total_paid, 0), 3)) AS balance_due
        FROM shipments s
        LEFT JOIN (
            -- Pre-aggregate charges by invoice with 3 decimal precision
            SELECT 
                s2.invoice_number,
                SUM(sc.total_amount) AS invoice_total
            FROM shipments s2
            LEFT JOIN shipment_charges sc ON s2.shipment_id = sc.shipment_id
            GROUP BY s2.invoice_number
        ) charges ON s.invoice_number = charges.invoice_number
        LEFT JOIN (
            -- Pre-aggregate payments by invoice (excluding credit top-ups) with 3 decimal precision
            SELECT 
                invoice_number,
                SUM(ABS(payment_amount)) AS total_paid
            FROM payment_receipts
            WHERE invoice_number != '0'
            AND payment_date <= ?
            GROUP BY invoice_number
        ) payments ON s.invoice_number = payments.invoice_number
        WHERE s.customer_id = ? 
        AND s.payment_date <= ?
        AND s.status != 'paid'
        HAVING balance_due > 0
        ORDER BY s.invoice_date ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $selected_date, $customer_id, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $balance = (float)$row['balance_due'];
        
        // Calculate cumulative balance
        $cumulative_balance += $balance;

        // Calculate due days
        $due_date = new DateTime($row['payment_date']); // payment_date is the due date
        $current_date = new DateTime($selected_date);
        $interval = $current_date->diff($due_date);
        $due_days = $interval->days;
        
        if ($current_date < $due_date) {
            $due_days = -$due_days; // Negative if not yet due
        } elseif ($current_date >= $due_date) {
            $due_days = $interval->days; // Positive if overdue
        } else {
            $due_days = 0; // Not due if balance is zero
        }

        $invoices[] = [
            'invoice_number' => $row['invoice_number'],
            'job_date' => $row['job_date'],
            'invoice_date' => $row['invoice_date'],
            'due_date' => $row['payment_date'], // payment_date is the due date
            'currency' => 'AED', // Default currency since no currency_name column exists
            'total_amount' => number_format((float)$row['invoice_total'], 2),
            'total_paid' => number_format((float)$row['total_paid'], 2),
            'balance' => number_format($balance, 2),
            'cumulative_balance' => number_format($cumulative_balance, 2),
            'due_days' => $due_days
        ];
    }

    $stmt->close();

    $response['data'] = $invoices;
    $response['total_amount_due'] = number_format($cumulative_balance, 2);
    
    // Add additional customer financial summary using PaymentService
    $response['customer_credit_balance'] = number_format($paymentService->getCustomerCreditBalance($customer_id), 2);
    
    // Get customer invoice summary for additional insights
    $customer_summary = $paymentService->getCustomerInvoicesSummary($customer_id);
    if ($customer_summary) {
        $response['customer_summary'] = [
            'total_invoices' => $customer_summary['total_invoices'] ?? 0,
            'total_invoice_amount' => number_format((float)($customer_summary['total_invoice_amount'] ?? 0), 2),
            'total_paid_amount' => number_format((float)($customer_summary['total_paid_amount'] ?? 0), 2),
            'total_outstanding' => number_format((float)($customer_summary['total_outstanding'] ?? 0), 2),
            'paid_invoices' => $customer_summary['paid_invoices'] ?? 0,
            'partial_invoices' => $customer_summary['partial_invoices'] ?? 0,
            'unpaid_invoices' => $customer_summary['unpaid_invoices'] ?? 0
        ];
    }
    
    // End performance monitoring
    $end_time = microtime(true);
    $response['performance'] = [
        'execution_time_ms' => round(($end_time - $start_time) * 1000, 2),
        'invoice_count' => count($invoices),
        'query_optimized' => true
    ];
    
    $response['status'] = 'success';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['status'] = 'error';
    
    // Add debug information for development
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        $response['debug'] = [
            'file' => __FILE__,
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
    }
}

$conn->close();
echo json_encode($response);
?>