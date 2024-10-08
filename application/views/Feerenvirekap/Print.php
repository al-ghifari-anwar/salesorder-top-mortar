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
    <title><?= "Rekap Sales " . $city['nama_city']  ?></title>
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
    <h1 class="text-center">Rekap Fee Renvi</h1>
    <h4 class="text-center">Bulan <?= date("F", strtotime("2023-" . $month . "-01")) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">Sales</th>
            <th style="border-bottom: 1px solid black;">Jumlah Toko Di Visit</th>
            <th style="border-bottom: 1px solid black;">Jumlah Toko Lunas</th>
        </tr>
        <?php if ($sales != null) : ?>
            <?php foreach ($sales as $sales) : ?>
                <tr>
                    <td class="text-left"><?= $sales['full_name'] ?></td>
                    <?php
                    $id_user = $sales['id_user'];
                    $this->db->select("DATE(date_visit) as date_visit");
                    $this->db->group_by("DATE(date_visit)");
                    $getDateVisit = $this->db->get_where('tb_visit', ['id_user' => $id_user, 'MONTH(date_visit)' => $month, 'YEAR(date_visit)' => date("Y")])->result_array();

                    $totalToko = 0;
                    foreach ($getDateVisit as $getDateVisit) {
                        $date = $getDateVisit['date_visit'];
                        $this->db->select("COUNT(*) AS jml_toko");
                        $this->db->group_by('tb_visit.id_contact');
                        $getGroupedContact = $this->db->get_where('tb_visit', ['id_user' => $id_user, 'DATE(date_visit)' => $date])->row_array();
                        $totalToko += $getGroupedContact['jml_toko'];
                    }
                    ?>
                    <td class="text-center"><?= $totalToko ?></td>
                    <td class="text-left"></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>