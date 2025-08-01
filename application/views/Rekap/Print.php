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
    <title><?= "Rekap Invoice Tgl " . date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></title>
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
    <h1 class="text-center">Rekap Invoice</h1>
    <h4 class="text-center">Tgl. <?= date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;" class="text-center">No.</th>
            <th style="border-bottom: 1px solid black;">No. Invoice</th>
            <th style="border-bottom: 1px solid black;">Tgl. Invoice</th>
            <th style="border-bottom: 1px solid black;">Jatuh Tempo</th>
            <th style="border-bottom: 1px solid black;">Nilai Invoice</th>
            <th style="border-bottom: 1px solid black;">Umur bdsr<br>Jatuh Tempo</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php if ($invoice != null) : ?>
            <?php
            $no = 1;
            $noInv = 1;
            foreach ($invoice as $dataInv) : ?>
                <tr>
                    <th class="text-center"><?= $no++; ?></th>
                    <th class="text-left"><?= $dataInv['nama'] . " - " . $dataInv['kode_city'] ?></th>
                    <td colspan="4"></td>
                </tr>
                <?php
                $storeInv = $this->MInvoice->getByStore($dateFrom, $dateTo, $dataInv['id_contact'], $no_invoice);
                $totalStore = 0;
                foreach ($storeInv as $storeInv) : ?>
                    <?php
                    $totalStore += $storeInv['total_invoice'];
                    $jatuhTempo = date('d M Y', strtotime("+" . $storeInv['termin_payment'] . " days", strtotime($storeInv['date_invoice'])));
                    ?>
                    <tr>
                        <td class="text-right"><?= $noInv++; ?></td>
                        <td class="text-center"><?= $storeInv['no_invoice'] ?></td>
                        <td class="text-center"><?= date("d M Y", strtotime($storeInv['date_invoice'])) ?></td>
                        <td class="text-center">
                            <?php if ($storeInv['termin_payment'] == 0 || $storeInv['termin_payment'] == 1 || $storeInv['termin_payment'] == 2) { ?>
                                <?= date("d M Y", strtotime($storeInv['date_invoice'])) ?>
                            <?php } else { ?>
                                <?= $jatuhTempo ?>
                            <?php } ?>
                        </td>
                        <td class="text-right"><?= number_format($storeInv['total_invoice'], 0, '.', ',') ?></td>
                        <td class="text-center">
                            <?php
                            $date1 = new DateTime(date("Y-m-d"));
                            $date2 = new DateTime($jatuhTempo);
                            $days  = $date2->diff($date1)->format('%a');
                            $operan = "";
                            if ($date1 < $date2) {
                                $operan = "-";
                            }
                            echo $operan . $days . " hari";
                            ?>
                        </td>
                        <!-- <td class="text-left"><?= $storeInv['nama'] ?></td> -->

                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4"></td>
                    <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore, 0, '.', ',') ?></th>
                    <td colspan="1"></td>
                </tr>
                <?php
                $totalAll += $totalStore;
                ?>
            <?php endforeach; ?>

            <tr>
                : <th colspan="4" class="text-right">Total Keseluruhan: </th>
                <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalAll, 0, '.', ',') ?></th>
                <td colspan="1"></td>
            </tr>
        <?php endif; ?>
    </table>
</body>

</html>