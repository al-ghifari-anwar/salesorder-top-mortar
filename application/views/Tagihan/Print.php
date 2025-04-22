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

$getCompany = $this->db->get_where('tb_company', ['id_distributor' => $this->session->userdata('id_distributor')])->row_array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
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
                            <img src="<?= base_url('assets/img/company_img/maslogo.png') ?>" style="width: 100px;">
                        </th>
                        <th class="text-left text-up">
                            <h3><?= $getCompany['name_company'] ?></h3>
                            <p style="font-size: 9px;">
                                <?php if ($getCompany['id_distributor'] != 6): ?>
                                    PT Miraswift Auto Solusi <br>
                                    Perumahan Bedali Agung Blok AI No 2, Bedali, Lawang, Kab. Malang
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

                            <h1 class="text-right text-bot">Invoice</h1>
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
                                <td><b><?= $distributor['nama_distributor'] ?></b><br><?= $distributor['alamat_distributor'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- Ship To -->
                <!-- <div style="margin-top: 10px;"></div>
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
                </div> -->
            </div>
            <div class="column" style="width: 40%;">
                <table class="border">
                    <tr>
                        <th class="border">Invoice Date</th>
                        <th class="border">Invoice Number</th>
                    </tr>
                    <tr>
                        <td class="border text-center">
                            <?= date('d M Y', strtotime($tagihan['date_tagihan'])) ?>
                        </td>
                        <td class="border text-center">
                            <?= $tagihan['no_tagihan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="border">Terms</th>
                        <th class="border">Jatuh Tempo</th>
                    </tr>
                    <tr>
                        <td class="border text-center">
                            <?= "COD" ?>
                        </td>
                        <td class="border text-center">
                            <?php
                            $jatuhTempo = date('d M Y', strtotime("+7 days", strtotime($tagihan['date_tagihan'])));
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
                        <th class="border">No</th>
                        <th class="border">Item</th>
                        <th class="border">Price</th>
                        <th class="border">QTY</th>
                        <th class="border">Amount</th>
                    </tr>
                    <?php
                    $totalAmount = 0;
                    $no = 1;
                    foreach ($tagihanDetails as $tagihanDetail) : ?>
                        <tr>
                            <td class="border-r"><?= $no++; ?></td>
                            <td class="border-r"><?= $tagihanDetail['type_tagihan_detail'] ?></td>
                            <td class="border-r text-right"><?= number_format($tagihanDetail['price_tagihan_detail'], 0, '.', ',') ?></td>
                            <td class="border-r text-right"><?= $tagihanDetail['qty_tagihan_detail'] ?></td>
                            <td class="border-r text-right"><?= number_format($tagihanDetail['total_tagihan_detail'], 0, '.', ',') ?></td>
                        </tr>
                        <?php $totalAmount += $tagihanDetail['total_tagihan_detail'] ?>
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
                        <td class="border"><?= penyebut($tagihan['total_tagihan']) ?></td>
                    </tr>
                </table>
                <table class="border" style="width: 100%; margin-right: 20px; margin-top: 0px;">
                    <tr>
                        <td>
                            Payment: BCA No Rekening <?= '8880964519' ?> atas nama PT Miraswift Auto Solusi<br><b>Harap transfer sesuai dengan nominal hingga digit terakhir</b>
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
                        <th class="text-left">Total Invoice:</th>
                        <th class="text-right"><?= number_format($tagihan['total_tagihan'], 0, '.', ',') ?></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- <hr style="margin-top: 0px; margin-bottom: 0px;"> -->
</body>

</html>