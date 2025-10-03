<?php
include './layouts/header.php';
include './layouts/sidebar.php';

// Get customer ID from query parameters
$customer_id = $_GET['customer_id'] ?? null;

if (!$customer_id) {
    echo "<div class='alert alert-danger'>Invalid Customer ID</div>";
    exit;
}

// Fetch invoices related to the customer
$invoices_sql = "
    SELECT s.*, 
           (SELECT SUM(amount) FROM shipment_charges WHERE shipment_id = s.shipment_id) AS total_charges
    FROM shipments s
    WHERE s.customer_id = ?
    ORDER BY s.invoice_date DESC";
$stmt = $conn->prepare($invoices_sql);
$stmt->bind_param("s", $customer_id);
$stmt->execute();
$invoices_result = $stmt->get_result();

// Separate invoices into categories
$completely_paid = [];
$partially_paid = [];
$due_invoices = [];

while ($invoice = $invoices_result->fetch_assoc()) {
    $total_charges = $invoice['total_charges'];
    $status = $invoice['status'];

    if ($status === 'paid') {
        $completely_paid[] = $invoice;
    } else {
        // Check payment records for partially paid invoices
        $payments_sql = "
            SELECT COALESCE(SUM(ABS(payment_amount)), 0) AS total_paid
            FROM payment_receipts
            WHERE invoice_number = ?";
        $payments_stmt = $conn->prepare($payments_sql);
        $payments_stmt->bind_param("s", $invoice['invoice_number']);
        $payments_stmt->execute();
        $payments_result = $payments_stmt->get_result();
        $payment_data = $payments_result->fetch_assoc();
        $total_paid = $payment_data['total_paid'];

        if ($total_paid > 0) {
            // Partially paid
            $invoice['total_paid'] = $total_paid;
            $invoice['due_amount'] = $total_charges - $total_paid;
            $partially_paid[] = $invoice;
        } else {
            // Due invoices
            $due_invoices[] = $invoice;
        }
    }
}

// Fetch credit top-ups and deductions
$credits_sql = "
    SELECT 
        ABS(payment_amount) AS amount, 
        payment_date, 
        note, 
        CASE 
            WHEN invoice_number = '0' THEN 'Credit Top-up'
            ELSE 'Credit Deduction'
        END AS type
    FROM payment_receipts
    WHERE customer_id = ? AND (invoice_number = '0' OR (invoice_number != '0' AND payment_amount < 0))
    ORDER BY payment_date DESC";
$stmt = $conn->prepare($credits_sql);
$stmt->bind_param("s", $customer_id);
$stmt->execute();
$credits_result = $stmt->get_result();

// Calculate remaining credit
$total_credit_topups = 0;
$total_credit_deductions = 0;

while ($credit = $credits_result->fetch_assoc()) {
    if ($credit['type'] === 'Credit Top-up') {
        $total_credit_topups += $credit['amount'];
    } else {
        $total_credit_deductions += $credit['amount'];
    }
}

$total_credit = $total_credit_topups - $total_credit_deductions;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Customer Payment History
            <a href="view_customers.php" class="btn btn-secondary mb-3">Back to Customers</a>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Customers</a></li>
            <li class="active">Payment History</li>
        </ol>
    </section>

    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#invoices" data-toggle="tab">Invoices</a></li>
                <li><a href="#credits" data-toggle="tab">Credit Summary</a></li>
            </ul>
            <div class="tab-content">
                <!-- Invoices Tab -->
                <div class="tab-pane active" id="invoices">
                    <h3>Completely Paid Invoices</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Total Charges</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completely_paid as $invoice): ?>
                                <tr>
                                    <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                    <td><?= number_format($invoice['total_charges'], 2) ?></td>
                                    <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                                    <td><span class="label label-success">Paid</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3>Partially Paid Invoices</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Total Charges</th>
                                <th>Total Paid</th>
                                <th>Due Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partially_paid as $invoice): ?>
                                <tr>
                                    <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                    <td><?= number_format($invoice['total_charges'], 2) ?></td>
                                    <td><?= number_format($invoice['total_paid'], 2) ?></td>
                                    <td><?= number_format($invoice['due_amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                                    <td><span class="label label-warning">Partially Paid</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3>Due Invoices</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Total Charges</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($due_invoices as $invoice): ?>
                                <tr>
                                    <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                    <td><?= number_format($invoice['total_charges'], 2) ?></td>
                                    <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                                    <td><span class="label label-danger">Due</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Credits Tab -->
                <div class="tab-pane" id="credits">
                    <h3>Credit Transactions</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $credits_result->data_seek(0); // Reset result pointer
                            while ($credit = $credits_result->fetch_assoc()):
                                $type = $credit['type'];
                                $amount = number_format(abs($credit['amount']), 2);
                                $date = htmlspecialchars($credit['payment_date']);
                                $note = htmlspecialchars($credit['note'] ?? '');
                            ?>
                                <tr>
                                    <td><?= $amount ?></td>
                                    <td><?= $date ?></td>
                                    <td><?= $type ?></td>
                                    <td><?= $note ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <h3>Remaining Credit</h3>
                    <div class="alert alert-info">
                        <strong>Total Remaining Credit:</strong> <?= number_format($total_credit, 2) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include './layouts/footer.php'; ?>