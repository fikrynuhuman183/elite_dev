<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>




      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Billing Section

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Billing</a></li>
            <li class="active">New Bill</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"></h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <?php
                if (isset($_SESSION['add_product'])) {

                    $message = $_SESSION['add_product'];
                    echo $message;

                    unset($_SESSION['add_product']);
                }
                 ?>
                <form id="billingForm" >
                  <div class="box-body">
                    <div class="form-group">
                      <label for="itemCode">Item Code</label>
                      <input type="text" class="form-control" id="itemCode" name="itemCode" onblur="fetchProductData()">
                    </div>
                    <div class="form-group">
                      <label for="itemName">Item Name</label>
                      <input type="text" id="itemName" class="form-control" name="itemName" readonly>
                    </div>
                    <div class="form-group">
                      <label for="quantity">Quantity</label>
                      <input type="number" id="quantity" class="form-control" name="quantity">

                    </div>
                    <div class="form-group">
                      <label for="sellingPrice">Selling Price</label>
                      <input type="number" id="sellingPrice" class="form-control" name="sellingPrice">
                    </div>
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Add to Bill</button>
                  </div>
                </form>
              </div><!-- /.box -->
              <!-- Input addon -->


            </div><!--/.col (left) -->
            <div class="col-md-6">
              <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                      <thead>
                          <tr>
                              <th>Product Code</th>
                              <th>Product Name</th>
                              <th>Quantity</th>
                              <th>Selling Price</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody id="billItems"> <!-- Updated: tbody added with an id for dynamically adding rows -->
                          <!-- Existing or initially loaded rows can go here if needed -->
                      </tbody>
                  </table>
              </div>
            </div>

          </div>   <!-- /.row -->
          <button onclick="printInvoice()" class="btn btn-default"><i class="fa fa-print"></i> Print Invoice</button>
        </section><!-- /.content -->
      </div>
    </div><!-- /.content-wrapper -->

    <script>
        function printInvoice() {

            var billItems = [];
            var tableRows = document.getElementById('billItems').getElementsByTagName('tr');

            for (var i = 0; i < tableRows.length; i++) {
                var columns = tableRows[i].getElementsByTagName('td');
                if (columns.length === 5) { // Ensure it's a valid row with 5 columns
                    var item = {
                        product_code: columns[0].innerText,
                        quantity: columns[2].innerText,
                        price: columns[3].innerText,
                    };
                    billItems.push(item);
                }
            }

            // Send data to the backend using AJAX
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        // Success, redirect to the invoice page
                        var invoiceId = JSON.parse(this.responseText).invoice_id;
                        window.location.href = 'invoice-print.php?invoice_id=' + invoiceId;
                    } else {
                        // Handle error
                        console.error("Error generating invoice: " + this.status);
                        // Display an alert or handle the error in another way
                        alert("Error generating invoice. Please try again.");
                    }
                }
            };

            // Set up the request
            xhr.open('POST', './backend/generate_invoice.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            // Convert the data to JSON and send the request
            xhr.send(JSON.stringify({ items: billItems }));
        }

        function fetchProductData() {
            var itemCode = document.getElementById('itemCode').value;

            // Use AJAX to fetch product data from the server (replace 'fetch_product.php' with your actual server-side script)
            // Assuming you have a server-side script that takes the item code and returns JSON data with item details
            // Update the URL and handling based on your actual implementation
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var data = JSON.parse(this.responseText);

                    // Update other fields with retrieved data
                    document.getElementById('itemName').value = data.itemName;
                    document.getElementById('sellingPrice').value = data.sellingPrice;
                }
            };
            xhr.open("GET", "fetch_product.php?itemCode=" + itemCode, true);
            xhr.send();
        }

        function addProduct() {
            var itemName = document.getElementById('itemName').value;
            var quantity = document.getElementById('quantity').value;
            var sellingPrice = document.getElementById('sellingPrice').value;

            // Validation: Check if itemCode is not empty (you can add more validation as needed)
            var itemCode = document.getElementById('itemCode').value;
            if (itemCode.trim() === "") {
                alert("Please enter Item Code");
                return;
            }

            // Create a new row for the added product in the billItems tbody
            var newRow = document.createElement('tr');
            newRow.innerHTML = '<td>' + itemCode + '</td>' +
                               '<td>' + itemName + '</td>' +
                               '<td>' + quantity + '</td>' +
                               '<td>' + sellingPrice + '</td>' +
                               '<td><button type="button" class="btn btn-block btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>';

            // Append the new row to the billItems tbody
            document.getElementById('billItems').appendChild(newRow);

            // Clear input fields for the next entry
            document.getElementById('itemCode').value = "";
            document.getElementById('itemName').value = "";
            document.getElementById('quantity').value = "";
            document.getElementById('sellingPrice').value = "";
        }

        function removeRow(button) {
            // Traverse up the DOM to the closest 'tr' element and remove it
            var row = button.closest('tr');
            row.remove();
        }

    </script>

<?php include './layouts/footer.php'; ?>
