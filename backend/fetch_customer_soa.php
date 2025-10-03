<?php
include 'conn.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$customer_id = $input['customer_id'] ?? null;
$selected_date = $input['soa_date'] ?? null;

if (!$customer_id || !$selected_date) {
    echo json_encode(['status' => 'error', 'message' => 'Missing customer ID or selected date.']);
    exit;
}

// Validate and sanitize the selected date
$selected_date = date('Y-m-d', strtotime($selected_date));

$response = [];
$cumulative_balance = 0;

try {
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


    // Query to get invoices and calculate balances - following customer_payment_history.php pattern
    $sql = "
        SELECT s.*, 
               (SELECT SUM(amount) FROM shipment_charges WHERE shipment_id = s.shipment_id) AS total_charges
        FROM shipments s
        WHERE s.customer_id = ? AND s.payment_date <= ?
        ORDER BY s.invoice_date ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $customer_id, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $invoice_number = $row['invoice_number'];
        $total_charges = (float)$row['total_charges']; // Changed from total_amount to total_charges
        $status = $row['status'];

        // Calculate total payments for this invoice - following customer_payment_history.php pattern
        $payment_sql = "
            SELECT COALESCE(SUM(ABS(payment_amount)), 0) AS total_paid
            FROM payment_receipts
            WHERE invoice_number = ?
        ";
        $payment_stmt = $conn->prepare($payment_sql);
        $payment_stmt->bind_param("s", $invoice_number);
        $payment_stmt->execute();
        $payment_result = $payment_stmt->get_result();
        $payment_row = $payment_result->fetch_assoc();
        $total_paid = (float)($payment_row['total_paid'] ?? 0);
        $payment_stmt->close();

        $balance = $total_charges - $total_paid; // Using total_charges

        // Only include invoices with a balance greater than 0 (following customer_payment_history.php logic)
        if ($status !== 'paid' && $balance > 0) {
             // Calculate cumulative balance
            $cumulative_balance += $balance;

            // Calculate due days
            $due_date = new DateTime($row['payment_date']); // payment_date is the due date
            $current_date = new DateTime($selected_date);
            $interval = $current_date->diff($due_date);
            $due_days = $interval->days;
            if ($current_date < $due_date && $balance > 0) {
                $due_days = -$due_days; // Negative if not yet due
            } else if ($current_date >= $due_date && $balance > 0) {
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
                'total_amount' => number_format($total_charges, 2),
                'balance' => number_format($balance, 2),
                'cumulative_balance' => number_format($cumulative_balance, 2),
                'imp_exp' => $row['imp_exp'] ?? 'N/A',
                'due_days' => $due_days
            ];
        }
    }

    $stmt->close();

    $response['data'] = $invoices;
    $response['total_amount_due'] = number_format($cumulative_balance, 2);
    $response['status'] = 'success';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['status'] = 'error';
}

$conn->close();
echo json_encode($response);
?>