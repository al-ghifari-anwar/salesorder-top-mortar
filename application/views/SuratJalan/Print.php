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
    <title> Surat Jalan</title>
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
                            <img src="<?= base_url('assets/img/company_img/') . $getCompany['img_company']  ?>" style="width: 100px;">
                        </th>
                        <th class="text-left text-up">
                            <h3><?= $getCompany['name_company'] ?></h3>
                        </th>
                    </tr>
                </table>
            </div>
            <div class="column" style="width: 30%;">
                <table>
                    <tr>
                        <th class="text-right">
                            <!-- <h5>COPY</h5> -->
                        </th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th class="text-bot" style="padding-top: 0px; padding-bottom: 0;">

                            <h1 class="text-right text-bot">Surat Jalan</h1>
                            <p><?= $suratjalan['no_surat_jalan'] ?></p>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="column" style="width: 60%;">
                <!-- Ship To -->
                <div style="margin-top: 10px;"></div>
                <div class="row">
                    <span>Shipped To: <?= $store['nama'] ?></span>
                    <br>
                    <span><?= $store['address'] ?></span>

                </div>
            </div>
            <div class="column" style="width: 40%;">
                <p class="text-right">Delivery Date: <?= $suratjalan['dalivery_date'] ?></p>
                <p class="text-right">Printed Date: <?= date("Y-m-d H:i:s") ?></p>
            </div>
        </div>

        <!-- Products -->
        <div class="row" style="margin-top: 10px;">
            <div class="column" style="width: 100%;">
                <table class="border" style="width: 100%;">
                    <tr>
                        <th class="border">No.</th>
                        <th class="border">Nama</th>
                        <th class="border">No Voucher</th>
                        <th class="border">QTY</th>
                    </tr>
                    <?php
                    $totalAmount = 0;
                    $no = 1;
                    foreach ($produk as $dataProduk) : ?>
                        <tr>
                            <td class="border-r"><?= $no++; ?></td>
                            <td class="border-r"><?= $dataProduk['nama_produk'] ?> <b><?= $dataProduk['is_bonus'] == 2 ? "(Retur)" : "" ?></b> <?= $dataProduk['no_voucher'] == null ? '' : "(" . $dataProduk['no_voucher'] . ")" ?></td>
                            <td class="border-r"><?= $dataProduk['no_voucher'] ?></td>
                            <td class="border-r text-right"><?= $dataProduk['qty_produk'] ?></td>
                        </tr>
                        <?php $totalAmount += $dataProduk['amount'] ?>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- Totals and Payment -->
        <div class="row">
            <p>Kurir: <?= $suratjalan['full_name'] ?></p>
            <p>Kendaraan: <?= $kendaraan['nama_kendaraan'] ?></p>
            <p>No. Polisi: <?= $kendaraan['nopol_kendaraan'] ?></p>
        </div>

        <div class="row">
            <table style="width: 100%;">
                <tr>
                    <td>Dibuat</td>
                    <td>Diterima</td>
                    <td>Dikirim</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- <hr style="margin-top: 0px; margin-bottom: 0px;"> -->

    <div class="page" style="margin-top: 15px;">
        <div class="row">
            <div class="column" style="width: 70%;">
                <!-- Store and City -->
                <table class="" style="margin-right: 50px;">
                    <tr>
                        <th class="text-left">
                            <img src="<?= base_url('assets/img/logo_retina.png') ?>" style="width: 100px;">
                        </th>
                        <th class="text-left text-up">
                            <h3><?= $this->session->userdata('nama_distributor') ?></h3>
                        </th>
                    </tr>
                </table>
            </div>
            <div class="column" style="width: 30%;">
                <table>
                    <tr>
                        <th class="text-right">
                            <!-- <h5>COPY</h5> -->
                        </th>
                    </tr>
                </table>
                <table>
                    <tr>
                        <th class="text-bot" style="padding-top: 0px; padding-bottom: 0;">

                            <h1 class="text-right text-bot">Surat Jalan</h1>
                            <p><?= $suratjalan['no_surat_jalan'] ?></p>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="column" style="width: 60%;">
                <!-- Ship To -->
                <div style="margin-top: 10px;"></div>
                <div class="row">
                    <span>Shipped To: <?= $store['nama'] ?></span>
                    <br>
                    <span><?= $store['address'] ?></span>

                </div>
            </div>
            <div class="column" style="width: 40%;">
                <p class="text-right">Delivery Date: <?= $suratjalan['dalivery_date'] ?></p>
                <p class="text-right">Printed Date: <?= date("Y-m-d H:i:s") ?></p>
            </div>
        </div>

        <!-- Products -->
        <div class="row" style="margin-top: 10px;">
            <div class="column" style="width: 100%;">
                <table class="border" style="width: 100%;">
                    <tr>
                        <th class="border">No.</th>
                        <th class="border">Nama</th>
                        <th class="border">No Voucher</th>
                        <th class="border">QTY</th>
                    </tr>
                    <?php
                    $totalAmount = 0;
                    $no = 1;
                    foreach ($produk as $dataProduk) : ?>
                        <tr>
                            <td class="border-r"><?= $no++; ?></td>
                            <td class="border-r"><?= $dataProduk['nama_produk'] ?> <b><?= $dataProduk['is_bonus'] == 2 ? "(Retur)" : "" ?></b> <?= $dataProduk['no_voucher'] == null ? '' : "(" . $dataProduk['no_voucher'] . ")" ?></td>
                            <td class="border-r"><?= $dataProduk['no_voucher'] ?></td>
                            <td class="border-r text-right"><?= $dataProduk['qty_produk'] ?></td>
                        </tr>
                        <?php $totalAmount += $dataProduk['amount'] ?>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <!-- Totals and Payment -->
        <div class="row">
            <p>Kurir: <?= $suratjalan['full_name'] ?></p>
            <p>Kendaraan: <?= $kendaraan['nama_kendaraan'] ?></p>
            <p>No. Polisi: <?= $kendaraan['nopol_kendaraan'] ?></p>
        </div>

        <div class="row">
            <table style="width: 100%;">
                <tr>
                    <td>Dibuat</td>
                    <td>Diterima</td>
                    <td>Dikirim</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>