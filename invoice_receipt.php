<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>
<?php require_once './backend/services/PaymentServices.php'; ?>

<?php
// Initialize PaymentService
$paymentService = new PaymentService($conn);
?>

<style media="screen">
/* print.css */
@media print {
  body * {
      visibility: hidden;
  }
  #printableArea, #printableArea * {
      visibility: visible;
  }
  #printableArea {
      position: absolute;
      left: 0;
      top: 0;
  }
}

.credit-balance-card {
  background-color: #fef5e7;
  border: 1px solid #f4cea3;
  border-radius: 10px;
  padding: 14px 18px;
  color: #000;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.credit-balance-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
}

.credit-toggle {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  color: #000;
  margin-bottom: 8px;
}

.credit-toggle input[type="checkbox"] {
  width: 20px;
  height: 20px;
  accent-color: #f4a261;
}

.credit-toggle span {
  font-size: 15px;
}

.credit-balance-text {
  margin: 0;
  font-size: 13px;
  color: #000;
}
</style>


      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1 class="page-title">Generate Receipt</h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reciepts</a></li>
            <li class="active">Generate Reciepts</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content printableArea">

          <div class="row">
          <form action="" method="POST" class="form-style">
            <div class="col-md-12">
              <div class="row">
                <!-- LEFT COLUMN -->
                <div class="col-md-6">
                  <div class="box box-primary">
                    <div class="box-body">
                      <?php
                        // Initialize variables
                        $selected = '';
                        $shipment_id  = '';
                        $customer_id = '';
                        $auto_receipt_number = '';
                        $customer_name = '';
                        $customer_credit_balance = 0.00;
                        
                        // Check if invoice_id is provided in URL
                        if (isset($_GET['invoice_id'])) {
                            $id = $_GET['invoice_id'];
                            $q = $conn->query("SELECT * FROM shipments WHERE invoice_number = '$id'");
                            if ($row = $q->fetch_assoc()) {
                                $selected = $row['invoice_number'];
                                $shipment_id = $row['shipment_id'];
                                $customer_id = $row['customer_id'];
                                
                                // Generate receipt number automatically when page loads with invoice_id
                                $auto_receipt_number = $paymentService->generateNextReceiptNumber($selected);
                                
                                // Get customer name
                                $sql = "SELECT name FROM customers WHERE customer_id = '$customer_id'";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    $customer_row = $result->fetch_assoc();
                                    $customer_name = $customer_row['name'];
                                }
                                
                                // Get customer credit balance
                                $customer_credit_balance = $paymentService->getCustomerCreditBalance($customer_id);
                            }
                        } else {
                            // Generate a temporary receipt number if no invoice is selected
                            $auto_receipt_number = 'RE-' . date('YmdHis');
                        }
                      ?>
                      
                      <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" id="date" name="date" required class="form-control">
                      </div>
                      <div class="form-group">
                        <label for="receipt_no">Receipt No:</label>
                        <input type="text" id="receipt_no" name="receipt_no" required class="form-control" value="<?= $auto_receipt_number ?>">
                      </div>

                      <!-- Job selection and customer name -->
                      <div class="form-group">
                        <label for="job">Job (Invoice Number):</label>
                        <input list="dropdown-jobs" class="form-control" name="jobs" id="jobs" placeholder="Enter Value" value="<?= $selected ?>" required>
                        
                      </div>

                      <div class="form-group">
                        <label for="customer">Customer:</label>
                        <input type="text" id="customer" name="customer" class="form-control" value="<?= $customer_name ?>" disabled>
                      </div>

                      <div class="form-group">
                        <label for="salesperson">Salesperson:</label>
                        <select id="salesperson" name="salesperson" required class="form-control">
                          <option value="">Select Salesperson</option>
                          <?php
                            $sql = "SELECT u_id, u_name FROM tbl_users WHERE u_id != 0";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $user_id = $row['u_id'];
                                    $user_name = $row['u_name'];
                                    echo "<option value='$user_name'>$user_name</option>";
                                }
                            }
                          ?>
                        </select>
                      </div>

                      <div class="credit-balance-card">
                        <label class="credit-toggle" for="deductCredit">
                          <input type="checkbox" id="deductCredit" name="deduct_credit" value="1" onchange="toggleCreditDeduction()">
                          <span>Deduct from customer credit</span>
                        </label>
                        <p class="credit-balance-text">
                          Customer credit balance:
                          <strong><?= number_format($customer_credit_balance, 2) ?></strong>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- RIGHT COLUMN (Ready for More Inputs) -->
                <div class="col-md-6">
                  <div class="box box-primary">
                    <div class="box-body">
                      <!-- Add your fields here -->
                      <h5>Additional Details</h5>
                      <!-- Example field -->
                      <?php
                      // These variables are already available: $shipment_id, $selected (invoice number)
                      $total_charges = 0;
                      $paid_amount = 0;
                      $remaining = 0;

                      // Use PaymentService to get total charges and paid amount
                      $total_charges = $paymentService->getInvoiceTotal($selected);
                      $paid_amount = $paymentService->getTotalPaidAmount($selected);

                      // Calculate remaining
                      $remaining = $total_charges - $paid_amount;
                      ?>
                      <div class="form-group">
                        <h5><strong>Invoice Summary</strong></h5>
                        <ul class="list-group">
                          <li class="list-group-item">
                            <strong>Total Charges:</strong>
                            <span id="total-charges"><?= round($total_charges, 2) ?></span>
                          </li>
                          <li class="list-group-item">
                            <strong>Amount Already Paid:</strong>
                            <span id="amount-paid"><?= round($paid_amount, 2) ?></span>
                          </li>
                          <li class="list-group-item">
                            <strong>Remaining Amount:</strong>
                            <span id="amount-remaining"><?= round($remaining, 2) ?></span>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>



          </div>

          <div class="row">

                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header">
                          <div class="row">
                            <div class="col-md-2">

                            <h3 class="box-title">Charges</h3>

                            </div>


                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-2">

                              <select id="globalCurrency" class="form-control" onchange="changeCurrencyForAllRows()">

                                  <?php

                                  $sql = "SELECT * FROM currencies";
                                  $result = $conn->query($sql);

                                  if ($result->num_rows > 0) {
                                      // Output data of each row
                                      while ($row = $result->fetch_assoc()) {
                                          $id = $row['id'];
                                          $currency = $row['currency'];
                                          $roe = $row['roe'];
                                          echo "<option value='$id'>$currency&nbsp;&nbsp;$roe</option>";
                                      }
                                  } else {
                                      echo "0 results";
                                  }
                                  ?>
                              </select>

                            </div>
                          </div>



                        </div>
                        <div class="box-body">

                            <!-- Payment Methods Section -->
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label style="font-weight: bold; margin-bottom: 15px;">Payment Methods:</label>
                                  
                                  <!-- Payment Method Checkboxes -->
                                  <div class="payment-methods-checkboxes" style="margin-bottom: 20px;">
                                    <div class="checkbox-group" style="display: flex; gap: 20px; flex-wrap: wrap;">
                                      <label class="checkbox-label" style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="cash_payment" name="payment_methods[]" value="cash" checked 
                                               onchange="togglePaymentRow('cash')" style="margin-right: 8px; transform: scale(1.2);">
                                        <span style="font-weight: 500;">Cash</span>
                                      </label>
                                      
                                      <label class="checkbox-label" style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="bank_transfer_payment" name="payment_methods[]" value="bank_transfer" 
                                               onchange="togglePaymentRow('bank_transfer')" style="margin-right: 8px; transform: scale(1.2);">
                                        <span style="font-weight: 500;">Bank Transfer</span>
                                      </label>
                                      
                                      <label class="checkbox-label" style="display: flex; align-items: center; cursor: pointer;">
                                        <input type="checkbox" id="cheque_payment" name="payment_methods[]" value="cheque" 
                                               onchange="togglePaymentRow('cheque')" style="margin-right: 8px; transform: scale(1.2);">
                                        <span style="font-weight: 500;">Cheque</span>
                                      </label>
                                    </div>
                                  </div>

                                  <!-- Dynamic Payment Rows Container -->
                                  <div id="payment-rows-container">
                                    <!-- Cash payment row (default) -->
                                    <div id="cash-payment-row" class="payment-method-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
                                      <h5 style="margin-bottom: 10px; color: #333;">Cash Payment</h5>
                                      <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 200px;">
                                          <label for="cash_description">Description:</label>
                                          <input type="text" id="cash_description" name="cash_description" class="form-control" placeholder="Cash payment description">
                                        </div>
                                        <div style="flex: 1; min-width: 150px;">
                                          <label for="cash_amount">Amount:</label>
                                          <input type="number" id="cash_amount" name="cash_amount" class="form-control payment-amount" 
                                                 step="0.01" min="0" placeholder="0.00" 
                                                 onchange="calculateTotalPayment()" 
                                                 oninput="calculateTotalPayment()">
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <!-- Full Payment Button and Summary -->
                                  <div style="margin-top: 20px; padding: 15px; border: 1px solid #28a745; border-radius: 5px; background-color: #f8fff9;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                      <button type="button" id="full-payment-btn" class="btn btn-success" onclick="setFullPayment()" 
                                              style="padding: 10px 20px; font-weight: bold;">
                                        Pay Full Amount
                                      </button>
                                      <div style="text-align: right;">
                                        <div style="margin-bottom: 5px;">
                                          <strong>Total Payment: <span id="currency-display">AED</span> <span id="total-payment-display">0.00</span></strong>
                                        </div>
                                        <div style="margin-bottom: 5px; font-size: 12px; color: #666;">
                                          <em>AED Equivalent: <span id="total-payment-aed-display">0.00</span></em>
                                        </div>
                                        <div style="color: #28a745;">
                                          <strong>Remaining: $<span id="remaining-amount-display"><?php echo isset($remaining) ? number_format($remaining, 2) : '0.00'; ?></span></strong>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <hr>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label>Note</label>
                                  <textarea id="special_note" class="form-control" rows="3" placeholder="Enter Special note"></textarea>
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-3">
                                  <button type="button" class="btn btn-danger form-control" onclick="collectFormData()">Add Payment & Print Invoice</button>
                              </div>
                            </div>
                        </div>
                    </div>

              </div>


            </div>
        </section><!-- /.content -->
      </div>
    </div><!-- /.content-wrapper -->
<?php include 'shipment_modals.php' ?>

    <iframe id="printFrame" style="display:none;"></iframe>

<script src="./scriptprint.js" type="text/javascript"></script>

<script type="text/javascript">
 
  window.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
    });

// Generate receipt number when invoice is selected
function generateReceiptNumber() {
    const invoiceNumber = document.getElementById('jobs').value;
    
    if (!invoiceNumber || invoiceNumber.trim() === '') {
        document.getElementById('receipt_no').value = '';
        return;
    }
    
    // Call backend to generate receipt number
    fetch('./backend/generate_receipt_number.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            invoice_number: invoiceNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('receipt_no').value = data.receipt_number;
        } else {
            console.error('Error generating receipt number:', data.message);
            // Fallback: use timestamp-based receipt number
            const timestamp = Date.now();
            document.getElementById('receipt_no').value = `RE-${timestamp}`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback: use timestamp-based receipt number
        const timestamp = Date.now();
        document.getElementById('receipt_no').value = `RE-${timestamp}`;
    });
}

function formatDate(dateString) {
    if(!dateString){
      return '';
    }
    const date = new Date(dateString);
    let day = date.getDate();
    let month = date.getMonth() + 1; // Months are zero-indexed
    const year = date.getFullYear();

    // Add leading zero to day and month if they are less than 10
    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;

    return `${day}/${month}/${year}`;
}
function generateInvoice(data, receiptId) {
    try {
        const originalTitle = document.title;
        document.title = data.receipt_no;
        let printContent = `
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        line-height: 1.4;
                        margin: 20px;
                        background-image: url('reciept.jpg');
                        background-size: 100% 100%;
                    }
                    .receipt-container {
                        max-width: 800px;
                        margin: 0 auto;
                    }
                    .receipt-title {
                        text-align: center;
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 20px;
                    }
                    .header-info {
                        margin-bottom: 30px;
                        margin-top: 50px;
                    }
                    .header-info p {
                        margin: 5px 0;
                    }
                    .customer-details {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 20px;
                        margin-bottom: 30px;
                        font-size: 11px;
                    }
                    .charges-table {
                        font-size: 12px;
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    .charges-table th, .charges-table td {
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: left;
                    }
                    .charges-table th {
                        background-color: #f2f2f2;
                    }
                    .total-section {
                        width: 100%;
                        text-align: right;
                    }
                    .total-section table {
                        margin-left: auto;
                        width: 300px;
                    }
                    .total-section td {
                        padding: 5px;
                    }
                    .disclaimer {
                        text-align: center;
                        font-weight: bold;
                        margin: 30px 0;
                        letter-spacing: 1px;
                    }
                    .payment-note {
                        text-align: left;
                        margin: 20px 0;
                        font-style: italic;
                    }
          .amount-in-words {
            margin: 25px 0;
            font-weight: 600;
            letter-spacing: 0.5px;
          }
                    .footer {
                        text-align: center;
                        margin-top: 40px;
                        font-size: 11px;
                    }
                    .text-right {
                        text-align: right;
                    }
                </style>
            </head>
            <body>
                <div class="receipt-container">
                    <br><br>

                    <div class="header-info">
                        <p><strong>DATE:</strong> ${formatDate(data.date)}</p>
                        <p><strong>RECEIPT NO:</strong> ${data.receipt_no}</p>
                        <p><strong>INVOICE NO:</strong> ${data.invoice_no}</p>
                        <p><strong>CUSTOMER:</strong> ${data.customer}</p>
                    </div>

                    <div class="customer-details">
                        <div><strong>SALESPERSON</strong><br>${data.salesperson}</div>
                        <div><strong>MODE OF PAYMENT</strong><br>${data.mode_of_payment}</div>
                    </div>

                    <table class="charges-table">
                        <thead>
                            <tr>
                              <th style="width: 70%;">DESCRIPTION</th>
                              <th style="width: 15%;" class="text-right">CURRENCY</th>
                              <th style="width: 15%;" class="text-right">TOTAL AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>`;

              data.borderCharges.forEach(charge => {
                printContent += `
                      <tr>
                          <td>${charge.description}</td>
                          <td class="text-right">${data.currency_name}</td>
                          <td class="text-right">${formatCurrency(charge.totalAmount)}</td>
                      </tr>`;
              });
              const invoiceTotal = parseFloat(document.getElementById('total-charges')?.innerText || 0);
              const alreadyPaid = parseFloat(document.getElementById('amount-paid')?.innerText || 0);
              const newlyPaid = parseFloat(data.total);
              const remaining = invoiceTotal - (alreadyPaid + newlyPaid);
              const amountInWords = numberToWords(newlyPaid);
              printContent += `
                          <tr><td>&nbsp;</td><td></td><td></td></tr>
                          <tr><td>&nbsp;</td><td></td><td></td></tr>
                          <tr><td>&nbsp;</td><td></td><td></td></tr>
                          
                          
                          <tr>
                              <td></td>
                              <th class="text-right">TOTAL (AED)</th>
                              <th class="text-right" id="new-payment-row">${formatCurrency(newlyPaid)}</th>
                          </tr>
                        </tbody>
                      </table>
                      <table style="width: 300px; float: right; font-size: 12px; margin-top: 20px;">
                          <tr>
                              <th class="text-right" style="padding: 5px;">Invoice Total</th>
                              <th class="text-right" style="padding: 5px;" id="invoice-total-row">${formatCurrency(invoiceTotal)}</th>
                          </tr>
                          <tr>
                              <th class="text-right" style="padding: 5px;">Total Payment</th>
                              <th class="text-right" style="padding: 5px;" id="already-paid-row">${formatCurrency(alreadyPaid+newlyPaid)}</th>
                          </tr>
                          <tr>
                              <th class="text-right" style="padding: 5px;">Remaining Due</th>
                              <th class="text-right" style="padding: 5px;" id="remaining-due-row">${formatCurrency(remaining)}</th>
                          </tr>
                      </table>
            <div class="amount-in-words">
              <strong>Amount in Words:</strong> ${amountInWords}
            </div>
                    </div>
                </body>
              </html>`;

      var img = new Image();
      img.src = "reciept.jpg";
      img.onload = function() {
        // Get the iframe element
        const printFrame = document.getElementById('printFrame');

        // Save receipt content before printing
        fetch('./backend/save_printed_receipt.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            receipt_id: receiptId, // make sure this is defined from your payment process
            html_content: printContent
          })
        })

        // Set the content of the iframe
        printFrame.src = 'about:blank';
        printFrame.contentWindow.document.open();
        printFrame.contentWindow.document.write(printContent);
        printFrame.contentWindow.document.close();

        // Print the content of the iframe
        printFrame.focus();
        printFrame.contentWindow.print();
        document.title = originalTitle;
      }

    } catch (error) {
        console.error('Error generating receipt:', error);
    }
}

// Helper function to format currency
function formatCurrency(amount) {
    return parseFloat(amount).toFixed(2);
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\//g, '.');
}

function numberToWords(number) {
    const units = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
    const teens = ["", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    const tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
    const thousands = ["", "Thousand", "Million", "Billion"];

    if (number === 0) return "Zero Dirhams";

    let word = '';

    function convert(num, idx) {
        if (num === 0) return '';
        let str = '';

        if (num > 99) {
            str += units[Math.floor(num / 100)] + ' Hundred ';
            num %= 100;
        }

        if (num > 10 && num < 20) {
            str += teens[num - 10] + ' ';
        } else {
            str += tens[Math.floor(num / 10)] + ' ';
            str += units[num % 10] + ' ';
        }

        return str + thousands[idx] + ' ';
    }

    let integerPart = Math.floor(number);
    let decimalPart = Math.round((number - integerPart) * 100);

    let thousandIndex = 0;

    while (integerPart > 0) {
        const numChunk = integerPart % 1000;
        if (numChunk > 0) {
            word = convert(numChunk, thousandIndex) + word;
        }
        integerPart = Math.floor(integerPart / 1000);
        thousandIndex++;
    }

    word = word.trim() + " Dirhams";

    if (decimalPart > 0) {
        word += " and " + convert(decimalPart, 0).trim() + " Fils";
    }

    return word + " only";
}


function addDropdownData() {
  const loading_street = document.getElementById('loading_street').value;
  const port_origin = document.getElementById('port_origin').value;
  const port_destination = document.getElementById('port_destination').value;
  const unloading_street = document.getElementById('unloading_street').value;
  const warehouse = document.getElementById('warehouse').value;
  const item_desc = document.getElementById('item_desc').value;



  const taxableValues = Array.from(document.querySelectorAll('input[name="taxableValues"]')).map(input => input.value);
  const chargeDescription = Array.from(document.querySelectorAll('input[name="chargeDescription"]')).map(input => input.value);
  const vehicle_num = Array.from(document.querySelectorAll('input[name="vehicle_num"]')).map(input => input.value);

    var data = {
        loading_street: loading_street,
        port_origin: port_origin,
        item_desc: item_desc,
        port_destination: port_destination,
        unloading_street: unloading_street,
        warehouse: warehouse,
        taxableValues: taxableValues,
        chargeDescription: chargeDescription,
        vehicle_num: vehicle_num,
    };
    console.log(data);
    // Collect data from all dynamic input fields
    // document.querySelectorAll('input[name="taxableValues"]').forEach(function(input) {
    //     data.taxableValues.push(input.value || ''); // Push empty string if value is falsy
    // });
    // document.querySelectorAll('input[name="chargeDescription"]').forEach(function(input) {
    //     data.chargeDescriptions.push(input.value || '');
    // });
    // document.querySelectorAll('input[name="vehicle_num"]').forEach(function(input) {
    //     data.vehicleNumbers.push(input.value || '');
    // });

    console.log(JSON.stringify(data)); // Log the JSON data

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                console.log('done adding data');
                // printInvoice();
                collectFormData()
            } else {
                // Handle error
                console.error("Error adding data: " + this.status);
                alert("Error adding data. Please try again.");
            }
        }
    };

    xhr.open("POST", "./backend/add_dropdown_data.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(data));
}


document.addEventListener('DOMContentLoaded', function() {

  const shippingModes = document.getElementsByName('shipping_mode');
  const importExport = document.getElementsByName('import_export');
  const shipmentIdField = document.getElementById('shipment_id');
  const job_number = document.getElementById('job_number');
  const invoice_number = document.getElementById('invoice_number');

  const dateInput = document.getElementById('job_date');
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  dateInput.value = `${year}-${month}-${day}`;

  // Generate receipt number for already selected invoice
  const jobsInput = document.getElementById('jobs');
  if (jobsInput && jobsInput.value && jobsInput.value.trim() !== '') {
    generateReceiptNumber();
  }

  shippingModes.forEach(radio => radio.addEventListener('change', generateShipmentId));
  importExport.forEach(radio => radio.addEventListener('change', generateShipmentId));
});


function collectFormData() {
  try {
    // Collect form data
    const date = document.getElementById('date').value;
    const salesperson = document.getElementById('salesperson').value;
    const receipt_no = document.getElementById('receipt_no').value;
    const customer = document.getElementById('customer').value;
    const job = document.getElementById('jobs').value;
    const special_note = document.getElementById('special_note').value;
    
    // Calculate total payment from payment methods and collect individual amounts
    let totalPaymentAmount = 0;
    let totalPaymentAmountAED = 0; // This will be the AED equivalent for backend
    let cashAmount = 0;
    let chequeAmount = 0;
    let bankTransferAmount = 0;
    
    // Get currency conversion rates
    const currencySelect = document.getElementById('globalCurrency');
    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    const currencyText = selectedOption.textContent.trim(); // e.g. "AED  3.685"
    const [currencyName, roe] = currencyText.split(/\s+/);
    
    // Get AED rate for conversion
    let aedValue = null;
    for (let i = 0; i < currencySelect.options.length; i++) {
        let optionText = currencySelect.options[i].text;
        if (optionText.split("\u00A0\u00A0")[0] === "AED") {
            aedValue = optionText.split("\u00A0\u00A0")[1];
            break;
        }
    }
    
    // Collect payment methods data
    const paymentMethods = [];
    const checkedMethods = document.querySelectorAll('input[name="payment_methods[]"]:checked');
    
    checkedMethods.forEach(checkbox => {
        const method = checkbox.value;
        const amountInput = document.getElementById(method + '_amount');
        const descriptionInput = document.getElementById(method + '_description');
        
        if (amountInput && amountInput.value && parseFloat(amountInput.value) > 0) {
            const amount = parseFloat(amountInput.value); // Amount in selected currency
            totalPaymentAmount += amount; // Total in selected currency
            
            // Convert to AED for backend processing
            const amountAED = amount * parseFloat(aedValue) / parseFloat(roe);
            totalPaymentAmountAED += amountAED;
            
            // Store individual amounts (in selected currency)
            switch(method) {
                case 'cash':
                    cashAmount = amount;
                    break;
                case 'cheque':
                    chequeAmount = amount;
                    break;
                case 'bank_transfer':
                    bankTransferAmount = amount;
                    break;
            }
            
            paymentMethods.push(method);
        }
    });
    
    // Collect charges table data
    const description = '';
    // Checkboxes
    const deductCredit = document.getElementById('deductCredit').checked;

    // Collect charges table data
    var borderCharges = Array.from(document.querySelectorAll('.border-charge-row')).map(row => {
      // const quantity = row.querySelector('input[name="quantity"]');
      const description = row.querySelector('input[name="description"]');
      // const currency = row.querySelector('select[name^="currencyRate"]');
      // const unit_price = row.querySelector('input[name="unit_price"]');
      const totalAmount = row.querySelector('input[name="totalAmount"]');

      return {
        // quantity: quantity ? quantity.value : '',
        description: description ? description.value : '',
        // currency: currency ? currency.value : '',
        // unit_price: unit_price ? unit_price.value : '',
        totalAmount: totalAmount ? totalAmount.value : ''
      };
    });

    // Prepare data to send
    var data = {
      date: date,
      receipt_no: receipt_no,
      salesperson: salesperson,
      customer: customer,
      invoice_no: job,
      special_note: special_note,
      total: totalPaymentAmount, // Total in selected currency
      total_aed: totalPaymentAmountAED, // Total in AED for backend processing
      cash_amount: cashAmount, // Individual amounts in selected currency
      cheque_amount: chequeAmount,
      bank_transfer_amount: bankTransferAmount,
      cash_description: document.getElementById('cash_description') ? document.getElementById('cash_description').value : '',
      cheque_description: document.getElementById('cheque_description') ? document.getElementById('cheque_description').value : '',
      bank_transfer_description: document.getElementById('bank_transfer_description') ? document.getElementById('bank_transfer_description').value : '',
      cheque_details: document.getElementById('cheque_details') ? document.getElementById('cheque_details').value : '',
      bank_transfer_details: document.getElementById('bank_transfer_details') ? document.getElementById('bank_transfer_details').value : '',
      currency_name: currencyName,
      currency_roe: roe,
      deduct_credit: deductCredit,
      borderCharges: borderCharges,
      payment_methods: paymentMethods
    };
    
    // Add individual payment method amounts and descriptions
    paymentMethods.forEach(method => {
        const amountInput = document.getElementById(method + '_amount');
        const descriptionInput = document.getElementById(method + '_description');
        
        if (amountInput) {
            data[method + '_amount'] = amountInput.value;
        }
        if (descriptionInput) {
            data[method + '_description'] = descriptionInput.value;
        }
        
        // Add details field for cheque and bank_transfer
        if (method === 'cheque' || method === 'bank_transfer') {
            const detailsInput = document.getElementById(method + '_details');
            if (detailsInput) {
                data[method + '_details'] = detailsInput.value;
            }
        }
    });
    
    // Validation
    if (totalPaymentAmount <= 0) {
        alert('Please enter at least one payment amount greater than 0.');
        return;
    }
    
    if (paymentMethods.length === 0) {
        alert('Please select at least one payment method and enter an amount.');
        return;
    }
    
    // Validate that individual amounts sum to total (frontend validation)
    const calculatedTotal = cashAmount + chequeAmount + bankTransferAmount;
    if (Math.abs(calculatedTotal - totalPaymentAmount) > 0.01) {
        alert(`Payment breakdown error: Individual amounts (${calculatedTotal.toFixed(2)}) don't match total (${totalPaymentAmount.toFixed(2)})`);
        return;
    }

    console.log(data);
    console.log(JSON.stringify(data));

    // Create an XMLHttpRequest object
    const xhr = new XMLHttpRequest();

    // Handle the response
    xhr.onreadystatechange = function () {
      if (this.readyState == 4) {
        if (this.status == 200) {
          console.log('done adding receipt data');
          let response = JSON.parse(this.responseText);
          let receiptId = response.receipt_id;
          generateInvoice(data,receiptId);
        }
        else if (this.status == 409) {
          alert("Receipt Number already Exists");
        }
        else {
          let response = this.responseText;
            try {
              let parsed = JSON.parse(response);
              if (parsed.error) {
                alert("Error: " + parsed.error);
              } else {
                alert("An unknown error occurred.");
              }
            } catch (e) {
              alert("Unexpected error: " + response);
            }
        }
      }
    };

    // Send the request with the data
    xhr.open('POST', './backend/save_receipt_payment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));
  } catch (error) {
    console.error('Error:', error);
    alert('An error occurred while inserting the receipt');
  }
}

let borderChargeCount = 1;

function calculateTotal() {
  const currencyRateElement =  document.getElementById('globalCurrency');

  let aedValue = null;
  let usdValue = null;

  for (let i = 0; i < currencyRateElement.options.length; i++) {
      let optionText = currencyRateElement.options[i].text;

      // Check if the option contains 'AED' at index 0 after splitting
      if (optionText.split("\u00A0\u00A0")[0] === "AED") {
          aedValue = optionText.split("\u00A0\u00A0")[1];
          // Exit the loop once the correct option is found
      }
      if (optionText.split("\u00A0\u00A0")[0] === "USD") {
          usdValue = optionText.split("\u00A0\u00A0")[1];
          // Exit the loop once the correct option is found
      }
  }
  let grandTotal = 0;

  document.querySelectorAll('.border-charge-row').forEach(row => {
      const quantity = 1;
      const unitPrice = parseFloat(row.querySelector('[name="unit_price"]').value) || 0;
      const selcurrencyRateElement =  document.getElementById('globalCurrency');
      const selectedCurrency = currencyRateElement.options[selcurrencyRateElement.selectedIndex].text;
      const currencyRate = selectedCurrency.split("\u00A0\u00A0")[1];
      // Calculate Line Total
      const lineTotal = quantity * unitPrice;
      row.querySelector('[name="totalAmount"]').value = lineTotal.toFixed(2);

      const amountAED =lineTotal * parseFloat(aedValue) / parseFloat(currencyRate);
      // row.querySelector('[name^="amountAED"]').value = amountAED.toFixed(2);

      // Add to grand total
      grandTotal += amountAED;
  });

  // Note: Total field not available in this form
  // document.getElementById('total').value = grandTotal.toFixed(2);
}

function addBorderChargeRow() {
  borderChargeCount++;

  // Create a new row
  const newRow = document.createElement('tr');
  newRow.className = 'border-charge-row';
  newRow.dataset.index = borderChargeCount;
  newRow.innerHTML = `
      <td><input type="text" class="form-control rate-input" id="quantity${borderChargeCount}" name="quantity" placeholder="Enter quantity"></td>
      <td><input type="text" class="form-control" id="description${borderChargeCount}" name="description"></td>
      <td>
        <select id="currencyRate${borderChargeCount}" name="currencyRate1" class="form-control">
          <?php

          $sql = "SELECT * FROM currencies";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              // Output data of each row
              while ($row = $result->fetch_assoc()) {
                  $id = $row['id'];
                  $currency = $row['currency'];
                  $roe = $row['roe'];
                  echo "<option value='$id'>$currency</option>";
              }
          } else {
              echo "0 results";
          }
          ?>
        </select>
      </td>
      <td><input type="text" class="form-control" id="unit_price${borderChargeCount}" name="unit_price"></td>
      <td><input type="text" class="form-control" id="totalAmount${borderChargeCount}" name="totalAmount" disabled></td>
      <td>
          <button type="button" class="btn btn-danger" onclick="removeBorderChargeRow(this)">-</button>
      </td>
  `;

  // Append the new row to the container
  const tbody = document.querySelector('#borderChargesContainer tbody');
  tbody.appendChild(newRow);
}

function removeBorderChargeRow(button) {
  const row = button.parentElement.parentElement;
  row.remove();

  // Update all remaining rows' labels
  updateBorderChargeLabels();
  // calculateTotal(); // Removed - no charges table in this form
}

function updateBorderChargeLabels() {
  const rows = document.querySelectorAll('.border-charge-row');
  rows.forEach((row, index) => {
      const newIndex = index + 1;
      row.dataset.index = newIndex;

      // Update row elements' IDs
      const quantity = row.querySelector('input[name="quantity"]');
      quantity.id = `quantity${newIndex}`;

      const description = row.querySelector('input[name="description"]');
      description.id = `description${newIndex}`;

      const currency = row.querySelector('input[name="currencyRate"]');
      currency.id = `currencyRate${newIndex}`;
      const unitPrice = row.querySelector('input[name="unit_price"]');
      unitPrice.id = `unit_price${newIndex}`;

      const totalAmount = row.querySelector('input[name="totalAmount"]');
      totalAmount.id = `totalAmount${newIndex}`;
  });
}

// Payment Methods Functions
function toggleCreditDeduction() {
    const creditCheckbox = document.getElementById('deductCredit');
    const paymentCheckboxes = document.querySelectorAll('input[name="payment_methods[]"]');
    
    if (creditCheckbox.checked) {
        // Disable non-cash payment method checkboxes and uncheck them
        paymentCheckboxes.forEach(checkbox => {
            if (checkbox.value !== 'cash') {
                checkbox.disabled = true;
                checkbox.checked = false; // Uncheck non-cash methods
                
                // Remove their payment rows if they exist
                const row = document.getElementById(checkbox.value + '-payment-row');
                if (row) {
                    row.remove();
                }
            }
        });
        
        // Ensure cash is checked and its row exists
        const cashCheckbox = document.getElementById('cash_payment');
        if (cashCheckbox) {
            cashCheckbox.checked = true;
            // Create cash payment row if it doesn't exist
            const cashRow = document.getElementById('cash-payment-row');
            if (!cashRow) {
                createPaymentRow('cash');
            }
        }
        
        // Recalculate payment totals
        calculateTotalPayment();
    } else {
        // Enable all payment method checkboxes
        paymentCheckboxes.forEach(checkbox => {
            checkbox.disabled = false;
        });
    }
}

function togglePaymentRow(paymentType) {
    const checkbox = document.getElementById(paymentType + '_payment');
    const row = document.getElementById(paymentType + '-payment-row');
    
    if (checkbox.checked) {
        if (!row) {
            // Create new payment row
            createPaymentRow(paymentType);
        }
    } else {
        if (row) {
            row.remove();
        }
    }
    
    calculateTotalPayment();
}

function createPaymentRow(paymentType) {
    const container = document.getElementById('payment-rows-container');
    const paymentTypeTitle = paymentType.charAt(0).toUpperCase() + paymentType.slice(1).replace('_', ' ');
    
    // Check if this payment type needs a details field
    const needsDetails = paymentType === 'cheque' || paymentType === 'bank_transfer';
    const detailsField = needsDetails ? `
                <div style="flex: 1; min-width: 200px;">
                    <label for="${paymentType}_details">Details:</label>
                    <textarea id="${paymentType}_details" name="${paymentType}_details" class="form-control" rows="2" placeholder="${paymentTypeTitle} payment details (optional)"></textarea>
                </div>` : '';
    
    const rowHtml = `
        <div id="${paymentType}-payment-row" class="payment-method-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
            <h5 style="margin-bottom: 10px; color: #333;">${paymentTypeTitle} Payment</h5>
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label for="${paymentType}_description">Description:</label>
                    <input type="text" id="${paymentType}_description" name="${paymentType}_description" class="form-control" placeholder="${paymentTypeTitle} payment description">
                </div>
                ${detailsField}
                <div style="flex: 1; min-width: 150px;">
                    <label for="${paymentType}_amount">Amount:</label>
                    <input type="number" id="${paymentType}_amount" name="${paymentType}_amount" class="form-control payment-amount" 
                           step="0.01" min="0" placeholder="0.00" 
                           onchange="calculateTotalPayment()" 
                           oninput="calculateTotalPayment()">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', rowHtml);
}

function calculateTotalPayment() {
    let totalPayment = 0;
    let totalPaymentAED = 0;
    const paymentAmounts = document.querySelectorAll('.payment-amount');
    
    // Get currency conversion rates
    const currencySelect = document.getElementById('globalCurrency');
    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    const currencyText = selectedOption.textContent.trim(); // e.g. "AED  3.685"
    const [currencyName, roe] = currencyText.split(/\s+/);
    
    // Get AED rate for conversion
    let aedValue = null;
    for (let i = 0; i < currencySelect.options.length; i++) {
        let optionText = currencySelect.options[i].text;
        if (optionText.split("\u00A0\u00A0")[0] === "AED") {
            aedValue = optionText.split("\u00A0\u00A0")[1];
            break;
        }
    }
    
    paymentAmounts.forEach(input => {
        const amount = parseFloat(input.value) || 0;
        totalPayment += amount;
        
        // Convert to AED
        if (aedValue && roe) {
            const amountAED = amount * parseFloat(aedValue) / parseFloat(roe);
            totalPaymentAED += amountAED;
        }
    });
    
    // Update display
    document.getElementById('currency-display').textContent = currencyName;
    document.getElementById('total-payment-display').textContent = totalPayment.toFixed(2);
    document.getElementById('total-payment-aed-display').textContent = totalPaymentAED.toFixed(2);
    
    // Calculate remaining amount (should be in AED since invoice totals are in AED)
    const remainingElement = document.getElementById('remaining-amount-display');
    const amountRemainingElement = document.getElementById('amount-remaining');
    
    if (amountRemainingElement) {
        // Use the server-calculated remaining amount as base
        const originalRemaining = parseFloat(amountRemainingElement.textContent) || 0;
        const newRemaining = Math.max(0, originalRemaining - totalPaymentAED); // Use AED amount
        remainingElement.textContent = newRemaining.toFixed(2);
    }
}

function setFullPayment() {
    const amountRemainingElement = document.getElementById('amount-remaining');
    
    if (!amountRemainingElement) {
        alert('Please select an invoice first to calculate full payment.');
        return;
    }
    
    const remainingAmountAED = parseFloat(amountRemainingElement.textContent) || 0;
    
    if (remainingAmountAED <= 0) {
        alert('This invoice is already fully paid.');
        return;
    }
    
    // Get all active payment method checkboxes
    const activeCheckboxes = document.querySelectorAll('input[name="payment_methods[]"]:checked');
    
    if (activeCheckboxes.length === 0) {
        alert('Please select at least one payment method first.');
        return;
    }
    
    // Get currency conversion rates to convert AED back to selected currency
    const currencySelect = document.getElementById('globalCurrency');
    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    const currencyText = selectedOption.textContent.trim(); // e.g. "AED  3.685"
    const [currencyName, roe] = currencyText.split(/\s+/);
    
    // Get AED rate for conversion
    let aedValue = null;
    for (let i = 0; i < currencySelect.options.length; i++) {
        let optionText = currencySelect.options[i].text;
        if (optionText.split("\u00A0\u00A0")[0] === "AED") {
            aedValue = optionText.split("\u00A0\u00A0")[1];
            break;
        }
    }
    
    // Convert remaining AED amount to selected currency
    let remainingAmountInSelectedCurrency = remainingAmountAED;
    if (aedValue && roe && currencyName !== 'AED') {
        remainingAmountInSelectedCurrency = remainingAmountAED * parseFloat(roe) / parseFloat(aedValue);
    }
    
    // Distribute the remaining amount among active payment methods (in selected currency)
    const amountPerMethod = remainingAmountInSelectedCurrency / activeCheckboxes.length;
    
    activeCheckboxes.forEach(checkbox => {
        const paymentType = checkbox.value;
        const amountInput = document.getElementById(paymentType + '_amount');
        if (amountInput) {
            amountInput.value = amountPerMethod.toFixed(2);
        }
    });
    
    calculateTotalPayment();
}

// Initialize payment calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotalPayment();
});

// Function called when global currency changes
function changeCurrencyForAllRows() {
    // Since this form doesn't have border charges, we just need to recalculate payment totals
    calculateTotalPayment();
}



</script>

<footer class="main-footer">

  <strong>Designed and Developed by <a href="#">Zeeshutterz & Infinite Coding</a></strong>
</footer>
</div><!-- ./wrapper -->

<!-- jQuery 2.1.3 -->
<script src="./plugins/jQuery/jQuery-2.1.3.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="./bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- DATA TABES SCRIPT -->
<script src="./plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="./plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="./plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='./plugins/fastclick/fastclick.min.js'></script>
<!-- AdminLTE App -->
<script src="./dist/js/app.min.js" type="text/javascript"></script>
<!-- AdminLTE for demo purposes -->
<script src="./dist/js/demo.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- page script -->
<script src="./scriptprint.js" type="text/javascript"></script>
