<?php include './layouts/header.php'; ?>
<?php include './layouts/sidebar.php'; ?>

<style media="screen">
/* print.css */
@media print {
  body * {
      visibility: hidden;
  }
  #printableArea, #printableArea * {
      visibility: visible;
  }
  #printableArea {
      position: absolute;
      left: 0;
      top: 0;
  }
}
</style>


      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Shipments

          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Shipments</a></li>
            <li class="active">Add New Shipment</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content printableArea">
          <div class="row">
            <!-- left column -->
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Shipment</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <?php
                if (isset($_SESSION['add_product'])) {

                    $message = $_SESSION['add_product'];
                    echo $message;

                    unset($_SESSION['add_product']);
                }
                 ?>
                  <div class="box-body">
                    <div class="form-group">
                      <!-- <label for="item_id">Shipment ID</label> -->
                      <input required type="text" name="shipment_id" class="form-control" id="shipment_id" placeholder="Enter Shipment ID">
                    </div>
                  </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div>
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Handled By</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <?php
                if (isset($_SESSION['add_product'])) {

                    $message = $_SESSION['add_product'];
                    echo $message;

                    unset($_SESSION['add_product']);
                }
                 ?>
                  <div class="box-body">
                    <div class="form-group">
                      <select id="total_discount_type" class="form-control">
                        <option value="percentage">Test1</option>
                        <option value="fixed" selected>Test2</option>
                      </select>

                    </div>
                  </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div>
            <!-- right column -->
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Customer</h3>
                </div>
                  <div class="  box-body">
                    <div class="row">
                      <div class=" form-group col-md-10 ">
                        <select id="customer_id" class="form-control">
                        </select>

                      </div>
                      <div class=" form-group col-md-2 ">
                        <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_customer">+</button>
                      </div>
                      <div class=" form-group col-md-6">
                        <!-- <label for="item_id">Shipment ID</label> -->
                        <input type="text" name="consignee" class="form-control" id="consignee" placeholder="Enter consignee(Optional)">
                      </div>
                    </div>

                  </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div>
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Supplier and Driver</h3>
                </div>
                  <div class="  box-body">
                    <div class="row">
                      <div class="row">

                      </div>
                      <div class=" form-group col-md-10 ">
                        <label for="total_discount_type">Supplier</label>
                        <select id="supplier_id" class="form-control">

                        </select>

                      </div>
                      <div class=" form-group col-md-2 ">
                        <label for="total_discount_type">Add New</label>
                        <button onclick="fetchSuppliersModal()" type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_supplier">+</button>
                      </div>
                      <div class=" form-group col-md-10 ">
                        <label for="item_id">Driver</label>
                        <select id="driver_id" class="form-control">

                        </select>

                      </div>
                      <div class=" form-group col-md-2 ">
                        <label for="total_discount_type">Add New</label>
                        <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_driver">+</button>
                      </div>
                    </div>

                  </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div>

          </div>   <!-- /.row -->
          <div class="row">

            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Loading Point details</h3>
                </div>
                  <div class="  box-body">
                    <div class="row">
                      <div class="form-group col-md-12">

                          <div class="row">

                              <div class="col-md-4">
                                <p for="=">Country</p>
                                <select name="loading_country" id="loading_country" class="form-control gds-cr" country-data-region-id="loading_region" data-language="en"></select>
                              </div>
                              <div class="col-md-4">
                                <p for="">Region</p>
                                <select name="loading_region" class="form-control" id="loading_region"></select>
                              </div>
                              <div class="col-md-4">
                                <p for="">City</p>
                                <input type="text" name="unloading_city" class="form-control" id="loading_city" placeholder="Enter consignee(Optional)">
                              </div>
                          </div>
                          <div class="row">
                            <div class="form-group col-md-12">
                              <br>
                                <div class="row">
                                  <div class="col-md-4">
                                    <p for="">Street</p>
                                    <input type="text" name="loading_street" class="form-control" id="loading_street" placeholder="Enter street(Optional)">
                                  </div>
                                  <div class="col-md-4">
                                    <p for="">Port of Orogin</p>
                                    <input type="text" name="port_origin" class="form-control" id="port_origin" placeholder="Enter Port">
                                  </div>
                                    <div class="col-md-4">
                                      <p for="">Warehouse</p>
                                      <input type="text" name="warehouse" class="form-control" id="warehouse" placeholder="Enter warehouse(Optional)">
                                    </div>
                                </div><br>
                                <div class="row">

                                    <div class="col-md-6">
                                      <p for="">Estimated Delivery</p>
                                      <input type="date" name="etd_delivery" class="form-control" id="etd_delivery" >
                                    </div>
                                    <div class="col-md-6">
                                      <p for="">Estimated Arrival</p>
                                      <input type="date" name="etd_departure" class="form-control" id="etd_departure">
                                    </div>

                                </div>
                            </div>
                          </div>
                      </div>
                    </div>

                  </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div>


            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Unoading Point details</h3>
                </div>
                  <div class="  box-body">
                    <div class="row">
                      <div class="form-group col-md-12">

                          <div class="row">

                                <div class="col-md-4">
                                  <p for="=">Country</p>
                                  <select name="unloading_country" id="unloading_country" class="form-control gds-cr" country-data-region-id="unloading_region" data-language="en"></select>

                                </div>
                                <div class="col-md-4">
                                  <p for="=">Region</p>
                                  <select name="unloading_region" class="form-control" id="unloading_region"></select>
                                </div>
                                <div class="col-md-4">
                                  <p for="">City</p>
                                  <input type="text" name="unloading_city" class="form-control" id="unloading_city" placeholder="Enter City">
                                </div>
                          </div>
                          <div class="row">
                            <div class="form-group col-md-12">
                              <br>

                                <div class="row">
                                    <div class="col-md-6">
                                      <p for="">Street</p>
                                      <input type="text" name="unloading_street" class="form-control" id="unloading_street" placeholder="Enter street(Optional)">
                                    </div>
                                    <div class="col-md-6">
                                      <p for="">Port of Destination</p>
                                      <input type="text" name="port_destination" class="form-control" id="port_destination" placeholder="Enter Port">
                                    </div>

                                </div>
                            </div>
                          </div>
                      </div>
                    </div>

                  </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                  <!-- general form elements -->
                  <div class="box box-primary">
                      <div class="box-header">
                          <h3 class="box-title">Shipping Details </h3>
                      </div>
                      <div class="box-body">


                          <div class="row">
                              <div class="form-group col-md-3">
                                <label for="">Shipping Mode</label> <br>
                                <div class="row">
                                  <div class="col-md-9">
                                    <select id="shipping_mode_id" class="form-control">
                                        <option value="percentage">Air</option>
                                        <option value="fixed" selected>Land</option>
                                    </select>
                                  </div>
                                  <div class=" form-group col-md-3 ">
                                    <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_shipping_mode">+</button>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="">Carrier</label> <br>
                                  <div class="row">
                                      <div class="col-md-12">
                                        <input type="text" name="carrier" class="form-control" id="carrier" placeholder="Enter Carrier(optional)">
                                      </div>
                                  </div>

                              </div>
                              <div class="form-group col-md-3">
                                <label for="">Item Description</label> <br>
                                  <div class="row">
                                      <div class="col-md-12">
                                        <input type="text" name="item_desc" class="form-control" id="item_desc" placeholder="Enter Item Desc">
                                      </div>
                                  </div>

                              </div>
                              <div class="form-group col-md-3">
                                <label for="">Weight (kg)</label> <br>
                                  <div class="row">
                                      <div class="col-md-12">
                                        <input type="text" name="weight" class="form-control" id="weight" placeholder="Enter Weight">
                                      </div>
                                  </div>

                              </div>
                          </div>
                          <hr>
                          <div class="row">
                            <div class="box-header">
                                <h3 class="box-title">Equipment Details </h3>
                            </div>
                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-12">
                                <div id="vehicleContainer">
                                    <div class="row vehicle-row" data-index="1">
                                        <div class="form-group col-md-3">
                                            <label for="vehicle_id1">Vehicle</label>
                                            <div class="row">
                                                <div class="form-group col-md-8">
                                                    <select id="vehicle_id1" class="form-control"></select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_vehicle">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="container_id1">Container</label>
                                            <div class="row">
                                                <div class="form-group col-md-8">
                                                    <select id="container_id1" class="form-control"></select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_container">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="vehicle_num1">Equipment Number</label>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <input type="text" name="vehicle_num1" class="form-control" id="vehicle_num1" placeholder="Enter equipment number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label for="removeButton1">Remove</label>
                                            <button disabled type="button" class="btn btn-danger form-control" onclick="removeVehicleRow(this)">-</button>
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label for="addButton">Add New</label>
                                            <button type="button" class="btn btn-primary form-control" onclick="addVehicleRow()">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="vehicleContainerRows"></div>
                          <!-- Container for the added rows -->

                          <div class="row">
                            <div class="form-group col-md-3">
                              <label for="">Number of units</label> <br>
                              <div class="row">
                                <div class=" form-group col-md-12 ">
                                  <input type="text" name="units" class="form-control" id="units" placeholder="Enter Number of units">

                                </div>
                              </div>
                            </div>
                          </div>

                      </div><!-- /.box-body -->
                  </div><!-- /.box -->
              </div>
          </div>
          <div class="row">


                            <div class="col-md-12">
                                <!-- general form elements -->
                                <div class="box box-primary">
                                    <div class="box-header">
                                      <div class="row">
                                        <div class="col-md-2">

                                        <h3 class="box-title">Charges</h3><br>

                                        </div>
                                      </div>
                                      <br>
                                      <div class="row">
                                        <div class="col-md-2">
                                          <select id="currency" class="form-control">
                                            <option selected value="AED">AED</option>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                          </select>

                                        </div>

                                      </div>

                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label for="freight_charge">Freight Charge</label>
                                                        <input type="text" name="freight_charge" class="form-control" id="freight_charge" placeholder="Enter charge">
                                                        <br>
                                                        <label for="freight_tax">Freight Tax</label>
                                                        <input type="text" name="freight_tax" class="form-control" id="freight_tax" placeholder="Enter tax amount">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="inspection_charges">Inspection Charges</label>
                                                        <input type="text" name="inspection_charges" class="form-control" id="inspection_charges" placeholder="Enter charge">
                                                        <br>
                                                        <label for="inspection_tax">Inspection Tax</label>
                                                        <input type="text" name="inspection_tax" class="form-control" id="inspection_tax" placeholder="Enter tax amount">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="additional_charges">Additional Charges</label>
                                                        <input type="text" name="additional_charges" class="form-control" id="additional_charges" placeholder="Enter charge">
                                                        <br>
                                                        <label for="additional_tax">Additional Tax</label>
                                                        <input type="text" name="additional_tax" class="form-control" id="additional_tax" placeholder="Enter tax amount">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="other_charges">Other Charges</label>
                                                        <input type="text" name="other_charges" class="form-control" id="other_charges" placeholder="Enter charge">
                                                        <br>
                                                        <label for="other_tax">Other Tax</label>
                                                        <input type="text" name="other_tax" class="form-control" id="other_tax" placeholder="Enter tax amount">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="discount">Discount</label>
                                                        <input type="text" name="discount" class="form-control" id="discount" placeholder="Enter charge">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="borderChargesContainer">
                                                    <!-- Initial rows with the "+" button in the first row -->
                                                    <div class="row border-charge-row" data-index="1">
                                                        <div class="col-md-3">
                                                            <label for="borderChargeValue1">Border Charge 1</label>
                                                            <input type="text" class="form-control" id="borderChargeValue1" name="borderChargeValue1" placeholder="Enter value">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="borderChargeDescription1">Description</label>
                                                            <input type="text" class="form-control" id="borderChargeDescription1" name="borderChargeDescription1" placeholder="Enter description">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="borderChargeVAT1">VAT</label>
                                                            <input type="text" class="form-control" id="borderChargeVAT1" name="borderChargeVAT1" placeholder="Enter VAT">
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="removeButton1">Remove</label>
                                                            <button disabled type="button" class="btn btn-danger form-control" onclick="removeBorderChargeRow(this)">-</button>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="addButton">Add New</label>
                                                            <button type="button" class="btn btn-primary form-control" onclick="addBorderChargeRow()">+</button>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row border-charge-row" data-index="2">
                                                        <div class="col-md-3">
                                                            <label for="borderChargeValue2">Border Charge 2</label>
                                                            <input type="text" class="form-control" id="borderChargeValue2" name="borderChargeValue2" placeholder="Enter value">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="borderChargeDescription2">Description</label>
                                                            <input type="text" class="form-control" id="borderChargeDescription2" name="borderChargeDescription2" placeholder="Enter description">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="borderChargeVAT2">VAT</label>
                                                            <input type="text" class="form-control" id="borderChargeVAT2" name="borderChargeVAT2" placeholder="Enter VAT">
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="removeButton2">Remove</label>
                                                            <button  type="button" class="btn btn-danger form-control" onclick="removeBorderChargeRow(this)">-</button>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="total">Total:</label>
                                                <input type="text" class="form-control" id="total" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="total">&nbsp;</label> <br>
                                                <button type="button" class="btn btn-primary" onclick="calculateTotal()">Calculate Total</button>
                                            </div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-3">
                                                <label for="total">&nbsp;</label> <br>
                                                <button type="button" class="btn btn-danger form-control" onclick="printInvoice()">Print Invoice</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

              </div>


            </div>
        </section><!-- /.content -->
      </div>
    </div><!-- /.content-wrapper -->

    <!-- MODALS -->
    <div class="modal fade" id="modal-add_supplier">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Supplier</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="addSupplierForm">
              <div class="form-group">
                <label for="supplierName">Name</label>
                <input type="text" class="form-control" id="supplierName" name="name" required>
              </div>
              <div class="form-group">
                <label for="supplierLocation">Location</label>
                <input type="text" class="form-control" id="supplierLocation" name="location" required>
              </div>
              <div class="form-group">
                <label for="supplierPhone">Phone</label>
                <input type="text" class="form-control" id="supplierPhone" name="phone" required>
              </div>
              <div class="form-group">
                <label for="supplierEmail">Email</label>
                <input type="email" class="form-control" id="supplierEmail" name="email" required>
              </div>
            </form>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="addSupplier()">Save changes</button>
          </div>
        </div>
      </div>
    </div>

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
            <form id="addCustomerForm">
              <div class="form-group">
                <label for="customerName">Name</label>
                <input type="text" class="form-control" id="customerName" name="name" required>
              </div>
              <div class="form-group">
                <label for="customerLocation">Location</label>
                <input type="text" class="form-control" id="customerLocation" name="location" required>
              </div>
              <div class="form-group">
                <label for="customerPhone">Phone</label>
                <input type="text" class="form-control" id="customerPhone" name="phone" required>
              </div>
              <div class="form-group">
                <label for="customerEmail">Email</label>
                <input type="email" class="form-control" id="customerEmail" name="email" required>
              </div>
            </form>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="addCustomer()">Save changes</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modal-add_driver">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Driver</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="addDriverForm">
                <div class="form-group">
                  <label for="">Supplier</label>
                  <select id="supplier_id_modal" class="form-control">

                  </select>
                </div>
                <div class="form-group">
                  <label for="driverName">Name</label>
                  <input type="text" class="form-control" id="driverName" name="name" required>
                </div>
                <div class="form-group">
                  <label for="driverLicense">License Number</label>
                  <input type="text" class="form-control" id="driverLicense" name="license" required>
                </div>
                <div class="form-group">
                  <label for="driverPhone">Phone</label>
                  <input type="text" class="form-control" id="driverPhone" name="phone" required>
                </div>
                <div class="form-group">
                  <label for="driverEmail">Email</label>
                  <input type="email" class="form-control" id="driverEmail" name="email" required>
                </div>
              </form>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="addDriver()">Save changes</button>
            </div>
          </div>
        </div>
      </div>

    <div class="modal fade" id="modal-add_vehicle">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Vehicle</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="addVehicleForm">
                <div class="form-group">
                  <label for="driverName">Name</label>
                  <input type="text" class="form-control" id="driverName" name="name" required>
                </div>
              </form>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="addVehicle()">Save changes</button>
            </div>
          </div>
        </div>
      </div>

    <div class="modal fade" id="modal-add_container">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title">Add Container</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <form id="addContainerForm">
                          <div class="form-group">
                              <label for="containerName">Name</label>
                              <input type="text" class="form-control" id="containerName" name="name" required>
                          </div>
                      </form>
                  </div>
                  <div class="modal-footer justify-content-between">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" onclick="addContainer()">Save changes</button>
                  </div>
              </div>
          </div>
      </div>

    <div class="modal fade" id="modal-add_shipping_mode">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Shipping Mode</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addShippingModeForm">
                        <div class="form-group">
                            <label for="shippingModeName">Name</label>
                            <input type="text" class="form-control" id="shippingModeName" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addShippingMode()">Save changes</button>
                </div>
            </div>
        </div>
    </div>


    <iframe id="printFrame" style="display:none;"></iframe>


    <script type="text/javascript">

    const conversionRates = {
        USD: 3.67, // 1 USD = 3.67 AED
        EUR: 4.1   // 1 EUR = 4.1 AED
    };

    async function printInvoice() {
    try {
        // Fetch customer details
        const customerId = document.getElementById('customer_id').value;
        const customerResponse = await fetch(`backend/getCustomerDetails.php?customer_id=${customerId}`);
        const customer = await customerResponse.json();

        // Fetch driver details
        const driverId = document.getElementById('driver_id').value;
        const driverResponse = await fetch(`backend/getDriverDetails.php?driver_id=${driverId}`);
        const driver = await driverResponse.json();

        // Fetch other details from form
        const loadingDetails = {
            country: document.getElementById('loading_country').value,
            region: document.getElementById('loading_region').value,
            city: document.getElementById('loading_city').value,
            street: document.getElementById('loading_street').value,
            port_origin: document.getElementById('port_origin').value,
            warehouse: document.getElementById('warehouse').value,
            etd_delivery: document.getElementById('etd_delivery').value,
            etd_departure: document.getElementById('etd_departure').value,
        };

        const unloadingDetails = {
            country: document.getElementById('unloading_country').value,
            region: document.getElementById('unloading_region').value,
            city: document.getElementById('unloading_city').value,
            street: document.getElementById('unloading_street').value,
            port_destination: document.getElementById('port_destination').value,
        };

        const units = document.getElementById('units').value;

        // Fetch and convert charges to AED
        const currency = document.getElementById('currency').value;
        const freightCharge = parseFloat(document.getElementById('freight_charge').value);
        const freightTax = parseFloat(document.getElementById('freight_tax').value);
        const inspectionCharges = parseFloat(document.getElementById('inspection_charges').value);
        const inspectionTax = parseFloat(document.getElementById('inspection_tax').value);
        const additionalCharges = parseFloat(document.getElementById('additional_charges').value);
        const additionalTax = parseFloat(document.getElementById('additional_tax').value);
        const otherCharges = parseFloat(document.getElementById('other_charges').value);
        const otherTax = parseFloat(document.getElementById('other_tax').value);
        const discount = parseFloat(document.getElementById('discount').value);

        // Convert charges to AED
        const convertToAED = (value, currency) => {
            if (currency === 'AED') return value;
            return value * conversionRates[currency];
        };

        const charges = {
            freightCharge: `${freightCharge} (${convertToAED(freightCharge, currency)} AED)`,
            freightTax: `${freightTax} (${convertToAED(freightTax, currency)} AED)`,
            inspectionCharges: `${inspectionCharges} (${convertToAED(inspectionCharges, currency)} AED)`,
            inspectionTax: `${inspectionTax} (${convertToAED(inspectionTax, currency)} AED)`,
            additionalCharges: `${additionalCharges} (${convertToAED(additionalCharges, currency)} AED)`,
            additionalTax: `${additionalTax} (${convertToAED(additionalTax, currency)} AED)`,
            otherCharges: `${otherCharges} (${convertToAED(otherCharges, currency)} AED)`,
            otherTax: `${otherTax} (${convertToAED(otherTax, currency)} AED)`,
            discount: `${discount} (${convertToAED(discount, currency)} AED)`,
        };





        // Create invoice HTML
        const invoiceHtml = `
            <div class="invoice">
                <h2>Invoice</h2>
                <div class="customer-details">
                    <p><strong>Customer:</strong> ${customer.name}</p>
                    <p><strong>Phone:</strong> ${customer.phone}</p>
                    <p><strong>Email:</strong> ${customer.email}</p>
                    <p><strong>Address:</strong> ${customer.address}</p>
                </div>
                <div class="loading-details">
                    <h3>Loading Point Details</h3>
                    <p><strong>Country:</strong> ${loadingDetails.country}</p>
                    <p><strong>Region:</strong> ${loadingDetails.region}</p>
                    <p><strong>City:</strong> ${loadingDetails.city}</p>
                    <p><strong>Street:</strong> ${loadingDetails.street}</p>
                    <p><strong>Port of Origin:</strong> ${loadingDetails.port_origin}</p>
                    <p><strong>Warehouse:</strong> ${loadingDetails.warehouse}</p>
                    <p><strong>Estimated Delivery:</strong> ${loadingDetails.etd_delivery}</p>
                    <p><strong>Estimated Arrival:</strong> ${loadingDetails.etd_departure}</p>
                </div>
                <div class="unloading-details">
                    <h3>Unloading Point Details</h3>
                    <p><strong>Country:</strong> ${unloadingDetails.country}</p>
                    <p><strong>Region:</strong> ${unloadingDetails.region}</p>
                    <p><strong>City:</strong> ${unloadingDetails.city}</p>
                    <p><strong>Street:</strong> ${unloadingDetails.street}</p>
                    <p><strong>Port of Destination:</strong> ${unloadingDetails.port_destination}</p>
                </div>
                <div class="driver-details">
                    <h3>Driver Details</h3>
                    <p><strong>Name:</strong> ${driver.name}</p>
                    <p><strong>Phone:</strong> ${driver.phone}</p>
                </div>
                <div class="units-details">
                    <h3>Units</h3>
                    <p><strong>Number of Units:</strong> ${units}</p>
                </div>
                <div class="charges-details">
                    <h3>Charges</h3>
                    <p><strong>Freight Charge:</strong> ${charges.freightCharge}</p>
                    <p><strong>Freight Tax:</strong> ${charges.freightTax}</p>
                    <p><strong>Inspection Charges:</strong> ${charges.inspectionCharges}</p>
                    <p><strong>Inspection Tax:</strong> ${charges.inspectionTax}</p>
                    <p><strong>Additional Charges:</strong> ${charges.additionalCharges}</p>
                    <p><strong>Additional Tax:</strong> ${charges.additionalTax}</p>
                    <p><strong>Other Charges:</strong> ${charges.otherCharges}</p>
                    <p><strong>Other Tax:</strong> ${charges.otherTax}</p>
                    <p><strong>Discount:</strong> ${charges.discount}</p>
                </div>
                <button onclick="window.print()">Print Invoice</button>
            </div>
        `;

        document.body.innerHTML = invoiceHtml;
    } catch (error) {
        console.error('Error generating invoice:', error);
    }
}


async function gatherFormDataForPrint() {
    try {
        // Fetch customer details
        const customerId = document.getElementById('customer_id').value;
        const customerResponse = await fetch(`backend/getCustomerDetails.php?customer_id=${customerId}`);
        const customer = await customerResponse.json();

        // Fetch driver details
        const driverId = document.getElementById('driver_id').value;
        const driverResponse = await fetch(`backend/getDriverDetails.php?driver_id=${driverId}`);
        const driver = await driverResponse.json();

        // Prepare the print content
        let printContent = `
            <html>
            <head>
                <title>Shipment Details</title>
                <style>
                    @page {
                        size: A4;
                        margin: 30mm 5mm 10mm 5mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        padding: 0 5mm;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 10px;
                    }
                    .header img {
                        max-width: 150px;
                        margin-bottom: 10px;
                    }
                    .section {
                        margin-bottom: 10px;
                        border-bottom: 1px solid #ccc;
                        padding-bottom: 10px;
                    }
                    .section h4 {
                        margin-bottom: 5px;
                        color: #333;
                    }
                    .section p {
                        margin: 2px 0;
                    }
                    .section p strong {
                        display: inline-block;
                        width: 120px;
                    }
                    .two-column, .three-column {
                        display: grid;
                        gap: 10px;
                    }
                    .two-column {
                        grid-template-columns: 1fr 1fr;
                    }
                    .three-column {
                        grid-template-columns: 1fr 1fr 1fr;
                    }
                    .charges-table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .charges-table th, .charges-table td {
                        border: 1px solid #ccc;
                        padding: 5px;
                        text-align: left;
                        font-size: 12px;
                    }
                    .charges-table th {
                        background-color: #f0f0f0;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <img style="width: 50%;" src="./dist/img/label_logo.jpg">
                        <h3>Shipment Details</h3>
                    </div>
        `;

        // Section: Shipment Information
        printContent += `
            <div class="section">
                <h4>Shipment Information</h4>
                <div class="two-column">
                    <div><p><strong>Shipment ID:</strong> ${document.getElementById('shipment_id').value}</p></div>
                    <div><p><strong>Handled By:</strong> ${document.getElementById('total_discount_type').selectedOptions[0].text}</p></div>
                </div>
            </div>
        `;

        // Section: Customer and Consignee
        printContent += `
            <div class="section">
                <h4>Customer and Consignee</h4>
                <div class="two-column">
                    <div><p><strong>Customer:</strong> ${customer.name}</p></div>
                    <div><p><strong>Phone:</strong> ${customer.phone}</p></div>
                    <div><p><strong>Email:</strong> ${customer.email}</p></div>
                    <div><p><strong>Address:</strong> ${customer.address}</p></div>
                    <div><p><strong>Consignee:</strong> ${document.getElementById('consignee').value}</p></div>
                </div>
            </div>
        `;

        // Section: Supplier and Driver
        printContent += `
            <div class="section">
                <h4>Supplier and Driver</h4>
                <div class="two-column">
                    <div><p><strong>Supplier:</strong> ${document.getElementById('supplier_id').selectedOptions[0]?.text || ''}</p></div>
                    <div><p><strong>Driver:</strong> ${driver.name}</p></div>
                    <div><p><strong>Driver Phone:</strong> ${driver.phone}</p></div>
                </div>
            </div>
        `;

        // Section: Loading and Unloading Points
        printContent += `
            <div class="section">
                <h4>Loading and Unloading Points</h4>
                <div class="two-column">
                    <div>
                        <p><strong>Loading Country:</strong> ${document.getElementById('loading_country').selectedOptions[0].text}</p>
                        <p><strong>Loading Region:</strong> ${document.getElementById('loading_region').selectedOptions[0]?.text || ''}</p>
                        <p><strong>Loading City:</strong> ${document.getElementById('loading_city').value}</p>
                        <p><strong>Warehouse:</strong> ${document.getElementById('warehouse').value}</p>
                    </div>
                    <div>
                        <p><strong>Unloading Country:</strong> ${document.getElementById('unloading_country').selectedOptions[0].text}</p>
                        <p><strong>Unloading Region:</strong> ${document.getElementById('unloading_region').selectedOptions[0]?.text || ''}</p>
                        <p><strong>Unloading City:</strong> ${document.getElementById('unloading_city').value}</p>
                        <p><strong>Zip Code:</strong> ${document.getElementById('zip_code').value}</p>
                    </div>
                </div>
            </div>
        `;

        // Section: Shipping Mode and Item Details
        printContent += `
            <div class="section">
                <h4>Shipping Mode and Item Details</h4>
                <div class="two-column">
                    <div><p><strong>Shipping Mode:</strong> ${document.getElementById('shipping_mode_id').selectedOptions[0].text}</p></div>
                    <div><p><strong>Item Description:</strong> ${document.getElementById('item_desc').value}</p></div>
                </div>
            </div>
        `;

        // Section: Equipment Details
        printContent += `
            <div class="section">
                <h4>Equipment Details</h4>
                <div class="two-column">
                    <div>
                        <p><strong>Vehicle:</strong> ${document.getElementById('vehicle_id').selectedOptions[0]?.text || ''}</p>
                        <p><strong>Vehicle Number:</strong> ${document.getElementById('vehicle_num').value}</p>
                    </div>
                    <div>
                        <p><strong>Container:</strong> ${document.getElementById('container_id').selectedOptions[0]?.text || ''}</p>
                        <p><strong>Container Number:</strong> ${document.getElementById('container_num').value}</p>
                    </div>
                </div>
            </div>
        `;

        // Section: Charges
        printContent += `
            <div class="section">
                <h4>Charges</h4>
                <table class="charges-table">
                    <thead>
                        <tr>
                            <th>Charge Description</th>
                            <th>Currency</th>
                            <th>Rate per Unit</th>
                            <th>Unit</th>
                            <th>Amount</th>
                            <th>Taxable Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Freight Charge</td>
                            <td>${document.getElementById('currency').value}</td>
                            <td></td>
                            <td></td>
                            <td>${document.getElementById('freight_charge').value}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Inspection Charges</td>
                            <td>${document.getElementById('currency').value}</td>
                            <td></td>
                            <td></td>
                            <td>${document.getElementById('inspection_charges').value}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Additional Charges</td>
                            <td>${document.getElementById('currency').value}</td>
                            <td></td>
                            <td></td>
                            <td>${document.getElementById('additional_charges').value}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td>${document.getElementById('currency').value}</td>
                            <td></td>
                            <td></td>
                            <td>${document.getElementById('tax').value}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Other Charges</td>
                            <td>${document.getElementById('currency').value}</td>
                            <td></td>
                            <td></td>
                            <td>${document.getElementById('other_charges').value}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td>${document.getElementById('currency').value}</td>
                            <td></td>
                            <td></td>
                            <td>${document.getElementById('discount').value}</td>
                            <td></td>
                        </tr>
        `;

        const borderChargeRows = document.querySelectorAll('.border-charge-row');
        borderChargeRows.forEach(row => {
            const index = row.dataset.index;
            const borderChargeValue = document.getElementById(`borderChargeValue${index}`).value;
            const borderChargeDescription = document.getElementById(`borderChargeDescription${index}`).value;
            printContent += `
                <tr>
                    <td>Border Charge ${index} (${borderChargeDescription})</td>
                    <td>${document.getElementById('currency').value}</td>
                    <td></td>
                    <td></td>
                    <td>${borderChargeValue}</td>
                    <td></td>
                </tr>
            `;
        });

        printContent += `
                    </tbody>
                </table>
            </div>
        `;

        // Section: Total
        printContent += `
            <div class="section">
                <p><strong>Total:</strong> ${document.getElementById('total').value}</p>
            </div>
        `;

        // Close the HTML content
        printContent += `
                </div>
            </body>
            </html>
        `;

        // Get the iframe element
        const printFrame = document.getElementById('printFrame');

        // Set the content of the iframe
        printFrame.src = 'about:blank';
        printFrame.contentWindow.document.open();
        printFrame.contentWindow.document.write(printContent);
        printFrame.contentWindow.document.close();

        // Print the content of the iframe
        printFrame.focus();
        printFrame.contentWindow.print();
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while generating the print content');
    }
}


async function collectFormData() {
  try {
      // Collect form data
      const shipmentId = document.getElementById('shipment_id').value;
      const consignee = document.getElementById('consignee').value;
      const customer = document.getElementById('customer_id').value;
      const supplier_id = document.getElementById('supplier_id').value;
      const loadingCountry = document.getElementById('loading_country').value;
      const loadingRegion = document.getElementById('loading_region').value;
      const loadingCity = document.getElementById('loading_city').value;
      const unloadingCountry = document.getElementById('unloading_country').value;
      const unloadingRegion = document.getElementById('unloading_region').value;
      const unloadingCity = document.getElementById('unloading_city').value;
      const etdDelivery = document.getElementById('etd_delivery').value;
      const etdDeparture = document.getElementById('etd_departure').value;
      const warehouse = document.getElementById('warehouse').value;
      const street = document.getElementById('street').value;
      const zipCode = document.getElementById('zip_code').value;
      const shippingMode = document.getElementById('shipping_mode_id').value;
      const itemDesc = document.getElementById('item_desc').value;
      const weight = document.getElementById('weight').value;
      const driverId = document.getElementById('driver_id').value;
      const vehicleId = document.getElementById('vehicle_id').value;
      const containerId = document.getElementById('container_id').value;
      const vehicleNum = document.getElementById('vehicle_num').value;
      const containerNum = document.getElementById('container_num').value;
      const freightCharge = document.getElementById('freight_charge').value;
      const inspectionCharges = document.getElementById('inspection_charges').value;
      const additionalCharges = document.getElementById('additional_charges').value;
      const tax = document.getElementById('tax').value;
      const otherCharges = document.getElementById('other_charges').value;
      const discount = document.getElementById('discount').value;

      // Collect border charges
      const borderCharges = Array.from(document.querySelectorAll('.border-charge-row')).map(row => {
          return {
              value: row.querySelector('input[name^="borderChargeValue"]').value,
              description: row.querySelector('input[name^="borderChargeDescription"]').value
          };
      });

      if (shipmentId === '') {
          alert('Enter Shipment ID');
          return;
      }

      // Prepare data to send
      const data = {
          shipmentId,
          consignee,
          customer,
          supplier_id,
          loadingCountry,
          loadingRegion,
          loadingCity,
          unloadingCountry,
          unloadingRegion,
          unloadingCity,
          etdDelivery,
          etdDeparture,
          warehouse,
          street,
          zipCode,
          shippingMode,
          itemDesc,
          weight,
          driverId,
          vehicleId,
          containerId,
          vehicleNum,
          containerNum,
          freightCharge,
          inspectionCharges,
          additionalCharges,
          tax,
          otherCharges,
          discount,
          borderCharges
      };

      // Send data to server
      const response = await fetch('./backend/insert_shipment.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
      });
      const result = await response.json();

      if (result.status === 'success') {
          alert('Shipment and border charges inserted successfully');
          await gatherFormDataForPrint();
      } else {
          alert('Error inserting shipment or border charges: ' + result.message);
      }
  } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while inserting the shipment');
  }
  }





            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('input[type="text"]').forEach(input => {
                    if (input.name.startsWith('borderChargeValue') || input.name.startsWith('borderChargeVAT') || input.name.startsWith('freight_charge') || input.name.startsWith('freight_tax') || input.name.startsWith('inspection_charges') || input.name.startsWith('inspection_tax') || input.name.startsWith('additional_charges') || input.name.startsWith('additional_tax') || input.name.startsWith('other_charges') || input.name.startsWith('other_tax')) {
                        total += parseFloat(input.value) || 0;
                    } else if (input.name === 'discount') {
                        total -= parseFloat(input.value) || 0;
                    }
                });

                // Get the selected currency
                const selectedCurrency = document.getElementById('currency').value;

                // If the selected currency is not AED, convert the total to AED
                let totalInAED = total;
                if (selectedCurrency !== 'AED') {
                    totalInAED = total * conversionRates[selectedCurrency];
                    document.getElementById('total').value = `${total.toFixed(2)} ${selectedCurrency} (${totalInAED.toFixed(2)} AED)`;
                } else {
                    document.getElementById('total').value = `${total.toFixed(2)} ${selectedCurrency}`;
                }
            }

        let borderChargeCount = 2;

        function addBorderChargeRow() {
            borderChargeCount++;

            // Create a new row
            const newRow = document.createElement('div');
            newRow.className = 'row border-charge-row';
            newRow.dataset.index = borderChargeCount;
            newRow.innerHTML = `
                <br>
                <div class="col-md-3">
                    <label for="borderChargeValue${borderChargeCount}">Border Charge ${borderChargeCount}</label>
                    <input type="text" class="form-control" id="borderChargeValue${borderChargeCount}" name="borderChargeValue${borderChargeCount}" placeholder="Enter value">
                </div>
                <div class="col-md-3">
                    <label for="borderChargeDescription${borderChargeCount}">Description</label>
                    <input type="text" class="form-control" id="borderChargeDescription${borderChargeCount}" name="borderChargeDescription${borderChargeCount}" placeholder="Enter description">
                </div>
                <div class="col-md-2">
                    <label for="borderChargeVAT${borderChargeCount}">VAT</label>
                    <input type="text" class="form-control" id="borderChargeVAT${borderChargeCount}" name="borderChargeVAT${borderChargeCount}" placeholder="Enter VAT">
                </div>
                <div class="col-md-1">
                    <label for="removeButton${borderChargeCount}">Remove</label>
                    <button type="button" class="btn btn-danger form-control" onclick="removeBorderChargeRow(this)">-</button>
                </div>
            `;

            // Append the new row to the container
            const container = document.getElementById('borderChargesContainer');
            container.appendChild(newRow);

            updateBorderChargeLabels();
        }

        function removeBorderChargeRow(button) {
            const row = button.parentElement.parentElement;
            row.remove();

            // Update all remaining rows' labels
            updateBorderChargeLabels();
        }

        function updateBorderChargeLabels() {
            const rows = document.querySelectorAll('.border-charge-row');
            rows.forEach((row, index) => {
                const newIndex = index + 1;
                row.dataset.index = newIndex;

                const label = row.querySelector('label[for^="borderChargeValue"]');
                label.setAttribute('for', `borderChargeValue${newIndex}`);
                label.textContent = `Border Charge ${newIndex}`;
                const valueInput = row.querySelector('input[name^="borderChargeValue"]');
                valueInput.id = `borderChargeValue${newIndex}`;
                valueInput.name = `borderChargeValue${newIndex}`;

                const descLabel = row.querySelector('label[for^="borderChargeDescription"]');
                descLabel.setAttribute('for', `borderChargeDescription${newIndex}`);
                const descInput = row.querySelector('input[name^="borderChargeDescription"]');
                descInput.id = `borderChargeDescription${newIndex}`;
                descInput.name = `borderChargeDescription${newIndex}`;

                const vatLabel = row.querySelector('label[for^="borderChargeVAT"]');
                vatLabel.setAttribute('for', `borderChargeVAT${newIndex}`);
                const vatInput = row.querySelector('input[name^="borderChargeVAT"]');
                vatInput.id = `borderChargeVAT${newIndex}`;
                vatInput.name = `borderChargeVAT${newIndex}`;

                const removeLabel = row.querySelector('label[for^="removeButton"]');
                removeLabel.setAttribute('for', `removeButton${newIndex}`);
                const removeButton = row.querySelector('button[onclick^="removeBorderChargeRow"]');
                removeButton.id = `removeButton${newIndex}`;
            });
        }


    // Call fetchCustomers on page load
    window.onload = function() {
        fetchCustomers();
        fetchSuppliers();
        fetchDrivers();
        fetchVehicles();
        fetchContainers();
        fetchShippingModes();
    };
    function fetchCustomers() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var customers = JSON.parse(this.responseText);
                var customerSelect = document.getElementById('customer_id');
                customerSelect.innerHTML = ''; // Clear existing options

                customers.forEach(function(customer) {
                    var option = document.createElement('option');
                    option.value = customer.customer_id;
                    option.textContent = customer.name;
                    customerSelect.appendChild(option);
                });
            }
        };

        xhr.open('GET', './backend/fetch_customers.php', true);
        xhr.send();
    }

    function fetchSuppliers() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var suppliers = JSON.parse(this.responseText);
                var supplierSelect = document.getElementById('supplier_id');
                supplierSelect.innerHTML = ''; // Clear existing options

                suppliers.forEach(function(supplier) {
                    var option = document.createElement('option');
                    option.value = supplier.supplier_id;
                    option.textContent = supplier.name;
                    supplierSelect.appendChild(option);
                });
            }
        };

        xhr.open('GET', './backend/fetch_suppliers.php', true);
        xhr.send();
        fetchSuppliersModal()
    }

    function fetchSuppliersModal() {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
              var suppliers = JSON.parse(this.responseText);
              var modal = document.getElementById('modal-add_driver');
              var supplierSelect = modal.querySelector('#supplier_id_modal');
              supplierSelect.innerHTML = ''; // Clear existing options

              suppliers.forEach(function(supplier) {
                  var option = document.createElement('option');
                  option.value = supplier.supplier_id;
                  option.textContent = supplier.name;
                  supplierSelect.appendChild(option);
              });
          }
      };

      xhr.open('GET', './backend/fetch_suppliers.php', true);
      xhr.send();
    }

    function fetchDrivers() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var drivers = JSON.parse(this.responseText);
                var driverSelect = document.getElementById('driver_id');
                driverSelect.innerHTML = ''; // Clear existing options

                drivers.forEach(function(driver) {
                    var option = document.createElement('option');
                    option.value = driver.driver_id;
                    option.textContent = driver.name;
                    driverSelect.appendChild(option);
                });
            }
        };

        xhr.open('GET', './backend/fetch_drivers.php', true);
        xhr.send();
    }

// VEHICLE AND CONTAINERS SECTION START ////////////////////////////////////////////////////////////////////////////
    function fetchVehicles() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var vehicles = JSON.parse(this.responseText);
                var vehicleSelects = document.querySelectorAll('select[id^="vehicle_id"]');

                vehicleSelects.forEach(function(vehicleSelect) {
                    vehicleSelect.innerHTML = ''; // Clear existing options
                    vehicles.forEach(function(vehicle) {
                        var option = document.createElement('option');
                        option.value = vehicle.vehicle_id;
                        option.textContent = vehicle.vehicle_name;
                        vehicleSelect.appendChild(option);
                    });
                });
            }
        };

        xhr.open('GET', './backend/fetch_vehicles.php', true);
        xhr.send();
    }

    function fetchContainers() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var containers = JSON.parse(this.responseText);
                var containerSelects = document.querySelectorAll('select[id^="container_id"]');

                containerSelects.forEach(function(containerSelect) {
                    containerSelect.innerHTML = ''; // Clear existing options
                    containers.forEach(function(container) {
                        var option = document.createElement('option');
                        option.value = container.container_id;
                        option.textContent = container.container_name;
                        containerSelect.appendChild(option);
                    });
                });
            }
        };

        xhr.open('GET', './backend/fetch_containers.php', true);
        xhr.send();
    }

    let vehicleRowCount = 1;

    function addVehicleRow() {
        vehicleRowCount++;

        // Create a new row
        const newRow = document.createElement('div');
        newRow.className = 'row vehicle-row';
        newRow.dataset.index = vehicleRowCount;
        newRow.innerHTML = `
            <div class="form-group col-md-3">
                <label for="vehicle_id${vehicleRowCount}">Vehicle</label>
                <div class="row">
                    <div class="form-group col-md-8">
                        <select id="vehicle_id${vehicleRowCount}" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_vehicle">+</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-3">
                <label for="container_id${vehicleRowCount}">Container</label>
                <div class="row">
                    <div class="form-group col-md-8">
                        <select id="container_id${vehicleRowCount}" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_container">+</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-3">
                <label for="vehicle_num${vehicleRowCount}">Equipment Number</label>
                <div class="row">
                    <div class="form-group col-md-12">
                        <input type="text" name="vehicle_num${vehicleRowCount}" class="form-control" id="vehicle_num${vehicleRowCount}" placeholder="Enter equipment number">
                    </div>
                </div>
            </div>
            <div class="form-group col-md-1">
                <label for="removeButton${vehicleRowCount}">Remove</label>
                <button type="button" class="btn btn-danger form-control" onclick="removeVehicleRow(this)">-</button>
            </div>
        `;

        // Append the new row to the container
        const container = document.getElementById('vehicleContainer');
        container.appendChild(newRow);

        // Update labels and IDs
        updateVehicleLabels();

        // Fetch and populate dropdowns
        fetchVehicles();
        fetchContainers();
    }

    function removeVehicleRow(button) {
        const row = button.parentElement.parentElement;
        row.remove();

        // Update all remaining rows' labels
        updateVehicleLabels();
    }

    function updateVehicleLabels() {
        const rows = document.querySelectorAll('.vehicle-row');
        rows.forEach((row, index) => {
            const newIndex = index + 1;
            row.dataset.index = newIndex;

            const vehicleLabel = row.querySelector('label[for^="vehicle_id"]');
            vehicleLabel.setAttribute('for', `vehicle_id${newIndex}`);
            const vehicleSelect = row.querySelector('select[id^="vehicle_id"]');
            vehicleSelect.id = `vehicle_id${newIndex}`;

            const containerLabel = row.querySelector('label[for^="container_id"]');
            containerLabel.setAttribute('for', `container_id${newIndex}`);
            const containerSelect = row.querySelector('select[id^="container_id"]');
            containerSelect.id = `container_id${newIndex}`;

            const numberLabel = row.querySelector('label[for^="vehicle_num"]');
            numberLabel.setAttribute('for', `vehicle_num${newIndex}`);
            const numberInput = row.querySelector('input[id^="vehicle_num"]');
            numberInput.id = `vehicle_num${newIndex}`;
            numberInput.name = `vehicle_num${newIndex}`;

            const removeLabel = row.querySelector('label[for^="removeButton"]');
            removeLabel.setAttribute('for', `removeButton${newIndex}`);
            const removeButton = row.querySelector('button[onclick^="removeVehicleRow"]');
            removeButton.id = `removeButton${newIndex}`;
        });
    }

// VEHICLE AND CONTAINERS SECTION END ///////////////////////////////////////////////////////////////


    function fetchShippingModes() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var shippingModes = JSON.parse(this.responseText);
                var shippingModeSelect = document.getElementById('shipping_mode_id');
                shippingModeSelect.innerHTML = ''; // Clear existing options

                shippingModes.forEach(function(shippingMode) {
                    var option = document.createElement('option');
                    option.value = shippingMode.shipping_mode_id;
                    option.textContent = shippingMode.shipping_mode_name;
                    shippingModeSelect.appendChild(option);
                });
            }
        };

        xhr.open('GET', './backend/fetch_shipping_modes.php', true);
        xhr.send();
    }

    function addSupplier() {
        var form = document.getElementById('addSupplierForm');
        var formData = new FormData(form);

        var supplierData = {
            name: formData.get('name'),
            location: formData.get('location'),
            phone: formData.get('phone'),
            email: formData.get('email')
        };

        console.log(supplierData);
        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, close the modal and refresh the supplier list
                    form.reset();
                    $('#modal-add_supplier').modal('hide');

                    alert('Supplier added successfully.');
                    fetchSuppliers();
                    // You can add code here to refresh the supplier list on the page
                } else {
                    // Handle error
                    console.error("Error adding supplier: " + this.status);
                    alert("Error adding supplier. Please try again.");
                }
            }
        };

        // Set up the request
        xhr.open('POST', './backend/add_supplier.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Convert the data to JSON and send the request
        xhr.send(JSON.stringify(supplierData));
    }

    function addCustomer() {
        var form = document.getElementById('addCustomerForm');
        var formData = new FormData(form);

        var customerData = {
            name: formData.get('name'),
            location: formData.get('location'),
            phone: formData.get('phone'),
            email: formData.get('email')
        };

        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, clear the form, close the modal, and show a success message
                    form.reset();
                    $('#modal-add_customer').modal('hide');
                    alert('Customer added successfully.');
                    fetchCustomers();
                    // You can add code here to refresh the customer list on the page
                } else {
                    // Handle error
                    console.error("Error adding customer: " + this.status);
                    alert("Error adding customer. Please try again.");
                }
            }
        };

        // Set up the request
        xhr.open('POST', './backend/add_customer.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Convert the data to JSON and send the request
        xhr.send(JSON.stringify(customerData));
    }

    function addDriver() {
        var form = document.getElementById('addDriverForm');
        var formData = new FormData(form);

        var supplierSelect = document.getElementById('supplier_id_modal');
        var supplierId = supplierSelect.value;

        var driverData = {
            supplier_id: supplierId,
            name: formData.get('name'),
            license: formData.get('license'),
            phone: formData.get('phone'),
            email: formData.get('email')
        };

        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, clear the form, close the modal, and show a success message
                    form.reset();
                    $('#modal-add_driver').modal('hide');
                    alert('Driver added successfully.');
                    // You can add code here to refresh the driver list on the page
                } else {
                    // Handle error
                    console.error("Error adding driver: " + this.status);
                    alert("Error adding driver. Please try again.");
                }
            }
        };

        // Set up the request
        xhr.open('POST', './backend/add_driver.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Convert the data to JSON and send the request
        xhr.send(JSON.stringify(driverData));
    }

    function addVehicle() {
        var form = document.getElementById('addVehicleForm');
        var formData = new FormData(form);

        var vehicleData = {
            name: formData.get('name'),
            // Add additional vehicle properties as needed
        };

        console.log(vehicleData);
        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, close the modal and refresh the vehicle list
                    form.reset();
                    $('#modal-add_vehicle').modal('hide');

                    alert('Vehicle added successfully.');
                    fetchVehicles();
                    // You can add code here to refresh the vehicle list on the page
                } else {
                    // Handle error
                    console.error("Error adding vehicle: " + this.status);
                    alert("Error adding vehicle. Please try again.");
                }
            }
        };

        // Set up the request
        xhr.open('POST', './backend/add_vehicle.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Convert the data to JSON and send the request
        xhr.send(JSON.stringify(vehicleData));
    }

    function addContainer() {
          var form = document.getElementById('addContainerForm');
          var formData = new FormData(form);

          var containerData = {
              name: formData.get('name'),
              // Add additional container properties as needed
          };

          console.log(containerData);
          // Send data to the backend using AJAX
          var xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function () {
              if (this.readyState == 4) {
                  if (this.status == 200) {
                      // Success, close the modal and refresh the container list
                      form.reset();
                      $('#modal-add_container').modal('hide');

                      alert('Container added successfully.');
                      fetchContainers();
                      // You can add code here to refresh the container list on the page
                  } else {
                      // Handle error
                      console.error("Error adding container: " + this.status);
                      alert("Error adding container. Please try again.");
                  }
              }
          };

          // Set up the request
          xhr.open('POST', './backend/add_container.php', true);
          xhr.setRequestHeader('Content-Type', 'application/json');

          // Convert the data to JSON and send the request
          xhr.send(JSON.stringify(containerData));
      }

    function addShippingMode() {
        var form = document.getElementById('addShippingModeForm');
        var formData = new FormData(form);

        var shippingModeData = {
            name: formData.get('name'),
            // Add additional shipping mode properties as needed
        };

        console.log(shippingModeData);
        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, close the modal and refresh the shipping mode list
                    form.reset();
                    $('#modal-add_shipping_mode').modal('hide');

                    alert('Shipping mode added successfully.');
                    fetchShippingModes();
                    // You can add code here to refresh the shipping mode list on the page
                } else {
                    // Handle error
                    console.error("Error adding shipping mode: " + this.status);
                    alert("Error adding shipping mode. Please try again.");
                }
            }
        };

        // Set up the request
        xhr.open('POST', './backend/add_shipping_mode.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Convert the data to JSON and send the request
        xhr.send(JSON.stringify(shippingModeData));
    }






    </script>
<?php include './layouts/footer.php'; ?>
