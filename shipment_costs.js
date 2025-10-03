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
    document.getElementById('addShipmentCostRowBtn').onclick = () => addShipmentCostRow();
    document.getElementById('saveShipmentCostsBtn').onclick = saveShipmentCosts;
});