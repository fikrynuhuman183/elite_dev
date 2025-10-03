let shipmentCostsCount = 1;
let suppliersList = [];
let currenciesList = [];

// Fetch customers/suppliers for dropdown
function fetchCustomersList(callback) {
    fetch('./backend/fetch_customers.php')
        .then(res => res.json())
        .then(data => {
            suppliersList = data; // Use customers as suppliers for this section
            if (callback) callback();
        });
}

// Get currencies from the DOM (from the Charges section)
function fetchCurrenciesList(callback) {
    fetch('./backend/fetch_currencies.php')
        .then(res => res.json())
        .then(data => {
            currenciesList = data;
            if (callback) callback();
        });
}

// Add a row to the shipment costs table
function addShipmentCostRow(cost = {}) {
    shipmentCostsCount++;
    const tbody = document.getElementById('shipmentCostsTableBody');
    const tr = document.createElement('tr');
    tr.className = 'shipment-cost-row';
    tr.dataset.index = shipmentCostsCount;

    // Supplier dropdown
    let supplierOptions = suppliersList.map(s =>
        `<option value="${s.customer_id}" ${cost.supplier_id == s.customer_id ? 'selected' : ''}>${s.name}</option>`
    ).join('');

    // Currency dropdown
    let currencyOptions = currenciesList.map(c =>
        `<option value="${c.id}" ${cost.currency == c.id ? 'selected' : ''}>${c.currency}</option>`
    ).join('');

    tr.innerHTML = `
        <td>
            <select class="form-control supplier-select" name="supplier_id">${supplierOptions}</select>
        </td>
        <td>
            <input type="text" class="form-control" name="description" value="${cost.description || ''}">
        </td>
        <td>
            <input type="text" class="form-control" name="tag" value="${cost.tag || ''}" placeholder="Tag">
        </td>
        <td>
            <select class="form-control currency-select" name="currency">${currencyOptions}</select>
        </td>
        <td>
            <input type="number" class="form-control" name="unit_rate" value="${cost.unit_rate || ''}" step="any">
        </td>
        <td>
            <input type="number" class="form-control" name="quantity" value="${cost.quantity || ''}" step="any">
        </td>
        <td>
            <input type="number" class="form-control" name="taxable" value="${cost.taxable || ''}" step="any">
        </td>
        <td>
            <input type="number" class="form-control" name="amount" value="${cost.amount || ''}" step="any" readonly>
        </td>
        <td>
            <input type="number" class="form-control" name="amount_AED" value="${cost.amount_AED || ''}" step="any" readonly>
        </td>
        <td>
            <input type="number" class="form-control" name="amount_USD" value="${cost.amount_USD || ''}" step="any" readonly>
        </td>
        <td>
            <input type="number" class="form-control total-amount" name="total_amount" value="${cost.total_amount || ''}" step="any" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger" onclick="this.closest('tr').remove(); calculateTotalCosts();">-</button>
        </td>
    `;
    tbody.appendChild(tr);

    // Add event listeners for calculation
    ['unit_rate', 'quantity', 'taxable', 'currency'].forEach(field => {
        tr.querySelector(`[name="${field}"]`).addEventListener('input', () => calculateRowCost(tr));
    });
    tr.querySelector(`[name="currency"]`).addEventListener('change', () => calculateRowCost(tr));
}

// Calculation logic for each row
function calculateRowCost(tr) {
    const unit_rate = parseFloat(tr.querySelector('[name="unit_rate"]').value) || 0;
    const quantity = parseFloat(tr.querySelector('[name="quantity"]').value) || 0;
    const taxable = parseFloat(tr.querySelector('[name="taxable"]').value) || 0;
    const currencyId = tr.querySelector('[name="currency"]').value;

    // Find selected currency's exchange rate
    let selectedCurrency = currenciesList.find(c => c.id == currencyId);
    let selectedRate = selectedCurrency ? parseFloat(selectedCurrency.roe) : 1;
    let aedRate = 1, usdRate = 1;
    currenciesList.forEach(c => {
        if (c.currency === "AED") aedRate = parseFloat(c.roe);
        if (c.currency === "USD") usdRate = parseFloat(c.roe);
    });

    const amount = unit_rate * quantity;
    const amount_AED = selectedRate ? amount * (aedRate / selectedRate) : amount;
    const amount_USD = selectedRate ? amount * (usdRate / selectedRate) : amount;
    const total_amount = amount_AED * (1 + taxable / 100);

    tr.querySelector('[name="amount"]').value = amount.toFixed(2);
    tr.querySelector('[name="amount_AED"]').value = amount_AED.toFixed(2);
    tr.querySelector('[name="amount_USD"]').value = amount_USD.toFixed(2);
    tr.querySelector('[name="total_amount"]').value = total_amount.toFixed(2);

    calculateTotalCosts();
}

// Calculate and display total costs
function calculateTotalCosts() {
    let total = 0;
    document.querySelectorAll('.total-amount').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    const totalInput = document.getElementById('shipmentCostsTotalInput');
    if (totalInput) totalInput.value = total.toFixed(2);
}

// Save shipment costs to backend
function saveShipmentCosts() {
    const shipment_id = document.getElementById('shipment_id').value;
    if (!shipment_id) {
        alert('Shipment ID is required.');
        return;
    }
    const rows = document.querySelectorAll('.shipment-cost-row');
    const costs = Array.from(rows).map(row => {
        const get = name => row.querySelector(`[name="${name}"]`).value;
        return {
            supplier_id: get('supplier_id'),
            description: get('description'),
            tag: get('tag'),
            currency: get('currency'),
            unit_rate: get('unit_rate'),
            quantity: get('quantity'),
            taxable: get('taxable'),
            amount: get('amount'),
            amount_AED: get('amount_AED'),
            amount_USD: get('amount_USD'),
            total_amount: get('total_amount')
        };
    });
    fetch('./backend/update_shipment_costs.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ shipment_id, costs })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') alert('Shipment costs updated!');
        else alert('Error updating shipment costs.');
    });
}

function loadShipmentCosts(shipment_id) {
    fetchCustomersList(() => {
        fetchCurrenciesList(() => {
            fetch(`./backend/fetch_shipment_costs.php?shipment_id=${shipment_id}`)
                .then(res => res.json())
                .then(costs => {
                    document.getElementById('shipmentCostsTableBody').innerHTML = '';
                    shipmentCostsCount = 0;
                    costs.forEach(cost => addShipmentCostRow(cost));
                    calculateTotalCosts();
                });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchCustomersList(() => {
        fetchCurrenciesList(() => {
            calculateTotalCosts();
        });
    });
    document.getElementById('printCostsBtn').onclick = printCostsInvoice;
    document.getElementById('addShipmentCostRowBtn').onclick = () => addShipmentCostRow();
    document.getElementById('saveShipmentCostsBtn').onclick = saveShipmentCosts;
});
function printCostsInvoice() {
    calculateTotal();
    const shipment_id = document.getElementById('shipment_id').value;
    if (!shipment_id) {
        alert('Please save the shipment first before printing costs invoice.');
        return;
    }
    
    // Collect charges data (same as existing invoice)
    const chargesRows = document.querySelectorAll('.border-charge-row');
    const chargesData = Array.from(chargesRows).map(row => {
        const index = row.dataset.index;
        return {
            index: index,
            description: document.getElementById(`chargeDescription${index}`).value,
            currency: document.getElementById(`currencyRate${index}`).options[document.getElementById(`currencyRate${index}`).selectedIndex].text,
            rate: document.getElementById(`rate${index}`).value,
            quantity: document.getElementById(`quantityUoM${index}`).value,
            amount: document.getElementById(`amount${index}`).value,
            amountAED: document.getElementById(`amountAED${index}`).value,
            amountUSD: document.getElementById(`amountUSD${index}`).value,
            taxable: document.getElementById(`taxableValues${index}`).value,
            totalAmount: document.getElementById(`totalAmount${index}`).value
        };
    });
    
    // Collect costs data
    const costsRows = document.querySelectorAll('.shipment-cost-row');
    const costsData = Array.from(costsRows).map((row, index) => {
        const supplierSelect = row.querySelector('[name="supplier_id"]');
        const currencySelect = row.querySelector('[name="currency"]');
        return {
            index: index + 1,
            supplier: supplierSelect?.options[supplierSelect?.selectedIndex]?.text || '',
            description: row.querySelector('[name="description"]')?.value || '',
            tag: row.querySelector('[name="tag"]')?.value || '',
            currency: currencySelect?.options[currencySelect?.selectedIndex]?.text || '',
            rate: row.querySelector('[name="unit_rate"]')?.value || '',
            quantity: row.querySelector('[name="quantity"]')?.value || '',
            amount: row.querySelector('[name="amount"]')?.value || '',
            amountAED: row.querySelector('[name="amount_AED"]')?.value || '',
            amountUSD: row.querySelector('[name="amount_USD"]')?.value || '',
            taxable: row.querySelector('[name="taxable"]')?.value || '',
            totalAmount: row.querySelector('[name="total_amount"]')?.value || ''
        };
    });
    
    // Calculate totals
    const totalCharges = parseFloat(document.getElementById('total')?.value || '0');
    const totalCosts = parseFloat(document.getElementById('shipmentCostsTotalInput')?.value || '0');
    const profit = totalCharges - totalCosts;
    
    // Collect all shipment data (same as existing)
    const data = {
        invoice_number: document.getElementById('invoice_number').value,
        invoice_date: document.getElementById('invoice_date').value,
        payment_date: document.getElementById('payment_date').value,
        job_number: document.getElementById('job_number').value,
        job_date: document.getElementById('job_date').value,
        bl_number: document.getElementById('bl_number').value,
        house_bl_number: document.getElementById('house_bl_number').value,
        consignee: document.getElementById('consignee').value,
        port_origin: document.getElementById('port_origin').value,
        port_destination: document.getElementById('port_destination').value,
        vessel: document.getElementById('vessel').value,
        voyage_number: document.getElementById('voyage_number').value,
        shipper_reference: document.getElementById('shipper_reference').value,
        etdDeparture: document.getElementById('etd_departure').value,
        etdDeparture_2: document.getElementById('etd_departure_2').value,
        etdDeparture_3: document.getElementById('etd_departure_3').value,
        etd_arrival: document.getElementById('etd_arrival').value,
        bill_of_entry: document.getElementById('bill_of_entry').value,
        units: document.getElementById('units').value,
        weight: document.getElementById('weight').value,
        height: document.getElementById('height').value,
        width: document.getElementById('width').value,
        length: document.getElementById('length').value,
        borderCharges: chargesData
    };
    
    generateCostsInvoice(data, chargesData, costsData, totalCharges, totalCosts, profit);
}

function generateCostsInvoice(data, chargesData, costsData, totalCharges, totalCosts, profit) {
    try {
        const customerSelect = document.getElementById('customer_id');
        const selectedCustomerText = customerSelect.options[customerSelect.selectedIndex].text;
        const shipperSelect = document.getElementById('supplier_id');
        const selectedShipperText = shipperSelect.options[shipperSelect.selectedIndex].text;
        const vat_number = document.getElementById('customerVATnumber').value;
        const originalTitle = document.title;
        document.title = data.invoice_number + " - COSTS ANALYSIS";
        
        let printContent = `
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        line-height: 1.2;
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
                        margin: 5px 0;
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
                    .costs-section {
                        margin-top: 20px;
                        border-top: 2px solid #000;
                        padding-top: 10px;
                    }
                    .profit-section {
                        margin-top: 20px;
                        border-top: 2px solid #000;
                        padding-top: 10px;
                        background-color: #f9f9f9;
                    }
                    .profit-positive { color: #28a745; }
                    .profit-negative { color: #dc3545; }
                    @media print {
                        @page { margin: 0; margin-top: -3; }
                        body { 
                            margin: 1.2cm;
                            margin-top: 4cm;
                            background-image: url('job_sheet.jpg');
                            background-size: 100% 100%;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="invoice">
                    
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
                    <!-- CHARGES SECTION (Revenue) -->
                    <div class="section">
                        <h3 style="text-align: center; margin-bottom: 15px;">CHARGES (REVENUE)</h3>
                        <table class="charges-table">
                            <thead>
                                <tr>
                                    <th style="text-align: left">No</th>
                                    <th style="text-align: left">Description</th>
                                    <th style="text-align: left">Curr</th>
                                    <th style="text-align: left">Rate/Unit</th>
                                    <th style="text-align: left">Units</th>
                                    <th style="text-align: left">Amount(Sel Curr)</th>
                                    <th style="text-align: left">Rate in AED</th>
                                    <th style="text-align: left">Rate in USD</th>
                                    <th style="text-align: left">Taxable Amt</th>
                                    <th style="text-align: left">Total Amount(AED)</th>
                                </tr>
                            </thead>
                            <tbody>`;

        // Add charges rows
        chargesData.forEach(charge => {
            printContent += `
                <tr>
                    <td style="text-align: left">${charge.index}</td>
                    <td style="text-align: left">${charge.description}</td>
                    <td style="text-align: left">${charge.currency}</td>
                    <td class="prices">${charge.rate}</td>
                    <td style="text-align: center">${charge.quantity}</td>
                    <td class="prices">${charge.amount}</td>
                    <td class="prices">${charge.amountAED}</td>
                    <td class="prices">${charge.amountUSD}</td>
                    <td>${charge.taxable}</td>
                    <td class="prices">${charge.totalAmount}</td>
                </tr>
            `;
        });

        printContent += `
                <tr>
                    <td style="text-align: left"></td>
                    <td style="text-align: left"><strong>Total Charges (AED):</strong></td>
                    <td style="text-align: left"></td>
                    <td class="prices"></td>
                    <td style="text-align: center"></td>
                    <td class="prices"></td>
                    <td class="prices"></td>
                    <td class="prices"></td>
                    <td></td>
                    <td class="prices"><strong>${totalCharges.toFixed(2)}</strong></td>
                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- COSTS SECTION (Expenses) -->
                    <div class="section costs-section">
                        <h3 style="text-align: center; margin-bottom: 15px;">COSTS (EXPENSES)</h3>
                        <table class="charges-table">
                            <thead>
                                <tr>
                                    <th style="text-align: left">No</th>
                                    <th style="text-align: left">Supplier</th>
                                    <th style="text-align: left">Description</th>
                                    <th style="text-align: left">Tag</th>
                                    <th style="text-align: left">Curr</th>
                                    <th style="text-align: left">Rate/Unit</th>
                                    <th style="text-align: left">Units</th>
                                    <th style="text-align: left">Amount(Sel Curr)</th>
                                    <th style="text-align: left">Rate in AED</th>
                                    <th style="text-align: left">Rate in USD</th>
                                    <th style="text-align: left">Taxable Amt</th>
                                    <th style="text-align: left">Total Amount(AED)</th>
                                </tr>
                            </thead>
                            <tbody>`;

        // Add costs rows
        costsData.forEach(cost => {
            printContent += `
                <tr>
                    <td style="text-align: left">${cost.index}</td>
                    <td style="text-align: left">${cost.supplier}</td>
                    <td style="text-align: left">${cost.description}</td>
                    <td style="text-align: left">${cost.tag}</td>
                    <td style="text-align: left">${cost.currency}</td>
                    <td class="prices">${cost.rate}</td>
                    <td style="text-align: center">${cost.quantity}</td>
                    <td class="prices">${cost.amount}</td>
                    <td class="prices">${cost.amountAED}</td>
                    <td class="prices">${cost.amountUSD}</td>
                    <td>${cost.taxable}</td>
                    <td class="prices">${cost.totalAmount}</td>
                </tr>
            `;
        });

        printContent += `
                <tr>
                    <td style="text-align: left"></td>
                    <td style="text-align: left"><strong>Total Costs (AED):</strong></td>
                    <td style="text-align: left"></td>
                    <td style="text-align: left"></td>
                    <td class="prices"></td>
                    <td style="text-align: center"></td>
                    <td class="prices"></td>
                    <td class="prices"></td>
                    <td class="prices"></td>
                    <td></td>
                    <td class="prices"><strong>${totalCosts.toFixed(2)}</strong></td>
                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PROFIT ANALYSIS SECTION -->
                    <div class="section profit-section">
                        <h3 style="text-align: center; margin-bottom: 15px;">PROFIT ANALYSIS</h3>
                        <table class="charges-table">
                            <tbody>
                                <tr>
                                    <td style="text-align: left; font-weight: bold;">Total Revenue (Charges):</td>
                                    <td class="prices" style="font-weight: bold;">${totalCharges.toFixed(2)} AED</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-weight: bold;">Total Expenses (Costs):</td>
                                    <td class="prices" style="font-weight: bold;">${totalCosts.toFixed(2)} AED</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-weight: bold;">Net Profit/Loss:</td>
                                    <td class="prices ${profit >= 0 ? 'profit-positive' : 'profit-negative'}" style="font-weight: bold; font-size: 14px;">
                                        ${profit.toFixed(2)} AED ${profit >= 0 ? '(PROFIT)' : '(LOSS)'}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-weight: bold;">Profit Margin:</td>
                                    <td class="prices ${profit >= 0 ? 'profit-positive' : 'profit-negative'}" style="font-weight: bold;">
                                        ${totalCharges > 0 ? ((profit / totalCharges) * 100).toFixed(2) : '0.00'}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="section">
                        <p>Amount in Words: AED ${numberToWords(profit)}</p>
                    </div>

                    `;

        // Vehicle data (same as existing)
        


        const special_note = document.getElementById('special_note').value;
        if(special_note){
            printContent += `<p>Note: ${special_note}</p>`;
        }

        printContent += `
                    </div>
                </div>
            </body>
            </html>
        `;

        var img = new Image();
        img.src = "job_sheet.jpg";
        img.onload = function() {
            const printFrame = document.getElementById('printFrame');
            printFrame.src = 'about:blank';
            printFrame.contentWindow.document.open();
            printFrame.contentWindow.document.write(printContent);
            printFrame.contentWindow.document.close();
            printFrame.focus();
            printFrame.contentWindow.print();
            document.title = originalTitle;
        }

    } catch (error) {
        console.error('Error generating costs invoice:', error);
    }
}