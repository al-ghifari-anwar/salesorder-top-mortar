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
    <title><?= "Laporan Stok " . $city['nama_city']  ?></title>
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
    <h1 class="text-center">Laporan Toko Dengan Voucher Expired <?= $city['nama_city'] ?></h1>
    <h4 class="text-center"></h4>
    <table class="border">
        <tr>
            <th class="border">No</th>
            <th class="border">Nama</th>
            <th class="border">Alamat</th>
            <th class="border">Nomor HP</th>
            <th class="border">Status</th>
            <th class="border">Reputasi</th>
            <th class="border">Tgl Expired</th>
        </tr>
        <?php if ($contact != null) : ?>
            <?php
            $no = 1;
            foreach ($contact as $contact) : ?>
                <?php
                $id_city = $city['id_city'];
                $id_contact = $contact['id_contact'];
                $dateNow = date("Y-m-d");
                $getStoreVc = $this->db->query("SELECT COUNT(*) AS jml_vc FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0")->row_array();
                $getStoreExpVc = $this->db->query("SELECT COUNT(*) AS jml_exp FROM tb_voucher WHERE id_contact = '$id_contact' AND DATE(exp_date) < '$dateNow' AND is_claimed = 0")->row_array();
                ?>
                <?php if ($getStoreVc['jml_vc'] == $getStoreExpVc['jml_exp']) : ?>
                    <tr>
                        <td class="text-center border-r"><?= $no++; ?></td>
                        <td class="text-left border-r"><?= $contact['nama']; ?></td>
                        <td class="text-left border-r"><?= $contact['address']; ?></td>
                        <td class="text-left border-r"><?= $contact['nomorhp']; ?></td>
                        <td class="text-left border-r"><?= $contact['store_status']; ?></td>
                        <td class="text-left border-r <?= $contact['reputation'] == 'good' ? 'bg-green' : 'bg-red' ?>"><?= $contact['reputation']; ?></td>
                        <td class="text-left border-r"><?= date("d M, Y", strtotime($contact['exp_date'])) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>