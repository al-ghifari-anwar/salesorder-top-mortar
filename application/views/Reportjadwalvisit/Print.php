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
            $contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();
            $visit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d', strtotime($date))])->row_array();
            $is_visited = 0;
            $id_distributor = $city['id_distributor'];

            $status_color = 'text-red';
            $has_session = "";

            if ($jadwalVisit['kategori_jadwal_visit'] == 'Toko Baru' || $jadwalVisit['kategori_jadwal_visit'] == 'passive' || $jadwalVisit['kategori_jadwal_visit'] == 'Akan passive' || $jadwalVisit['kategori_jadwal_visit'] == 'Toko Aktif') {

                $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                $wa_token = $haloai['token_haloai'];
                $business_id = $haloai['business_id_haloai'];
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://www.haloai.co.id/api/open/room/v1/details?phoneNumber=' . $contact['nomorhp'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $wa_token,
                        'X-HaloAI-Business-Id: ' . $business_id,
                        'Content-Type: application/json'
                    ),
                ));

                $responseLastMsg = curl_exec($curl);

                curl_close($curl);

                $resLastMsg = json_decode($responseLastMsg, true);

                if (isset($resLastMsg['data'])) {
                    if (isset($resLastMsg['data']['sessionStatus'])) {
                        if ($resLastMsg['data']['sessionStatus'] != 'expired') {
                            $is_visited = 1;
                            $status_color = 'text-green';
                            $has_session = "yes" . $contact['nomorhp'];
                        }
                    } else {
                        $has_session = "no" . $contact['nomorhp'];
                    }
                }
            } else {
                if ($visit) {
                    $status_color = 'text-green';
                    $is_visited = 1;
                }
            }
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $jadwalVisit['nama'] ?></td>
                <td><?= $jadwalVisit['filter_jadwal_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['kategori_jadwal_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['is_new'] == 0 ? $jadwalVisit['last_visit'] : 'Blm Visit' ?></td>
                <td class="text-center"><?= $jadwalVisit['days_jadwal_visit'] ?></td>
                <td class="text-center <?= $status_color ?>"><?= $is_visited == 1 ? 'Yes' : 'No' ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>