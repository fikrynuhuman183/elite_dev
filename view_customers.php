<?php
include './layouts/header.php';
include './layouts/sidebar.php';

// Include PaymentService class
require_once 'backend/services/PaymentServices.php';

// Initialize PaymentService
$paymentService = new PaymentService($conn);
?>


      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            View Customers

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Customers</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">

              <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Customer Phone</th>
                            <th>Customer Email</th>                            
                            <th>Address</th>
                            <th>Credit</th>
                            <th>Total Due</th>
                            <th>Actions</th>
                            <th>SOA</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Fetch all customers
                        $customers_sql = "SELECT * FROM `customers`";
                        $customers_rs = $conn->query($customers_sql);

                        if ($customers_rs->num_rows > 0) {
                            // Display all customers
                            while ($row = $customers_rs->fetch_assoc()) {
                                $customer_id = $row['customer_id'];

                                // Get customer credit balance using PaymentService
                                $credit = $paymentService->getCustomerCreditBalance($customer_id);
                                
                                // Get customer invoices summary using PaymentService
                                $invoicesSummary = $paymentService->getCustomerInvoicesSummary($customer_id);
                                $due_amount = $invoicesSummary['totals']['total_due_amount'];
                    ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['location']) ?></td>
                                    <td><?= number_format($credit, 2) ?></td>
                                    <td><?= number_format($due_amount, 2) ?></td>
                                    <td>
                                        <button onclick="editCustomer('<?= $customer_id ?>')" class="btn btn-primary">View & Edit</button>
                                        <button onclick="deleteCustomer('<?= $customer_id ?>')" class="btn btn-danger">Delete</button>
                                        <a href="customer_receipt.php?customer_id=<?= $customer_id ?>" target="_blank" class="btn btn-success">Add Payment</a>
                                        <a href="customer_payment_history.php?customer_id=<?= $customer_id ?>" target="_blank" class="btn btn-info">View Customer History</a>
                                    </td>
                                    <td>
                                        <a href="customer_soa.php?customer_id=<?= $customer_id ?>" class="btn btn-primary btn-sm">View SOA</a>
                                    </td>

                                </tr>
                    <?php
                            }
                        }
                    ?>
                    </tbody>




                </table>

                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <div class="modal fade" id="modal-add_customer">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Customer</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="addCustomerForm" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="customerName">Name</label>
                  <input type="text" class="form-control" id="customerName" name="name">
                </div>
                <div class="form-group">
                  <label for="customerLocation">Location</label>
                  <input type="text" class="form-control" id="customerLocation" name="location">
                </div>
                <div class="form-group">
                  <label for="customerPhone">Phone</label>
                  <input type="text" class="form-control" id="customerPhone" name="phone">
                </div>
                <div class="form-group">
                  <label for="customerPhone">Phone (Optional)</label>
                  <input type="text" class="form-control" id="phone_optional" name="phone_optional">
                </div>
                <div class="form-group">
                  <label for="customerEmail">Email</label>
                  <input type="email" class="form-control" id="customerEmail" name="email" required>
                </div>
                <div class="form-group">
                  <label for="customerJoinDate">Join Date</label>
                  <input type="date" class="form-control" id="customerJoinDate" name="join_date" required>
                </div>
                <div class="form-group">
                  <label for="customerExpiryDate">Expiry Date</label>
                  <input type="date" class="form-control" id="customerExpiryDate" name="expiry_date" required>
                </div>
                <div class="form-group">
                  <label for="">VAT Number</label>
                  <input type="text" class="form-control" id="vat_number" name="vat_number" required>
                </div>
                <div class="form-group">
                    <label for="customerAttachment">Attachments</label>
                    <input multiple type="file" class="form-control" id="customerAttachment" name="attachment[]">
                </div>

                <div id="existingAttachments" style="display: none; margin-top: 10px;">
                    <p>Current Attachments:</p>
                    <ul id="attachmentList"></ul> <!-- List for multiple existing attachments -->
                </div>
              </form>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="updateCustomer()" >Save changes</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="modal-add_payment">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Record Payment</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="addPaymentForm">
                <input type="hidden" id="payment_customer_id" name="customer_id">
                <div class="form-group">
                  <label for="paymentAmount">Amount</label>
                  <input type="number" step="0.01" class="form-control" id="paymentAmount" name="amount" required>
                </div>
                <div class="form-group">
                  <label for="paymentDate">Payment Date</label>
                  <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                </div>
                <div class="form-group">
                  <label for="paymentNote">Note</label>
                  <textarea class="form-control" id="paymentNote" name="note" rows="3"></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="submitPayment()">Save Payment</button>
            </div>
          </div>
        </div>
      </div>

      <footer class="main-footer">

        <strong>Designed and Developed by <a href="#">Infinite Coding</a></strong>
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
      // Function to show the payment modal
      function showPaymentModal(customerId) {
        // Set the current date as default
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('paymentDate').value = today;
        
        // Set the customer ID in the hidden field
        document.getElementById('payment_customer_id').value = customerId;
        
        // Reset the form and show modal
        document.getElementById('paymentAmount').value = '';
        document.getElementById('paymentNote').value = '';
        $('#modal-add_payment').modal('show');
      }
      function submitPayment() {
        const formData = {
            customer_id: $('#payment_customer_id').val(),
            amount: $('#paymentAmount').val(),
            payment_date: $('#paymentDate').val() || new Date().toISOString().slice(0, 10),
            note: $('#paymentNote').val() || ''
        };

        $.post('save_payment.php', formData, function(response) {
            if (response.success) {
                alert('Payment saved!');
                $('#modal-add_payment').modal('hide');
                location.reload(); // Refresh the page
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function() {
            alert('Server error occurred');
        });
    }



      $(function () {
        $("#example1").dataTable();
        $('#example2').dataTable({
          "bPaginate": true,
          "bLengthChange": false,
          "bFilter": false,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false
        });
      });

      function updateCustomer() {
          var form = document.getElementById('addCustomerForm');
          var formData = new FormData(form);

          // Append the customer ID to the form data, which you can set dynamically when opening the modal
          var customer_id = $('#addCustomerForm').attr('data-id');
          formData.append('customer_id', customer_id);

          // Send data to the backend using AJAX
          var xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function () {
              if (this.readyState == 4) {
                  if (this.status == 200) {
                      // Success, clear the form, close the modal, and show a success message
                      form.reset();
                      $('#modal-add_customer').modal('hide');
                      alert('Customer updated successfully.');
                      fetchCustomers();  // You can refresh the customer list on the page
                  } else {
                      // Handle error
                      console.error("Error updating customer: " + this.status);
                      alert("Error updating customer. Please try again.");
                  }
              }
          };

          // Set up the request
          xhr.open('POST', './backend/update_customer.php', true);

          // Send the form data
          xhr.send(formData);
      }

      function deleteCustomer(customer_id) {
        if (confirm("Are you sure you want to delete this customer details?")) {
            // AJAX request to delete_product.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "./backend/delete_customer.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Reload the spage after deletion
                    window.location.reload();
                }
            };
            xhr.send("product_code=" + customer_id);
        }
    }


    function editCustomer(customer_id) {
        // Make an AJAX request to get the customer details
        $.ajax({
            url: './backend/get_customer.php',  // PHP script to fetch customer data
            type: 'POST',
            data: { id: customer_id },
            success: function(response) {
                var customer = JSON.parse(response);

                // Fill the modal fields with customer data
                $('#customerName').val(customer.name);
                $('#customerLocation').val(customer.location);
                $('#customerPhone').val(customer.phone);
                $('#phone_optional').val(customer.phone_optional);
                $('#customerEmail').val(customer.email);
                $('#customerJoinDate').val(customer.join_date);
                $('#customerExpiryDate').val(customer.expiry_date);
                $('#vat_number').val(customer.vat_number);

                // Handle existing attachments
                if (customer.attachments && customer.attachments.length > 0) {
                    $('#existingAttachments').show(); // Show the attachments section
                    var attachmentList = $('#attachmentList');
                    attachmentList.empty(); // Clear any previous list

                    // Add each attachment as a link
                    customer.attachments.forEach(function(attachment) {
                        var listItem = $('<li></li>').append(
                            $('<a></a>').attr('href', attachment.url).attr('target', '_blank').text(attachment.name)
                        );
                        attachmentList.append(listItem);
                    });
                } else {
                    $('#existingAttachments').hide(); // Hide the section if no attachments
                }

                // Set the form's action to update the customer
                $('#addCustomerForm').attr('action', 'update_customer.php');
                $('#addCustomerForm').attr('data-id', customer_id);

                // Change the modal title to 'Edit Customer'
                $('.modal-title').text('Edit Customer');

                // Open the modal
                $('#modal-add_customer').modal('show');
            },
            error: function() {
                alert('Error fetching customer details');
            }
        });
    }


    </script>



  </body>
</html>
