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

<?php

  $invoice_id = $_REQUEST['invoice_id'];
  $customer ='';
  $supplier = '';

  $sql = "SELECT * FROM shipments WHERE shipment_id='$invoice_id'";
  $rs = $conn->query($sql);
  if ($rs->num_rows > 0) {
      while ($row = $rs->fetch_assoc()) {
        $customer = $row['customer_id'];
        $supplier = $row['supplier_id'];
        $loading_country = $row['loading_country'];
        $loading_region = $row['loading_region'];
        $unloading_country = $row['unloading_country'];
        $unloading_region = $row['unloading_region'];
  ?>

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

                <div class="box-body">
                  <div class="row">


                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Job Number</label>
                        <input type="text" name="job_number" class="form-control" id="job_number" value="<?= $row['job_number'] ?>" placeholder="">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Invoice Number</label>
                        <input type="text" name="invoice_number" value="<?= $row['invoice_number'] ?>"class="form-control" id="invoice_number"  placeholder="">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="shipment_id">Shipment ID</label>
                        <input required type="text" name="shipment_id" value="<?= $row['shipment_id'] ?>" class="form-control" id="shipment_id"  readonly>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">BL Number</label>
                        <input type="text" name="bl_number" class="form-control" value="<?= $row['bl_number'] ?>" id="bl_number"  placeholder="Enter BL Number">
                      </div>
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">House BL Number</label>
                        <input type="text" name="house_bl_number" class="form-control" value="<?= $row['house_bl_number'] ?>" id="house_bl_number"  placeholder="Enter BL Number">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="">BL Type</label>
                          <select id="bl_type" class="form-control">
                              <option value="none" <?= $row['bl_type'] == 'none' ? 'selected' : '' ?>>Select</option>
                              <option value="SWB" <?= $row['bl_type'] == 'SWB' ? 'selected' : '' ?>>SWB</option>
                              <option value="original" <?= $row['bl_type'] == 'original' ? 'selected' : '' ?>>original</option>
                              <option value="surrender" <?= $row['bl_type'] == 'surrender' ? 'selected' : '' ?>>surrender</option>
                          </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Top Note</label>
                        <textarea id="note"  class="form-control" rows="3" placeholder="Enter note"><?= $row['note'] ?></textarea>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Bill of entry</label>
                        <input type="text" value="<?= $row['bill_of_entry'] ?>" name="bill_of_entry" class="form-control" id="bill_of_entry"  placeholder="Enter Bill of Entry">
                      </div>
                    </div>




                </div><!-- /.box-body -->
              </div>
            </div>
          </div>
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                </div><!-- /.box-header -->

                  <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                        <input type="hidden" id="handled_by" name="" value="<?= $row['handled_by'] ?>">
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="">Job Open Date</label>
                          <input required type="date" value="<?= $row['job_date'] ?>" name="job_date" class="form-control"  id="job_date">

                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="">Invoice Date</label>
                          <input required type="date" value="<?= $row['invoice_date'] ?>" name="invoice_date" class="form-control" id="invoice_date">

                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="">Payment Due date</label>
                          <input required type="date" name="payment_date" value="<?= $row['payment_date'] ?>" class="form-control"  id="payment_date">

                        </div>
                      </div>
                    </div>


                  </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div>
          </div>
          <div class="row">
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
                        <select onchange="selectConsignee()" id="customer_id" class="form-control">
                        </select>

                      </div>
                      <div class=" form-group col-md-2 ">
                        <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_customer">+</button>
                      </div>
                      <div class=" form-group col-md-6">
                        <label for="">Consignee</label>
                        <input type="text" value="<?= $row['consignee'] ?>" name="consignee" class="form-control" id="consignee" placeholder="Enter consignee(Optional)">

                      </div>
                      <div class="form-group col-md-6">
                        <label for="">VAT Number</label>
                        <input type="text"  name="customerVATnumber" class="form-control" id="customerVATnumber" placeholder="Enter VAT Number">
                      </div>
                    </div>

                  </div><!-- /.box-body -->

              </div><!-- /.box -->

            </div>
            <div class="col-md-6">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Shipper</h3>
                </div>
                  <div class="  box-body">
                    <div class="row">

                      <div class=" form-group col-md-6">
                        <label for="total_discount_type">Shipper</label>
                        <select id="supplier_id" class="form-control">

                        </select>

                      </div>
                      <div class=" form-group col-md-2 ">
                        <label for="total_discount_type">Add New</label>
                        <button onclick="fetchSuppliersModal()" type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_supplier">+</button>
                      </div>
                      <div class="col-md-4">
                        <label for="">Shipper Reference</label>
                        <input type="text" value="<?= $row['shipper_reference'] ?>" name="shipper_reference" class="form-control" id="shipper_reference" placeholder="Enter Reference number">
                      </div>

                        <div class=" form-group col-md-6">
                          <label for="">Vessel</label>
                          <input type="text" value="<?= $row['vessel'] ?>" name="vessel" class="form-control" id="vessel" placeholder="Enter vessel">
                        </div>

                        <div class=" form-group col-md-6">
                          <label for="">Voyage Number</label>
                          <input type="text" value="<?= $row['voyage_number'] ?>" name="voyage_number" class="form-control" id="voyage_number" placeholder="Enter voyage number">
                        </div>
                    </div>

                  </div><!-- /.box-body -->
              </div><!-- /.box -->

            </div>

          </div>   <!-- /.row -->
          <div class="row">
              <div class="col-md-12">
                  <!-- general form elements -->
                  <div class="box box-primary">
                      <div class="box-header">
                          <h3 class="box-title">Shipment Details </h3>
                      </div>
                      <div class="box-body">


                          <div class="row">
                              <!-- <div class="form-group col-md-3">
                                <label for="">Shipment Mode</label> <br>
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
                              </div> -->
                              <!-- <div class="form-group col-md-3">
                                <label for="">Carrier</label> <br>
                                  <div class="row">
                                      <div class="col-md-12">
                                        <input type="text" name="carrier" class="form-control" id="carrier" placeholder="Enter Carrier(optional)">
                                      </div>
                                  </div>

                              </div> -->
                              <div class="form-group col-md-3">
                                <label for="">Item Description</label> <br>
                                  <div class="row">
                                      <div class="col-md-12">
                                        <!-- <input type="text" name="item_desc" class="form-control" id="item_desc" placeholder="Enter Item Desc"> -->
                                        <input value="<?= $row['item_description'] ?>" list="dropdown-options" class="form-control" name="item_desc" id="item_desc">
                                        <datalist id="dropdown-options">
                                          <?php

                                          $sql2 = "SELECT item_desc FROM item_desc";
                                          $result2 = $conn->query($sql2);

                                          if ($result2->num_rows > 0) {
                                              // Output data of each row
                                              while ($row2 = $result2->fetch_assoc()) {
                                                  $item_desc = $row2['item_desc'];
                                                  echo "<option value='$item_desc'></option>";
                                              }
                                          }
                                          ?>
                                        </datalist>
                                      </div>
                                  </div>

                              </div>

                              <div class="form-group col-md-3">
                                <label for="">Weight (kg)</label> <br>
                                  <div class="row">
                                      <div class="col-md-12">
                                        <input value="<?= $row['weight'] ?>" type="text" name="weight" class="form-control" id="weight" placeholder="Enter Weight">
                                      </div>
                                  </div>

                              </div>
                              <div class="form-group col-md-5">
                                <label for="">Volume (cbm)</label> <br>
                                  <div class="row">
                                      <div class="col-md-4">
                                        <input value="<?= $row['height'] ?>" type="text" name="height" class="form-control" id="height" placeholder="height">
                                      </div>
                                      <div class="col-md-4">
                                        <input value="<?= $row['width'] ?>" type="text" name="width" class="form-control" id="width" placeholder="width">
                                      </div>
                                      <div class="col-md-4">
                                        <input value="<?= $row['length'] ?>" type="text" name="length" class="form-control" id="length" placeholder="length">
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
                                  <?php
                                      // Query to fetch selected vehicles from shipment_equipments table
                                      $sql3 = "SELECT * FROM shipment_equipments WHERE shipment_id='$invoice_id'";
                                      $result3 = $conn->query($sql3);

                                      if ($result3->num_rows > 0) {
                                          $vehicleRowCount = 0;
                                          while ($row3 = $result3->fetch_assoc()) {
                                              $vehicleRowCount++;
                                              $vehicle_id = $row3['equipment_id'];
                                              $vehicle_num = $row3['equipment_number'];
                                              $equipment_desc = $row3['description'] ?? '';
                                              $equipment_weight = $row3['weight'] ?? '';
                                              $equipment_packs = $row3['packs'] ?? '';
                                              ?>
                                              <div class="row vehicle-row" data-index="<?= $vehicleRowCount ?>">
                                                  <div class="form-group col-md-3">
                                                      <label for="vehicle_id<?= $vehicleRowCount ?>">Equipment</label>
                                                      <div class="row">
                                                          <div class="form-group col-md-9">
                                                              <select name="vehicle_id<?= $vehicleRowCount ?>" id="vehicle_id<?= $vehicleRowCount ?>" class="form-control">
                                                                  <?php
                                                                  $sql4 = "SELECT vehicle_id, vehicle_name FROM vehicles";
                                                                  $result4 = $conn->query($sql4);
                                                                  if ($result4->num_rows > 0) {
                                                                      while ($row4 = $result4->fetch_assoc()) {
                                                                          $v_id = $row4['vehicle_id'];
                                                                          $v_name = $row4['vehicle_name'];
                                                                          echo "<option value='$v_id'" . ($vehicle_id == $v_id ? ' selected' : '') . ">$v_name</option>";
                                                                      }
                                                                  } else {
                                                                      echo "<option value=''>No vehicles available</option>";
                                                                  }
                                                                  ?>
                                                              </select>
                                                          </div>
                                                          <div class="form-group col-md-3">
                                                              <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_vehicle">+</button>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="form-group col-md-2">
                                                      <label for="vehicle_num<?= $vehicleRowCount ?>">Equipment Number</label>
                                                      <input list="dropdown-vehicle_num<?= $vehicleRowCount ?>" class="form-control eq-number" name="vehicle_num<?= $vehicleRowCount ?>" id="vehicle_num<?= $vehicleRowCount ?>" value="<?= $vehicle_num ?>">
                                                      <datalist id="dropdown-vehicle_num<?= $vehicleRowCount ?>">
                                                          <?php
                                                          $sql5 = "SELECT eq_number FROM equipment_numbers";
                                                          $result5 = $conn->query($sql5);
                                                          if ($result5->num_rows > 0) {
                                                              while ($row5 = $result5->fetch_assoc()) {
                                                                  $eq_number = $row5['eq_number'];
                                                                  echo "<option value='$eq_number'" . ($eq_number == $vehicle_num ? ' selected' : '') . ">$eq_number</option>";
                                                              }
                                                          }
                                                          ?>
                                                      </datalist>
                                                  </div>
                                                  <div class="form-group col-md-2">
                                                      <label for="equipment_desc<?= $vehicleRowCount ?>">Description</label>
                                                      <input type="text" class="form-control" name="equipment_desc<?= $vehicleRowCount ?>" id="equipment_desc<?= $vehicleRowCount ?>" value="<?= htmlspecialchars($equipment_desc) ?>" placeholder="Description">
                                                  </div>
                                                  <div class="form-group col-md-2">
                                                      <label for="equipment_weight<?= $vehicleRowCount ?>">Weight</label>
                                                      <input type="text" class="form-control" name="equipment_weight<?= $vehicleRowCount ?>" id="equipment_weight<?= $vehicleRowCount ?>" value="<?= htmlspecialchars($equipment_weight) ?>" placeholder="Weight">
                                                  </div>
                                                  <div class="form-group col-md-1">
                                                      <label for="equipment_packs<?= $vehicleRowCount ?>">Packs</label>
                                                      <input type="text" class="form-control" name="equipment_packs<?= $vehicleRowCount ?>" id="equipment_packs<?= $vehicleRowCount ?>" value="<?= htmlspecialchars($equipment_packs) ?>" placeholder="Packs">
                                                  </div>
                                                  <div class="form-group col-md-1">
                                                      <label for="removeButton<?= $vehicleRowCount ?>">Remove</label>
                                                      <button type="button" class="btn btn-danger form-control" onclick="removeVehicleRow(this)">-</button>
                                                  </div>
                                                  <?php if ($vehicleRowCount == 1) { ?>
                                                  <div class="form-group col-md-1">
                                                      <label for="addButton">Add New</label>
                                                      <button type="button" class="btn btn-primary form-control" onclick="addVehicleRow()">+</button>
                                                  </div>
                                                  <?php } ?>
                                              </div>
                                              <?php
                                          }
                                      }
                                      ?>


                                </div>
                            </div>
                        </div>

                        <div id="vehicleContainerRows"></div>
                          <!-- Container for the added rows -->

                          <div class="row">
                            <div class="form-group col-md-3">
                              <label for="">Number of Packs</label> <br>
                              <div class="row">
                                <div class=" form-group col-md-12 ">
                                  <input value="<?= $row['units'] ?>" type="text" name="units" class="form-control" id="units" placeholder="Enter Number of Packs">

                                </div>
                              </div>
                            </div>
                          </div>

                      </div><!-- /.box-body -->
                  </div><!-- /.box -->
              </div>
          </div>
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
                              <!-- <div class="col-md-4">
                                <p for="">City</p>
                                <input type="text" name="unloading_city" class="form-control" id="loading_city" placeholder="Enter consignee(Optional)">
                              </div> -->
                          </div>
                          <div class="row">
                            <div class="form-group col-md-12">
                              <br>
                                <div class="row">
                                  <div class="col-md-6">
                                    <p for="">Place of Loading</p>
                                    <input value="<?= $row['loading_street'] ?>" list="dropdown-loading_street" class="form-control" name="loading_street" id="loading_street" placeholder="Enter City">
                                    <datalist id="dropdown-loading_street">
                                      <?php

                                      $sql1 = "SELECT place FROM places";
                                      $result1 = $conn->query($sql1);

                                      if ($result1->num_rows > 0) {
                                          // Output data of each row
                                          while ($row1 = $result1->fetch_assoc()) {
                                              $item_desc = $row1['place'];
                                              echo "<option value='$item_desc'></option>";
                                          }
                                      } else {
                                          echo "0 results";
                                      }
                                      ?>
                                    </datalist>
                                  </div>
                                  <div class="col-md-6">
                                    <p for="">Port of Origin</p>

                                    <input value="<?= $row['port_of_origin'] ?>" list="dropdown-port_origin" class="form-control" name="port_origin" id="port_origin" placeholder="Enter Port">
                                    <datalist id="dropdown-port_origin">
                                      <?php

                                      $sql1 = "SELECT port FROM ports";
                                      $result1 = $conn->query($sql1);

                                      if ($result1->num_rows > 0) {
                                          // Output data of each row
                                          while ($row1 = $result1->fetch_assoc()) {
                                              $item_desc = $row1['port'];
                                              echo "<option value='$item_desc'></option>";
                                          }
                                      } else {
                                          echo "0 results";
                                      }
                                      ?>
                                    </datalist>
                                  </div>

                                  <!-- <div class="col-md-6">
                                    <p for="">Port of Loading</p>
                                    <input type="text" name="port_loading" class="form-control" id="port_loading" placeholder="Enter Port">
                                  </div> -->
                                </div><br>
                                <div class="row">

                                    <div class="col-md-6">
                                      <p for="">Warehouse</p>
                                      <input value="<?= $row['warehouse'] ?>" list="dropdown-warehouse" class="form-control" name="warehouse" id="warehouse" placeholder="Enter Warehouse">
                                      <datalist id="dropdown-warehouse">
                                        <?php

                                        $sql1 = "SELECT warehouse FROM warehouses";
                                        $result1 = $conn->query($sql1);

                                        if ($result1->num_rows > 0) {
                                            // Output data of each row
                                            while ($row1 = $result1->fetch_assoc()) {
                                                $item_desc1 = $row1['warehouse'];
                                                echo "<option value='$item_desc'></option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                      </datalist>
                                    </div>
                                    <div class="col-md-6">
                                      <p for="">Estimated Departure</p>
                                      <input value="<?= $row['etd_departure'] ?>" type="date" name="etd_departure" class="form-control" id="etd_departure" >
                                    </div>
                                    <div class="col-md-6">
                                      <br>
                                      <p for="">Estimated Departure 2(optional)</p>
                                      <input value="<?= $row['etd_departure_2'] ?>" type="date" name="etd_departure_2" class="form-control" id="etd_departure_2" >
                                    </div>
                                    <div class="col-md-6">
                                      <br>
                                      <p for="">Estimated Departure 3(optional)</p>
                                      <input type="date" value="<?= $row['etd_departure_3'] ?>" name="etd_departure_3" class="form-control" id="etd_departure_3" >
                                    </div>
                                    <div class="col-md-6">
                                      <br>
                                      <p for="">Gate In</p>
                                      <input value="<?= $row['gate_in'] ?>" type="date" name="gate_in" class="form-control" id="gate_in" >
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
                  <h3 class="box-title">Offloading Point details</h3>
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
                                <!-- <div class="col-md-4">
                                  <p for="">City</p>
                                  <input type="text" name="unloading_city" class="form-control" id="unloading_city" placeholder="Enter City">
                                </div> -->
                          </div>
                          <div class="row">
                            <div class="form-group col-md-12">
                              <br>

                                <div class="row">
                                  <div class="col-md-6">
                                    <p for="">Port of Destination</p>
                                    <input value="<?= $row['port_of_destination'] ?>" list="dropdown-port_destination" class="form-control" name="port_destination" id="port_destination" placeholder="Enter Port">
                                    <datalist id="dropdown-port_destination">
                                      <?php

                                      $sql1 = "SELECT port FROM ports";
                                      $result1 = $conn->query($sql1);

                                      if ($result1->num_rows > 0) {
                                          // Output data of each row
                                          while ($row1 = $result1->fetch_assoc()) {
                                              $item_desc = $row1['port'];
                                              echo "<option value='$item_desc'></option>";
                                          }
                                      } else {
                                          echo "0 results";
                                      }
                                      ?>
                                    </datalist>
                                  </div>
                                    <div class="col-md-6">
                                      <p for="">Place of Offloading</p>
                                      <input value="<?= $row['unloading_street'] ?>" list="dropdown-unloading_street" class="form-control" name="unloading_street" id="unloading_street" placeholder="Enter City">
                                      <datalist id="dropdown-unloading_street">
                                        <?php

                                        $sql1 = "SELECT place FROM places";
                                        $result1 = $conn->query($sql1);

                                        if ($result1->num_rows > 0) {
                                            // Output data of each row
                                            while ($row1 = $result1->fetch_assoc()) {
                                                $item_desc = $row1['place'];
                                                echo "<option value='$item_desc'></option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                      </datalist>
                                    </div>

                                    <br>
                                </div>
                                <div class="row">
                                  <br>
                                  <div class="col-md-6">
                                    <p for="">Estimated Arrival</p>
                                    <input value="<?= $row['etd_arrival'] ?>" type="date" name="etd_arrival" class="form-control" id="etd_arrival">
                                  </div>
                                  <div class="col-md-6">
                                    <p for="">Gate Out</p>
                                    <input type="date" value="<?= $row['gate_out'] ?>" name="gate_out" class="form-control" id="gate_out" >
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
                          <div class="row">
                            <div class="col-md-2">

                            <h3 class="box-title">Charges</h3>

                            </div>


                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-2">

                              <select id="globalCurrency" class="form-control" onchange="changeCurrencyForAllRows()">

                                  <?php

                                  $sql6 = "SELECT * FROM currencies";
                                  $result6 = $conn->query($sql6);

                                  if ($result6->num_rows > 0) {
                                      // Output data of each row
                                      while ($row6 = $result6->fetch_assoc()) {
                                          $id = $row6['id'];
                                          $currency = $row6['currency'];
                                          $roe = $row6['roe'];
                                          echo "<option value='$id'>$currency&nbsp;&nbsp;$roe</option>";
                                      }
                                  } else {
                                      echo "0 results";
                                  }
                                  ?>
                              </select>

                            </div>
                          </div>



                        </div>
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-12">
                                  <div class="">
                                    <div id="borderChargesContainer" class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                              <tr>
                                                <th>Description</th>
                                                <th>
                                                    Currency/Ex.Rate

                                                </th>
                                                <th>Rate per Unit</th>
                                                <th>Qty/UoM</th>
                                                <th>Taxable Amount (%)</th>
                                                <th>Amount (Selected Currency)</th>
                                                <th>Amount(AED)</th>
                                                <th>Amount(USD)</th>

                                                <th>Total Amount</th>
                                                <th>Action</th>
                                              </tr>

                                            </thead>
                                            <tbody>
                                              <?php
                                                $sql3 = "SELECT * FROM shipment_charges WHERE shipment_id='$invoice_id'";
                                                $result3 = $conn->query($sql3);

                                                if ($result3->num_rows > 0) {
                                                  $borderChargeCount = 0; // Initialize the counter
                                                  while ($row3 = $result3->fetch_assoc()) {
                                                      $chargeDescription = $row3['charge_details'];
                                                      $currencyRate = $row3['currency'];
                                                      $rate = $row3['unit_rate'];

                                                      $quantityUoM = $row3['quantity'];
                                                      $taxableValues = $row3['taxable'];
                                                      $amount = $row3['amount'];
                                                      if ($row3['amount'] < 0) {
                                                        continue; // Skip this iteration if amount is negative
                                                    }
                                                      $amountAED = $row3['amount_AED'];
                                                      $amountUSD = $row3['amount_USD'];
                                                      $totalAmount = $row3['total_amount'];
                                                      $borderChargeCount++; // Increment the counter
                                                      ?>
                                                      <tr class="border-charge-row" data-index="<?= $borderChargeCount ?>">
                                                          <td>
                                                              <input list="dropdown-chargeDescription<?= $borderChargeCount ?>" class="form-control charge-desc" name="chargeDescription" id="chargeDescription<?= $borderChargeCount ?>" value="<?= $chargeDescription ?>" placeholder="Enter description">
                                                              <datalist id="dropdown-chargeDescription<?= $borderChargeCount ?>">
                                                                  <?php
                                                                  $sql6 = "SELECT detail FROM charge_details";
                                                                  $result6 = $conn->query($sql6);
                                                                  if ($result6->num_rows > 0) {
                                                                      while ($row6 = $result6->fetch_assoc()) {
                                                                          $item_desc = $row6['detail'];
                                                                          echo "<option value='$item_desc'></option>";
                                                                      }
                                                                  } else {
                                                                      echo "<option>No charge descriptions available</option>";
                                                                  }
                                                                  ?>
                                                              </datalist>
                                                          </td>
                                                          <td>
                                                              <select id="currencyRate<?= $borderChargeCount ?>" name="currencyRate<?= $borderChargeCount ?>" class="form-control">
                                                                  <?php
                                                                  $sql6 = "SELECT * FROM currencies";
                                                                  $result6 = $conn->query($sql6);
                                                                  if ($result6->num_rows > 0) {
                                                                      while ($row6 = $result6->fetch_assoc()) {
                                                                          $id = $row6['id'];
                                                                          $currency = $row6['currency'];
                                                                          echo "<option value='$id'" . ($currencyRate == $id ? " selected" : "") . ">$currency</option>";
                                                                      }
                                                                  } else {
                                                                      echo "<option>No currencies available</option>";
                                                                  }
                                                                  ?>
                                                              </select>
                                                          </td>
                                                          <td><input type="text" class="form-control rate-input" id="rate<?= $borderChargeCount ?>" name="rate<?= $borderChargeCount ?>" value="<?= $rate ?>" placeholder="Enter rate"></td>
                                                          <td><input type="text" class="form-control" id="quantityUoM<?= $borderChargeCount ?>" name="quantityUoM<?= $borderChargeCount ?>" value="<?= $quantityUoM ?>"></td>
                                                          <td>
                                                              <input list="dropdown-taxableValues<?= $borderChargeCount ?>" class="form-control taxable-value" name="taxableValues" id="taxableValues<?= $borderChargeCount ?>" value="<?= $taxableValues ?>" placeholder="Enter Value">
                                                              <datalist id="dropdown-taxableValues<?= $borderChargeCount ?>">
                                                                  <?php
                                                                  $sql6 = "SELECT value FROM tax_percentages";
                                                                  $result6 = $conn->query($sql6);
                                                                  if ($result6->num_rows > 0) {
                                                                      while ($row6 = $result6->fetch_assoc()) {
                                                                          $item_desc = $row6['value'];
                                                                          echo "<option value='$item_desc'></option>";
                                                                      }
                                                                  } else {
                                                                      echo "<option>No taxable values available</option>";
                                                                  }
                                                                  ?>
                                                              </datalist>
                                                          </td>
                                                          <td><input type="text" class="form-control" id="amount<?= $borderChargeCount ?>" name="amount<?= $borderChargeCount ?>" value="<?= $amount ?>" disabled></td>
                                                          <td><input type="text" class="form-control" id="amountAED<?= $borderChargeCount ?>" name="amountAED<?= $borderChargeCount ?>" value="<?= $amountAED ?>" disabled></td>
                                                          <td><input type="text" class="form-control" id="amountUSD<?= $borderChargeCount ?>" name="amountUSD<?= $borderChargeCount ?>" value="<?= $amountUSD ?>" disabled></td>
                                                          <td><input type="text" class="form-control" id="totalAmount<?= $borderChargeCount ?>" name="totalAmount<?= $borderChargeCount ?>" value="<?= $totalAmount ?>" disabled></td>
                                                          <td>
                                                              <button type="button" class="btn btn-danger" onclick="removeBorderChargeRow(this)">-</button>
                                                          </td>
                                                      </tr>
                                                      <?php
                                                  }
                                                }
                                                ?>

                                                <!-- Additional rows will be added here -->
                                            </tbody>

                                        </table>

                                    </div>
                                  </div>
                                  <button type="button" class="btn btn-primary " onclick="addBorderChargeRow()">+</button> &nbsp;&nbsp;Add New Row
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="total">Total (AED):</label>
                                    <input type="text" class="form-control" id="total" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="total">&nbsp;</label> <br>
                                    <button type="button" class="btn btn-primary" onclick="calculateTotal()">Calculate Total</button>
                                </div>
                                <div class="col-md-3"></div>

                            </div>
                            <br>
                            <div class="row">
                              <div class="col-md-12">
                                
                                  <label>Special Note</label>
                                  <textarea id="special_note" class="form-control" rows="3" placeholder="Enter Special note"><?= $row['special_note'] ?></textarea>
                                </div>

                              </div>
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="col-md-3">
                                    <button type="button" class="btn btn-danger form-control" onclick="addDropdownData(false)">Print & Generate Invoice</button>
                                  </div>
                                  <div class="col-md-3">
                                    <button type="button" class="btn btn-success form-control" onclick="addDropdownData()">Save Shipment</button>
                                  </div>
                                </div>
                              </div>

                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Attachments</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="attachments">Existing Attachments</label>
                                    <ul id="attachment-list">
                                        <?php
                                        $attachments_sql = "SELECT * FROM shipment_attachments WHERE shipment_id='$invoice_id'";
                                        $attachments_result = $conn->query($attachments_sql);
                                        if ($attachments_result->num_rows > 0) {
                                            while ($attachment = $attachments_result->fetch_assoc()) {
                                                echo "<li style='margin-bottom: 5px;'>
                                                        <a href='./uploads/{$attachment['attachment_path']}' target='_blank' style='margin-right: 10px;'>{$attachment['attachment_path']}</a>
                                                        <button type='button' class='btn btn-danger btn-sm' onclick='deleteAttachment({$attachment['id']})'>Delete</button>
                                                      </li>";
                                            }
                                        } else {
                                            echo "<li>No attachments found.</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label for="attachments">Add New Files</label>
                                    <input type="file" id="attachments" name="attachments[]" multiple class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xlsx,.xls">
                                    <small class="text-muted">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF, XLSX, XLS</small>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="uploadAttachments()">
                                    <i class="fa fa-upload"></i> Upload Files
                                </button>
                                <div id="uploadStatus"></div>
                            </div>
                        </div>
                        <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Shipment Costs</h3>
                    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-add_customer">+ Add Customer/Supplier</button>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="shipmentCostsTable">
                            <thead>
                                <tr>
                                    <th>Supplier/Customer</th>
                                    <th>Description</th>
                                    <th>Tag</th>
                                    <th>Currency</th>
                                    <th>Rate per Unit</th>
                                    <th>Qty/UoM</th>
                                    <th>Taxable (%)</th>
                                    <th>Amount</th>
                                    <th>Amount(AED)</th>
                                    <th>Amount(USD)</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="shipmentCostsTableBody">
                                <!-- JS will populate rows -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary" id="addShipmentCostRowBtn">+ Add Row</button>
                    <button type="button" class="btn btn-success" id="saveShipmentCostsBtn">Update Costs</button>
                    <button type="button" class="btn btn-info" id="printCostsBtn">Print Costs Invoice</button>  
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-3">
                        <label for="shipmentCostsTotalInput">Total Costs (AED):</label>
                        <input type="text" class="form-control" id="shipmentCostsTotalInput" value="0.00" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-primary" onclick="calculateTotalCosts()">Calculate Total</button>
                    </div>
                    <div class="col-md-6"></div>
                </div>
            </div>
                    </div>

              </div>


            </div>
            
        </section><!-- /.content -->
      </div>

    <?php }} ?>
    </div><!-- /.content-wrapper -->
<?php include 'shipment_modals.php' ?>
<script src="./js/shipment_costs.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <iframe id="printFrame" style="display:none;"></iframe>

<script src="./scriptprint.js" type="text/javascript"></script>

<script type="text/javascript">


// Attatchments handling section
function deleteAttachment(attachmentId) {
    if (confirm('Are you sure you want to delete this attachment?')) {
        fetch('./backend/delete_attachment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: attachmentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Attachment deleted successfully.');
                location.reload();
            } else {
                alert('Error deleting attachment.');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
function uploadAttachments() {
    const shipmentId = document.getElementById('shipment_id').value;
    const files = document.getElementById('attachments').files;

    if (!shipmentId) {
        alert('Please generate a Shipment ID before uploading files.');
        return;
    }

    if (files.length === 0) {
        alert('Please select files to upload.');
        return;
    }

    const formData = new FormData();
    formData.append('shipment_id', shipmentId);

    for (let i = 0; i < files.length; i++) {
        formData.append('attachments[]', files[i]);
    }

    // Show loading bar and disable upload button
    const uploadButton = document.querySelector('button[onclick="uploadAttachments()"]');
    const uploadStatus = document.getElementById('uploadStatus');
    
    uploadButton.disabled = true;
    uploadButton.textContent = 'Uploading...';
    
    uploadStatus.innerHTML = `
        <div style="margin-top: 10px;">
            <div style="background-color: #f0f0f0; border-radius: 10px; overflow: hidden;">
                <div id="uploadProgressBar" style="
                    width: 0%; 
                    height: 20px; 
                    background: linear-gradient(90deg, #4CAF50, #45a049); 
                    transition: width 0.3s ease;
                    border-radius: 10px;
                "></div>
            </div>
            <div id="uploadProgressText" style="text-align: center; margin-top: 5px; font-size: 14px;">
                Preparing upload...
            </div>
        </div>
    `;

    const xhr = new XMLHttpRequest();
    
    // Track upload progress
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            const progressBar = document.getElementById('uploadProgressBar');
            const progressText = document.getElementById('uploadProgressText');
            
            progressBar.style.width = percentComplete + '%';
            progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
        }
    });

    xhr.open('POST', './backend/upload_attachments.php', true);

    xhr.onload = function () {
        // Re-enable upload button
        uploadButton.disabled = false;
        uploadButton.textContent = 'Upload Files';
        
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                uploadStatus.innerHTML = `
                    <div style="color: green; margin-top: 10px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">
                        <strong>✓ Success!</strong> Files uploaded successfully.
                    </div>
                `;
                // Clear the file input
                document.getElementById('attachments').value = '';
                // Refresh the page to show new attachments
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                uploadStatus.innerHTML = `
                    <div style="color: red; margin-top: 10px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">
                        <strong>✗ Error:</strong> ${response.message}
                    </div>
                `;
            }
        } else {
            uploadStatus.innerHTML = `
                <div style="color: red; margin-top: 10px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">
                    <strong>✗ Error:</strong> An error occurred while uploading files.
                </div>
            `;
        }
    };

    xhr.onerror = function () {
        // Re-enable upload button
        uploadButton.disabled = false;
        uploadButton.textContent = 'Upload Files';
        
        uploadStatus.innerHTML = `
            <div style="color: red; margin-top: 10px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">
                <strong>✗ Error:</strong> Network error occurred while uploading files.
            </div>
        `;
    };

    xhr.send(formData);
}
// End of file attachments section


function formatDate(dateString) {
    if(!dateString){
      return '';
    }
    const date = new Date(dateString);
    let day = date.getDate();
    let month = date.getMonth() + 1; // Months are zero-indexed
    const year = date.getFullYear();

    // Add leading zero to day and month if they are less than 10
    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;

    return `${day}/${month}/${year}`;
}
function generateInvoice(data) {
    try {
      const customerSelect = document.getElementById('customer_id');
      const selectedCustomerText = customerSelect.options[customerSelect.selectedIndex].text;
      const shipperSelect = document.getElementById('supplier_id');
      const selectedShipperText = shipperSelect.options[shipperSelect.selectedIndex].text;
      const vat_number = document.getElementById('customerVATnumber').value;
      const originalTitle = document.title;
      document.title = data.invoice_number;

      let printContent = `
            <html>
            <head>
                <style>
                    body {
                    font-family: Arial, sans-serif;
                    font-size: 12px; /* Decrease the font size */
                    line-height: 1.2; /* Decrease the line spacing */
                    background-image: url('invoicebg9.jpg');
                    background-size: cover; /* This ensures the image covers the whole background */
                    background-position: center; /* Centers the image */
                    background-repeat: no-repeat; /* Prevents image repetition */
                    }
                    .invoice {
                        width: 100%;
                    }
                    .section {
                        margin-bottom: 20px;
                    }
                    .header {
                        text-align: center;
                        margin: 20px;
                    }
                    .header img {
                      max-width: 150px;
                      margin: 10px;
                    }
                    .section .column {
                        float: left;
                        width: Auto;
                        margin-right: 2%;
                    }
                    .special-1{
                      float: left;
                      width: 17%;
                      margin-right: 2%;
                    }
                    .special-2{
                      float: left;
                      width: 29%;
                      margin-right: 2%;
                    }
                    .section .column:last-child {
                        margin-right: 0;
                    }
                    .clear {
                        clear: both;
                    }
                    .charges-table, .charges-table th, .charges-table td {
                        border: 0.5px solid #000;
                        border-collapse: collapse;
                        padding: 4px;
                        text-align: right;
                        font-size: 10px;
                    }
                    .charges-table{
                      width:100%;
                    }
                    p {
                        margin: 5px 0; /* Decrease the top and bottom margin */
                    }
                    .prices {
                      text-align: right;
                    }
                    .charges-table th {
                        background-color: #f2f2f2;
                    }

                    .details {
                      font-size: 10px;
                    }
                    @media print {
                      @page { margin: 0;
                      margin-top: -3; }
                      body { margin: 1.2cm;
                        margin-top: 4cm;
                       }
                    }
                </style>
            </head>
            <body>
                <div class="invoice">
                    <div class="header">

                    </div>

                    <div class="section" >

                        <div class=" special-1">

                            <p>Customer</p>
                            <p>Shipper</p>
                            <p>Consignee</p>
                            <p>Port of Origin</p>
                            <p>Final Destination</p>
                            <p>Vessel</p>
                            <p>Voyage NUmber</p>
                            <p>Shipper Ref. No</p>
                            <p>ETD</p>
                            <p>ETA</p>
                            <p>Bill of Entry No</p>

                        </div>
                        <div class=" special-2">

                            <p>: ${selectedCustomerText}</p>
                            <p>: ${selectedShipperText}</p>
                            <p>: ${data.consignee || ''}</p>
                            <p>: ${data.port_origin}</p>
                            <p>: ${data.port_destination}</p>
                            <p>: ${data.vessel}</p>
                            <p>: ${data.voyage_number}</p>
                            <p>: ${data.shipper_reference}</p>
                            <p>: ${formatDate(data.etdDeparture)} ${formatDate(data.etdDeparture_2)} ${formatDate(data.etdDeparture_3)}</p>
                            <p>: ${formatDate(data.etd_arrival)}</p>
                            <p>: ${data.bill_of_entry}</p>

                        </div>

                        <div style="width:20%;" class=" special-1">
                            <p>Our VAT Number</p>
                            <p>Customer VAT Number</p>
                            <p>Invoice Number</p>
                            <p>Invoice Date</p>
                            <p>Payment Due Date</p>
                            <p>Job Number</p>
                            <p>Job Date</p>
                            <p>Master BL Number</p>
                            <p>House BL Number</p>
                            <p>Number of Packs</p>
                            <p>Weight (Kgs)</p>
                            <p>Volume (CBM)</p>
                        </div>
                        <div style="width:25%;"  class=" special-2">
                            <p style="color:red;">: 104311454300003</p>
                            <p>: ${vat_number}</p>
                            <p>: ${data.invoice_number}</p>
                            <p>: ${formatDate(data.invoice_date)}</p>
                            <p>: ${formatDate(data.payment_date)}</p>
                            <p>: ${data.job_number}</p>
                            <p>: ${formatDate(data.job_date)}</p>
                            <p>: ${data.bl_number}</p>
                            <p>: ${data.house_bl_number}</p>
                            <p>: ${data.units}</p>
                            <p>: ${data.weight}</p>
                            <p>: H: ${data.height} | W: ${data.width} | L: ${data.length}</p>
                        </div>



                        <div class="clear"></div>
                    </div>

                    <div class="section">
                        <table class="charges-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Description</th>
                                    <th>Curr</th>
                                    <th>Rate/Unit</th>
                                    <th>Units</th>
                                    <th>Amount(Sel Curr)</th>
                                    <th>Rate in AED</th>
                                    <th>Rate in USD</th>
                                    <th>Taxable Amt</th>
                                    <th>Total Amount(AED)</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    const top_note = document.getElementById('note').value;
                    if(top_note){
                      printContent += `
                              <p>
                                Note: ${top_note}
                              </p>
                          `;
                    }

                    const borderChargeRows = document.querySelectorAll('.border-charge-row');
                    borderChargeRows.forEach(row => {
                      const index = row.dataset.index;
                      const chargeDescription = document.getElementById(`chargeDescription${index}`).value;
                      const currencyRateElement = document.getElementById(`currencyRate${index}`);
                      const selectedCurrency = currencyRateElement.options[currencyRateElement.selectedIndex].text;
                      // const currencyRate = selectedCurrency.split("\u00A0\u00A0")[1];
                      const rate = document.getElementById(`rate${index}`).value;
                      const quantity = document.getElementById(`quantityUoM${index}`).value;
                      const amount = document.getElementById(`amount${index}`).value;
                      const amountAED = document.getElementById(`amountAED${index}`).value;
                      const amountUSD = document.getElementById(`amountUSD${index}`).value;
                      const taxableValues = document.getElementById(`taxableValues${index}`).value;
                      const totalAmount = document.getElementById(`totalAmount${index}`).value;

                      printContent += `
                              <tr>
                                  <td style="text-align: left">${index}</td>
                                  <td style="text-align: left">${chargeDescription}</td>
                                  <td style="text-align: left">${selectedCurrency}</td>
                                  <td class="prices">${rate}</td>
                                  <td style="text-align: center">${quantity}</td>
                                  <td class="prices">${amount}</td>
                                  <td class="prices">${amountAED}</td>
                                  <td class="prices">${amountUSD}</td>
                                  <td>${taxableValues}</td>
                                  <td class="prices">${totalAmount}</td>
                              </tr>

                          `;
                      });

                    printContent += `
                            <tr>
                                <td style="text-align: left"></td>
                                <td style="text-align: left"><strong>Total (AED):</strong></td>
                                <td style="text-align: left"></td>
                                <td class="prices"></td>
                                <td style="text-align: center"></td>
                                <td class="prices"></td>
                                <td class="prices"></td>
                                <td class="prices"></td>
                                <td></td>
                                <td class="prices">${data.borderCharges.reduce((sum, charge) => sum + parseFloat(charge.totalAmount), 0).toFixed(2)}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section">
                        <p>Amount in Words: AED ${numberToWords(parseFloat(document.getElementById('total').value))}</p>
                    </div>

                    <div class="section">
                    <p>Container No. Type & Vehicle No.</p>`;
                    let vehicleData = {}; // Object to hold vehicle types and their corresponding numbers

                      const vehicleRows = document.querySelectorAll('.vehicle-row');

                      vehicleRows.forEach(row => {
                          const index = row.dataset.index;
                          const selectElement = document.getElementById(`vehicle_id${index}`);
                          let vehicleId = '';

                          if (selectElement.selectedIndex >= 0) {
                              vehicleId = selectElement.options[selectElement.selectedIndex].text;
                          }
                          const equipmentNumber = document.getElementById(`vehicle_num${index}`).value;

                          // If vehicleId already exists in vehicleData, add equipmentNumber to the array
                          if (vehicleData[vehicleId]) {
                              vehicleData[vehicleId].push(equipmentNumber);
                          } else {
                              // Otherwise, create a new entry
                              vehicleData[vehicleId] = [equipmentNumber];
                          }
                      });


                      Object.keys(vehicleData).forEach(vehicleId => {
                          const numbers = vehicleData[vehicleId];
                          const count = numbers.length > 1 ? `${numbers.length} x ` : ''; // Check if there are multiple entries

                          printContent += `${count}${vehicleId} - ${numbers.join(', ')}<br>`;
                      });

                      const special_note = document.getElementById('special_note').value;
                      if(special_note){
                        printContent += `
                                <p>
                                  Note: ${special_note}
                                </p>
                            `;
                      }
                      printContent += `
                              </div>

                      </body>
                      </html>
                  `;
            var img = new Image();
        img.src = "invoicebg9.jpg";
        img.onload = function() {
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
          document.title = originalTitle;
        }

    } catch (error) {
        console.error('Error generating invoice:', error);
    }
}
function numberToWords(number) {
    const units = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
    const teens = ["", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    const tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
    const thousands = ["", "Thousand", "Million", "Billion"];

    if (number === 0) return "Zero Dirhams";

    let word = '';

    function convert(num, idx) {
        if (num === 0) return '';
        let str = '';

        if (num > 99) {
            str += units[Math.floor(num / 100)] + ' Hundred ';
            num %= 100;
        }

        if (num > 10 && num < 20) {
            str += teens[num - 10] + ' ';
        } else {
            str += tens[Math.floor(num / 10)] + ' ';
            str += units[num % 10] + ' ';
        }

        return str + thousands[idx] + ' ';
    }

    let integerPart = Math.floor(number);
    let decimalPart = Math.round((number - integerPart) * 100);

    let thousandIndex = 0;

    while (integerPart > 0) {
        const numChunk = integerPart % 1000;
        if (numChunk > 0) {
            word = convert(numChunk, thousandIndex) + word;
        }
        integerPart = Math.floor(integerPart / 1000);
        thousandIndex++;
    }

    word = word.trim() + " Dirhams";

    if (decimalPart > 0) {
        word += " and " + convert(decimalPart, 0).trim() + " Fils";
    }

    return word + " only";
}


function addDropdownData(only_save=true) {
  const loading_street = document.getElementById('loading_street').value;
  const port_origin = document.getElementById('port_origin').value;
  const port_destination = document.getElementById('port_destination').value;
  const unloading_street = document.getElementById('unloading_street').value;
  const warehouse = document.getElementById('warehouse').value;
  const item_desc = document.getElementById('item_desc').value;

  if (typeof saveShipmentCosts === "function") {
        saveShipmentCosts();
    }

  const taxableValues = Array.from(document.querySelectorAll('input[name="taxableValues"]')).map(input => input.value);
  const chargeDescription = Array.from(document.querySelectorAll('input[name="chargeDescription"]')).map(input => input.value);
  const vehicle_num = Array.from(document.querySelectorAll('input[name="vehicle_num"]')).map(input => input.value);

    var data = {
        loading_street: loading_street,
        port_origin: port_origin,
        item_desc: item_desc,
        port_destination: port_destination,
        unloading_street: unloading_street,
        warehouse: warehouse,
        taxableValues: taxableValues,
        chargeDescription: chargeDescription,
        vehicle_num: vehicle_num,
    };
    console.log(data);
    // Collect data from all dynamic input fields
    // document.querySelectorAll('input[name="taxableValues"]').forEach(function(input) {
    //     data.taxableValues.push(input.value || ''); // Push empty string if value is falsy
    // });
    // document.querySelectorAll('input[name="chargeDescription"]').forEach(function(input) {
    //     data.chargeDescriptions.push(input.value || '');
    // });
    // document.querySelectorAll('input[name="vehicle_num"]').forEach(function(input) {
    //     data.vehicleNumbers.push(input.value || '');
    // });

    console.log(JSON.stringify(data)); // Log the JSON data

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                console.log('done adding data');
                // printInvoice();
                collectFormData(only_save)
            } else {
                // Handle error
                console.error("Error adding data: " + this.status);
                alert("Error adding data. Please try again.");
            }
        }
    };

    xhr.open("POST", "./backend/add_dropdown_data.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(data));
}


document.addEventListener('DOMContentLoaded', function() {

  //load shipment costs
  var shipment_id = document.getElementById('shipment_id').value;
  loadShipmentCosts(shipment_id);
  console.log('Shipment ID:', shipment_id);

  const shippingModes = document.getElementsByName('shipping_mode');
  const importExport = document.getElementsByName('import_export');
  const shipmentIdField = document.getElementById('shipment_id');
  const job_number = document.getElementById('job_number');
  const invoice_number = document.getElementById('invoice_number');



  function generateShipmentId() {
    const selectedMode = Array.from(shippingModes).find(radio => radio.checked);
    const selectedImportExport = Array.from(importExport).find(radio => radio.checked);

    if (selectedMode && selectedImportExport) {
      const mode = selectedMode.value;
      const impExp = selectedImportExport.value;

      fetch('./backend/get_last_shipment_id.php')
        .then(response => response.json())
        .then(data => {
          const lastId = data.lastId || 0;
          const newId = parseInt(lastId) + 1;
          shipmentIdField.value = `${impExp}-${mode}-${newId.toString().padStart(3, '0')}`;
          job_number.value = `${impExp}-${mode}-${newId.toString().padStart(3, '0')}`;
          invoice_number.value = `${impExp}-${mode}-IN${newId.toString().padStart(3, '0')}`;
        })
        .catch(error => console.error('Error fetching last shipment ID:', error));
    }
  }

  shippingModes.forEach(radio => radio.addEventListener('change', generateShipmentId));
  importExport.forEach(radio => radio.addEventListener('change', generateShipmentId));
});



function collectFormData(only_save=true) {
  calculateTotal()
  try {
    // Collect form data
    const shipmentId = document.getElementById('shipment_id').value;
    const handled_by = document.getElementById('handled_by').value;
    const job_date = document.getElementById('job_date').value;
    const invoice_date = document.getElementById('invoice_date').value;
    const payment_date = document.getElementById('payment_date').value;

    const invoice_number = document.getElementById('invoice_number').value;
    const job_number = document.getElementById('job_number').value;
    const bl_number = document.getElementById('bl_number').value;
    const bl_type = document.getElementById('bl_type').value;
    const house_bl_number = document.getElementById('house_bl_number').value;
    const bill_of_entry = document.getElementById('bill_of_entry').value;
    const note = document.getElementById('note').value;

    const customer_id = document.getElementById('customer_id').value;
    const consignee = document.getElementById('consignee').value;

    const supplier_id = document.getElementById('supplier_id').value;
    const shipper_reference = document.getElementById('shipper_reference').value;
    const vessel = document.getElementById('vessel').value;
    const voyage_number = document.getElementById('voyage_number').value;

    const itemDesc = document.getElementById('item_desc').value;
    const weight = document.getElementById('weight').value;
    const height = document.getElementById('height').value;
    const width = document.getElementById('width').value;
    const length = document.getElementById('length').value;

    const equipments = Array.from(document.querySelectorAll('.vehicle-row')).map((row, index) => {
        const idx = row.dataset.index;
        return {
            equipment: row.querySelector(`select[name="vehicle_id${idx}"]`)?.value || '',
            eq_number: row.querySelector(`input[name="vehicle_num${idx}"]`)?.value || '',
            description: row.querySelector(`input[name="equipment_desc${idx}"]`)?.value || '',
            weight: row.querySelector(`input[name="equipment_weight${idx}"]`)?.value || '',
            packs: row.querySelector(`input[name="equipment_packs${idx}"]`)?.value || ''
        };
    });

    const units = document.getElementById('units').value;

    const loadingCountry = document.getElementById('loading_country').value;
    const loadingRegion = document.getElementById('loading_region').value;
    const loading_street = document.getElementById('loading_street').value;
    const port_origin = document.getElementById('port_origin').value;
    const warehouse = document.getElementById('warehouse').value;
    const etdDeparture = document.getElementById('etd_departure').value;
    const etdDeparture_2 = document.getElementById('etd_departure_2').value;
    const etdDeparture_3 = document.getElementById('etd_departure_3').value;
    const gate_in = document.getElementById('gate_in').value;
    const gate_out = document.getElementById('gate_out').value;

    const unloadingCountry = document.getElementById('unloading_country').value;
    const unloadingRegion = document.getElementById('unloading_region').value;
    const port_destination = document.getElementById('port_destination').value;
    const unloading_street = document.getElementById('unloading_street').value;
    const etd_arrival = document.getElementById('etd_arrival').value;
    const special_note = document.getElementById('special_note').value;

    // Collect border charges
    var borderCharges = Array.from(document.querySelectorAll('.border-charge-row')).map(row => {
      const description = row.querySelector('input[name^="chargeDescription"]');
      const rate = row.querySelector('input[name^="rate"]');
      const currency = row.querySelector('select[name^="currencyRate"]');
      const quantity = row.querySelector('input[name^="quantityUoM"]');
      const taxable = row.querySelector('input[name^="taxableValues"]');
      const amount = row.querySelector('input[name^="amount"]');
      const amountAED = row.querySelector('input[name^="amountAED"]');
      const amountUSD = row.querySelector('input[name^="amountUSD"]');
      const totalAmount = row.querySelector('input[name^="totalAmount"]');
      return {
          description: description ? description.value : '',
          rate: rate ? rate.value : '',
          currency: currency ? currency.value : '',
          quantity: quantity ? quantity.value : '',
          taxable: taxable ? taxable.value : '',
          amount: amount ? amount.value : '',
          amountAED: amountAED ? amountAED.value : '',
          amountUSD: amountUSD ? amountUSD.value : '',
          totalAmount: totalAmount ? totalAmount.value : '',
      };
    });

    // if (shipmentId === '') {
    //     alert('Enter Shipment ID');
    //     return;
    // }

    // Prepare data to send
    var data = {
        shipmentId: shipmentId,
        handled_by: handled_by,
        job_date: job_date,
        invoice_date: invoice_date,
        payment_date:payment_date,

        invoice_number:invoice_number,
        job_number: job_number,
        bl_number: bl_number,
        bl_type: bl_type,
        house_bl_number:house_bl_number,
        bill_of_entry:bill_of_entry,
        note:note,
        special_note:special_note,

        customer_id: customer_id,
        consignee: consignee,

        supplier_id: supplier_id,
        shipper_reference:shipper_reference,
        vessel:vessel,
        voyage_number:voyage_number,

        item_desc: itemDesc, // Use itemDesc variable for consistency
        weight: weight,
        height:height,
        width:width,
        length:length,

        equipments: equipments,
        units: units,

        loadingCountry: loadingCountry,
        loadingRegion: loadingRegion,
        loading_street: loading_street,
        port_origin: port_origin,
        warehouse: warehouse,
        etdDeparture: etdDeparture,
        etdDeparture_2: etdDeparture_2,
        etdDeparture_3: etdDeparture_3,
        gate_in: gate_in,
        gate_out, gate_out,

        unloadingCountry: unloadingCountry,
        unloadingRegion: unloadingRegion,
        port_destination: port_destination,
        unloading_street: unloading_street,
        etd_arrival: etd_arrival,

        borderCharges: borderCharges,
    };
    console.log(data);
    console.log(JSON.stringify(data));
    // Create an XMLHttpRequest object
    const xhr = new XMLHttpRequest();


    // Handle the response
    xhr.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                console.log('done adding shipment data');
                if (!only_save) {
                  generateInvoice(data); // Only call generateInvoice if only_save is false
                }else{
                  alert('Shipment data added successfully!');
                }
            }
            else if (this.status == 409) {
              alert("Shipment ID already Exists");

            }
             else {
                // Handle error
                console.error("Error adding data: " + this.status);
                alert("Error adding Shipment data. Please try again.");
            }
        }
    };

    // Send the request with the data
    xhr.open('POST', './backend/insert_shipment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));
  } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while inserting the shipment');
  }
}

// Button for easy saving
document.addEventListener('keydown', function (event) {
    if (event.ctrlKey && event.key === 's') {
        event.preventDefault(); // Prevent the default browser save action
        addDropdownData(); // Call the collectFormData function
    }
});

const currencyRateElement =  document.getElementById('globalCurrency');

let aedValue = null;
let usdValue = null;

for (let i = 0; i < currencyRateElement.options.length; i++) {
    let optionText = currencyRateElement.options[i].text;

    // Check if the option contains 'AED' at index 0 after splitting
    if (optionText.split("\u00A0\u00A0")[0] === "AED") {
        aedValue = optionText.split("\u00A0\u00A0")[1];
         // Exit the loop once the correct option is found
    }
    if (optionText.split("\u00A0\u00A0")[0] === "USD") {
        usdValue = optionText.split("\u00A0\u00A0")[1];
         // Exit the loop once the correct option is found
    }
}

    function calculateTotal() {

        let grandTotal = 0;


        document.querySelectorAll('.border-charge-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('[name^="quantityUoM"]').value) || 0;
            const rate = parseFloat(row.querySelector('[name^="rate"]').value) || 0;
            const tax = parseFloat(row.querySelector('[name^="taxableValues"]').value) || 0;
            const selcurrencyRateElement =  row.querySelector('select');
            const selectedCurrency = currencyRateElement.options[selcurrencyRateElement.selectedIndex].text;
            const currencyRate = selectedCurrency.split("\u00A0\u00A0")[1];




                          // Calculate amount
            const amount = quantity * rate
            row.querySelector('[name^="amount"]').value = amount.toFixed(2);

            // Convert to AED

            const amountAED =amount * parseFloat(aedValue) / parseFloat(currencyRate);
            row.querySelector('[name^="amountAED"]').value = amountAED.toFixed(2);

            const amountUSD =amount * parseFloat(usdValue) / parseFloat(currencyRate);
            row.querySelector('[name^="amountUSD"]').value = amountUSD.toFixed(2);

            const totalAmount = amountAED * (1 + tax/100);
            // Set total amount
            row.querySelector('[name^="totalAmount"]').value = totalAmount.toFixed(2);



            // Add to grand total
            grandTotal += totalAmount ;
        });

        // Display the grand total
        document.getElementById('total').value = grandTotal.toFixed(2);
    }

    let borderChargeCount = document.querySelectorAll('.border-charge-row').length;

    function addBorderChargeRow() {
        borderChargeCount++;

        // Create a new row
        const newRow = document.createElement('tr');
        newRow.className = 'border-charge-row';
        newRow.dataset.index = borderChargeCount;
        newRow.innerHTML = `
            <tr class="border-charge-row" data-index="${borderChargeCount}">
              <td>
                <input list="dropdown-chargeDescription${borderChargeCount}" class="form-control charge-desc" name="chargeDescription" id="chargeDescription${borderChargeCount}" placeholder="Enter description">
                <datalist id="dropdown-chargeDescription${borderChargeCount}">
                  <?php

                  $sql = "SELECT detail FROM charge_details";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                      // Output data of each row
                      while ($row = $result->fetch_assoc()) {
                          $item_desc = $row['detail'];
                          echo "<option value='$item_desc'></option>";
                      }
                  } else {
                      echo "0 results";
                  }
                  ?>
                </datalist>
              </td>
              <td>
                <select id="currencyRate${borderChargeCount}" name="currencyRate${borderChargeCount}" class="form-control">
                <?php

                $sql = "SELECT * FROM currencies";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $currency = $row['currency'];
                        $roe = $row['roe'];
                        echo "<option value='$id'>$currency</option>";
                    }
                } else {
                    echo "0 results";
                }
                ?>
                </select>
              </td>
              <td><input type="text" class="form-control rate-input" id="rate${borderChargeCount}" name="rate${borderChargeCount}" placeholder="Enter rate"></td>

              <td><input type="text" class="form-control" id="quantityUoM${borderChargeCount}" name="quantityUoM${borderChargeCount}" value="1"></td>
              <td>
              <input list="dropdown-taxableValues${borderChargeCount}" class="form-control taxable-value" name="taxableValues" id="taxableValues${borderChargeCount}" placeholder="Enter Value">
              <datalist id="dropdown-taxableValues${borderChargeCount}">
                <?php

                $sql = "SELECT value FROM tax_percentages";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $item_desc = $row['value'];
                        echo "<option value='$item_desc'></option>";
                    }
                } else {
                    echo "0 results";
                }
                ?>
              </datalist>
              </td>
              <td><input type="text" class="form-control" id="amount${borderChargeCount}" name="amount${borderChargeCount}" disabled></td>
              <td><input type="text" class="form-control" id="amountAED${borderChargeCount}" name="amountAED${borderChargeCount}" disabled></td>
              <td><input type="text" class="form-control" id="amountUSD${borderChargeCount}" name="amountUSD${borderChargeCount}" disabled></td>

              <td><input type="text" class="form-control" id="totalAmount${borderChargeCount}" name="totalAmount${borderChargeCount}" disabled></td>
              <td>
                <button type="button" class="btn btn-danger" onclick="removeBorderChargeRow(this)">-</button>
              </td>
            </tr>
          `;


        // Append the new row to the container
        const tbody = document.querySelector('#borderChargesContainer tbody');
        tbody.appendChild(newRow);

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

              const serviceDesc = row.querySelector('input[name^="serviceDescription"]');
              serviceDesc.id = `serviceDescription${newIndex}`;
              serviceDesc.name = `serviceDescription${newIndex}`;

              const quantityUoM = row.querySelector('input[name^="quantityUoM"]');
              quantityUoM.id = `quantityUoM${newIndex}`;
              quantityUoM.name = `quantityUoM${newIndex}`;

              const rate = row.querySelector('input[name^="rate"]');
              rate.id = `rate${newIndex}`;
              rate.name = `rate${newIndex}`;

              const currencyRate = row.querySelector('input[name^="currencyRate"]');
              currencyRate.id = `currencyRate${newIndex}`;
              currencyRate.name = `currencyRate${newIndex}`;

              const invoiceAmountFC = row.querySelector('input[name^="invoiceAmountFC"]');
              invoiceAmountFC.id = `invoiceAmountFC${newIndex}`;
              invoiceAmountFC.name = `invoiceAmountFC${newIndex}`;

              const invoiceAmountAED = row.querySelector('input[name^="invoiceAmountAED"]');
              invoiceAmountAED.id = `invoiceAmountAED${newIndex}`;
              invoiceAmountAED.name = `invoiceAmountAED${newIndex}`;

              const taxableValues = row.querySelector('input[name^="taxableValues"]');
              taxableValues.id = `taxableValues${newIndex}`;
              taxableValues.name = `taxableValues${newIndex}`;

              const vatGroup = row.querySelector('input[name^="vatGroup"]');
              vatGroup.id = `vatGroup${newIndex}`;
              vatGroup.name = `vatGroup${newIndex}`;

              const vatRate = row.querySelector('input[name^="vatRate"]');
              vatRate.id = `vatRate${newIndex}`;
              vatRate.name = `vatRate${newIndex}`;

              const vatAmount = row.querySelector('input[name^="vatAmount"]');
              vatAmount.id = `vatAmount${newIndex}`;
              vatAmount.name = `vatAmount${newIndex}`;

              const totalAmount = row.querySelector('input[name^="totalAmount"]');
              totalAmount.id = `totalAmount${newIndex}`;
              totalAmount.name = `totalAmount${newIndex}`;
          });
      }


    window.onload = function() {
        fetchSuppliers();
        fetchCustomers();
        fetchContainers();
        setTimeout(function() {
           selectLoadingAndUnloadingRegions();
       }, 500);
    };
    function selectLoadingAndUnloadingRegions() {
        // Assume these variables are passed or available globally
        var loadingCountry = "<?= $loading_country ?>";
        var loadingRegion = "<?= $loading_region ?>";
        var unloadingCountry = "<?= $unloading_country ?>";
        var unloadingRegion = "<?= $unloading_region ?>";

        // Set the values in the corresponding select elements
        if (loadingCountry) {
            document.getElementById('loading_country').value = loadingCountry;
            $('#loading_country').trigger('change'); // Trigger change for plugin
        }
        if (loadingRegion) {
            document.getElementById('loading_region').value = loadingRegion;
            $('#loading_region').trigger('change'); // Trigger change for plugin
        }
        if (unloadingCountry) {
            document.getElementById('unloading_country').value = unloadingCountry;
            $('#unloading_country').trigger('change'); // Trigger change for plugin
        }
        if (unloadingRegion) {
            document.getElementById('unloading_region').value = unloadingRegion;
            $('#unloading_region').trigger('change'); // Trigger change for plugin
        }
    }

    function selectConsignee(){
      var selectElement = document.getElementById("customer_id");
    //   if(selectElement){
    //       var selectedOption = selectElement.options[selectElement.selectedIndex].text;
    //       if(document.getElementById('consignee').value==''){
    //         document.getElementById('consignee').value = selectedOption;
    //       }
    //   }



    //   if (selectElement) {
    //     customerId = selectElement.value
    //     fetch('./backend/get_customer_vat.php', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/x-www-form-urlencoded',
    //         },
    //         body: 'customer_id=' + encodeURIComponent(customerId)
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         var vatNumber = data.vat_number || '';
    //         var vatNumberField = document.getElementById('customerVATnumber');
    //         vatNumberField.value = vatNumber;
    //     })
    //     .catch(error => console.error('Error fetching VAT number:', error));
    //   }

    }

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
                        // Check if this customer matches the PHP variable $customer
                         if (customer.customer_id == "<?= $customer ?>") {
                             option.selected = true; // Select the option
                         }

                         customerSelect.appendChild(option);
                    });
                    
                    customerId = customerSelect.value
                    fetch('./backend/get_customer_vat.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'customer_id=' + encodeURIComponent(customerId)
                    })
                    .then(response => response.json())
                    .then(data => {
                        var vatNumber = data.vat_number || '';
                        var vatNumberField = document.getElementById('customerVATnumber');
                        vatNumberField.value = vatNumber;
                    })
                    .catch(error => console.error('Error fetching VAT number:', error));
                }
            };

            xhr.open('GET', './backend/fetch_customers.php', true);
            xhr.send();
        }
        function changeCurrencyForAllRows() {
        var selectedCurrency = document.getElementById("globalCurrency").value;
        var currencyElements = document.querySelectorAll("[id^='currencyRate']");

        currencyElements.forEach(function(element) {
            element.value = selectedCurrency;
        });
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
                    // Check if this supplier matches the PHP variable $supplier
                   if (supplier.supplier_id == "<?= $supplier ?>") {
                       option.selected = true; // Select the option
                   }

                   supplierSelect.appendChild(option);
                   selectConsignee();
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

    function fetchVehicles() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var vehicles = JSON.parse(this.responseText);
                var vehicleSelects = document.querySelectorAll('select[id^="vehicle_id"]');

                vehicleSelects.forEach(function(vehicleSelect) {
                    var selectedValue = vehicleSelect.value;
                    vehicleSelect.innerHTML = ''; // Clear existing options
                    vehicles.forEach(function(vehicle) {
                        var option = document.createElement('option');
                        option.value = vehicle.vehicle_id;
                        option.textContent = vehicle.vehicle_name;
                        vehicleSelect.appendChild(option);
                    });
                    vehicleSelect.value = selectedValue;
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

    let vehicleRowCount = document.querySelectorAll('.vehicle-row').length;;

    function addVehicleRow() {
        vehicleRowCount++;
        const newRow = document.createElement('div');
        newRow.className = 'row vehicle-row';
        newRow.dataset.index = vehicleRowCount;
        newRow.innerHTML = `
            <div class="form-group col-md-3">
                <label for="vehicle_id${vehicleRowCount}">Equipment</label>
                <div class="row">
                    <div class="form-group col-md-9">
                        <select name="vehicle_id${vehicleRowCount}" id="vehicle_id${vehicleRowCount}" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <button type="button" class="btn btn-primary form-control" data-toggle="modal" data-target="#modal-add_vehicle">+</button>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="vehicle_num${vehicleRowCount}">Equipment Number</label>
                <input list="dropdown-vehicle_num${vehicleRowCount}" class="form-control eq-number" name="vehicle_num${vehicleRowCount}" id="vehicle_num${vehicleRowCount}">
                <datalist id="dropdown-vehicle_num${vehicleRowCount}"></datalist>
            </div>
            <div class="form-group col-md-2">
                <label for="equipment_desc${vehicleRowCount}">Description</label>
                <input type="text" class="form-control" name="equipment_desc${vehicleRowCount}" id="equipment_desc${vehicleRowCount}" placeholder="Description">
            </div>
            <div class="form-group col-md-2">
                <label for="equipment_weight${vehicleRowCount}">Weight</label>
                <input type="text" class="form-control" name="equipment_weight${vehicleRowCount}" id="equipment_weight${vehicleRowCount}" placeholder="Weight">
            </div>
            <div class="form-group col-md-1">
                <label for="equipment_packs${vehicleRowCount}">Packs</label>
                <input type="text" class="form-control" name="equipment_packs${vehicleRowCount}" id="equipment_packs${vehicleRowCount}" placeholder="Packs">
            </div>
            <div class="form-group col-md-1">
                <label for="removeButton${vehicleRowCount}">Remove</label>
                <button type="button" class="btn btn-danger form-control" onclick="removeVehicleRow(this)">-</button>
            </div>
        `;
        document.getElementById('vehicleContainer').appendChild(newRow);
        updateVehicleLabels();
        fetchVehicles();
    }

    function removeVehicleRow(button) {
        const row = button.parentElement.parentElement;
        row.remove();
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

            const numberLabel = row.querySelector('label[for^="vehicle_num"]');
            numberLabel.setAttribute('for', `vehicle_num${newIndex}`);
            const numberInput = row.querySelector('input[id^="vehicle_num"]');
            numberInput.id = `vehicle_num${newIndex}`;
            numberInput.name = `vehicle_num${newIndex}`;

            const descLabel = row.querySelector('label[for^="equipment_desc"]');
            descLabel.setAttribute('for', `equipment_desc${newIndex}`);
            const descInput = row.querySelector('input[id^="equipment_desc"]');
            descInput.id = `equipment_desc${newIndex}`;
            descInput.name = `equipment_desc${newIndex}`;

            const weightLabel = row.querySelector('label[for^="equipment_weight"]');
            weightLabel.setAttribute('for', `equipment_weight${newIndex}`);
            const weightInput = row.querySelector('input[id^="equipment_weight"]');
            weightInput.id = `equipment_weight${newIndex}`;
            weightInput.name = `equipment_weight${newIndex}`;

            const packsLabel = row.querySelector('label[for^="equipment_packs"]');
            packsLabel.setAttribute('for', `equipment_packs${newIndex}`);
            const packsInput = row.querySelector('input[id^="equipment_packs"]');
            packsInput.id = `equipment_packs${newIndex}`;
            packsInput.name = `equipment_packs${newIndex}`;

            const removeLabel = row.querySelector('label[for^="removeButton"]');
            removeLabel.setAttribute('for', `removeButton${newIndex}`);
            const removeButton = row.querySelector('button[onclick^="removeVehicleRow"]');
            removeButton.id = `removeButton${newIndex}`;
        });
    }

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

        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, clear the form, close the modal, and show a success message
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

        // Send the form data
        xhr.send(formData);
    }

    function addCustomer() {
        var form = document.getElementById('addCustomerForm');
        var formData = new FormData(form);

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

        // Send the form data
        xhr.send(formData);
    }

    function addDriver() {
        var form = document.getElementById('addDriverForm');
        var formData = new FormData(form);

        // Include the supplier_id in the FormData
        var supplierSelect = document.getElementById('supplier_id_modal');
        var supplierId = supplierSelect.value;
        formData.append('supplier_id', supplierId);

        // Send data to the backend using AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    // Success, clear the form, close the modal, and show a success message
                    form.reset();
                    $('#modal-add_driver').modal('hide');
                    alert('Driver added successfully.');
                    fetchDrivers()
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

        // Send the form data
        xhr.send(formData);

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


    <footer class="main-footer">

          <strong>Designed and Developed by <a href="#">Zeeshutterz & Infinite Coding</a></strong>
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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- page script -->
        <script src="./scriptprint.js" type="text/javascript"></script>
        <script src="./scriptedit.js" type="text/javascript"></script>
