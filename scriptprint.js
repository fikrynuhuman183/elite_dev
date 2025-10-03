function printInvoice(data){

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
            street: document.getElementById('loading_street').value,
            port_origin: document.getElementById('port_origin').value,
            warehouse: document.getElementById('warehouse').value,
            etd_departure: document.getElementById('etd_departure').value,
        };

        const unloadingDetails = {
            country: document.getElementById('unloading_country').value,
            region: document.getElementById('unloading_region').value,
            street: document.getElementById('unloading_street').value,
            port_destination: document.getElementById('port_destination').value,
            etd_arrival: document.getElementById('etd_arrival').value,
        };

        const units = document.getElementById('units').value;


        let printContent = `
            <html>
            <head>
                <title>Shipment Details</title>
                <style>
                    @page {
                        size: A4;
                        margin: 5mm 5mm 10mm 5mm; /* Increased top margin, reduced bottom margin, and adjusted left and right margins */
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px; /* Smaller font size */
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        padding: 0 5mm; /* Match left and right page margins */
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
                        font-size:12px
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
                    <div><p><strong>Shipment ID:</strong> ${data.shipment_id}</p></div>
                    <div><p><strong>Handled By:</strong> ${document.getElementById('handled_by').selectedOptions[0].text}</p></div>
                </div>
            </div>
        `;

        // Section: Customer and Consignee
        printContent += `
            <div class="section">
                <h4>Customer and Consignee</h4>
                <div class="two-column">
                    <p><strong>Customer:</strong> ${customer.name}</p>
                    <p><strong>Phone:</strong> ${customer.phone}</p>
                    <p><strong>Email:</strong> ${customer.email}</p>
                    <p><strong>Address:</strong> ${customer.address}</p>
                    <p><strong>VAT No:</strong> ${customer.vat_number}</p>
                    <div><p><strong>Consignee:</strong> ${document.getElementById('consignee').value}</p></div>
                </div>
            </div>
        `;

        // Section: Supplier and Driver
        printContent += `
            <div class="section">
                <h4>Driver</h4>
                <div class="two-column">
                    <div><p><strong>Type:</strong> ${driver.driver_type}</p></div>
                    <div><p><strong>Name:</strong> ${driver.name}</p></div>
                    <div><p><strong>Phone:</strong> ${driver.phone}</p></div>
                </div>
            </div>
        `;

        // Section: Loading and Unloading Points
        printContent += `
            <div class="section">

                <div class="two-column">
                    <div>
                    <h4>Loading Point Details</h4>
                        <p><strong>Country:</strong> ${loadingDetails.country}</p>
                        <p><strong>Region:</strong> ${loadingDetails.region}</p>
                        <p><strong>City:</strong> ${loadingDetails.street}</p>
                        <p><strong>Port of Origin:</strong> ${loadingDetails.port_origin}</p>
                        <p><strong>ETD:</strong> ${loadingDetails.etd_departure}</p>
                        <p><strong>Warehouse:</strong> ${loadingDetails.warehouse}</p>

                    </div>
                    <div>
                    <h4>Offloading Point Details</h4>
                        <p><strong>Country:</strong> ${unloadingDetails.country}</p>
                        <p><strong>Region:</strong> ${unloadingDetails.region}</p>
                        <p><strong>City:</strong> ${unloadingDetails.street}</p>
                        <p><strong>Port of Destination:</strong> ${unloadingDetails.port_destination}</p>
                        <p><strong>ETA:</strong> ${unloadingDetails.etd_arrival}</p>
                    </div>
                </div>
            </div>
        `;

        // Section: Shipping Mode and Item Details
        printContent += `
            <div class="section">
                <h4>Shipment Details</h4>
                <div class="two-column">
                    <div><p><strong>Description:</strong> ${document.getElementById('item_desc').value}</p></div>
                    <div><p><strong>Number of Units:</strong> ${units}</p></div>
                    <div><p><strong>Weight:</strong> ${document.getElementById('weight').value}</p></div>
                </div>
            </div>
        `;

        printContent += `
            <div class="section">
                <h4>Charge Details</h4>
                <table class="charges-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Charge Details</th>
                            <th>Currency</th>
                            <th>ROE</th>
                            <th>Amount</th>
                            <th>Amount(AED)</th>
                            <th>Taxable</th>
                            <th>Non Taxable</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>`
        const borderChargeRows = document.querySelectorAll('.border-charge-row');
        borderChargeRows.forEach(row => {
            const index = row.dataset.index;
            const chargeDescription = document.getElementById(`chargeDescription${index}`).value;
            const currencyRateElement = document.getElementById(`currencyRate${index}`);
            const selectedCurrency = currencyRateElement.options[currencyRateElement.selectedIndex].text;
            const currencyRate = selectedCurrency.split("\u00A0\u00A0")[1]; // Extracting the rate part
            const amount = document.getElementById(`amount${index}`).value;
            const amountAED = document.getElementById(`amountAED${index}`).value;
            const taxableValues = document.getElementById(`taxableValues${index}`).value;
            const totalAmount = document.getElementById(`totalAmount${index}`).value;

            printContent += `
                <tr>
                    <td>${index}</td>
                    <td>${chargeDescription}</td>
                    <td>${selectedCurrency.split("\u00A0\u00A0")[0]}</td>
                    <td>${currencyRate}</td>
                    <td>${amount}</td>
                    <td>${amountAED}</td>
                    <td>${taxableValues}</td>
                    <td>${totalAmount}</td>
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

    const vehicleRows = document.querySelectorAll('.vehicle-row');

    printContent += `
        <div class="section">
            <h4>Equipment Details</h4>
            <table class="charges-table">
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Equipment Number</th>
                    </tr>
                </thead>
                <tbody>`

    vehicleRows.forEach(row => {
        const index = row.dataset.index;
        const selectElement = document.getElementById(`vehicle_id${index}`);
        const vehicleId = selectElement.options[selectElement.selectedIndex].text;
        const equipmentNumber = document.getElementById(`vehicle_num${index}`).value;

        printContent += `
            <tr>
                <td>${vehicleId}</td>
                <td>${equipmentNumber}</td>

            </tr>
        `;
    });

    printContent += `
                </tbody>
            </table>
        </div>
    `;

    printContent += `
       <div>
          <br>
           <p>Terms: All discrepancies must be reported within 7 days; otherwise, all charges will be considered correct.</p>
           <p>The payment check should be made payable to Elite Link Logistics LLC and should be crossed.</p>
           <p>All our transactions are subject to the NAFL Standard Trading conditions.</p><br>
           <p>Bank Details:</p>
           <p>Account Title: ELITE LINK LOGISTICS LLC</p>
           <p>IBAN: AE040030013580652920001</p>
           <p>Account Number: 13580652920001</p>
           <p>BIC / SWIFT: ADCBAEAAXXX</p>
           <p>Bank: ABU DHABI COMMERCIAL BANK</p>
           <p>Branch Code / Branch Name: 251 / AL RIGGAH ROAD</p>
           <p>For: ELITE LINK LOGISTICS LLC</p>
           <p>THIS IS A COMPUTER GENERATED DOCUMENT AND DOES NOT REQUIRE SIGNATURE</p>
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

    }  catch (error) {
    console.error('Error generating invoice:', error);
    }
}




//
// async function gatherFormDataForPrint() {
//     try {
//         // Fetch customer details
//         const customerId = document.getElementById('customer_id').value;
//         const customerResponse = await fetch(`backend/getCustomerDetails.php?customer_id=${customerId}`);
//         const customer = await customerResponse.json();
//
//         // Fetch driver details
//         const driverId = document.getElementById('driver_id').value;
//         const driverResponse = await fetch(`backend/getDriverDetails.php?driver_id=${driverId}`);
//         const driver = await driverResponse.json();
//
//         // Prepare the print content
//         let printContent = `
//             <html>
//             <head>
//                 <title>Shipment Details</title>
//                 <style>
//                     @page {
//                         size: A4;
//                         margin: 30mm 5mm 10mm 5mm;
//                     }
//                     body {
//                         font-family: Arial, sans-serif;
//                         font-size: 12px;
//                         margin: 0;
//                         padding: 0;
//                     }
//                     .container {
//                         padding: 0 5mm;
//                     }
//                     .header {
//                         text-align: center;
//                         margin-bottom: 10px;
//                     }
//                     .header img {
//                         max-width: 150px;
//                         margin-bottom: 10px;
//                     }
//                     .section {
//                         margin-bottom: 10px;
//                         border-bottom: 1px solid #ccc;
//                         padding-bottom: 10px;
//                     }
//                     .section h4 {
//                         margin-bottom: 5px;
//                         color: #333;
//                     }
//                     .section p {
//                         margin: 2px 0;
//                     }
//                     .section p strong {
//                         display: inline-block;
//                         width: 120px;
//                     }
//                     .two-column, .three-column {
//                         display: grid;
//                         gap: 10px;
//                     }
//                     .two-column {
//                         grid-template-columns: 1fr 1fr;
//                     }
//                     .three-column {
//                         grid-template-columns: 1fr 1fr 1fr;
//                     }
//                     .charges-table {
//                         width: 100%;
//                         border-collapse: collapse;
//                     }
//                     .charges-table th, .charges-table td {
//                         border: 1px solid #ccc;
//                         padding: 5px;
//                         text-align: left;
//                         font-size: 12px;
//                     }
//                     .charges-table th {
//                         background-color: #f0f0f0;
//                     }
//                 </style>
//             </head>
//             <body>
//                 <div class="container">
//                     <div class="header">
//                         <img style="width: 50%;" src="./dist/img/label_logo.jpg">
//                         <h3>Shipment Details</h3>
//                     </div>
//         `;
//
//         // Section: Shipment Information
//         printContent += `
//             <div class="section">
//                 <h4>Shipment Information</h4>
//                 <div class="two-column">
//                     <div><p><strong>Shipment ID:</strong> ${document.getElementById('shipment_id').value}</p></div>
//                     <div><p><strong>Handled By:</strong> ${document.getElementById('total_discount_type').selectedOptions[0].text}</p></div>
//                 </div>
//             </div>
//         `;
//
//         // Section: Customer and Consignee
//         printContent += `
//             <div class="section">
//                 <h4>Customer and Consignee</h4>
//                 <div class="two-column">
//                     <div><p><strong>Customer:</strong> ${customer.name}</p></div>
//                     <div><p><strong>Phone:</strong> ${customer.phone}</p></div>
//                     <div><p><strong>Email:</strong> ${customer.email}</p></div>
//                     <div><p><strong>Address:</strong> ${customer.address}</p></div>
//                     <div><p><strong>Consignee:</strong> ${document.getElementById('consignee').value}</p></div>
//                 </div>
//             </div>
//         `;
//
//         // Section: Supplier and Driver
//         printContent += `
//             <div class="section">
//                 <h4>Supplier and Driver</h4>
//                 <div class="two-column">
//                     <div><p><strong>Supplier:</strong> ${document.getElementById('supplier_id').selectedOptions[0]?.text || ''}</p></div>
//                     <div><p><strong>Driver:</strong> ${driver.name}</p></div>
//                     <div><p><strong>Driver Phone:</strong> ${driver.phone}</p></div>
//                 </div>
//             </div>
//         `;
//
//         // Section: Loading and Unloading Points
//         printContent += `
//             <div class="section">
//                 <h4>Loading and Unloading Points</h4>
//                 <div class="two-column">
//                     <div>
//                         <p><strong>Loading Country:</strong> ${document.getElementById('loading_country').selectedOptions[0].text}</p>
//                         <p><strong>Loading Region:</strong> ${document.getElementById('loading_region').selectedOptions[0]?.text || ''}</p>
//                         <p><strong>Loading City:</strong> ${document.getElementById('loading_city').value}</p>
//                         <p><strong>Warehouse:</strong> ${document.getElementById('warehouse').value}</p>
//                     </div>
//                     <div>
//                         <p><strong>Unloading Country:</strong> ${document.getElementById('unloading_country').selectedOptions[0].text}</p>
//                         <p><strong>Unloading Region:</strong> ${document.getElementById('unloading_region').selectedOptions[0]?.text || ''}</p>
//                         <p><strong>Unloading City:</strong> ${document.getElementById('unloading_city').value}</p>
//                         <p><strong>Zip Code:</strong> ${document.getElementById('zip_code').value}</p>
//                     </div>
//                 </div>
//             </div>
//         `;
//
//         // Section: Shipping Mode and Item Details
//         printContent += `
//             <div class="section">
//                 <h4>Shipping Mode and Item Details</h4>
//                 <div class="two-column">
//                     <div><p><strong>Shipping Mode:</strong> ${document.getElementById('shipping_mode_id').selectedOptions[0].text}</p></div>
//                     <div><p><strong>Item Description:</strong> ${document.getElementById('item_desc').value}</p></div>
//                 </div>
//             </div>
//         `;
//
//         // Section: Equipment Details
//         printContent += `
//             <div class="section">
//                 <h4>Equipment Details</h4>
//                 <div class="two-column">
//                     <div>
//                         <p><strong>Vehicle:</strong> ${document.getElementById('vehicle_id').selectedOptions[0]?.text || ''}</p>
//                         <p><strong>Vehicle Number:</strong> ${document.getElementById('vehicle_num').value}</p>
//                     </div>
//                     <div>
//                         <p><strong>Container:</strong> ${document.getElementById('container_id').selectedOptions[0]?.text || ''}</p>
//                         <p><strong>Container Number:</strong> ${document.getElementById('container_num').value}</p>
//                     </div>
//                 </div>
//             </div>
//         `;
//
//         // Section: Charges
//         printContent += `
//             <div class="section">
//                 <h4>Charges</h4>
//                 <table class="charges-table">
//                     <thead>
//                         <tr>
//                             <th>Charge Description</th>
//                             <th>Currency</th>
//                             <th>Rate per Unit</th>
//                             <th>Unit</th>
//                             <th>Amount</th>
//                             <th>Taxable Amount</th>
//                         </tr>
//                     </thead>
//                     <tbody>
//                         <tr>
//                             <td>Freight Charge</td>
//                             <td>${document.getElementById('currency').value}</td>
//                             <td></td>
//                             <td></td>
//                             <td>${document.getElementById('freight_charge').value}</td>
//                             <td></td>
//                         </tr>
//                         <tr>
//                             <td>Inspection Charges</td>
//                             <td>${document.getElementById('currency').value}</td>
//                             <td></td>
//                             <td></td>
//                             <td>${document.getElementById('inspection_charges').value}</td>
//                             <td></td>
//                         </tr>
//                         <tr>
//                             <td>Additional Charges</td>
//                             <td>${document.getElementById('currency').value}</td>
//                             <td></td>
//                             <td></td>
//                             <td>${document.getElementById('additional_charges').value}</td>
//                             <td></td>
//                         </tr>
//                         <tr>
//                             <td>Tax</td>
//                             <td>${document.getElementById('currency').value}</td>
//                             <td></td>
//                             <td></td>
//                             <td>${document.getElementById('tax').value}</td>
//                             <td></td>
//                         </tr>
//                         <tr>
//                             <td>Other Charges</td>
//                             <td>${document.getElementById('currency').value}</td>
//                             <td></td>
//                             <td></td>
//                             <td>${document.getElementById('other_charges').value}</td>
//                             <td></td>
//                         </tr>
//                         <tr>
//                             <td>Discount</td>
//                             <td>${document.getElementById('currency').value}</td>
//                             <td></td>
//                             <td></td>
//                             <td>${document.getElementById('discount').value}</td>
//                             <td></td>
//                         </tr>
//         `;
