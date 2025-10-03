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
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Products</h3>
                  <div class="box-tools">
                    <div class="input-group">
                      <input type="text" name="table_search" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                      <div class="input-group-btn">
                        <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">

                    <tr>
                      <th>Product Code</th>
                      <th>Product Name</th>
                      <th>Quantity</th>
                      <th>Cost</th>
                      <th>Selling Price</th>
                      <th>Actions</th>
                    </tr>
                    <?php
  					          $sql = "SELECT * FROM `tbl_products`";
  					          $rs= $conn->query($sql);
  					          if($rs->num_rows > 0){
  					            while($row = $rs->fetch_assoc()){
  					        ?>

                    <tr>
                      <td><?= $row['product_code'] ?></td>
                      <td><?= $row['product_name'] ?></td>
                      <td><?= $row['product_quantity'] ?></td>
                      <td><?= $row['product_cost'] ?></td>
                      <td><?= $row['product_selling_price'] ?></td>
                    </tr>

                    <?php
                      }}
                     ?>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
          </div>  <!-- /.row -->
        </section><!-- /.content -->
      </div>
    </div><!-- /.content-wrapper -->
<?php include './layouts/footer.php'; ?>
