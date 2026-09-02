<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist JadwalVisit </title>
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
    <h3 class="text-center"><?= date('F', strtotime($month)) ?></h3>
    <h1 class="text-center">Report Jadwal Visit Bulanan (<?= $city['nama_city'] ?>)</h1>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Cluster/Hari</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Filter</th>
            <th style="border-bottom: 1px solid black;">Kategori</th>
            <th style="border-bottom: 1px solid black;">Last Visit</th>
            <th style="border-bottom: 1px solid black;">Hari</th>
            <th style="border-bottom: 1px solid black;">Total</th>
            <th style="border-bottom: 1px solid black;">Tervisit</th>
            <th style="border-bottom: 1px solid black;">Terbalas</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php
        // Filter 1 (Cluster & days 0 - 7)
        $no = 1;
        foreach ($jadwalVisits as $jadwalVisit): ?>
            <?php
            $id_contact = $jadwalVisit['id_contact'];
            $contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();
            $visit = $this->db->not_like('source_visit', 'absen')->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d', strtotime($date))])->row_array();
            $is_visited = 0;
            $id_distributor = $city['id_distributor'];

            $status_color = 'text-red';
            $status_visit_color = 'text-red';
            $has_session = "";

            if ($jadwalVisit['is_yes'] == 1) {
                $status_color = 'text-green';
                $is_visited = 1;
            } else {
                $status_color = 'text-red';
                $is_visited = 0;
            }

            if ($visit) {
                $status_visit_color = 'text-green';
            } else {
                $status_visit_color = 'text-red';
            }

            $date = $jadwalVisit['date_jadwal_visit'];

            $cluster = 0;
            if (date('D', strtotime($date)) == 'Mon' || date('D', strtotime($date)) == 'Thu') {
                $cluster = 1;
            } else if (date('D', strtotime($date)) == 'Tue' || date('D', strtotime($date)) == 'Fri') {
                $cluster = 2;
            } else if (date('D', strtotime($date)) == 'Wed' || date('D', strtotime($date)) == 'Sat') {
                $cluster = 3;
            }

            $dayName = '';
            if (date('D', strtotime($date)) == 'Mon') {
                $dayName = 'senin';
            } else if (date('D', strtotime($date)) == 'Tue') {
                $dayName = 'selasa';
            } else if (date('D', strtotime($date)) == 'Wed') {
                $dayName = 'rabu';
            } else if (date('D', strtotime($date)) == 'Thu') {
                $dayName = 'kamis';
            } else if (date('D', strtotime($date)) == 'Fri') {
                $dayName = 'jumat';
            } else if (date('D', strtotime($date)) == 'Sat') {
                $dayName = 'sabtu';
            }
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= $cluster . ' / ' . $dayName ?></td>
                <td><?= $jadwalVisit['is_tambahan'] == 1 ? "[+] " : '' ?><?= $jadwalVisit['nama'] ?></td>
                <td><?= $jadwalVisit['is_tambahan'] == 1 ? "Tambahan" : $jadwalVisit['filter_jadwal_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['kategori_jadwal_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['is_new'] == 0 ? $jadwalVisit['last_visit'] : 'Blm Visit' ?></td>
                <td class="text-center"><?= $jadwalVisit['days_jadwal_visit'] ?></td>
                <td class="text-center"><?= number_format($jadwalVisit['total_invoice'], 0, '.', ',') ?></td>
                <td class="text-center <?= $status_visit_color ?>"><?= $visit != null ? 'Yes' : 'No' ?></td>
                <td class="text-center <?= $status_color ?>"><?= $is_visited == 1 ? 'Yes' : 'No' ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>