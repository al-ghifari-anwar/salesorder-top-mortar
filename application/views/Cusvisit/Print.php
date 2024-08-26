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
    <h1 class="text-center">Customer Visit <?= $city['nama_city'] ?></h1>
    <h4 class="text-center">Tgl. <?= date("d M Y", strtotime($dateFrom)) . " - " . date("d M Y", strtotime($dateTo)) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Address</th>
            <th style="border-bottom: 1px solid black;">No. HP</th>
            <th style="border-bottom: 1px solid black;">Visit Tagihan</th>
            <th style="border-bottom: 1px solid black;">Visit Passive</th>
            <th style="border-bottom: 1px solid black;">Total</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php if ($contacts == null) : ?>
            <tr>
                <td colspan="7">No Data</td>
            </tr>
        <?php endif; ?>
        <?php if ($contacts != null) :
            $no = 1;
            $total_visitTagihan = 0;
            $total_visitPassive = 0;
        ?>
            <?php foreach ($contacts as $contact) : ?>
                <?php
                $id_contact = $contact['id_contact'];
                $this->db->group_by('DATE(tb_visit.date_visit)');
                $getVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit) >=' => $dateFrom, 'DATE(date_visit) <=' => $dateTo, 'source_visit !=' => 'normal', 'is_approved' => 1, 'is_deleted' => 0])->result_array();

                $getVisitTagihan = 0;
                $getVisitPassive = 0;
                foreach ($getVisit as $getVisit) {
                    if ($getVisit['source_visit'] == 'jatem1' || $getVisit['source_visit'] == 'jatem2' || $getVisit['source_visit'] == 'jatem3' || $getVisit['source_visit'] == 'weekly' || $getVisit['source_visit'] == 'renvipenagihan') {
                        $getVisitTagihan += 1;
                    } else if ($getVisit['source_visit'] == 'voucher' || $getVisit['source_visit'] == 'passive' || $getVisit['source_visit'] == 'renvisales') {
                        $getVisitPassive += 1;
                    }
                }
                ?>
                <?php if ($getVisit != null) :
                    $total_visitTagihan += $getVisitTagihan;
                    $total_visitPassive += $getVisitPassive;
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $contact['nama'] . " (" . $contact['id_contact'] . ")" ?></td>
                        <td><?= $contact['address'] ?></td>
                        <td><?= $contact['nomorhp'] ?></td>
                        <td class="text-center">
                            <?= $getVisitTagihan == null ? 0 : $getVisitTagihan ?>
                        </td>
                        <td class="text-center">
                            <?= $getVisitPassive == null ? 0 : $getVisitPassive ?>
                        </td>
                        <td class="text-center">
                            <?= $getVisitTagihan + $getVisitPassive ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($getVisit == null) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <?php if ($contact['store_status'] == 'passive') : ?>
                            <td style="color: red;"><?= "[Belum Divisit] " . $contact['nama'] . " (" . $contact['id_contact'] . ")" ?></td>
                        <?php endif; ?>
                        <?php if ($contact['store_status'] == 'active') : ?>
                            <td style="color: green;"><?= "[Active] " . $contact['nama'] . " (" . $contact['id_contact'] . ")" ?></td>
                        <?php endif; ?>
                        <?php if ($contact['store_status'] == 'data') : ?>
                            <td style="color: yellow;"><?= "[Data] " . $contact['nama'] . " (" . $contact['id_contact'] . ")" ?></td>
                        <?php endif; ?>
                        <td><?= $contact['address'] ?></td>
                        <td><?= $contact['nomorhp'] ?></td>
                        <td class="text-center">
                            <?= 0 ?>
                        </td>
                        <td class="text-center">
                            <?= 0 ?>
                        </td>
                        <td class="text-center">
                            <?= 0 ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <tr>
                <td class="text-right border" colspan="4">Total Visit</td>
                <td class="text-center border"><?= $total_visitTagihan ?></td>
                <td class="text-center border"><?= $total_visitPassive ?></td>
                <td class="text-center border"><?= $total_visitTagihan + $total_visitPassive ?></td>
            </tr>
        <?php endif; ?>
    </table>
</body>

</html>