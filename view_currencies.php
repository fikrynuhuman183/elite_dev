<?php
include './layouts/header.php';
include './layouts/sidebar.php';
?>


      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            View Currencies

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Currencies</a></li>
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
                              <th>Currency</th>
                              <th>ROE</th>
                              <th>Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                          $sql = "SELECT * FROM `currencies`";
                          $rs = $conn->query($sql);
                          if ($rs->num_rows > 0) {
                              while ($row = $rs->fetch_assoc()) {
                          ?>
                          <tr>
                              <td><?= $row['currency'] ?></td>
                              <td><input class="form-control" type="text" name="roe_<?= $row['id'] ?>" id="roe_<?= $row['id'] ?>" value="<?= $row['roe'] ?>"></td>
                              <td>
                                  <button onclick="updateCurrency('<?= $row['id'] ?>')" class="btn btn-success">Update</button>
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

      function updateCurrency(id) {
          var roe = document.getElementById('roe_' + id).value;

          var xhr = new XMLHttpRequest();
          xhr.open("POST", "./backend/update_currency.php", true);
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

          xhr.onreadystatechange = function () {
              if (xhr.readyState === 4 && xhr.status === 200) {
                  alert("Currency updated successfully!");
                  // Optionally, refresh the table or update the row to reflect changes
              }
          };

          xhr.send("id=" + id + "&roe=" + roe);
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
    </script>



  </body>
</html>
