<?php
include 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}
$invoice_no = $data['invoice_no'] ?? '';
// Check if shipment is already marked as paid
$statusCheckSql = "SELECT status FROM shipments WHERE invoice_number = ?";
$stmt = $conn->prepare($statusCheckSql);
$stmt->bind_param("s", $invoice_no);
$stmt->execute();
$statusResult = $stmt->get_result();

if ($statusResult->num_rows > 0) {
    $status = $statusResult->fetch_assoc()['status'];
    if (strtolower($status) === 'paid') {
        http_response_code(400);
        echo json_encode(['error' => "This invoice is already marked as paid. No more payments can be added."]);
        exit;
    }
}
$stmt->close();


$receipt_id = $data['receipt_no'];
$deleted_id = null;

if ($receipt_id == '') {
    // Generate random receipt ID using timestamp and random number
    $timestamp = time(); // Current Unix timestamp
    $random_num = mt_rand(1000, 9999); // Random 4-digit number
    $receipt_id = 'RCPT-INV' . date('Ymd-His', $timestamp) . '-' . $random_num;
    
}

// Check if receipt already exists
$check_sql = "SELECT * FROM payment_receipts WHERE receipt_no = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $receipt_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $deleted_id = $row['id'];
    $conn->query("DELETE FROM payment_receipts WHERE id = '$deleted_id'");
}
$stmt->close();

try {
    $conn->begin_transaction();

    // Get customer_id
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE name = ?");
    $stmt->bind_param("s", $data['customer']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Customer not found: " . $data['customer']);
    }
    $customer_id = $result->fetch_assoc()['customer_id'];
    $stmt->close();

    $invoice_no = $data['invoice_no'] ?? '';
    $full_payment = !empty($data['full_payment']) ? 1 : 0;
    $payment_amount = floatval($data['total']);

    // Make amount negative if deduct_credit is set
    if (!empty($data['deduct_credit'])) {
        $payment_amount *= -1;
    }

    // Get shipment_id and total charges
    $shipmentDataSql = "
        SELECT s.shipment_id, SUM(sc.total_amount) AS total
        FROM shipments s
        JOIN shipment_charges sc ON s.shipment_id = sc.shipment_id
        WHERE s.invoice_number = ?
        GROUP BY s.shipment_id
    ";
    $stmt = $conn->prepare($shipmentDataSql);
    $stmt->bind_param("s", $invoice_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No shipment found with invoice number: $invoice_no");
    }

    $row = $result->fetch_assoc();
    $shipment_id = $row['shipment_id'];
    $invoice_total = floatval($row['total']);
    $stmt->close();

    // Get total previous partial payments
    $partialSql = "
        SELECT SUM(payment_amount) AS total_paid
        FROM payment_receipts
        WHERE invoice_number = ? AND full_payment != 1
    ";
    $stmt = $conn->prepare($partialSql);
    $stmt->bind_param("s", $invoice_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $previous_paid = floatval($result->fetch_assoc()['total_paid'] ?? 0);
    $stmt->close();

    $total_paid_now = $previous_paid + $payment_amount;

    // If full payment or total paid now >= invoice value
    if ($full_payment || $total_paid_now >= $invoice_total) {
        $payable = min($payment_amount, $invoice_total - $previous_paid);
        $credit = $payment_amount - $payable;

        // Insert normal payment
        $stmt = $conn->prepare("
            INSERT INTO payment_receipts (
                id, customer_id, invoice_number, receipt_no,
                payment_amount, full_payment, created_at,
                payment_date, note, mode_of_payment, description, currency_name, currency_roe, salesperson
            ) VALUES (?, ?, ?, ?, ?, 1, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iissdsssssds",
            $deleted_id,
            $customer_id,
            $invoice_no,
            $receipt_id,
            $payable,
            $data['date'],
            $data['special_note'],
            $data['mode_of_payment'],
            $data['description'],
            $data['currency_name'],
            $data['currency_roe'],
            $data['salesperson']
        );
        $stmt->execute();
        $stmt->close();

        // Mark shipment as paid
        $updateStatus = $conn->prepare("UPDATE shipments SET status = 'paid' WHERE invoice_number = ?");
        $updateStatus->bind_param("s", $invoice_no);
        $updateStatus->execute();
        $updateStatus->close();

        // Insert credit if there's extra payment
        if ($credit > 0) {
            $credit_receipt_id = $receipt_id . '-credit';
            $stmt = $conn->prepare("
                INSERT INTO payment_receipts (
                    customer_id, invoice_number, receipt_no,
                    payment_amount, full_payment, created_at,
                    payment_date, note, mode_of_payment
                ) VALUES (?, 0, ?, ?, 0, CURRENT_TIMESTAMP, ?, ?, ?)
            ");
            $stmt->bind_param(
                "isdsss",
                $customer_id,
                $credit_receipt_id,
                $credit,
                $data['date'],
                $data['special_note'],
                $data['mode_of_payment']
            );
            $stmt->execute();
            $stmt->close();
        }

    } else {
        // Insert as partial payment
        $stmt = $conn->prepare("
            INSERT INTO payment_receipts (
                id, customer_id, invoice_number, receipt_no,
                payment_amount, full_payment, created_at,
                payment_date, note, mode_of_payment, description, currency_name, currency_roe, salesperson
            ) VALUES (?, ?, ?, ?, ?, 0, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iissdsssssds",
            $deleted_id,
            $customer_id,
            $invoice_no,
            $receipt_id,
            $payment_amount,
            $data['date'],
            $data['special_note'],
            $data['mode_of_payment'],
            $data['description'],
            $data['currency_name'],
            $data['currency_roe'],
            $data['salesperson']
        );
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    http_response_code(200);
    echo json_encode(['success' => true, 'receipt_id' => $receipt_id]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
