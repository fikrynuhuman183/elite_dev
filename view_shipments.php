<?php
include './layouts/header.php';
include './layouts/sidebar.php';
?>

<?php
// Handle the date range inputs
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$statusFilter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

// Create the SQL query with date range and status filtering
$sql = "SELECT s.*, c.name AS customer_name FROM shipments s LEFT JOIN customers c ON s.customer_id = c.customer_id ORDER BY s.invoice_date DESC";

$conditions = [];

// Only add date condition if both dates are provided and not empty
if (!empty($startDate) && !empty($endDate)) {
    // Ensure dates are in YYYY-MM-DD format (MySQL compatible)
    $startDate = date('Y-m-d', strtotime($startDate));
    $endDate = date('Y-m-d', strtotime($endDate));
  $conditions[] = "s.invoice_date BETWEEN '$startDate' AND '$endDate'";
}

if (!empty($statusFilter) && $statusFilter !== 'all') {
  $conditions[] = "s.status = '$statusFilter'";
}

if (!empty($conditions)) {
  $sql = "SELECT s.*, c.name AS customer_name FROM shipments s LEFT JOIN customers c ON s.customer_id = c.customer_id WHERE " . implode(' AND ', $conditions) . " ORDER BY s.invoice_date DESC";
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
                        <div class="col-md-3">
                          <label for="start_date">Start Date:</label>
                          <input class="form-control" type="date" id="start_date" name="start_date" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3">
                          <label for="end_date">End Date:</label>
                          <input class="form-control" type="date" id="end_date" name="end_date" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-3">
                          <label for="status_filter">Status:</label>
                          <select class="form-control" id="status_filter" name="status_filter">
                            <option value="all" <?= $statusFilter == 'all' || $statusFilter == '' ? 'selected' : '' ?>>All Status</option>
                            <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="generated" <?= $statusFilter == 'generated' ? 'selected' : '' ?>>Generated</option>
                            <option value="submitted" <?= $statusFilter == 'submitted' ? 'selected' : '' ?>>Submitted</option>
                            <option value="paid" <?= $statusFilter == 'paid' ? 'selected' : '' ?>>Paid</option>
                          </select>
                        </div>
                        <div class="col-md-3">
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
                              <th>Customer</th>
                              <th>Date</th>
                              <th>Status</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                          if ($rs->num_rows > 0) {
                              while ($row = $rs->fetch_assoc()) {
                          ?>
                          <tr id="row-<?= $row['shipment_id'] ?>">
                              <td><?= $row['shipment_number'] ?></td>
                              <td><?= $row['invoice_number'] ?></td>
                              <td><?= isset($row['customer_name']) && $row['customer_name'] !== null ? htmlspecialchars($row['customer_name']) : 'N/A' ?></td>
                              <td><?= $row['invoice_date'] ?></td>
                              <td>
                                <?php
                                $status_class = match($row['status']) {
                                    'paid' => 'btn-success',
                                    'pending' => 'btn-danger',
                                    'submitted' => 'btn-primary',
                                    'generated' => 'btn-info',
                                    default => 'btn-warning',
                                };
                                ?>

                                <?php
                                if (!empty($row['status'])){ ?>
                                    <button class="btn <?= $status_class ?>" >
                                         <?= ucfirst($row['status']) ?>
                                     </button>
                                <?php }else{ ?>
                                    <button class="btn btn-danger" >
                                         Pending
                                     </button>
                                <?php } ?>
                                 
                              </td>
                              <td>
                                  <a href="./edit_shipment.php?invoice_id=<?= $row['shipment_id'] ?>" target="_blank" class="btn btn-primary">
                                      <?= ($row['status'] == 'pending' || $row['status'] == 'generated') ? 'View & Edit' : 'View & Print' ?>
                                  </a>

                                  
                                      <?php if ($u_id == 1) { ?>
                                          <button onclick="deleteInvoice('<?= $row['shipment_id'] ?>')" class="btn btn-danger">Delete</button>
                                          <?php if ($row['status'] != 'paid') { ?>
                                            <select onchange="confirmStatusChange('<?= $row['shipment_id'] ?>', this.value)" class="form-control">
                                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="generated" <?= $row['status'] == 'generated' ? 'selected' : '' ?>>Generated</option>
                                                <option value="submitted" <?= $row['status'] == 'submitted' ? 'selected' : '' ?>>Submitted</option>                                              
                                            </select>
                                          <?php } ?>

                                      <?php }else{ ?>

                                      <?php if ($row['status'] == 'pending' || $row['status'] == 'generated') { ?>
                                          <select onchange="confirmStatusChange('<?= $row['shipment_id'] ?>', this.value)" class="form-control">
                                              <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                              <option value="generated" <?= $row['status'] == 'generated' ? 'selected' : '' ?>>Generated</option>
                                              <option value="submitted" <?= $row['status'] == 'submitted' ? 'selected' : '' ?>>Submitted</option>
                                          </select>
                                      <?php }} ?>
                                 
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

      // Updating status change confirmation
      function confirmStatusChange(shipmentId, status) {
          if (status === 'submitted') {
              if (confirm('Are you sure you want to change the status to Submitted? this action cannot be changed or edited again')) {
                  updateStatus(shipmentId, status);
              } else {
                  // Reset the dropdown to the previous value
                  location.reload();
              }
          } else {
              updateStatus(shipmentId, status);
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
        $("#example1").dataTable({
          "bPaginate": true,
          "bLengthChange": true,
          "bFilter": false,
          "aaSorting": [],
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false,
          "iDisplayLength": 15,
          "aLengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "All"]],
          
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
