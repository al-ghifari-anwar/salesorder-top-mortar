<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen Harian</title>
</head>

<body>
    <style>
        body {
            font-family: monospace;
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

        .border-l {
            border-left: 1px solid black;
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

        .bg-red {
            background-color: red;
        }
    </style>
    <h1 class="text-center">Laporan Absensi</h1>
    <h4 class="text-center"><?= date('d F Y', strtotime($dateFrom)) . ' - ' . date('d F Y', strtotime($dateTo)) ?></h4>
    <table class="border">
        <tr>
            <th class="text-left border" colspan="4">Nama</th>
        </tr>
        <?php
        $no = 1;
        foreach ($users as $user): ?>
            <tr>
                <th class="text-left border" colspan="4"><?= $user['full_name'] ?></th>
            </tr>
            <tr>
                <th class="border">Tanggal</th>
                <th class="border">Jam Masuk</th>
                <th class="border">Lokasi</th>
            </tr>
            <?php
            $id_user = $user['id_user'];
            $level_user = $user['level_user'];

            $this->db->where_in('source_visit', ['absen_in_bc', 'absen_in_store']);
            $absensis = $this->db->get_where('tb_visit', ['id_user' => $id_user, 'DATE(date_visit) >=' => $dateFrom, 'DATE(date_visit) <=' => $dateTo])->result_array();
            ?>
            <?php foreach ($absensis as $absensi): ?>
                <?php
                $dateAbsen = date('Y-m-d', strtotime($absensi['date_visit']));

                $id_contact = $absensi['id_contact'];
                $lokasi = '';

                if ($absensi['source_visit'] == 'absen_in_bc') {
                    $gudang = $this->db->get_where('tb_gudang', ['id_gudang' => $id_contact])->row_array();
                    $lokasi = $gudang['nama_gudang'];
                } else if ($absensi['source_visit'] == 'absen_in_store') {
                    $store = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();
                    $lokasi = $store['nama'];
                }
                ?>
                <tr>
                    <td class="text-center border"><?= date('d M Y', strtotime($absensi['date_visit'])) ?></td>
                    <td class="text-center border"><?= date('H:i', strtotime($absensi['date_visit'])) ?></td>
                    <td class="text-left border"><?= $lokasi ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>
</body>

</html>