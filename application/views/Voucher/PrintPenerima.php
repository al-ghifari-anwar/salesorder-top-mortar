<?php
// echo json_encode($contacts);
// die;
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
    <title>Kelayakan Penerima Voucher <?= $city['nama_city'] ?></title>
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
    <h1 class="text-center">Kelayakan Penerima Voucher <?= $city['nama_city'] ?></h1>
    <h4 class="text-center">Tgl. <?= date("d M Y", strtotime($dateNow)) ?></h4>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Address</th>
            <th style="border-bottom: 1px solid black;">Status</th>
            <th style="border-bottom: 1px solid black;">Reputation</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php if ($contacts != null) :
            $no = 1;
        ?>
            <?php foreach ($contacts as $contact) : ?>
                <?php if ($contact['store_status'] == 'passive' || $contact['store_status'] == 'data') { ?>
                    <?php if (date("Y-m-d", strtotime($contact['exp_date'])) < date("Y-m-d")) : ?>
                        <?php if ($contact['reputation'] == 'good') : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $contact['nama'] . "(" . $contact['id_contact'] . ")" ?></td>
                                <td><?= $contact['address'] ?></td>
                                <td><?= $contact['store_status'] ?></td>
                                <td><?= $contact['reputation'] ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php } else if ($contact['id_voucher'] == null) { ?>
                    <?php if ($contact['store_status'] == 'passive' || $contact['store_status'] == 'data') : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $contact['nama'] . "(" . $contact['id_contact'] . ")" ?></td>
                            <td><?= $contact['address'] ?></td>
                            <td><?= $contact['store_status'] ?></td>
                            <td><?= $contact['reputation'] ?></td>
                        </tr>
                    <?php endif; ?>
                <?php } ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>