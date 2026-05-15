<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= "Laporan Target Visit " . $user['full_name']  ?></title>
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
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Laporan Target Visit <?= $user['full_name'] ?></h1>
    <h4 class="text-center">Tanggal <?= date('d M Y', strtotime($dateFrom)) . " - " . date('d M Y', strtotime($dateTo)) ?></h4>
    <table class="border">
        <tr>
            <th class="border">No</th>
            <th class="border">Toko</th>
            <th class="border">Tgl Visit</th>
            <th class="border">Last Visit</th>
            <th class="border">Jml Visit</th>
            <th class="border">Skor</th>
        </tr>
        <?php
        $no = 1;
        foreach ($groupedVisits as $groupedVisit): ?>
            <?php
            $id_contact = $groupedVisit['id_contact'];
            $id_user = $groupedVisit['id_user'];

            $contact = $this->MContact->getById($id_contact);

            // Check Yes
            $checkYes = $this->db->get_where('tb_jadwal_visit', ['id_contact' => $id_contact, 'date_jadwal_visit >=' => $dateFrom, 'date_jadwal_visit <=' => $dateTo, 'is_yes' => 1])->row_array();

            // Date Visit Most
            $this->db->not_like('source_visit', 'absen');
            $this->db->order_by('id_visit', 'DESC');
            $mostVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'id_user' => $id_user])->row_array();

            $dateMostVisit = date('Y-m-d', strtotime($mostVisit['date_visit']));

            // Date last visit before most
            $this->db->not_like('source_visit', 'absen');
            $this->db->where('DATE(date_visit) <', $dateMostVisit);
            $this->db->order_by('id_visit', 'DESC');
            $lastVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'id_user' => $id_user])->row_array();

            $nameColor = '';
            if ($checkYes) {
                $nameColor = 'text-green';
            }

            $paymentScore = $controller->paymentScoring($contact);
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="<?= $nameColor ?>"><?= $groupedVisit['nama'] ?></td>
                <td class="text-center"><?= date('d F Y', strtotime($mostVisit['date_visit'])) ?></td>
                <td class="text-center"><?= date('d F Y', strtotime($lastVisit['date_visit'])) ?></td>
                <td class="text-center"><?= $groupedVisit['jmlVisit'] ?></td>
                <td class="text-center"><?= $paymentScore ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>