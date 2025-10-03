<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php';
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
            Add Profile

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Profiles</a></li>
            <!-- <li class="active">Add Profile</li> -->
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-md-6">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Add new Profile</h3>
                </div>
                <?php
                if (isset($_SESSION['add_profile'])) {

                    $message = $_SESSION['add_profile'];
                    echo $message;

                    unset($_SESSION['add_profile']);
                }
                 ?>
                <form action="./backend/add_profile.php" method="post">
                  <div class="box-body">

                    <div class="form-group">
                      <label for="item_name">Profile Name</label>
                      <input required type="text" name="profile_name" class="form-control" id="profile_name" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Profile Password</label>
                      <input required type="text" name="profile_pass" class="form-control" id="profile_pass" placeholder="Enter Password">
                    </div>


                  </div>

                  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Add Profile</button>
                  </div>
                </form>
              </div>


            </div>
            <!-- right column -->
            <!-- right column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <?php
                if (isset($_SESSION['update_profile'])) {
                    $message = $_SESSION['update_profile'];
                    echo $message;
                    unset($_SESSION['update_profile']);
                }
                ?>
                <?php
                // Fetch product details if ID is provided
                if (isset($_REQUEST['profile_id']) && !empty($_REQUEST['profile_id'])) {
                    $profile_id = $_REQUEST['profile_id'];
                    $sql = "SELECT * FROM tbl_users WHERE u_id='$profile_id'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        // Display fetched product details for editing
                        ?>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Edit Profile</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->

                    <form action="./backend/update_profile.php" method="post">
                        <div class="box-body">


                                    <div class="form-group">
                                      <label for="item_name">Profile Name</label>
                                      <input required type="text" name="profile_name" value="<?= $row['u_name'] ?>" class="form-control" id="profile_name" >
                                      <input required type="hidden" name="u_id" class="form-control" id="u_id" value="<?php echo $row['u_id']; ?>"">
                                    </div>
                                    <div class="form-group">
                                      <label for="item_name">Profile Password</label>
                                      <input required type="text" name="profile_pass" value="<?= $row['u_pass'] ?>" class="form-control" id="profile_pass" >
                                    </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div><!-- /.box -->
                <?php
                }
            }
            ?>
            </div>


          </div>   <!-- /.row -->
        </section><!-- /.content -->
      </div>
    </div><!-- /.content-wrapper -->
    <script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<?php include './layouts/footer.php'; ?>
<script type="text/javascript">

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

</script>
