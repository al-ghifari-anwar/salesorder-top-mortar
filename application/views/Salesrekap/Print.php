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
    <h1 class="text-center">Rekap Sales</h1>
    <h4 class="text-center">Bulan <?= date("F", strtotime("2023-" . $month . "-01")) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">Sales</th>
            <th style="border-bottom: 1px solid black;">Data -> Aktif</th>
            <th style="border-bottom: 1px solid black;">Pasif -> Aktif</th>
            <th style="border-bottom: 1px solid black;">Aktif -> Pasif</th>
            <th style="border-bottom: 1px solid black;">Penjualan</th>
            <th style="border-bottom: 1px solid black;">Total Visit</th>
        </tr>
        <?php if ($sales != null) : ?>
            <?php
            $total_visit = 0;
            foreach ($sales as $dataSales) : ?>
                <tr>
                    <th class="text-left"><?= $dataSales['full_name'] . " - " . $dataSales['kode_city'] ?></th>
                    <td colspan="5"></td>
                </tr>
                <?php
                // Get All Visit
                // $this->db->group_by('tb_visit.id_contact');
                $visit = $this->db->get_where('tb_visit', ['id_user' => $dataSales['id_user'], 'MONTH(date_visit)' => $month, 'YEAR(date_visit)' => date("Y")])->result_array();
                // echo json_encode($visit);
                // die;
                $total_visit = count($visit);

                $total_tokoAktif = 0;
                $total_penjualan = 0;

                // Get Penjualan
                $this->db->select('SUM(qty_produk) AS total_qty');
                $this->db->join('tb_surat_jalan', 'tb_detail_surat_jalan.id_surat_jalan = tb_surat_jalan.id_surat_jalan');
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
                $penjualan = $this->db->get_where('tb_detail_surat_jalan', ['MONTH(date_closing)' => $month, 'YEAR(date_closing)' => date("Y"), 'id_city' => $city['id_city']])->row_array();
                $total_penjualan += $penjualan['total_qty'];
                // echo $this->db->last_query();
                // die;

                // Get Toko Aktif
                $tokoAktif = $this->db->get_where('tb_contact', ['store_status' => 'active'])->result_array();
                $total_tokoAktif += count($tokoAktif);

                // Get Toko Data -> Aktif
                $this->db->select("MAX(id_status_change) AS id_status_change, tb_status_change.id_contact, MAX(tb_status_change.created_at) AS created_at");
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
                $this->db->group_by('tb_status_change.id_contact');
                $dataToActive = $this->db->get_where('tb_status_change', ['tb_contact.id_city' => $city['id_city'], 'MONTH(tb_status_change.created_at)' => $month, 'YEAR(tb_status_change.created_at)' => date("Y")])->result_array();
                $total_dataToActive = 0;
                foreach ($dataToActive as $dataToActive) {
                    $dataDataActive = $this->db->get_where('tb_status_change', ['id_status_change ' => $dataToActive['id_status_change']])->row_array();
                    if ($dataDataActive['status_from'] == 'data' && $dataDataActive['status_to'] == 'active') {
                        $total_dataToActive++;
                    }
                }
                // Get Toko Pasif -> Aktif
                $this->db->select("MAX(id_status_change) AS id_status_change, tb_status_change.id_contact, MAX(tb_status_change.created_at) AS created_at");
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
                $this->db->group_by('tb_status_change.id_contact');
                $passiveToActive = $this->db->get_where('tb_status_change', ['tb_contact.id_city' => $city['id_city'], 'MONTH(tb_status_change.created_at)' => $month, 'YEAR(tb_status_change.created_at)' => date("Y")])->result_array();
                $total_passiveToActive = 0;
                foreach ($passiveToActive as $passiveToActive) {
                    $dataPassiveActive = $this->db->get_where('tb_status_change', ['id_status_change ' => $passiveToActive['id_status_change']])->row_array();
                    if ($dataPassiveActive['status_from'] == 'passive' && $dataPassiveActive['status_to'] == 'active') {
                        $total_passiveToActive++;
                    }
                }
                // Get Toko Aktif -> Pasif
                $this->db->select("MAX(id_status_change) AS id_status_change, tb_status_change.id_contact, MAX(tb_status_change.created_at) AS created_at");
                $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
                $this->db->group_by('tb_status_change.id_contact');
                $activeToPassive = $this->db->get_where('tb_status_change', ['tb_contact.id_city' => $city['id_city'], 'MONTH(tb_status_change.created_at)' => $month, 'YEAR(tb_status_change.created_at)' => date("Y")])->result_array();
                // echo $this->db->last_query();
                // die;
                $total_activeToPassive = 0;
                foreach ($activeToPassive as $activeToPassive) {
                    $dataActivePassive = $this->db->get_where('tb_status_change', ['id_status_change ' => $activeToPassive['id_status_change']])->row_array();
                    if ($dataActivePassive['status_from'] == 'active' && $dataActivePassive['status_to'] == 'passive') {
                        $total_activeToPassive++;
                    }
                }
                ?>
                <tr>
                    <td></td>
                    <td class="text-center"><?= $total_dataToActive ?></td>
                    <td class="text-center"><?= $total_passiveToActive ?></td>
                    <td class="text-center"><?= $total_activeToPassive ?></td>
                    <td class="text-center"><?= $total_penjualan ?></td>
                    <td class="text-center"><?= $total_visit ?></td>
                </tr>
                <tr>
                    <th class="text-center">Total Fee</th>
                    <td class="text-center" style="border-top: 1px solid black;"><?= number_format($total_dataToActive * 100000, '0', ',', '.') ?></td>
                    <td class="text-center" style="border-top: 1px solid black;"><?= number_format($total_passiveToActive * 50000, '0', ',', '.') ?></td>
                    <td class="text-center" style="border-top: 1px solid black;"><?= number_format($total_activeToPassive * -50000, '0', ',', '.') ?></td>
                    <td class="text-center" style="border-top: 1px solid black;"><?= number_format($total_penjualan * 250, '0', ',', '.') ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>