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
    <title><?= "Toko Tebus Murah Aktif per-" . date('d F Y') ?></title>
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
    <h1 class="text-center">Data Toko Tebus Murah Aktif</h1>
    <h4 class="text-center">per Tanggal <?= date("d F Y") ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Status</th>
            <th style="border-bottom: 1px solid black;">Kota</th>
            <th style="border-bottom: 1px solid black;">Tgl SJ Tebus Murah</th>
        </tr>
        <?php if ($contactTebusmurahs != null) : ?>
            <?php
            $no = 1;
            foreach ($contactTebusmurahs as $contactTebusmurah) : ?>
                <?php
                $sjTebusmurah = $this->db->where('id_contact', $contactTebusmurah['id_contact'])->where('is_tebus_murah', 1)->get('tb_surat_jalan')->row_array();

                $sjLain = $this->db->where('id_contact', $contactTebusmurah['id_contact'])->where('is_tebus_murah', 0)->get('tb_surat_jalan')->row_array();
                ?>
                <?php if (!$sjLain): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $contactTebusmurah['nama'] ?></td>
                        <td><?= $contactTebusmurah['store_status'] ?></td>
                        <td><?= $contactTebusmurah['nama_city'] ?></td>
                        <td><?= $sjTebusmurah['date_closing'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>