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
    <title><?= "Laporan Stok " . $city['nama_city']  ?></title>
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
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Laporan Auto Transfer Test</h1>
    <h4 class="text-center">Tanggal <?= date('d M, Y', strtotime($dates[0] . " 00:00:00")) . " - " . date('d M, Y', strtotime($dates[1] . " 23:59:59")) ?></h4>
    <table class="border">
        <tr>
            <th class="border">No.</th>
            <th class="border">Invoice</th>
            <th class="border">Rek. Asal</th>
            <th class="border">Rek. Tujuan</th>
            <th class="border">Ref. No</th>
            <th class="border">Trans. Date</th>
            <th class="border">Remark</th>
            <th class="border">QTY</th>
            <th class="border">Amount</th>
        </tr>
        <?php if ($city != null) : ?>
            <?php

            foreach ($city as $city) : ?>
                <tr>
                    <td class="text-left border-r" colspan="8"><?= $city['nama_city']; ?></td>
                </tr>
                <?php
                $this->db->join('tb_invoice', 'tb_invoice.id_surat_jalan = tb_log_bca_test.id_surat_jalan');
                $getLog = $this->db->get_where('tb_log_bca_test', ['id_city' => $id_city, 'DATE(transaction_date) >= ' => $dateFrom, 'DATE(transaction_date) <= ' => $dateTo])->result_array();
                ?>
                <?php
                $no = 1;
                foreach ($getLog as $log) : ?>
                    <tr>
                        <td class="text-center border-r"><?= $no++; ?></td>
                        <td class="text-left border-r"><?= $log['no_invoice']; ?></td>
                        <td class="text-left border-r"><?= $log['norek_asal'] ?></td>
                        <td class="text-left border-r"><?= $city['norek_city'] ?></td>
                        <td class="text-left border-r"><?= $log['reference_no'] ?></td>
                        <td class="text-center border-r"><?= $log['transaction_date'] ?></td>
                        <td class="text-left border-r"><?= $log['remark'] ?></td>
                        <td class="text-center border-r"><?= $log['qty_sak'] ?></td>
                        <td class="text-center border-r"><?= $log['amount_transfered'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>