
    <!-- MODALS -->
    <div class="modal fade" id="modal-add_supplier">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add Shipper</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="addSupplierForm" enctype="multipart/form-data">
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
              <div class="form-group">
                <label for="supplierJoinDate">Join Date</label>
                <input type="date" class="form-control" id="supplierJoinDate" name="join_date" required>
              </div>
              <div class="form-group">
                <label for="supplierExpiryDate">Expiry Date</label>
                <input type="date" class="form-control" id="supplierExpiryDate" name="expiry_date" required>
              </div>
              <div class="form-group">
                <label for="supplierAttachment">Attachment</label>
                <input type="file" class="form-control" id="supplierAttachment" name="attachment">
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
            <form id="addCustomerForm" enctype="multipart/form-data">
              
              <label>Role</label>
              <select name="role" class="form-control">
                  <option selected value="customer">Customer</option>
                  <option value="supplier">Supplier</option>
                  <option value="both">Both</option>
              </select>
              <div class="form-group">
                <label for="customerName">Name</label>
                <input type="text" class="form-control" id="customerName" name="name">
              </div>
              <div class="form-group">
                <label for="customerLocation">Location</label>
                <input type="text" class="form-control" id="customerLocation" name="location">
              </div>
              <div class="form-group">
                <label for="customerPhone">Phone</label>
                <input type="text" class="form-control" id="customerPhone" name="phone">
              </div>
              <div class="form-group">
                <label for="customerPhone">Phone (Optional)</label>
                <input type="text" class="form-control" id="phone_optional" name="phone_optional">
              </div>
              <div class="form-group">
                <label for="customerEmail">Email</label>
                <input type="email" class="form-control" id="customerEmail" name="email" required>
              </div>
              <div class="form-group">
                <label for="customerJoinDate">Join Date</label>
                <input type="date" class="form-control" id="customerJoinDate" name="join_date" required>
              </div>
              <div class="form-group">
                <label for="customerExpiryDate">Expiry Date</label>
                <input type="date" class="form-control" id="customerExpiryDate" name="expiry_date" required>
              </div>
              <div class="form-group">
                <label for="">VAT Number</label>
                <input type="text" class="form-control" id="vat_number" name="vat_number" required>
              </div>
              <div class="form-group">
                <label for="customerAttachment">Attachment</label>
                <input type="file" multiple class="form-control" id="customerAttachment" name="attachment[]">
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
              <form id="addDriverForm" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="">Carrier</label>
                      <select id="supplier_id_modal" class="form-control">

                      </select>
                    </div>
                    </div>
                    <div class="col-md-6">
                      <label for="">Type</label>
                      <select name="driver_type" id="driver_type" class="form-control">
                        <option value="Driver">Driver</option>
                        <option value="Vessel">Vessel</option>
                        <option value="Aircraft">Aircraft</option>
                        <option value="Voyage">Voyage</option>
                      </select>
                    </div>
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
                <div class="form-group">
                  <label for="driverAttachment">Attachment</label>
                  <input type="file" class="form-control" id="driverAttachment" name="attachment">
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
