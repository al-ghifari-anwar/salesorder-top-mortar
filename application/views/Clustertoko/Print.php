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
    <title><?= $title ?></title>
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
    <!-- <h3 class="text-center"><?= $this->session->userdata('full_name') ?></h3> -->
    <!-- <h3 class="text-center"><?= date('d F Y') . ' - ' . date('H:i:s') ?></h3> -->
    <h1 class="text-center">Cluster Toko</h1>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Alamat</th>
            <th style="border-bottom: 1px solid black;">Cluster</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <tr>
            <th class="border text-left" colspan="4">Cluster 1</th>
        </tr>
        <?php
        // Cluster 1
        $no = 1;
        foreach ($contact1s as $contact1): ?>
            <?php
            $id_contact = $contact1['id_contact'];
            $getBadScore = $this->db->get_where('tb_bad_score', ['id_contact' => $id_contact])->row_array();

            $isBad = false;

            if ($getBadScore) {
                if ($getBadScore['is_approved'] == 1) {
                    $isBad = true;
                }
            }
            ?>

            <?php if ($isBad == false): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $contact1['nama'] ?></td>
                    <td><?= $contact1['address'] ?></td>
                    <td><?= $contact1['cluster'] ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <th class="border text-left" colspan="4">Cluster 2</th>
        </tr>
        <?php
        // Cluster 2
        $no = 1;
        foreach ($contact2s as $contact2): ?>
            <?php
            $id_contact = $contact2['id_contact'];
            $getBadScore = $this->db->get_where('tb_bad_score', ['id_contact' => $id_contact])->row_array();

            $isBad = false;

            if ($getBadScore) {
                if ($getBadScore['is_approved'] == 1) {
                    $isBad = true;
                }
            }
            ?>

            <?php if ($isBad == false): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $contact2['nama'] ?></td>
                    <td><?= $contact2['address'] ?></td>
                    <td><?= $contact2['cluster'] ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <th class="border text-left" colspan="4">Cluster 3</th>
        </tr>
        <?php
        // Cluster 3
        $no = 1;
        foreach ($contact3s as $contact3): ?>
            <?php
            $id_contact = $contact3['id_contact'];
            $getBadScore = $this->db->get_where('tb_bad_score', ['id_contact' => $id_contact])->row_array();

            $isBad = false;

            if ($getBadScore) {
                if ($getBadScore['is_approved'] == 1) {
                    $isBad = true;
                }
            }
            ?>

            <?php if ($isBad == false): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $contact3['nama'] ?></td>
                    <td><?= $contact3['address'] ?></td>
                    <td><?= $contact3['cluster'] ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr>
            <th class="border text-left" colspan="4">Tanpa Cluster</th>
        </tr>
        <?php
        // Cluster 0
        $no = 1;
        foreach ($contacts as $contact): ?>
            <?php
            $id_contact = $contact['id_contact'];
            $getBadScore = $this->db->get_where('tb_bad_score', ['id_contact' => $id_contact])->row_array();

            $isBad = false;

            if ($getBadScore) {
                if ($getBadScore['is_approved'] == 1) {
                    $isBad = true;
                }
            }
            ?>

            <?php if ($isBad == false): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $contact['nama'] ?></td>
                    <td><?= $contact['address'] ?></td>
                    <td><?= $contact['cluster'] ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</body>

</html>