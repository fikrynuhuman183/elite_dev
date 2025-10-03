<?php include 'layouts/header.php'; ?>
<?php include 'layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Customer Statement of Account (SOA)
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Customers</a></li>
            <li class="active">SOA</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Generate SOA</h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="soaDate">Select Date:</label>
                    <input type="date" class="form-control" id="soaDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <button class="btn btn-primary" id="generateSoaBtn">Generate SOA</button>
            </div>
        </div>

        <div class="box box-info" id="customerInfoBox" style="display: none;">
            <div class="box-header with-border">
                <h3 class="box-title">Customer Information</h3>
                <div class="box-tools pull-right">
                    <h4 id="selectedDateDisplay"></h4>
                </div>
            </div>
            <div class="box-body">
                <p><strong>Customer:</strong> <span id="customerNameDisplay"></span></p>
                <p><strong>Email:</strong> <span id="customerEmailDisplay"></span></p>
                <p><strong>Contact No:</strong> <span id="customerContactDisplay"></span></p>
                <p><strong>TRN/TAX Number:</strong> <span id="customerTrnDisplay"></span></p>
            </div>
        </div>

        <div class="box" id="soaTableBox" style="display: none;">
            <div class="box-header with-border">
                <h3 class="box-title">Statement of Account</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-primary" id="printSoaBtn">
                        <i class="fa fa-print"></i> Print SOA
                    </button>
                </div>
            </div>
            <div class="box-body">
                <table id="soaTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Total Amount</th>
                            <th>Balance</th>
                            <th>Cumulative Balance</th>
                            <th>Due Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- SOA data will be loaded here -->
                    </tbody>
                    <tfoot>
                         <tr>
                            <td colspan="5" style="text-align:right;"><strong>Total Amount Due:</strong></td>
                            <td id="totalDueAmount"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<?php include 'layouts/footer.php'; ?>
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

<script>
$(document).ready(function() {
    // Get customer ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const customerId = urlParams.get('customer_id');
    
    // Global variables to store SOA data for printing
    let currentSoaData = null;
    let currentCustomerData = null;
    let currentSelectedDate = null;

    if (!customerId) {
        // Handle case where customer_id is not provided
        alert("Customer ID is missing.");
        return;
    }

    // Function to fetch customer details
    function fetchCustomerDetails(custId) {
        $.ajax({
            url: 'backend/getCustomerDetails.php',
            method: 'GET',
            data: { customer_id: custId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#customerNameDisplay').text(response.data.name + ' (ID: ' + response.data.customer_id + ')');
                    $('#customerEmailDisplay').text(response.data.email);
                    $('#customerContactDisplay').text(response.data.phone);
                    $('#customerTrnDisplay').text(response.data.vat_number);
                    $('#customerInfoBox').show();
                    
                    // Store customer data for printing
                    currentCustomerData = response.data;
                } else {
                    console.error('Error fetching customer details:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error fetching customer details:', status, error);
            }
        });
    }

    // Fetch customer details on page load
    fetchCustomerDetails(customerId);

    $('#generateSoaBtn').on('click', function() {
        const selectedDate = $('#soaDate').val();

        if (!selectedDate) {
            alert("Please select a date.");
            return;
        }

        $('#selectedDateDisplay').text('SOA as of: ' + selectedDate);

        $.ajax({
            url: 'backend/fetch_customer_soa.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                customer_id: customerId,
                soa_date: selectedDate
            }),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    currentSoaData = response.data;
                    currentSelectedDate = selectedDate;
                    populateSoaTable(response.data);
                    $('#soaTableBox').show();
                } else {
                    console.error('Error generating SOA:', response.message);
                    $('#soaTable tbody').empty();
                    $('#totalDueAmount').text('N/A');
                    $('#soaTableBox').show();
                    alert('Error generating SOA: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error generating SOA:', status, error);
                $('#soaTable tbody').empty();
                $('#totalDueAmount').text('N/A');
                $('#soaTableBox').show();
                alert('An error occurred while generating the SOA.');
            }
        });
    });

    function populateSoaTable(data) {
        const tbody = $('#soaTable tbody');
        tbody.empty(); // Clear previous data

        let cumulativeBalance = 0;

        if (data.length === 0) {
            tbody.append('<tr><td colspan="7" style="text-align:center;">No due invoices found for the selected date.</td></tr>');
            $('#totalDueAmount').text('0.00');
            return;
        }

        data.forEach(row => {
            cumulativeBalance += parseFloat(row.balance);
            const newRow = `
                <tr>
                    <td>${row.invoice_number}</td>
                    <td>${row.invoice_date}</td>
                    <td>${row.due_date}</td>
                    <td>${row.total_amount}</td>
                    <td>${row.balance}</td>
                    <td>${row.cumulative_balance}</td>
                    <td>${row.due_days}</td>
                </tr>
            `;
            tbody.append(newRow);
        });

        $('#totalDueAmount').text(cumulativeBalance.toFixed(2));
    }

    // Print SOA button click handler
    $('#printSoaBtn').on('click', function() {
        if (currentSoaData && currentCustomerData && currentSelectedDate) {
            generateSOA(currentSoaData, currentCustomerData, currentSelectedDate);
        } else {
            alert('No SOA data available to print. Please generate SOA first.');
        }
    });

    // Function to generate printable SOA
    function generateSOA(soaData, customerData, selectedDate) {
        try {
            const originalTitle = document.title;
            document.title = `SOA_${customerData.name}_${selectedDate}`;
            
            let totalDue = 0;
            soaData.forEach(row => {
                totalDue += parseFloat(row.balance.replace(/,/g, ''));
            });

            let printContent = `
                <html>
                <head>
                    <style>
                        @media print {
                            @page {
                                size: A4;
                                margin: 0 1in 0 1in;
                            }
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                        body {
                            font-family: Arial, sans-serif;
                            font-size: 10px;
                            line-height: 1.2;
                            margin: 20px;
                            background-image: url('SOA_template.jpeg');
                            background-size: 100%;
                            background-repeat: no-repeat;
                        }
                        .soa-container {
                            
                            padding-top: 20px;
                            padding-right: 10px;
                            padding-left: 10px;
                        }
                        .header-section {
                            margin-top: 80px;
                            margin-bottom: 30px;
                        }
                        .customer-info {
                            margin-bottom: 20px;
                        }
                        .customer-info p {
                            margin: 3px 0;
                            font-size: 10px;
                        }
                        .date-info {
                            text-align: right;
                            margin-bottom: 20px;
                            font-size: 10px;
                        }
                        .soa-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                            font-size: 9px;
                            page-break-inside: avoid;
                        }
                        .soa-table th, .soa-table td {
                            border: 1px solid #000;
                            padding: 2px 4px;
                            text-align: left;
                            line-height: 1.1;
                            height: 18px;
                            vertical-align: middle;
                        }
                        .soa-table th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                            text-align: center;
                            height: 20px;
                        }
                        .soa-table tr {
                            page-break-inside: avoid;
                            height: 18px;
                        }
                        .soa-table tbody tr:nth-child(25n) {
                            page-break-after: always;
                        }
                        .text-right {
                            text-align: right;
                        }
                        .text-center {
                            text-align: center;
                        }
                        .total-row {
                            font-weight: bold;
                            background-color: #f9f9f9;
                            page-break-inside: avoid;
                        }
                        
                        .page-break {
                            page-break-before: always;
                        }
                    </style>
                </head>
                <body>
                    <div class="soa-container">
                        <div class="header-section">
                            <div class="date-info">
                                <p><strong>Date:</strong> ${formatDate(selectedDate)}</p>
                            </div>
                            
                            <div class="customer-info">
                                <p><strong>Customer:</strong> ${customerData.name}</p>
                                <p><strong>Email:</strong> ${customerData.email || 'N/A'}</p>
                                <p><strong>Contact:</strong> ${customerData.phone || 'N/A'}</p>
                                <p><strong>TRN/TAX Number:</strong> ${customerData.vat_number || 'N/A'}</p>
                            </div>
                        </div>

                        <table class="soa-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Invoice Number</th>
                                    <th style="width: 12%;">Invoice Date</th>
                                    <th style="width: 12%;">Due Date</th>
                                    <th style="width: 14%;">Total Amount</th>
                                    <th style="width: 14%;">Balance</th>
                                    <th style="width: 14%;">Cumulative Balance</th>
                                    <th style="width: 14%;">Due Days</th>
                                </tr>
                            </thead>
                            <tbody>`;

            let rowCount = 0;
            soaData.forEach((row, index) => {
                rowCount++;
                // Add page break every 25 rows to ensure we don't exceed 2 inches from bottom
                const pageBreakClass = (rowCount % 25 === 0 && index < soaData.length - 1) ? ' page-break' : '';
                
                printContent += `
                    <tr${pageBreakClass}>
                        <td class="text-center">${row.invoice_number}</td>
                        <td class="text-center">${formatDate(row.invoice_date)}</td>
                        <td class="text-center">${formatDate(row.due_date)}</td>
                        <td class="text-right">${row.total_amount}</td>
                        <td class="text-right">${row.balance}</td>
                        <td class="text-right">${row.cumulative_balance}</td>
                        <td class="text-center">${row.due_days}</td>
                    </tr>`;
            });

            printContent += `
                                <tr class="total-row">
                                    <td colspan="5" class="text-right"><strong>Total Amount Due:</strong></td>
                                    <td class="text-right"><strong>${totalDue.toFixed(2)}</strong></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="amount-in-words" style="margin-top: 20px; padding: 10px; border: 1px solid #000; background-color: #f9f9f9;">
                            <p style="margin: 0; font-size: 10px; font-weight: bold;">
                                Amount in Words: ${numberToWords(totalDue)}
                            </p>
                        </div>
                        
                    </div>
                </body>
                </html>`;

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

        } catch (error) {
            console.error('Error generating SOA:', error);
            alert('Error generating printable SOA. Please try again.');
        }
    }

    // Helper function to format date
    function formatDate(dateString) {
        if(!dateString) return '';
        const date = new Date(dateString);
        let day = date.getDate();
        let month = date.getMonth() + 1;
        const year = date.getFullYear();

        if (day < 10) day = '0' + day;
        if (month < 10) month = '0' + month;

        return `${day}/${month}/${year}`;
    }
    function formatDate(dateString) {
        if(!dateString) return '';
        const date = new Date(dateString);
        let day = date.getDate();
        let month = date.getMonth() + 1;
        const year = date.getFullYear();

        if (day < 10) day = '0' + day;
        if (month < 10) month = '0' + month;

        return `${day}/${month}/${year}`;
    }

    // Add number to words function
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
});
</script>
