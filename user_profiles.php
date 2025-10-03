<?php
include './layouts/header.php';
include './layouts/sidebar.php';

if($u_id!=1){


      header('location:../login.php');
      session_destroy();
      exit();
}
?>


      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            View Profiles

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Profiles</a></li>
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
                            <th>User Name</th>
                            <th>Password</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM `tbl_users`";
                        $rs= $conn->query($sql);
                        if($rs->num_rows > 0){
                            while($row = $rs->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?= $row['u_name'] ?></td>
                            <td><?= $row['u_pass'] ?></td>
                            <td>
                                <a href="./add_profiles.php?profile_id=<?= $row['u_id'] ?>" class="btn btn-primary">Edit</a>
                                <button onclick="deleteProfile('<?= $row['u_id'] ?>')" class="btn btn-danger">Delete</button>
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


      function deleteProfile(productCode) {
    if (confirm("Are you sure you want to delete this product?")) {
        // AJAX request to delete_product.php
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "./backend/delete_profile.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Reload the page after deletion
                window.location.reload();
            }
        };
        xhr.send("product_code=" + productCode);
    }
}
    </script>



  </body>
</html>
