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
    <h1 class="text-center">Rekap Status Toko</h1>
    <h4 class="text-center">Bulan <?= date("F", strtotime("2023-" . $month . "-01")) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">Nama Toko</th>
            <th style="border-bottom: 1px solid black;">Tgl Order</th>
            <th style="border-bottom: 1px solid black;">Nomor HP</th>
        </tr>
        <?php if ($store != null) : ?>
            <tr>
                <th class="text-center" colspan="3">Data -> Active</th>
            </tr>
            <?php foreach ($store as $store1) : ?>
                <?php
                $this->db->select("MAX(date_closing) as date_order");
                $this->db->group_by('id_contact');
                $lastOrder = $this->db->get_where('tb_surat_jalan', ['id_contact' => $store1['id_contact'], 'is_closing' => 1])->row_array()
                ?>
                <?php if ($store1['status_from'] == 'data' && $store1['status_to'] == 'active') : ?>
                    <tr>
                        <td class="text-center"><?= $store1['nama'] ?></td>
                        <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_order'])) ?></td>
                        <td class="text-center"><?= $store1['nomorhp'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="3">Passive -> Active</th>
            </tr>
            <?php foreach ($store as $store2) : ?>
                <?php
                $this->db->select("MAX(date_closing) as date_order");
                $this->db->group_by('id_contact');
                $lastOrder = $this->db->get_where('tb_surat_jalan', ['id_contact' => $store2['id_contact'], 'is_closing' => 1])->row_array()
                ?>
                <?php if ($store2['status_from'] == 'passive' && $store2['status_to'] == 'active') : ?>
                    <tr>
                        <td class="text-center"><?= $store2['nama'] ?></td>
                        <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_order'])) ?></td>
                        <td class="text-center"><?= $store2['nomorhp'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="3">Active -> Passive</th>
            </tr>
            <?php foreach ($store as $store3) : ?>
                <?php
                $this->db->select("MAX(date_closing) as date_order");
                $this->db->group_by('id_contact');
                $lastOrder = $this->db->get_where('tb_surat_jalan', ['id_contact' => $store3['id_contact'], 'is_closing' => 1])->row_array()
                ?>
                <?php if ($store3['status_from'] == 'active' && $store3['status_to'] == 'passive') : ?>
                    <tr>
                        <td class="text-center"><?= $store3['nama'] ?></td>
                        <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_order'])) ?></td>
                        <td class="text-center"><?= $store3['nomorhp'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>