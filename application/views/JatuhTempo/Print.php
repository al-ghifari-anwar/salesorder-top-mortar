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
    <title><?= "Rekap Piutang Jatuh Tempo Per Tgl " . date("d M Y") ?></title>
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
    <h1 class="text-center">Rekap Piutang Jatuh Tempo <?= $city['nama_city'] ?></h1>
    <h4 class="text-center">Per Tgl. <?= date("d M Y") ?></h4>
    <table>
        <?php
        $totalStore1 = 0;
        $totalStore2 = 0;
        $totalStore3 = 0;
        $totalAll = 0;
        ?>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Nama Toko</th>
            <th style="border-bottom: 1px solid black;">No. Invoice</th>
            <th style="border-bottom: 1px solid black;">Tgl. Invoice</th>
            <th style="border-bottom: 1px solid black;">Jatuh Tempo</th>
            <th style="border-bottom: 1px solid black;">Sisa Hutang</th>
            <th style="border-bottom: 1px solid black;">Umur Hutang</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <tr>
            <th colspan="6">Umur Hutang 0 - 7 Hari</th>
        </tr>
        <?php
        $no07 = 0;
        foreach ($invoice as $storeInv) : ?>
            <?php
            $jatuhTempo = date('d M Y', strtotime("+" . $storeInv['termin_payment'] . " days", strtotime($storeInv['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $daysWithOperan = $operan . $days;
            // echo $daysWithOperan;
            // die;
            ?>
            <?php if ($daysWithOperan >= 0 && $daysWithOperan <= 7) :
            ?>
                <?php
                $no07++;
                // print_r("If 1");
                $id_invoice = $storeInv['id_invoice'];
                $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                $sisaHutang = $storeInv['total_invoice'] - $payment['amount_payment'];
                $totalStore1 += $sisaHutang;

                ?>

                <?php if ($sisaHutang > 0) : ?>
                    <tr>
                        <td class="text-center"><?= $no07 ?></td>
                        <td class="text-center"><?= $storeInv['nama'] ?></td>
                        <td class="text-center"><?= $storeInv['no_invoice'] ?></td>
                        <td class="text-center"><?= date("d M Y", strtotime($storeInv['date_invoice'])) ?></td>
                        <td class="text-center">
                            <?php if ($storeInv['termin_payment'] == 0 || $storeInv['termin_payment'] == 1 || $storeInv['termin_payment'] == 2) { ?>
                                <?= date("d M Y", strtotime($storeInv['date_invoice'])) ?>
                            <?php } else { ?>
                                <?= $jatuhTempo ?>
                            <?php } ?>
                        </td>
                        <td class="text-right"><?= number_format($sisaHutang, 0, '.', ',') ?></td>
                        <td class="text-center">
                            <?php
                            if ($days != 0) {
                                echo $operan . $days . " hari";
                            } else {
                                echo "Hari ini";
                            }
                            ?>
                        </td>
                        <!-- <td class="text-left"><?= $storeInv['nama'] ?></td> -->

                    </tr>
                <?php endif; ?>
                <!-- <tr>
                    <td colspan="4"></td>
                    <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore1, 0, '.', ',') ?></th>
                    <td colspan="1"></td>
                </tr> -->
                <?php
                $totalAll += $totalStore1;
                ?>
            <?php endif; ?>

        <?php endforeach; ?>
        <tr>
            <td colspan="4"></td>
            <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore1, 0, '.', ',') ?></th>
            <td colspan="1"></td>
        </tr>
        <tr>
            <th colspan="6">Umur Hutang 8 - 15 Hari</th>
        </tr>
        <?php
        $no815 = 0;
        foreach ($invoice as $storeInv2) : ?>
            <?php
            $jatuhTempo = date('d M Y', strtotime("+" . $storeInv2['termin_payment'] . " days", strtotime($storeInv2['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $daysWithOperan = $operan . $days;
            ?>
            <?php if ($daysWithOperan >= 8 && $daysWithOperan <= 15) : ?>
                <?php
                $no815++;
                $id_invoice = $storeInv2['id_invoice'];
                $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                $sisaHutang = $storeInv2['total_invoice'] - $payment['amount_payment'];
                $totalStore2 += $sisaHutang;

                ?>

                <?php if ($sisaHutang > 0) : ?>
                    <tr>
                        <td class="text-center"><?= $no815 ?></td>
                        <td class="text-center"><?= $storeInv2['nama'] ?></td>
                        <td class="text-center"><?= $storeInv2['no_invoice'] ?></td>
                        <td class="text-center"><?= date("d M Y", strtotime($storeInv2['date_invoice'])) ?></td>
                        <td class="text-center">
                            <?php if ($storeInv2['termin_payment'] == 0 || $storeInv2['termin_payment'] == 1 || $storeInv2['termin_payment'] == 2) { ?>
                                <?= date("d M Y", strtotime($storeInv2['date_invoice'])) ?>
                            <?php } else { ?>
                                <?= $jatuhTempo ?>
                            <?php } ?>
                        </td>
                        <td class="text-right"><?= number_format($sisaHutang, 0, '.', ',') ?></td>
                        <td class="text-center">
                            <?php
                            if ($days != 0) {
                                echo $operan . $days . " hari";
                            } else {
                                echo "Hari ini";
                            }
                            ?>
                        </td>
                        <!-- <td class="text-left"><?= $storeInv2['nama'] ?></td> -->

                    </tr>
                <?php endif; ?>
                <!-- <tr>
                    <td colspan="4"></td>
                    <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore2, 0, '.', ',') ?></th>
                    <td colspan="1"></td>
                </tr> -->
                <?php
                $totalAll += $totalStore2;
                ?>
            <?php endif; ?>

        <?php endforeach; ?>
        <tr>
            <td colspan="4"></td>
            <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore2, 0, '.', ',') ?></th>
            <td colspan="1"></td>
        </tr>
        <tr>
            <th colspan="6">Umur Hutang Lebih Dari 15 Hari</th>
        </tr>
        <?php
        $no16 = 0;
        foreach ($invoice as $storeInv3) : ?>
            <?php
            $jatuhTempo = date('d M Y', strtotime("+" . $storeInv3['termin_payment'] . " days", strtotime($storeInv3['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $daysWithOperan = $operan . $days;
            ?>
            <?php if ($daysWithOperan >= 16) : ?>
                <?php
                // echo "AWDAWDA";
                $id_invoice = $storeInv3['id_invoice'];
                $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                $sisaHutang = $storeInv3['total_invoice'] - $payment['amount_payment'];
                $totalStore3 += $sisaHutang;

                ?>
                <?php if ($sisaHutang > 0) :

                    $no16++;
                ?>
                    <tr>
                        <td class="text-center"><?= $no16 ?></td>
                        <td class="text-center"><?= $storeInv3['nama'] ?></td>
                        <td class="text-center"><?= $storeInv3['no_invoice'] ?></td>
                        <td class="text-center"><?= date("d M Y", strtotime($storeInv3['date_invoice'])) ?></td>
                        <td class="text-center">
                            <?php if ($storeInv3['termin_payment'] == 0 || $storeInv3['termin_payment'] == 1 || $storeInv3['termin_payment'] == 2) { ?>
                                <?= date("d M Y", strtotime($storeInv3['date_invoice'])) ?>
                            <?php } else { ?>
                                <?= $jatuhTempo ?>
                            <?php } ?>
                        </td>
                        <td class="text-right"><?= number_format($sisaHutang, 0, '.', ',') ?></td>
                        <td class="text-center">
                            <?php
                            if ($days != 0) {
                                echo $operan . $days . " hari";
                            } else {
                                echo "Hari ini";
                            }
                            ?>
                        </td>
                        <!-- <td class="text-left"><?= $storeInv3['nama'] ?></td> -->

                    </tr>
                <?php endif; ?>
                <!-- <tr>
                    <td colspan="4"></td>
                    <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore3, 0, '.', ',') ?></th>
                    <td colspan="1"></td>
                </tr> -->
                <?php
                $totalAll += $totalStore3;
                ?>
            <?php endif; ?>

        <?php endforeach; ?>
        <tr>
            <td colspan="4"></td>
            <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalStore3, 0, '.', ',') ?></th>
            <td colspan="1"></td>
        </tr>
        <!-- <tr>
            : <th colspan="4" class="text-right">Total Keseluruhan: </th>
            <th class="text-right" style="border-top: 1px solid black;"><?= number_format($totalAll, 0, '.', ',') ?></th>
            <td colspan="1"></td>
        </tr> -->

    </table>
</body>

</html>