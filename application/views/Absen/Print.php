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
    <title>Absen Harian</title>
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

        .bg-red {
            background-color: red;
        }
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Absen Harian </h1>
    <h4 class="text-center">Tgl. <?= date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></h4>
    <table class="border">
        <tr>
            <th class="border">No.</th>
            <th class="border">Nama</th>
            <?php foreach ($periods as $period) : ?>
                <?php
                $tgl = $period->format("d");
                $day = $period->format("l");
                ?>
                <th class="border <?= $day == 'Sunday' ? 'text-red' : '' ?>"><?= $tgl ?></th>
            <?php endforeach; ?>
        </tr>
        <?php
        $no = 1;
        foreach ($users as $user) : ?>
            <tr>
                <td class="text-center border"><?= $no++ ?></td>
                <td class="text-left border"><?= $user['full_name'] ?></td>
                <?php foreach ($periods as $period_user) : ?>
                    <?php
                    $id_user = $user['id_user'];
                    $tgl = $period_user->format("d");
                    $day = $period_user->format("l");
                    $date = $period_user->format("Y-m-d");

                    $absen = $this->db->get_where('tb_visit', ['id_user' => $id_user, 'DATE(date_visit)' => $date, 'source_visit' => 'absen_in'])->row_array();
                    ?>
                    <th class="border <?= $day == 'Sunday' ? 'bg-red' : '' ?>"><?= $absen != null ? 'Y' : '' ?></th>
                <?php endforeach; ?>
            </tr>
        <?php endforeach ?>
    </table>
</body>

</html>