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
    <title>Checklist Renvi - <?= $this->session->userdata('full_name') ?></title>
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
    <h3 class="text-center"><?= $this->session->userdata('full_name') ?></h3>
    <h1 class="text-center">Checklist Renvi</h1>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Kategori</th>
            <th style="border-bottom: 1px solid black;">Jatuh Tempo</th>
            <th style="border-bottom: 1px solid black;">Hari</th>
            <th style="border-bottom: 1px solid black;">Umur Hutang</th>
            <th style="border-bottom: 1px solid black;">Total</th>
            <th style="border-bottom: 1px solid black;">Selected</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php
        $no = 1;
        foreach ($renvis as $renvi): ?>
            <?php
            $id_contact = $renvi['id_contact'];
            $created_at = date('Y-m-d', strtotime($renvi['created_at']));

            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($created_at);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;

            // Jatem Days
            $date1jatem = new DateTime(date("Y-m-d"));
            $date2jatem = new DateTime($renvi['jatem']);
            $daysJatem  = $date2jatem->diff($date1jatem)->format('%a');
            $operanJatem = "";
            if ($date1jatem < $date2jatem) {
                $operanJatem = "-";
            }
            $daysJatem = $operanJatem . $daysJatem;

            // Invoice
            $jatem = $renvi['jatem'];
            $total_inv = $this->db->query("SELECT SUM(total_invoice) AS total_invoice FROM tb_invoice JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan WHERE id_contact = '$id_contact' AND status_invoice = 'waiting' AND DATE(date_invoice) <= '$jatem' ")->row_array();
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $renvi['nama'] ?></td>
                <td class="text-center"><?= $renvi['type_renvis'] ?></td>
                <td class="text-center"><?= $renvi['jatuh_tempo'] ?></td>
                <td class="text-center"><?= $days ?></td>
                <td class="text-center"><?= $daysJatem ?></td>
                <td>Rp. <?= number_format($total_inv['total_invoice'], 0, ',', '.') ?></td>
                <td class="text-center"><?= $renvi['selected'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>