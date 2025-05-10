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
    <title>Customer Visit <?= $city['nama_city'] ?></title>
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

        .text-blue {
            color: blue;
        }
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Rekap Target Tukang</h1>
    <h4 class="text-center">Tgl. <?= date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Nama</th>
            <th style="border-bottom: 1px solid black;">No. HP</th>
            <th style="border-bottom: 1px solid black;">Tgl Lahir</th>
            <th style="border-bottom: 1px solid black;">Kota</th>
            <th style="border-bottom: 1px solid black;">Tgl</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php foreach ($users as $user): ?>
            <?php
            $id_user = $user['id_user'];
            $from = date("Y-m-d", strtotime($dateFrom));
            $to = date("Y-m-d", strtotime($dateTo));
            $tukangs = $this->db->get_where('tb_tukang', ['id_user_post' => $id_user, 'DATE(created_at) >=' => $from, 'DATE(created_at) <=' => $to])->result_array();
            ?>
            <tr>
                <th colspan="6" class="border text-left"><?= $user['full_name'] ?></th>
            </tr>
            <?php
            $noTukang = 1;
            $countTukang = 0;
            foreach ($tukangs as $tukang): ?>
                <?php
                $countTukang++;
                $id_city = $tukang['id_city'];
                $city = $this->db->get_where('tb_city', ['id_city' => $id_city])->row_array();
                ?>
                <tr>
                    <td class="text-center border-r border-l"><?= $noTukang++; ?></td>
                    <td class="border-r"><?= $tukang['nama'] ?></td>
                    <td class="text-center border-r"><?= $tukang['nomorhp'] ?></td>
                    <td class="text-center border-r"><?= $tukang['tgl_lahir'] != '0000-00-00 00:00:00' ? date('d M Y', strtotime($tukang['tgl_lahir'])) : '-' ?></td>
                    <td class="text-center border-r"><?= $city['nama_city'] ?></td>
                    <td class="text-center border-r"><?= date('d F Y - H:i', strtotime($tukang['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="6" class="border text-left text-blue">Total: <?= $countTukang ?></th>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>