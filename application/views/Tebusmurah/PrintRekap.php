<?php
var_dump($city);
die;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= "Tebus Murah Kota " . $city['nama_city']  ?></title>
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
    <h1 class="text-center">Data Toko Tebus Murah</h1>
    <h3 class="text-center">Kota <?= $city['nama_city'] ?></h3>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">Nama Toko</th>
            <th style="border-bottom: 1px solid black;">Tgl Tebus Murah</th>
            <th style="border-bottom: 1px solid black;">Order Lanjutan</th>
        </tr>
        <?php
        $totalTebusmurah = 0;
        $totalOrderLanjutan = 0;
        foreach ($contacts as $contact): ?>
            <?php
            $totalTebusmurah += 1;
            $sjTebusmurah = $this->db->where('id_contact', $contact['id_contact'])->where('is_tebus_murah', 1)->get('tb_surat_jalan')->row_array();
            $sjReguler = $this->db->where('id_contact', $contact['id_contact'])->where('is_tebus_murah', 0)->where('DATE(dalivery_date) >=', date('d M Y', strtotime($sjTebusmurah['dalivery_date'])))->get('tb_surat_jalan')->row_array();

            if ($sjReguler) {
                $totalOrderLanjutan += 1;
            }
            ?>
            <tr>
                <td><?= $contact['nama'] ?></td>
                <td><?= date('d M Y', strtotime($sjTebusmurah['dalivery_date'])) ?></td>
                <td><?= $sjReguler ? 'Ya' : 'Tidak' ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th>
                Total Tebus Murah: <?= $totalTebusmurah ?>
                <br>
                Total Order Lanjutan: <?= $totalOrderLanjutan ?>
            </th>
        </tr>
    </table>
</body>

</html>