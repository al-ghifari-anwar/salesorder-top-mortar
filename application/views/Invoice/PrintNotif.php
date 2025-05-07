<?php
function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

$getCompany = $this->db->get_where('tb_company', ['id_distributor' => $id_distributor])->row_array();
?>
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
            font-size: 10px;
        }

        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .border-r {
            border-right: 1px solid black;
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

        .text-up {
            vertical-align: top;
        }

        .text-bot {
            vertical-align: bottom;
        }

        .page {
            height: 50%;
        }

        .skinny {
            margin: 0 0 0 0;
            padding: 0 0 0 0;
        }
    </style>
    <!-- ORIGINAL -->
    <div class="page">
        <div class="row">
            <div class="column" style="width: 70%;">
                <!-- Store and City -->
                <table class="" style="margin-right: 50px;">
                    <tr>
                        <th class="text-left">
                            <img src="<?= base_url('assets/img/company_img/') . $getCompany['img_company'] ?>" style="width: 100px;">
                        </th>
                        <th class="text-left text-up">
                            <h3><?= $getCompany['name_company'] ?></h3>
                            <p style="font-size: 9px;">
                                <?php if ($getCompany['id_distributor'] != 6): ?>
                                    PT Top Mortar Indonesia Pergudangan Bizpoint Blok F No 50, Cikupa, Tangerang
                                <?php endif; ?>
                            </p>
                        </th>
                    </tr>
                </table>
            </div>
            <div class="column" style="width: 30%;">
                <table>
                    <tr>
                        <th class="text-right">
                            <h3><b>ASLI</b></h3>
                        </th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th class="text-bot" style="padding-top: 0px; padding-bottom: 0;">

                            <h1 class="text-right text-bot">Sales Invoice</h1>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="column" style="width: 60%;">
                <!-- Bill To -->
                <div class="row">
                    <div class="column" style="width: 10%;">
                        <span>Bill To:</span>
                    </div>
                    <div class="column" style="width: 0%;">
                        <table class="border" style="margin-right: 5px;">
                            <tr>
                                <td><b><?= $store['nama'] ?></b><br><?= $store['address'] ?><br><?= $store['nomorhp'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- Ship To -->
                <div style="margin-top: 10px;"></div>
                <div class="row">
                    <div class="column" style="width: 10%;">
                        <span>Ship To:</span>
                    </div>
                    <div class="column" style="width: 0%;">
                        <table class="border" style="margin-right: 5px;">
                            <tr>
                                <td><b><?= $invoice['ship_to_name'] ?></b><br><?= $invoice['ship_to_address'] ?><br><?= $invoice['ship_to_phone'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="column" style="width: 40%;">
                <table class="border">
                    <tr>
                        <th class="border">Invoice Date</th>
                        <th class="border">Invoice Number</th>
                    </tr>
                    <tr>
                        <td class="border text-center">
                            <?= date('d M Y', strtotime($invoice['date_invoice'])) ?>
                        </td>
                        <td class="border text-center">
                            <?= $invoice['no_invoice'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="border">Terms</th>
                        <th class="border">Jatuh Tempo</th>
                    </tr>
                    <tr>
                        <td class="border text-center">
                            <?php if ($store['termin_payment'] == 0 || $store['termin_payment'] == 1 || $store['termin_payment'] == 2) { ?>
                                <?= "COD" ?>
                            <?php } else { ?>
                                <?= $store['termin_payment'] . " Hari" ?>
                            <?php } ?>
                        </td>
                        <td class="border text-center">
                            <?php
                            $jatuhTempo = date('d M Y', strtotime("+" . $store['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                            ?>
                            <?= date('d M Y', strtotime($jatuhTempo)) ?>
                        </td>
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
                        <th class="border">Amount</th>
                    </tr>
                    <?php
                    $totalAmount = 0;
                    foreach ($produk as $dataProduk) : ?>
                        <tr>
                            <td class="border-r"><?= $invoice['no_surat_jalan'] ?></td>
                            <td class="border-r"><?= $dataProduk['nama_produk'] ?> <b><?= $dataProduk['is_bonus'] == 2 ? "(Free)" : "" ?></b> <?= $dataProduk['no_voucher'] == null ? '' : "(" . $dataProduk['no_voucher'] . ")" ?></td>
                            <td class="border-r text-right"><?= $dataProduk['qty_produk'] ?></td>
                            <td class="border-r"><?= $dataProduk['name_satuan'] ?></td>
                            <td class="border-r text-right"><?= number_format($dataProduk['price'], 0, '.', ',') ?></td>
                            <td class="border-r text-right"><?= number_format($dataProduk['amount'], 0, '.', ',') ?></td>
                        </tr>
                        <?php $totalAmount += $dataProduk['amount'] ?>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- Totals and Payment -->
        <div class="row">
            <div class="column" style="width: 70%;">
                <table class="" style="width: 90%; margin-right: 20px;">
                    <tr>
                        <td>Terbilang: </td>
                        <td class="border"><?= penyebut($invoice['total_invoice']) ?></td>
                    </tr>
                </table>
                <table class="border" style="width: 100%; margin-right: 20px; margin-top: 0px;">
                    <tr>
                        <td>
                            Payment: BCA No Rekening <?= $this->session->userdata('jenis_distributor') == 'pusat' ? '8880762231' : '8880964519' ?> atas nama PT Top Mortar Indonesia<br><b>Harap transfer sesuai dengan nominal hingga digit terakhir</b>
                        </td>
                    </tr>
                </table>

            </div>
            <div class="column" style="width: 30%;">
                <table class="border">
                    <tr>
                        <th class="text-left">Subtotal Invoice:</th>
                        <th class="text-right"><?= number_format($totalAmount, 0, '.', ',') ?></th>
                    </tr>
                    <tr>
                        <th class="text-left">Potongan Invoice:</th>
                        <th class="text-right">-<?= number_format($totalAmount - $invoice['total_invoice'], 0, '.', ',') ?></th>
                    </tr>
                    <tr>
                        <th class="text-left">Total Invoice:</th>
                        <th class="text-right"><?= number_format($invoice['total_invoice'], 0, '.', ',') ?></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>