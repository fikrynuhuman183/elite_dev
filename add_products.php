<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>




      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Add Product

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Products</a></li>
            <li class="active">Add Product</li>
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
                  <h3 class="box-title">Add new product</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <?php
                if (isset($_SESSION['add_product'])) {

                    $message = $_SESSION['add_product'];
                    echo $message;

                    unset($_SESSION['add_product']);
                }
                 ?>
                <form action="./backend/add_product.php" method="post">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="item_id">Item ID</label>
                      <input required type="text" name="item_id" class="form-control" id="item_id" placeholder="Enter Item ID">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Item Name</label>
                      <input required type="text" name="item_name" class="form-control" id="item_name" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Quantity</label>
                      <input required type="text" name="quantity" class="form-control" id="quantity" placeholder="Quantity">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Cost</label>
                      <input required type="text" name="cost" class="form-control" id="cost" placeholder="Cost">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Sellling Price</label>
                      <input required type="text" name="selling_price" class="form-control" id="selling_price" placeholder="Selling Price">
                    </div>


                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                  </div>
                </form>
              </div><!-- /.box -->



              <!-- Input addon -->


            </div><!--/.col (left) -->
            <!-- right column -->
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Add Quantity</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <?php
                if (isset($_SESSION['update_product'])) {

                    $message = $_SESSION['update_product'];
                    echo $message;

                    unset($_SESSION['update_product']);
                }
                 ?>
                <form action="./backend/update_quantity.php" method="post">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="item_id">Item ID</label>
                      <input required type="text" name="item_id" class="form-control" id="item_id" placeholder="Enter Item ID">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Quantity to add</label>
                      <input required type="text" name="quantity" class="form-control" id="quantity" placeholder="Quantity">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Cost</label>
                      <input required type="text" name="cost" class="form-control" id="cost" placeholder="Cost">
                    </div>
                    <div class="form-group">
                      <label for="item_name">Sellling Price</label>
                      <input required type="text" name="selling_price" class="form-control" id="selling_price" placeholder="Selling Price">
                    </div>


                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update Quantity</button>
                  </div>
                </form>
              </div><!-- /.box -->



              <!-- Input addon -->


            </div>

          </div>   <!-- /.row -->
        </section><!-- /.content -->
      </div>
    </div><!-- /.content-wrapper -->
<?php include './layouts/footer.php'; ?>
