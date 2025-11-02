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
    <h3 class="text-center"><?= date('d F Y') . ' - ' . date('H:i:s') ?></h3>
    <h1 class="text-center">Jadwal Visit (<?= $city['nama_city'] ?>) - Cluster <?= $cluster ?></h1>
    <table>
        <tr>
            <th style="border-bottom: 1px solid black;">No.</th>
            <th style="border-bottom: 1px solid black;">Toko</th>
            <th style="border-bottom: 1px solid black;">Filter</th>
            <th style="border-bottom: 1px solid black;">Kategori</th>
            <th style="border-bottom: 1px solid black;">Last Visit</th>
            <th style="border-bottom: 1px solid black;">Hari</th>
            <th style="border-bottom: 1px solid black;">Umur Hutang</th>
            <th style="border-bottom: 1px solid black;">Total</th>
            <!-- <th style="border-bottom: 1px solid black;">Nama Pelanggan</th> -->
        </tr>
        <?php
        $jadwalVisits = array();
        ?>
        <tr>
            <td colspan="7" class="border">Data Mentah</td>
        </tr>
        <?php
        // Filter 1 (Cluster & days 0 - 7)
        $no = 1;
        foreach ($renvis as $renvi): ?>
            <?php
            $type_renvis = $renvi['type_renvis'];
            $id_contact = $renvi['id_contact'];
            $created_at = date('Y-m-d', strtotime($renvi['created_at']));

            $last_visit = '';
            $date_last_for_counter = '';
            if ($renvi['is_new'] == 1) {
                if ($type_renvis == 'jatem1') {
                    // $date_last_for_counter = date('Y-m-d', strtotime($renvi['jatem']));
                    // $last_visit = $renvi['jatuh_tempo'];
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                } else if ($type_renvis == 'tagih_mingguan') {
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                    // $date_last_for_counter = date('Y-m-d', strtotime($renvi['date_invoice']));
                    // $last_visit = date('d M Y', strtotime($renvi['date_invoice']));
                } else {
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                }
            } else {
                $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                $last_visit = date('d M Y', strtotime($renvi['created_at']));
            }

            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($date_last_for_counter);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;

            // Invoice
            $id_invoice = $renvi['id_invoice'];
            $invoices = $this->MInvoice->getByIdInvoiceWaiting($id_invoice);

            $total_invoice = 0;
            foreach ($invoices as $invoice) {
                $id_invoice = $invoice['id_invoice'];
                // Jatem Days
                $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                $dateInv1 = new DateTime(date("Y-m-d"));
                $dateInv2 = new DateTime($jatemInv);
                $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                $operanInvJatem = "";
                if ($dateInv1 < $dateInv2) {
                    $operanInvJatem = "-";
                }
                $daysInvJatem = $operanInvJatem . $daysInvJatem;

                $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                $amountPayment = $payment == null ? 0 : $payment['amount_payment'];
                $sisaHutang = $invoice['total_invoice'] - $amountPayment;

                if ($renvi['type_renvis'] != 'tagih_mingguan') {
                    if ($daysInvJatem >= 0) {
                        $total_invoice += $sisaHutang;
                    }
                } else {
                    if ($daysInvJatem <= 0) {
                        $total_invoice += $sisaHutang;
                    } else {
                        $total_invoice += $sisaHutang;
                    }
                }
            }

            if ($total_invoice == 0) {
                $invoices = $this->MInvoice->getByIdContactWaiting($id_contact);
                foreach ($invoices as $invoice) {
                    $id_invoice = $invoice['id_invoice'];
                    // Jatem Days
                    $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                    $dateInv1 = new DateTime(date("Y-m-d"));
                    $dateInv2 = new DateTime($jatemInv);
                    $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                    $operanInvJatem = "";
                    if ($dateInv1 < $dateInv2) {
                        $operanInvJatem = "-";
                    }
                    $daysInvJatem = $operanInvJatem . $daysInvJatem;

                    $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                    $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];

                    if ($renvi['type_renvis'] != 'tagih_mingguan') {
                        if ($daysInvJatem >= 0) {
                            $total_invoice += $sisaHutang;
                        }
                    } else {
                        if ($daysInvJatem <= 0) {
                            $total_invoice += $sisaHutang;
                        }
                    }
                }
            }

            // Jatem Days
            $date1jatem = new DateTime(date("Y-m-d"));
            $date2jatem = new DateTime($renvi['jatem']);
            $daysJatem  = $date2jatem->diff($date1jatem)->format('%a');
            $operanJatem = "";
            if ($date1jatem < $date2jatem) {
                $operanJatem = "-";
            }
            $daysJatem = $operanJatem . $daysJatem;

            $renvisFilter = [
                'filter' => 'Cluster ' . $cluster . ', 0 & 7 Hari',
                'nama' => $renvi['nama'],
                'type_renvis' => $renvi['type_renvis'],
                'last_visit' => $last_visit,
                'days' => $days,
                'daysJatem' => $daysJatem,
                'total_invoice' => $total_invoice,
            ];

            if ($renvi['cluster'] == $cluster) {
                if (count($jadwalVisits) <= 10) {
                    if ($days == 0 || $days >= 7) {
                        array_push($jadwalVisits, $renvisFilter);
                    }
                }
            }
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $renvi['nama'] ?></td>
                <td>-</td>
                <td class="text-center"><?= $renvi['type_renvis'] ?></td>
                <td class="text-center"><?= $last_visit ?></td>
                <td class="text-center"><?= $days ?></td>
                <td class="text-center"><?= $daysJatem ?></td>
                <td>Rp. <?= number_format($total_invoice, 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php
        // Filter 2 (Hari Bayar, Free Cluster & days 0 - 7)
        $no = 1;
        foreach ($renvis as $renvi): ?>
            <?php
            $type_renvis = $renvi['type_renvis'];
            $id_contact = $renvi['id_contact'];
            $created_at = date('Y-m-d', strtotime($renvi['created_at']));

            $last_visit = '';
            $date_last_for_counter = '';
            if ($renvi['is_new'] == 1) {
                if ($type_renvis == 'jatem1') {
                    // $date_last_for_counter = date('Y-m-d', strtotime($renvi['jatem']));
                    // $last_visit = $renvi['jatuh_tempo'];
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                } else if ($type_renvis == 'tagih_mingguan') {
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                    // $date_last_for_counter = date('Y-m-d', strtotime($renvi['date_invoice']));
                    // $last_visit = date('d M Y', strtotime($renvi['date_invoice']));
                } else {
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                }
            } else {
                $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                $last_visit = date('d M Y', strtotime($renvi['created_at']));
            }

            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($date_last_for_counter);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;

            // Invoice
            $id_invoice = $renvi['id_invoice'];
            $invoices = $this->MInvoice->getByIdInvoiceWaiting($id_invoice);

            $total_invoice = 0;
            foreach ($invoices as $invoice) {
                $id_invoice = $invoice['id_invoice'];
                // Jatem Days
                $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                $dateInv1 = new DateTime(date("Y-m-d"));
                $dateInv2 = new DateTime($jatemInv);
                $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                $operanInvJatem = "";
                if ($dateInv1 < $dateInv2) {
                    $operanInvJatem = "-";
                }
                $daysInvJatem = $operanInvJatem . $daysInvJatem;

                $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                $amountPayment = $payment == null ? 0 : $payment['amount_payment'];
                $sisaHutang = $invoice['total_invoice'] - $amountPayment;

                if ($renvi['type_renvis'] != 'tagih_mingguan') {
                    if ($daysInvJatem >= 0) {
                        $total_invoice += $sisaHutang;
                    }
                } else {
                    if ($daysInvJatem <= 0) {
                        $total_invoice += $sisaHutang;
                    } else {
                        $total_invoice += $sisaHutang;
                    }
                }
            }

            if ($total_invoice == 0) {
                $invoices = $this->MInvoice->getByIdContactWaiting($id_contact);
                foreach ($invoices as $invoice) {
                    $id_invoice = $invoice['id_invoice'];
                    // Jatem Days
                    $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                    $dateInv1 = new DateTime(date("Y-m-d"));
                    $dateInv2 = new DateTime($jatemInv);
                    $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                    $operanInvJatem = "";
                    if ($dateInv1 < $dateInv2) {
                        $operanInvJatem = "-";
                    }
                    $daysInvJatem = $operanInvJatem . $daysInvJatem;

                    $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                    $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];

                    if ($renvi['type_renvis'] != 'tagih_mingguan') {
                        if ($daysInvJatem >= 0) {
                            $total_invoice += $sisaHutang;
                        }
                    } else {
                        if ($daysInvJatem <= 0) {
                            $total_invoice += $sisaHutang;
                        }
                    }
                }
            }

            // Jatem Days
            $date1jatem = new DateTime(date("Y-m-d"));
            $date2jatem = new DateTime($renvi['jatem']);
            $daysJatem  = $date2jatem->diff($date1jatem)->format('%a');
            $operanJatem = "";
            if ($date1jatem < $date2jatem) {
                $operanJatem = "-";
            }
            $daysJatem = $operanJatem . $daysJatem;

            $renvisFilter = [
                'filter' => 'Cluster Lain di hari bayar ' . $renvi['hari_bayar'] . ',  0 & 7 Hari',
                'nama' => $renvi['nama'],
                'type_renvis' => $renvi['type_renvis'],
                'last_visit' => $last_visit,
                'days' => $days,
                'daysJatem' => $daysJatem,
                'total_invoice' => $total_invoice,
            ];

            if (count($jadwalVisits) <= 10) {
                if ($renvi['cluster'] != 1) {
                    if ($renvi['hari_bayar'] == $dayName) {
                        if ($days == 0 || $days >= 7) {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                    }
                }
            }
            ?>
        <?php endforeach; ?>
        <?php
        // Filter 3 (Toko yang akan passive)
        $id_city = $city['id_city'];
        $contactActives = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster])->result_array();

        foreach ($contactActives as $contactActive) {
            $id_contact = $contactActive['id_contact'];
            $lastOrder = $this->db->query("SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact")->row_array();

            if ($lastOrder != null) {
                $dateMin6Week = date('Y-m-d', strtotime("-6 week"));
                $dateMin2Month = date("Y-m-d", strtotime("-2 month"));
                $dateLastOrder = date("Y-m-d", strtotime($lastOrder['date_closing']));

                if ($dateLastOrder <= $dateMin6Week && $dateLastOrder >= $dateMin2Month) {
                    if (count($jadwalVisits) <= 10) {
                        $renvisFilter = [
                            'filter' => 'Toko akan pasif dalam 2 minggu',
                            'nama' => $contactActive['nama'],
                            'type_renvis' => 'Akan passive',
                            'last_visit' => '-',
                            'days' => '-',
                            'daysJatem' => '-',
                            'total_invoice' => 0,
                        ];

                        array_push($jadwalVisits, $renvisFilter);
                    }
                }
            }
        }
        ?>
        <?php
        // Filter 4 (Toko data / baru)
        $id_city = $city['id_city'];
        $contactDatas = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'data'])->result_array();

        foreach ($contactDatas as $contactData) {
            if (count($jadwalVisits) <= 10) {
                $id_contact = $contactActive['id_contact'];

                $renvisFilter = [
                    'filter' => 'Toko Baru',
                    'nama' => $contactData['nama'],
                    'type_renvis' => 'Toko Baru',
                    'last_visit' => '-',
                    'days' => '-',
                    'daysJatem' => '-',
                    'total_invoice' => 0,
                ];

                array_push($jadwalVisits, $renvisFilter);
            }
        }
        ?>
        <?php
        // Filter 5
        foreach ($renvisPassives as $renvisPassive) {
            $date_last_for_counter = date('Y-m-d', strtotime($renvisPassive['created_at']));
            $last_visit = date('d M Y', strtotime($renvisPassive['created_at']));

            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($date_last_for_counter);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;

            $renvisFilter = [
                'filter' => 'Passive',
                'nama' => $renvisPassive['nama'],
                'type_renvis' => $renvisPassive['type_renvis'],
                'last_visit' => $last_visit,
                'days' => $days,
                'daysJatem' => '-',
                'total_invoice' => 0,
            ];

            if ($renvisPassive['cluster'] == $cluster) {
                if (count($jadwalVisits) <= 10) {
                    // if ($days == 0 || $days >= 7) {
                    array_push($jadwalVisits, $renvisFilter);
                    // }
                }
            }
        }
        ?>
        <tr>
            <td colspan="7" class="border">Hasil Filter</td>
        </tr>
        <?php
        $noJadwal = 1;
        foreach ($jadwalVisits as $jadwalVisit): ?>
            <tr>
                <td class="text-center"><?= $noJadwal++; ?></td>
                <td><?= $jadwalVisit['nama'] ?></td>
                <td class="text-center"><?= $jadwalVisit['filter'] ?></td>
                <td class="text-center"><?= $jadwalVisit['type_renvis'] ?></td>
                <td class="text-center"><?= $jadwalVisit['last_visit'] ?></td>
                <td class="text-center"><?= $jadwalVisit['days'] ?></td>
                <td class="text-center"><?= $jadwalVisit['daysJatem'] ?></td>
                <td>Rp. <?= number_format($jadwalVisit['total_invoice'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>