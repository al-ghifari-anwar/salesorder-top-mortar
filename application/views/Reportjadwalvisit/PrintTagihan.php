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
    <h1 class="text-center">Tagihan Tidak Tervisit - Cluster <?= $cluster ?></h1>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Kota</th>
            <th style="border-bottom: 1px solid black;">Filter</th>
            <th style="border-bottom: 1px solid black;">Kategori</th>
            <th style="border-bottom: 1px solid black;">Last Visit</th>
            <th style="border-bottom: 1px solid black;">Hari</th>
            <th style="border-bottom: 1px solid black;">Total</th>
            <!-- <th style="border-bottom: 1px solid black;">Tervisit</th> -->
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

            $status_color = 'text-red';
            $status_visit_color = 'text-red';
            $has_session = "";

            if ($visit) {
                $status_visit_color = 'text-green';
            } else {
                $status_visit_color = 'text-red';
            }
            ?>
            <?php if ($visit): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $jadwalVisit['nama'] ?> (<?= $jadwalVisit['id_contact'] ?>)</td>
                    <td><?= $jadwalVisit['nama_city'] ?></td>
                    <td><?= $jadwalVisit['filter_jadwal_visit'] ?></td>
                    <td class="text-center"><?= $jadwalVisit['kategori_jadwal_visit'] ?></td>
                    <td class="text-center"><?= $jadwalVisit['is_new'] == 0 ? $jadwalVisit['last_visit'] : 'Blm Visit' ?></td>
                    <td class="text-center"><?= $jadwalVisit['days_jadwal_visit'] ?></td>
                    <td class="text-center"><?= number_format($jadwalVisit['total_invoice'], 0, '.', ',') ?></td>
                    <!-- <td class="text-center <?= $status_visit_color ?>"><?= $visit != null ? 'Yes' : 'No' ?></td> -->
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</body>

</html>