<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>

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
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                
                  <div class="  box-body">
                    <div class="row">
                      <div class="form-group col-md-12">

                          <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="date" id="date" name="date" required class="form-control">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label for="receipt_no">Receipt No:</label>
                                <input type="text" id="receipt_no" name="receipt_no" required class="form-control">
                            </div>
                          </div>
                        </div>
                          <div class="row">
                            <div class="form-group col-md-12">
                              <br>
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                        <?php
                                          $selected = '';
                                          if (isset($_GET['customer_id'])) {
                                              $id = $_GET['customer_id'];
                                              $q = $conn->query("SELECT name FROM customers WHERE customer_id = $id");
                                              if ($row = $q->fetch_assoc()) {
                                                  $selected = $row['name'];
                                              }
                                          }
                                        ?>
                                        <label for="customer">Customer:</label>
                                        <input list="dropdown-customers" class="form-control" name="customer" value="<?= $selected ?>" id="customer" placeholder="Enter Value">
                                        <datalist id="dropdown-customers">
                                          <?php

                                          $sql = "SELECT * FROM customers";
                                          $result = $conn->query($sql);

                                          if ($result->num_rows > 0) {
                                              // Output data of each row
                                              while ($row = $result->fetch_assoc()) {
                                                  $name = $row['name'];
                                                  echo "<option value='$name'></option>";
                                              }
                                          } else {
                                              echo "0 results";
                                          }
                                          ?>
                                        </datalist>
                                    </div>
                                  </div>
                                  <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to">To (Address):</label>
                                        <input type="text" id="to" name="to" required class="form-control">
                                    </div>
                                  </div> -->
                  <div class="col-md-6">
                  <div class="form-group">
                    <label for="salesperson">Salesperson:</label>
                    <select id="salesperson" name="salesperson" required class="form-control">
                      <option value="">Select Salesperson</option>
                      <?php
                      $usersSql = "SELECT u_id, u_name FROM tbl_users ORDER BY u_name";
                      $usersResult = $conn->query($usersSql);
                      if ($usersResult && $usersResult->num_rows > 0) {
                        while ($userRow = $usersResult->fetch_assoc()) {
                          $userId = htmlspecialchars($userRow['u_id']);
                          $userName = htmlspecialchars($userRow['u_name']);
                          echo "<option value='{$userName}'>{$userName}</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  </div>
                                  <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="job">Job (Invoice Number):</label>
                                        <input list="dropdown-jobs" class="form-control" name="jobs" id="jobs" placeholder="Enter Value">
                                        <datalist id="dropdown-jobs">
                                          <?php

                                          $sql = "SELECT invoice_number FROM shipments";
                                          $result = $conn->query($sql);

                                          if ($result->num_rows > 0) {
                                              // Output data of each row
                                              while ($row = $result->fetch_assoc()) {
                                                  $item_desc = $row['invoice_number'];
                                                  echo "<option value='$item_desc'></option>";
                                              }
                                          } else {
                                              echo "0 results";
                                          }
                                          ?>
                                        </datalist>
                                    </div>
                                  </div> -->
                                  <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mode_of_payment">Mode of Payment:</label>
                                        <input type="text" id="mode_of_payment" name="mode_of_payment" required class="form-control">
                                    </div>
                                  </div>
                                  <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date:</label>
                                        <input type="date" id="due_date" name="due_date" required class="form-control">
                                    </div>
                                  </div> -->

                                </div><br>


                            </div>
                          </div>
                      </div>
                    </div>

                  </div><!-- /.box-body -->

              </div><!-- /.box -->

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

                            <div class="row">
                                <div class="col-md-12">
                                  <div class="">
                                    <div id="borderChargesContainer" class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                              <tr>
                                                  <th>Description</th>
                                                  <th>Amount</th>
                                                  <th>Total</th>
                                              </tr>

                                            </thead>
                                            <tbody>
                                                <tr class="border-charge-row" data-index="1">

                                                  <td><input type="text" class="form-control" id="description" name="description"></td>
                                                  <!-- <td>
                                                    <select id="currencyRate1" name="currencyRate1" class="form-control">
                                                      <?php

                                                      $sql = "SELECT * FROM currencies";
                                                      $result = $conn->query($sql);

                                                      if ($result->num_rows > 0) {
                                                          // Output data of each row
                                                          while ($row = $result->fetch_assoc()) {
                                                              $id = $row['id'];
                                                              $currency = $row['currency'];
                                                              $roe = $row['roe'];
                                                              echo "<option value='$id'>$currency - $roe</option>";
                                                          }
                                                      } else {
                                                          echo "0 results";
                                                      }
                                                      ?>
                                                    </select>
                                                  </td> -->
                                                  <td><input type="text" class="form-control" id="unit_price" name="unit_price"></td>

                                                  <td><input type="text" class="form-control" id="totalAmount" name="totalAmount" disabled></td>
                                                  
                                                </tr>
                                                <!-- Additional rows will be added here -->
                                            </tbody>

                                        </table>

                                    </div>
                                  </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="total">Total (AED):</label>
                                    <input type="text" class="form-control" id="total" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="total">&nbsp;</label> <br>
                                    <button type="button" class="btn btn-primary" onclick="calculateTotal()">Calculate Total</button>
                                </div>
                                <div class="col-md-3"></div>

                            </div>
                            <br>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <label>Note</label>
                                  <textarea id="special_note" class="form-control" rows="3" placeholder="Enter Special note"></textarea>
                                </div>

                              </div>
                              <div class="col-md-3">
                                  <button type="button" class="btn btn-danger form-control" onclick="collectFormData()">Print Invoice</button>
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
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                        font-size: 12px;
                    }
                    .charges-table th, .charges-table td {
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: left;
                        font-size: 12px;
                    }
                    .charges-table th {
                        background-color: #f2f2f2;
                        font-size: 12px;
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

                    printContent += `
                            <tr><td>&nbsp</td><td></td><td></td></tr>
                            <tr><td>&nbsp</td><td></td><td></td></tr>
                            <tr><td>&nbsp</td><td></td><td></td></tr>
                            <tr><td>&nbsp</td><td></td><td></td></tr>
                            <tr>
                                <td></td>
                                <th class="text-right" >TOTAL (AED)</th>
                                <th class="text-right">${formatCurrency(data.total)}</th>
                            </tr>
                        </tbody>
                    </table>
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


  shippingModes.forEach(radio => radio.addEventListener('change', generateShipmentId));
  importExport.forEach(radio => radio.addEventListener('change', generateShipmentId));
});




function collectFormData() {
  calculateTotal();
  try {
    // Collect form data
    const date = document.getElementById('date').value;
    const salesperson = document.getElementById('salesperson').value;
    const receipt_no = document.getElementById('receipt_no').value;
    const customer = document.getElementById('customer').value;
    const mode_of_payment = document.getElementById('mode_of_payment').value;
    const special_note = document.getElementById('special_note').value;
    const total = document.getElementById('total').value;
    const currencySelect = document.getElementById('globalCurrency');
    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    const currencyText = selectedOption.textContent.trim(); // e.g. "AED  3.685"

    const [currencyName, roe] = currencyText.split(/\s+/);
    // Collect charges table data
    const description = document.getElementById('description').value;
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
      salesperson: salesperson,
      receipt_no: receipt_no,
      customer: customer,
      mode_of_payment: mode_of_payment,
      special_note: special_note,
      total: total,
      currency_name: currencyName,
      currency_roe: roe,
      description: description,
      borderCharges: borderCharges
    };

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
          // Handle error
          console.error("Error adding data: " + this.status);
          alert("Error adding Receipt data. Please try again.");
        }
      }
    };

    // Send the request with the data
    xhr.open('POST', './backend/save_payment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));
  } catch (error) {
    console.error('Error:', error);
    alert('An error occurred while inserting the receipt');
  }
}



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

let borderChargeCount = 1;

function calculateTotal() {
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

  // Display the grand total
  document.getElementById('total').value = grandTotal.toFixed(2);
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
  calculateTotal(); // Recalculate total after removing a row
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
        <script src="./scriptedit.js" type="text/javascript"></script>
