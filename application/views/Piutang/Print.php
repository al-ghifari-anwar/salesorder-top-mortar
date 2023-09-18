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
    <title><?= "Rincian Pembayaran Invoice Tgl " . date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></title>
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

        table,
        th,
        td {
            border: 0px solid black;
        }
    </style>
    <h3 class="text-center">PT TOP MORTAR INDONESIA</h3>
    <h1 class="text-center">Rincian Piutang Invoice</h1>
    <h4 class="text-center">Tgl. <?= date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No</th>
            <th style="border-bottom: 1px solid black;">Tgl Cek</th>
            <th style="border-bottom: 1px solid black;">Terbayar</th>
            <!-- <th style="border-bottom: 1px solid black;">Nilai Invoice</th> -->
            <!-- <th style="border-bottom: 1px solid black;">Umur bdsr<br>Jatuh Tempo</th> -->
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php foreach ($invoice as $dataInv) : ?>
            <tr>
                <th class="text-left"><?= $dataInv['no_invoice'] .  " - " . date("d M Y", strtotime($dataInv['date_invoice'])) ?></th>
                <th><?= $dataInv['nama'] . " - " . $dataInv['nama_city'] ?></th>
                <td colspan="1"></td>
            </tr>
            <?php
            $payment = $this->MPayment->getByIdInvoice($dataInv['id_invoice'], $dateFrom, $dateTo);
            $totalPayment = 0;
            foreach ($payment as $payment) : ?>
                <?php
                $totalPayment += $payment['amount_payment'];
                // $jatuhTempo = date('d M Y', strtotime("+" . $payment['termin_payment'] . " days", strtotime($payment['date_invoice'])));
                ?>
                <tr>
                    <td class="text-center"><?= $payment['id_payment'] ?></td>
                    <td class="text-center"><?= date("d M Y", strtotime($payment['date_payment'])) ?></td>
                    <td class="text-right"><?= number_format($payment['amount_payment'], 0, '.', ',') ?></td>
                    <!-- <td class="text-center">0</td> -->
                    <!-- <td class="text-center">0</td> -->
                    <!-- <td class="text-left"><?= $payment['nama'] ?></td> -->

                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2" class="text-left">&nbsp;&nbsp;&nbsp;Total Dari <?= $dataInv['no_invoice'] ?></th>
                <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalPayment, 0, '.', ',') ?></th>
                <!-- <td colspan="1"></td> -->
            </tr>
            <tr>
                <th colspan="2" class="text-right">Nilai Invoice</th>
                <th class="text-right"><?= number_format($dataInv['total_invoice'], 0, '.', ',') ?></th>
                <!-- <td colspan="1"></td> -->
            </tr>
            <tr>
                <th colspan="2" class="text-right">Hutang Invoice</th>
                <th class="text-right"><?= number_format($dataInv['total_invoice'] - $totalPayment, 0, '.', ',') ?></th>
                <!-- <td colspan="1"></td> -->
            </tr>
            <tr style="height: 20px;">
                <th colspan="3"></th>
            </tr>
            <?php
            $totalAll += $totalPayment;
            ?>
        <?php endforeach; ?>
        <tr style="height: 200px;">
            <th colspan="3"></th>
        </tr>
        <tr>
            : <th colspan="2" class="text-right">Total Keseluruhan: </th>
            <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalAll, 0, '.', ',') ?></th>
            <!-- <td colspan="1"></td> -->
        </tr>

    </table>
</body>

</html>