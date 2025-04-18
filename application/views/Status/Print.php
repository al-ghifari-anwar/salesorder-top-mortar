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

        .bg-green {
            background-color: green;
        }

        .bg-red {
            background-color: red;
        }
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Rekap Status Toko</h1>
    <h4 class="text-center">Bulan <?= date("F", strtotime($year . "-" . $month . "-01")) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">Nama Toko</th>
            <th style="border-bottom: 1px solid black;">Tgl Order</th>
            <th style="border-bottom: 1px solid black;">Kota</th>
            <th style="border-bottom: 1px solid black;">Reputation</th>
        </tr>
        <?php if ($store != null) : ?>
            <tr>
                <th class="text-center" colspan="4">Data -> Active</th>
            </tr>
            <?php foreach ($store as $store1) : ?>
                <?php
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
                $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
                $store1 = $this->db->get_where('tb_status_change', ['id_status_change ' => $store1['id_status_change']])->row_array();
                $this->db->select("MAX(date_closing) as date_order");
                $this->db->group_by('id_contact');
                $lastOrder = $this->db->get_where('tb_surat_jalan', ['id_contact' => $store1['id_contact'], 'is_closing' => 1])->row_array()
                ?>
                <?php if ($store1['status_from'] == 'data' && $store1['status_to'] == 'active') : ?>
                    <tr>
                        <td class="text-center"><?= $store1['nama'] ?></td>
                        <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_order'])) ?></td>
                        <td class="text-center"><?= $store1['nama_city'] ?></td>
                        <td class="text-center <?= $store1['reputation'] == 'good' ? 'bg-green' : 'bg-red' ?>"><?= $store1['reputation'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="4">Passive -> Active</th>
            </tr>
            <?php foreach ($store as $store2) : ?>
                <?php
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
                $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
                $store2 = $this->db->get_where('tb_status_change', ['id_status_change ' => $store2['id_status_change']])->row_array();
                $this->db->select("MAX(date_closing) as date_order");
                $this->db->group_by('id_contact');
                $lastOrder = $this->db->get_where('tb_surat_jalan', ['id_contact' => $store2['id_contact'], 'is_closing' => 1])->row_array()
                ?>
                <?php if ($store2['status_from'] == 'passive' && $store2['status_to'] == 'active') : ?>
                    <tr>
                        <td class="text-center"><?= $store2['nama'] ?></td>
                        <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_order'])) ?></td>
                        <td class="text-center"><?= $store2['nama_city'] ?></td>
                        <td class="text-center <?= $store2['reputation'] == 'good' ? 'bg-green' : 'bg-red' ?>"><?= $store2['reputation'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="4">Active -> Passive</th>
            </tr>
            <?php foreach ($store as $store3) : ?>
                <?php
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
                $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
                $store3 = $this->db->get_where('tb_status_change', ['id_status_change ' => $store3['id_status_change']])->row_array();
                $this->db->select("MAX(date_closing) as date_order");
                $this->db->group_by('id_contact');
                $lastOrder = $this->db->get_where('tb_surat_jalan', ['id_contact' => $store3['id_contact'], 'is_closing' => 1])->row_array()
                ?>
                <?php if ($store3['status_from'] == 'active' && $store3['status_to'] == 'passive' && $lastOrder != null) : ?>
                    <tr>
                        <td class="text-center"><?= $store3['nama'] ?></td>
                        <td class="text-center"><?= date("d M, Y", strtotime($lastOrder['date_order'])) ?></td>
                        <td class="text-center"><?= $store3['nama_city'] ?></td>
                        <td class="text-center <?= $store3['reputation'] == 'good' ? 'bg-green' : 'bg-red' ?>"><?= $store3['reputation'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>