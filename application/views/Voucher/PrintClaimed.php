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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= "Laporan Voucher Sudah Claim " . $city['nama_city']  ?></title>
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

        .bg-green {
            background-color: green;
        }

        .bg-red {
            background-color: red;
        }
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Laporan Voucher Yang Sudah Di Claim <?= $city['nama_city'] ?></h1>
    <h4 class="text-center"></h4>
    <table class="border">
        <tr>
            <th class="border">No</th>
            <th class="border">Kode Voucher</th>
            <th class="border">Nama Toko</th>
            <th class="border">Nomor HP</th>
            <th class="border">Status</th>
            <th class="border">Reputasi</th>
            <!-- <th class="border">Tgl Expired</th> -->
        </tr>
        <?php if ($voucher != null) : ?>
            <?php
            $no = 1;
            foreach ($voucher as $voucher) : ?>
                <?php
                $id_city = $city['id_city'];
                ?>
                <tr>
                    <td class="text-center border-r"><?= $no++; ?></td>
                    <td class="text-left border-r"><?= $voucher['no_voucher']; ?></td>
                    <td class="text-left border-r"><?= $voucher['nama']; ?></td>
                    <td class="text-left border-r"><?= $voucher['nomorhp']; ?></td>
                    <td class="text-left border-r"><?= $voucher['store_status']; ?></td>
                    <td class="text-left border-r <?= $voucher['reputation'] == 'good' ? 'bg-green' : 'bg-red' ?>"><?= $voucher['reputation']; ?></td>
                    <!-- <td class="text-left border-r"><?= date("d M, Y", strtotime($voucher['exp_date'])) ?></td> -->
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>