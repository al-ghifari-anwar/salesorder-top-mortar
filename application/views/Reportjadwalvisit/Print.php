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
    <title>Checklist JadwalVisit - <?= $this->session->userdata('full_name') ?></title>
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

        .text-red {
            color: red;
        }

        .text-green {
            color: green;
        }
    </style>
    <!-- <h3 class="text-center"><?= $this->session->userdata('full_name') ?></h3> -->
    <h3 class="text-center"><?= date('d F Y', strtotime($date)) . ' - ' . date('H:i:s') ?></h3>
    <h1 class="text-center">Report Jadwal Visit (<?= $city['nama_city'] ?>) - Cluster <?= $cluster ?></h1>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Filter</th>
            <th style="border-bottom: 1px solid black;">Kategori</th>
            <th style="border-bottom: 1px solid black;">Last Visit</th>
            <th style="border-bottom: 1px solid black;">Hari</th>
            <th style="border-bottom: 1px solid black;">Tervisit</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php
        // Filter 1 (Cluster & days 0 - 7)
        $no = 1;
        foreach ($jadwalVisits as $jadwalVisit): ?>
            <?php
            $id_contact = $jadwalVisit['id_contact'];
            $visit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d', strtotime($date))])->row_array();

            $status_color = 'text-red';
            if ($visit) {
                $status_color = 'text-green';
            }
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $jadwalVisit['nama'] ?></td>
                <td><?= $jadwalVisit['filter_jadwal_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['kategori_jadwal_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['is_new'] == 0 ? $jadwalVisit['last_visit'] : 'Blm Visit' ?></td>
                <td class="text-center"><?= $jadwalVisit['days_jadwal_visit'] ?></td>
                <td class="text-center <?= $status_color ?>"><?= $visit ? 'Yes' : 'No' ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>