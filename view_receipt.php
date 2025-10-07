<?php 

include './backend/conn.php';include './layouts/header.php';



// Get receipt ID from URL// Get receipt ID from URL

$receipt_id = $_GET['id'] ?? null;$receipt_id = $_GET['id'] ?? null;



if (!$receipt_id) {if (!$receipt_id) {

    die("No receipt ID provided");    echo "<div class='alert alert-danger'>No receipt ID provided</div>";

}    exit;

}

// Fetch receipt from database

$stmt = $conn->prepare("SELECT receipt_id, html_content FROM saved_receipts WHERE receipt_id = ?");// Fetch receipt data

$stmt->bind_param("s", $receipt_id);$url = './backend/print_receipt.php?id=' . urlencode($receipt_id);

$stmt->execute();$data = @file_get_contents($url);

$result = $stmt->get_result();

if ($data === false) {

if ($result->num_rows === 0) {    echo "<div class='alert alert-danger'>Failed to load receipt data</div>";

    die("Receipt not found");    exit;

}}



$row = $result->fetch_assoc();$receipt_data = json_decode($data, true);

$html_content = $row['html_content'];

if (!$receipt_data || !isset($receipt_data['html_content'])) {

// Remove background images for clean viewing    echo "<div class='alert alert-danger'>Invalid receipt data</div>";

$html_content = preg_replace('/background-image\s*:\s*url\([^)]*\)\s*;?/i', '', $html_content);    exit;

$html_content = preg_replace('/background\s*:\s*url\([^)]*\)[^;]*;?/i', '', $html_content);}



$conn->close();// Remove background images from HTML

?>$html_content = $receipt_data['html_content'];

<!DOCTYPE html>$html_content = preg_replace('/background-image\s*:\s*url\([^)]*\)\s*;?/i', '', $html_content);

<html>$html_content = preg_replace('/background\s*:\s*url\([^)]*\)[^;]*;?/i', '', $html_content);

<head>?>

    <meta charset="UTF-8">

    <title>Receipt - <?= htmlspecialchars($receipt_id) ?></title><!DOCTYPE html>

    <style><html>

        body {<head>

            margin: 0;    <meta charset="UTF-8">

            padding: 20px;    <title>Receipt - <?= htmlspecialchars($receipt_data['receipt_id'] ?? $receipt_id) ?></title>

            background: #f4f4f4;    <style>

        }        body {

        .action-bar {            margin: 0;

            max-width: 1200px;            padding: 20px;

            margin: 0 auto 20px;            background: #f4f4f4;

            text-align: right;            font-family: Arial, sans-serif;

            padding: 10px;        }

            background: white;        .receipt-container {

            border-radius: 4px;            max-width: 1200px;

            box-shadow: 0 2px 4px rgba(0,0,0,0.1);            margin: 0 auto;

        }            background: white;

        .action-bar button {            padding: 30px;

            padding: 10px 20px;            box-shadow: 0 0 10px rgba(0,0,0,0.1);

            margin-left: 10px;        }

            border: none;        .action-bar {

            border-radius: 4px;            text-align: right;

            cursor: pointer;            margin-bottom: 20px;

            font-size: 14px;            padding-bottom: 20px;

        }            border-bottom: 2px solid #eee;

        .btn-print {        }

            background: #3c8dbc;        .action-bar button {

            color: white;            padding: 10px 20px;

        }            margin-left: 10px;

        .btn-print:hover {            border: none;

            background: #367fa9;            border-radius: 4px;

        }            cursor: pointer;

        .btn-close {            font-size: 14px;

            background: #dd4b39;        }

            color: white;        .btn-print {

        }            background: #3c8dbc;

        .btn-close:hover {            color: white;

            background: #d33724;        }

        }        .btn-print:hover {

        .receipt-wrapper {            background: #367fa9;

            max-width: 1200px;        }

            margin: 0 auto;        .btn-close {

            background: white;            background: #dd4b39;

            padding: 30px;            color: white;

            box-shadow: 0 0 10px rgba(0,0,0,0.1);        }

            border-radius: 4px;        .btn-close:hover {

        }            background: #d33724;

        @media print {        }

            body {        @media print {

                background: white;            .action-bar {

                padding: 0;                display: none;

            }            }

            .action-bar {            body {

                display: none;                background: white;

            }                padding: 0;

            .receipt-wrapper {            }

                box-shadow: none;            .receipt-container {

                padding: 0;                box-shadow: none;

                max-width: 100%;                padding: 0;

            }            }

        }        }

    </style>    </style>

</head></head>

<body><body>

    <div class="action-bar">    <div class="receipt-container">

        <button class="btn-print" onclick="window.print()">        <div class="action-bar">

            Print Receipt            <button class="btn-print" onclick="window.print()">

        </button>                <i class="fa fa-print"></i> Print Receipt

        <button class="btn-close" onclick="window.close()">            </button>

            Close            <button class="btn-close" onclick="window.close()">

        </button>                <i class="fa fa-times"></i> Close

    </div>            </button>

    <div class="receipt-wrapper">        </div>

        <?= $html_content ?>        <div class="receipt-content">

    </div>            <?= $html_content ?>

</body>        </div>

</html>    </div>

</body>
</html>
