<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $invoice['no_invoice'] ?></title>
</head>

<body>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            padding: 5px;
        }

        table {
            width: 100%;
        }

        .column {
            float: left;
            width: 50%;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }
    </style>
    <div class="row">
        <div class="column">
            <!-- Store and City -->
            <table class="border" style="margin-right: 50px;">
                <tr>
                    <th class="border"><?= $store['nama'] ?></th>
                </tr>
                <tr>
                    <td><?= $store['nama_city'] ?></td>
                </tr>
            </table>
            <!-- Bill To -->
            <div style="margin-top: 10px;"></div>
            <div class="row">
                <div class="column" style="width: 15%;">
                    <span>Bill To:</span>
                </div>
                <div class="column" style="width: 85%;">
                    <table class="border" style="margin-right: 50px;">
                        <tr>
                            <th><?= $store['nama'] ?></th>
                        </tr>
                        <tr>
                            <td><?= $store['address'] ?><br><?= $store['nomorhp'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- Ship To -->
            <div style="margin-top: 10px;"></div>
            <div class="row">
                <div class="column" style="width: 15%;">
                    <span>Ship To:</span>
                </div>
                <div class="column" style="width: 85%;">
                    <table class="border" style="margin-right: 50px;">
                        <tr>
                            <th><?= $invoice['ship_to_name'] ?></th>
                        </tr>
                        <tr>
                            <td><?= $invoice['ship_to_address'] ?><br><?= $invoice['ship_to_phone'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="column">
            <table class="border">
                <tr>
                    <th class="border">Invoice Date</th>
                    <th class="border">Invoice Number</th>
                </tr>
                <tr>
                    <td class="border">
                        <?= date('d M Y', strtotime($invoice['date_invoice'])) ?>
                    </td>
                    <td class="border">
                        <?= $invoice['no_invoice'] ?>
                    </td>
                </tr>
                <tr>
                    <th class="border">Terms</th>
                    <th class="border">FOB</th>
                </tr>
                <tr>
                    <td class="border"></td>
                    <td class="border"></td>
                </tr>
                <tr>
                    <th class="border">Ship Via</th>
                    <th class="border">Ship Date</th>
                </tr>
                <tr>
                    <td class="border"><?= $kendaraan['nama_kendaraan'] . " + " . $courier['full_name'] ?></td>
                    <td class="border"><?= date('d M Y', strtotime($invoice['dalivery_date'])) ?></td>
                </tr>
                <tr>
                    <th class="border">PO. No.</th>
                    <th class="border">Currency</th>
                </tr>
                <tr>
                    <td class="border"></td>
                    <td class="border">IDR</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Products -->
    <div class="row" style="margin-top: 10px;">
        <div class="column" style="width: 100%;">
            <table class="border" style="width: 100%;">
                <tr>
                    <th class="border">DO No.</th>
                    <th class="border">Item</th>
                    <th class="border">QTY</th>
                    <th class="border">Item</th>
                    <th class="border">Unit Price</th>
                    <th class="border">Disc</th>
                    <th class="border">Tax</th>
                    <th class="border">Amount</th>
                </tr>
                <?php foreach ($produk as $dataProduk) : ?>
                    <tr>
                        <td class="border"><?= $invoice['no_surat_jalan'] ?></td>
                        <td class="border"><?= $dataProduk['nama_produk'] ?></td>
                        <td class="border text-right"><?= $dataProduk['qty_produk'] ?></td>
                        <td class="border">SAK</td>
                        <td class="border text-right"><?= number_format($dataProduk['price'], 0, '.', ',') ?></td>
                        <td class="border">0</td>
                        <td class="border"></td>
                        <td class="border text-right"><?= number_format($dataProduk['amount'], 0, '.', ',') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- Totals and Payment -->
    <div class="row" style="margin-top: 20px;">
        <div class="column" style="width: 70%;">
            <table class="border" style="width: 100%; margin-right: 50px;">
                <tr>
                    <td>Payment: BCA No Rekening 8880762231 atas nama PT Top Mortar Indonesia</td>
                </tr>
            </table>
        </div>
        <div class="column" style="width: 30%;">
            <table class="border">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right"><?= number_format($invoice['subtotal_invoice'], 0, '.', ',') ?></td>
                </tr>
                <tr>
                    <td>PPN 10%:</td>
                    <td class="text-right"><?= number_format(0, 0, '.', ',') ?></td>
                </tr>
                <tr>
                    <th class="text-left">Total Invoice:</th>
                    <th class="text-right"><?= number_format($invoice['total_invoice'], 0, '.', ',') ?></th>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>