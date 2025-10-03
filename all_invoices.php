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
    $sql .= " WHERE job_date BETWEEN '$startDate' AND '$endDate'";
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
            <li class="active">View Invoices</li>
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
                                <th>ID</th>
                                <th>Invoice Number</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($rs->num_rows > 0) {
                                while ($row = $rs->fetch_assoc()) {
                                  $invoice_number = $row['invoice_number']; // Assuming $row['invoice_number'] is 'EXP-SEA-IN24001'
                                  preg_match('/IN\d*(\d+)/', $invoice_number, $matches);
                                  $number = isset($matches[1]) ? (int)$matches[1] : null;
                            ?>
                            <tr>
                                <td><?= $number ?></td>
                                <td><?= $row['invoice_number'] ?></td>
                                <td><?= $row['invoice_date'] ?></td>
                                <td>
                                  <a href="./print_shipment.php?invoice_id=<?= $row['shipment_id'] ?>" target="_blank" class="btn btn-primary">View</a>
                                  <a href="./edit_shipment.php?invoice_id=<?= $row['shipment_id'] ?>" class="btn btn-primary">Edit</a>
                                  <button onclick="deleteInvoice('<?= $row['shipment_id'] ?>')" class="btn btn-danger">Delete</button>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='6'>No Purchase History</td></tr>";
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


      function deleteInvoice(invoice_id) {
          if (confirm("Are you sure you want to delete this invoice?")) {
              // AJAX request to delete_invoice.php
              var xhr = new XMLHttpRequest();
              xhr.open("POST", "./backend/delete_invoice.php", true);
              xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
              xhr.onreadystatechange = function() {
                  if (xhr.readyState == 4 && xhr.status == 200) {
                      // Reload the page after deletion
                      window.location.reload();
                  }
              };
              xhr.send("invoice_id=" + invoice_id);
          }
      }

    </script>



  </body>
</html>
