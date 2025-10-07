<?php
include './layouts/header.php';
include './layouts/sidebar.php';
?>

<?php
// Handle the date range inputs
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Create the SQL query with date range filtering
$sql = "SELECT * FROM shipments ORDER BY id DESC";

if ($startDate && $endDate) {
    $sql .= " WHERE payment_date BETWEEN '$startDate' AND '$endDate'";
}



$rs = $conn->query($sql);

// Calculate total sales and total profit
$totalSales = 0;
$totalProfit = 0;
?>


      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            View Invoices

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Invoices</a></li>
            <li class="active">View Invoice Payment Details</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">

              <div class="box-body">

                    <form method="post" action="">
                      <div class="row">
                        <div class="col-md-4">
                          <label for="start_date">Start Date:</label>
                          <input class="form-control" type="date" id="start_date" name="start_date" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-4">
                          <label for="end_date">End Date:</label>
                          <input class="form-control" type="date" id="end_date" name="end_date" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-4">
                          <label for="end_date">&nbsp;</label>
                          <button class="btn-primary form-control" type="submit">Filter</button>
                        </div>

                      </div>

                    </form>
                    <br><br>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php
                          $invoice_id = $_GET['invoice_id'] ?? null;

                          if ($invoice_id) {
                              $sql = "SELECT * FROM payment_receipts WHERE invoice_number = ?";
                              $stmt = $conn->prepare($sql);
                              $stmt->bind_param("s", $invoice_id);
                              $stmt->execute();
                              $result = $stmt->get_result();

                              if ($result->num_rows > 0) {
                                  while ($payment = $result->fetch_assoc()) {
                                      $amount = $payment['payment_amount'];
                                      $type = $amount < 0 ? 'Credit Deduction' : 'Payment';
                                      $display_amount = number_format(abs($amount), 2);
                                      $date = htmlspecialchars($payment['payment_date']);
                                      $note = htmlspecialchars($payment['note'] ?? '');
                          ?>
                                      <tr>
                                          <td><?= $display_amount ?></td>
                                          <td><?= $date ?></td>
                                          <td><?= $type ?></td>
                                          <td><?= $note ?></td>
                                          <td>
                                              <!-- Optional actions -->
                                              <?php if ($u_id == 1): ?>
                                                  <button class="btn btn-danger" onclick="deleteReceipt('<?= $payment['id'] ?>')">Delete</button>
                                              <?php endif; ?>
                                              <button class="btn btn-info" onclick="viewInvoiceById('<?= $payment['receipt_no'] ?>')">View Invoice</button>
                                              <button class="btn btn-secondary" onclick="printReceiptById('<?= $payment['receipt_no'] ?>')">Print</button>

                                          </td>
                                      </tr>
                          <?php
                                  }
                              }
                          }
                          ?>
                        </tbody>

                    </table>

                  <table id="creditNoteTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Credit Note Number</th>
                            <th>Charge Details</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $invoice_id = $_GET['invoice_id'] ?? null;

                        if ($invoice_id) {
                            // Step 1: Get the shipment_id from the shipments table using the invoice number
                            $shipment_stmt = $conn->prepare("SELECT shipment_id FROM shipments WHERE invoice_number = ?");
                            $shipment_stmt->bind_param("s", $invoice_id);
                            $shipment_stmt->execute();
                            $shipment_result = $shipment_stmt->get_result();

                            if ($shipment_result->num_rows > 0) {
                                $shipment_row = $shipment_result->fetch_assoc();
                                $shipment_id = $shipment_row['shipment_id'];

                                // Step 2: Get credit notes for the shipment_id
                                $credit_stmt = $conn->prepare("SELECT id,credit_note_number, charge_details, amount FROM credit_notes WHERE shipment_id = ?");
                                $credit_stmt->bind_param("s", $shipment_id);
                                $credit_stmt->execute();
                                $credit_result = $credit_stmt->get_result();

                                if ($credit_result->num_rows > 0) {
                                    while ($credit = $credit_result->fetch_assoc()) {
                                      $id = $credit['id'];
                                        $credit_note_number = htmlspecialchars($credit['credit_note_number']);
                                        $charge_details = htmlspecialchars($credit['charge_details']);
                                        $amount = number_format($credit['amount'], 2);
                                        ?>
                                        <tr>
                                            <td><?= $credit_note_number ?></td>
                                            <td><?= $charge_details ?></td>
                                            <td><?= $amount ?></td>
                                            <td>
                                            <?php if ($u_id == 1): ?>
                                              <button class="btn btn-danger btn-sm" onclick="deleteCreditNote('<?= $id ?>')">Delete</button>
                                              <?php endif; ?>
                                          </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>

                    <div class="box-footer">

                  </div>

                </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      
      <!-- Modal for viewing invoice -->
      <div class="modal fade" id="viewInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="viewInvoiceModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 95%; max-width: 1400px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <h4 class="modal-title" id="viewInvoiceModalLabel">Invoice Preview</h4>
            </div>
            <div class="modal-body" style="padding: 0; background:#fff;">
              <iframe id="viewInvoiceFrame" style="width: 100%; height: 75vh; border: none;">
              </iframe>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="printCurrentInvoice()">Print</button>
            </div>
          </div>
        </div>
      </div>
      
      <iframe id="printFrame" style="display:none;"></iframe>

      <footer class="main-footer">
        <strong>Designed and Developed by <a href="#">Infinite Coding</a> & Zeeshutterz</strong>
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
    <!-- page script -->
    <script type="text/javascript">
      let currentInvoiceData = null;
      
      function viewInvoiceById(receiptId) {
        const viewFrame = document.getElementById('viewInvoiceFrame');
        
        // Show modal
        $('#viewInvoiceModal').modal('show');
        
        // Fetch invoice data
        fetch('./backend/print_receipt.php?id=' + encodeURIComponent(receiptId))
          .then(res => res.json())
          .then(data => {
            currentInvoiceData = data;
            
            // Remove background images from the HTML content
            let cleanHtml = data.html_content;
            cleanHtml = cleanHtml.replace(/background-image\s*:\s*url\([^)]*\)\s*;?/gi, '');
            cleanHtml = cleanHtml.replace(/background\s*:\s*url\([^)]*\)[^;]*;?/gi, '');
            
            // Write content to iframe
            viewFrame.contentWindow.document.open();
            viewFrame.contentWindow.document.write(cleanHtml);
            viewFrame.contentWindow.document.close();
          })
          .catch(err => {
            alert('Failed to load invoice: ' + err.message);
            $('#viewInvoiceModal').modal('hide');
          });
      }
      
      function printCurrentInvoice() {
        if (!currentInvoiceData) {
          alert('No invoice data available to print');
          return;
        }
        
        const printFrame = document.getElementById('printFrame');
        const originalTitle = document.title;
        
        document.title = currentInvoiceData.receipt_id;
        
        var img = new Image();
        img.src = "reciept.jpg";
        
        img.onload = function () {
          printFrame.src = 'about:blank';
          printFrame.contentWindow.document.open();
          printFrame.contentWindow.document.write(currentInvoiceData.html_content);
          printFrame.contentWindow.document.close();
          printFrame.contentWindow.focus();
          printFrame.contentWindow.print();
          document.title = originalTitle;
        };
      }
      
      function printReceiptById(receiptId) {
        fetch('./backend/print_receipt.php?id=' + encodeURIComponent(receiptId))
          .then(res => res.json()) // Parse the response as JSON
          .then(data => {
            const printFrame = document.getElementById('printFrame');
            const originalTitle = document.title;

            // Set the document title to the receipt_no
            document.title = data.receipt_id;

            var img = new Image();
            img.src = "reciept.jpg";  // Replace with the correct path if needed

            // Wait for the image to load before proceeding with printing
            img.onload = function () {
              // Once the image is loaded, inject content into the iframe
              printFrame.src = 'about:blank';
              printFrame.contentWindow.document.open();
              printFrame.contentWindow.document.write(data.html_content);
              printFrame.contentWindow.document.close();
              printFrame.contentWindow.focus();
              printFrame.contentWindow.print();
              document.title = originalTitle; // Reset the title after printing
              
            };
          })
          .catch(err => alert('Failed to print: ' + err));
      }

      function deleteCreditNote(id) {
        if (confirm("Are you sure you want to delete this credit note?")) {
            fetch("./backend/delete_credit_note.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + encodeURIComponent(id)
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload(); // Refresh page to reflect changes
            })
            .catch(error => {
                alert("An error occurred: " + error);
            });
        }
    }
    function updateStatus(shipmentId, status) {
        fetch('./backend/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ shipment_id: shipmentId, status: status }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // alert('Status updated successfully.');
                location.reload();
            } else {
                alert('Failed to update status: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
      $(function () {
        $("#example1").DataTable({
          "columnDefs": [
            { "orderable": false, "targets": 4 } // Disable sorting on Actions column
          ],
          "order": [[1, "desc"]], // Sort by Date (column index 1) descending by default
          "language": {
            "emptyTable": "No Payment History Found",
            "zeroRecords": "No matching records found"
          }
        });
        $('#example2').dataTable({
          "pageLength": 50,
          "bPaginate": true,
          "bLengthChange": false,
          "bFilter": false,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false
        });
      });
      $(function () {
        $("#creditNoteTable").DataTable({
          "pageLength": 50,
          "columnDefs": [
            { "orderable": false, "targets": 3 } // Disable sorting on Actions column
          ],
          "order": [[0, "desc"]], // Sort by Credit Note Number descending by default
          "language": {
            "emptyTable": "No Credit Notes Found",
            "zeroRecords": "No matching records found"
          }
        });
        $('#example2').dataTable({
          "pageLength": 50,
          "bPaginate": true,
          "bLengthChange": false,
          "bFilter": false,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false
        });
      });


      function deleteReceipt(invoice_id) {
          if (confirm("Are you sure you want to delete this invoice?")) {
              // AJAX request to delete_invoice.php
              var xhr = new XMLHttpRequest();
              xhr.open("POST", "./backend/delete_receipt.php", true);
              xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
              xhr.onreadystatechange = function() {
                  if (xhr.readyState == 4 && xhr.status == 200) {
                      // Reload the page after deletion
                      window.location.reload();
                  }
              };
              xhr.send("receipt_id=" + invoice_id);
          }
      }

    </script>



  </body>
</html>
