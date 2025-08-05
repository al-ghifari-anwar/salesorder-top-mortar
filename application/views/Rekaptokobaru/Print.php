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
    <title><?= "Rekap Rencana Visit " . $city['nama_city']  ?></title>
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
    <h1 class="text-center">Rekap Toko Baru <?= $city['nama_city'] ?> (<?= $id_user != 0 ? $users['full_name'] : 'Semua Sales' ?>)</h1>
    <h4 class="text-center">Tanggal <?= date('d F Y', strtotime($dateFrom)) . " - " . date('d F Y', strtotime($dateTo)) ?></h4>
    <table class="border">
        <tr>
            <th class="border">Toko</th>
            <th class="border">Nomor</th>
            <th class="border">Address</th>
            <th class="border">Tanggal</th>
        </tr>
        <?php if ($id_user == 0) { ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <th colspan="4" class="border text-left" style="color: blue;">User: <?= $user['full_name'] ?></th>
                </tr>
                <?php foreach ($contacts as $contact): ?>
                    <?php
                    $id_user = $user['id_user'];
                    $id_contact = $contact['id_contact'];

                    $userContact = $this->db->query("SELECT * FROM tb_bid JOIN tb_contact ON tb_contact.id_contact = tb_bid.id_contact JOIN tb_user ON tb_user.id_user = tb_bid.id_user JOIN tb_action_bid ON tb_action_bid.id_bid = tb_bid.id_bid WHERE tb_bid.id_contact = '$id_contact' AND field_action_bid = 'Send new message' AND tb_bid.id_user = '$id_user' ORDER BY tb_bid.id_bid LIMIT 1")->row_array();
                    ?>

                    <?php if ($userContact): ?>
                        <tr>
                            <td class="border"><?= $userContact['nama'] ?></td>
                            <td class="border"><?= $userContact['nomorhp'] ?></td>
                            <td class="border"><?= $userContact['address'] ?></td>
                            <td class="border"><?= date("d F Y", strtotime($userContact['created_at'])) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php } else if ($id_user != 0) { ?>
            <?php foreach ($contacts as $contact): ?>
                <?php
                $id_user = $users['id_user'];
                $id_contact = $contact['id_contact'];

                $userContact = $this->db->query("SELECT * FROM tb_bid JOIN tb_contact ON tb_contact.id_contact = tb_bid.id_contact JOIN tb_user ON tb_user.id_user = tb_bid.id_user JOIN tb_action_bid ON tb_action_bid.id_bid = tb_bid.id_bid WHERE tb_bid.id_contact = '$id_contact' AND field_action_bid = 'Send new message' AND tb_bid.id_user = '$id_user' ORDER BY tb_bid.id_bid LIMIT 1")->row_array();
                ?>

                <?php if ($userContact): ?>
                    <tr>
                        <td class="border"><?= $userContact['nama'] ?></td>
                        <td class="border"><?= $userContact['nomorhp'] ?></td>
                        <td class="border"><?= $userContact['address'] ?></td>
                        <td class="border"><?= date("d F Y", strtotime($userContact['created_at'])) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php } ?>
    </table>
</body>

</html>