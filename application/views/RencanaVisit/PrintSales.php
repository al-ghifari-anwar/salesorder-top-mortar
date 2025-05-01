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
    <title><?= "Rekap Rencana Visit " . $city['nama_city']  ?></title>
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
    <h1 class="text-center">Absen Sales <?= $city['nama_city'] ?> (Rencana Visit)</h1>
    <h4 class="text-center">Bulan <?= date("F", strtotime("2023-" . $month . "-01")) ?></h4>
    <table class="border">
        <tr>
            <th class="border">Customer</th>
            <th class="border">Address</th>
            <th class="border">Category</th>
            <th class="border">Purpose</th>
            <th class="border">Feedback</th>
            <th class="border">Date</th>
            <th class="border">Distance(KM)</th>
            <th class="border">Time</th>
        </tr>
        <?php if ($user != null) : ?>
            <?php foreach ($user as $user) : ?>
                <?php
                $id_city = $city['id_city'];
                $id_user = $user['id_user'];

                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
                $this->db->group_by('DATE(tb_visit.date_visit)');
                $dateGroup = $this->db->get_where("tb_visit", ['id_user' => $id_user, 'MONTH(date_visit)' => $month, 'tb_contact.id_city' => $id_city])->result_array();

                ?>
                <tr>
                    <th colspan="7" class="border text-left" style="color: blue;">User: <?= $user['full_name'] ?></th>
                </tr>
                <?php
                $total = 0;
                foreach ($dateGroup as $dateGroup) : ?>
                    <tr>
                        <th colspan="7" class="border text-left"><?= date("d/m/Y", strtotime($dateGroup['date_visit'])) ?></th>
                    </tr>
                    <?php

                    $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
                    $this->db->order_by('tb_visit.date_visit', 'DESC');
                    $visitByDate = $this->db->get_where('tb_visit', ['id_user' => $id_user, 'DATE(date_visit)' => date("Y-m-d", strtotime($dateGroup['date_visit'])), 'tb_visit.is_deleted' => 0, 'tb_contact.id_city' => $id_city, 'is_approved' => 1, 'source_visit !=' => 'normal'])->result_array();

                    $this->db->select("COUNT(*) AS total_visit");
                    $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
                    $this->db->group_by('tb_visit.id_contact');
                    $getTotal = $this->db->get_where('tb_visit', ['id_user' => $id_user, 'DATE(date_visit)' => date("Y-m-d", strtotime($dateGroup['date_visit'])), 'tb_visit.is_deleted' => 0, 'tb_contact.id_city' => $id_city, 'is_approved' => 1, 'source_visit !=' => 'normal'])->num_rows();
                    $total += $getTotal;
                    ?>
                    <?php foreach ($visitByDate as $visit) : ?>
                        <tr class="border">
                            <td class="border"><?= $visit['nama'] ?></td>
                            <td class="border"><?= $visit['address'] ?></td>
                            <td class="border">
                                <?php
                                if ($visit['source_visit'] == 'jatem1' || $visit['source_visit'] == 'jatem2' || $visit['source_visit'] == 'jatem3' || $visit['source_visit'] == 'weekly') {
                                    echo "Penagihan";
                                } else if ($visit['source_visit'] == 'passive' || $visit['source_visit'] == 'voucher' || $visit['source_visit'] == 'renvisales') {
                                    echo "Passive";
                                } else if ($visit['source_visit'] == 'mg') {
                                    echo "MG";
                                }
                                ?>
                            </td>
                            <td class="border"><?= $visit['laporan_visit'] ?> <?= $visit['pay_date'] != null ? ' - Tanggal dijanjikan: ' . date("d F Y", strtotime($visit['pay_date'])) : ''  ?> </td>
                            <td class="border"><?= $visit['approve_message'] ?></td>
                            <td class="border"><?= date("d M, Y", strtotime($visit['date_visit'])) ?></td>
                            <td class="border"><?= $visit['distance_visit'] ?></td>
                            <td class="border"><?= date("H:i", strtotime($visit['date_visit'])) ?> - (<?= $visit['id_visit'] ?>)</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <tr>
                    <th colspan="7" class="border text-left" style="color: blue;">Total: <?= $total ?></th>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>