<?php
include './layouts/header.php';
include './layouts/sidebar.php';

// Include PaymentService class
require_once './backend/services/PaymentServices.php';

// Enable verbose error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get customer ID from query parameters
$customer_id = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : null;

if (!$customer_id) {
    echo "<div class='alert alert-danger'>Invalid Customer ID</div>";
    exit;
}

// Initialize PaymentService
$paymentService = new PaymentService($conn);

// Fetch customer details
$customer_data = $paymentService->getCustomerDetails($customer_id);

if (!$customer_data) {
    echo "<div class='alert alert-danger'>Customer not found</div>";
    exit;
}

$customer_name = $customer_data['name'] ?? 'Unknown Customer';

// Fetch invoice summaries via PaymentService
$invoice_summary = $paymentService->getCustomerInvoicesSummary($customer_id);

$completely_paid = $invoice_summary['invoices']['paid'] ?? [];
$partially_paid = $invoice_summary['invoices']['partial'] ?? [];
$due_invoices = $invoice_summary['invoices']['unpaid'] ?? [];

$totals = $invoice_summary['totals'] ?? [];
$total_jobs = $totals['total_jobs'] ?? 0;
$closed_jobs = $totals['closed_jobs'] ?? 0;
$due_jobs = $totals['due_jobs'] ?? (count($partially_paid) + count($due_invoices));
$total_paid_amount = $totals['total_paid_amount'] ?? 0;
$total_due_amount = $totals['total_due_amount'] ?? 0;

// Get credit balance using PaymentService
$customer_credit_balance = $paymentService->getCustomerCreditBalance($customer_id);

// Fetch credit summary via PaymentService
$credit_summary = $paymentService->getCustomerCreditSummary($customer_id);
$credit_transactions = $credit_summary['transactions'] ?? [];
$total_credit_topups = $credit_summary['topups'] ?? 0;
$total_credit_deductions = $credit_summary['deductions'] ?? 0;
$total_credit = $credit_summary['remaining_credit'] ?? 0;

// Output debug information to browser console
echo "<script>console.log('CustomerPaymentHistory Debug', " .
    json_encode([
        'customer_id' => $customer_id,
        'customer_name' => $customer_name,
        'invoice_summary' => $invoice_summary,
        'credit_summary' => $credit_summary
    ]) .
");</script>";
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
        <!-- Customer Summary Card -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-user"></i> Customer Summary - 
                    <span style="color: #3c8dbc; font-weight: bold; background: #f0f8ff; padding: 5px 15px; border-radius: 4px; margin-left: 10px;">
                        <?= htmlspecialchars($customer_name) ?>
                    </span>
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-file-text"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Jobs</span>
                                <span class="info-box-number"><?= $total_jobs ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Closed Jobs (Paid)</span>
                                <span class="info-box-number"><?= $closed_jobs ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid Amount</span>
                                <span class="info-box-number"><?= number_format($total_paid_amount, 2) ?> AED</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-orange">
                            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Due Jobs</span>
                                <span class="info-box-number"><?= $due_jobs ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Due Amount</span>
                                <span class="info-box-number"><?= number_format($total_due_amount, 2) ?> AED</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box <?= $customer_credit_balance >= 0 ? 'bg-purple' : 'bg-maroon' ?>">
                            <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Credit Balance</span>
                                <span class="info-box-number"><?= number_format($customer_credit_balance, 2) ?> AED</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#invoices" data-toggle="tab">Invoices</a></li>
                <li><a href="#credits" data-toggle="tab">Credit Summary</a></li>
            </ul>
            <div class="tab-content">
                <!-- Invoices Tab -->
                <div class="tab-pane active" id="invoices">
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
                            <?php if (count($partially_paid) > 0): ?>
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
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No partially paid invoices</td></tr>
                            <?php endif; ?>
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
                            <?php if (count($due_invoices) > 0): ?>
                                <?php foreach ($due_invoices as $invoice): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                        <td><?= number_format($invoice['total_charges'], 2) ?></td>
                                        <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                                        <td><span class="label label-danger">Due</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No due invoices</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

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
                            <?php if (count($completely_paid) > 0): ?>
                                <?php foreach ($completely_paid as $invoice): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                        <td><?= number_format($invoice['total_charges'], 2) ?></td>
                                        <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                                        <td><span class="label label-success">Paid</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No fully paid invoices</td></tr>
                            <?php endif; ?>
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
                            <?php if (count($credit_transactions) > 0): ?>
                                <?php foreach ($credit_transactions as $credit): ?>
                                    <?php
                                        $amount = number_format(abs($credit['amount']), 2);
                                        $date = htmlspecialchars($credit['payment_date']);
                                        $type = htmlspecialchars($credit['type_label'] ?? $credit['type']);
                                        $note = htmlspecialchars($credit['note'] ?? '');
                                    ?>
                                    <tr>
                                        <td><?= $amount ?></td>
                                        <td><?= $date ?></td>
                                        <td><?= $type ?></td>
                                        <td><?= $note ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No credit transactions found</td></tr>
                            <?php endif; ?>
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