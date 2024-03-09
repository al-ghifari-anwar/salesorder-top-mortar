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
    <title><?= "Rekap Sales " . $city['nama_city']  ?></title>
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
    <h1 class="text-center">Data Toko Passive</h1>
    <h3 class="text-center">Kota <?= $city['nama_city'] ?></h3>
    <h4 class="text-center">Bulan <?= date("M") ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">Nama Toko</th>
            <th style="border-bottom: 1px solid black;">Last Order</th>
            <th style="border-bottom: 1px solid black;">Reputation</th>
        </tr>
        <tr>
            <th colspan="3" class="text-center">Pasif lebih dari 2 bulan</th>
        </tr>
        <?php if ($contact_passive != null) : ?>
            <?php foreach ($contact_passive as $contact_passive) : ?>
                <?php
                $id_contact = $contact_passive['id_contact'];
                $lastOrder = $this->db->query("SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact")->row_array();
                ?>
                <?php if ($lastOrder != null) : ?>
                    <?php
                    $dateMin2Month = date('Y-m-d', strtotime("-2 month"));
                    $dateLastOrder = date("Y-m-d", strtotime($lastOrder['date_closing']));
                    ?>
                    <?php if ($dateLastOrder < $dateMin2Month) : ?>
                        <tr>
                            <td><?= $contact_passive['nama'] ?></td>
                            <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_closing'])) ?></td>
                            <td class="text-center"><?= $contact_passive['reputation'] ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <th colspan="3" class="text-center">Akan passive dalam 2 minggu</th>
        </tr>
        <?php if ($contact_active != null) : ?>
            <?php foreach ($contact_active as $contact_active) : ?>
                <?php
                $id_contact = $contact_active['id_contact'];
                $lastOrder = $this->db->query("SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact")->row_array();
                ?>
                <?php if ($lastOrder != null) : ?>
                    <?php
                    $dateMin6Week = date('Y-m-d', strtotime("-6 week"));
                    $dateMin2Month = date("Y-m-d", strtotime("-2 month"));
                    $dateLastOrder = date("Y-m-d", strtotime($lastOrder['date_closing']));
                    ?>
                    <?php if ($dateLastOrder <= $dateMin6Week && $dateLastOrder >= $dateMin2Month) : ?>
                        <tr>
                            <td><?= $contact_active['nama'] ?></td>
                            <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_closing'])) ?></td>
                            <td class="text-center"><?= $contact_active['reputation'] ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>