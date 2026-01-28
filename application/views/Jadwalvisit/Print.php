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
            <td colspan="8" class="border">Data Mentah</td>
        </tr>
        <?php
        // Filter 1 (Janji Bayar)
        $id_city = $city['id_city'];
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $janjiBayars = $this->db->get_where('tb_visit', ['pay_date' => date('Y-m-d'), 'tb_contact.id_city' => $id_city])->result_array();

        foreach ($janjiBayars as $janjiBayar) {
            if (count($jadwalVisits) <= 14) {
                $date_visit_janji_bayar = date('Y-m-d', strtotime($janjiBayar['date_visit']));

                $id_contact = $janjiBayar['id_contact'];

                $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();

                $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                $lastPayVisit = $this->db->query("SELECT * FROM tb_visit WHERE is_pay = 'pay' AND DATE(date_visit) > '$date_visit_janji_bayar' AND id_contact = '$id_contact'")->row_array();

                $date1 = new DateTime(date("Y-m-d"));
                $date2 = new DateTime($date_last_for_counter);
                $days  = $date2->diff($date1)->format('%a');
                $operan = "";
                if ($date1 < $date2) {
                    $operan = "-";
                }
                $days = $operan . $days;

                $id_invoice = $janjiBayar['id_invoice'];
                $invoice = $this->MInvoice->getById($id_invoice);
                // Jatem Days
                $jatemInv = date('Y-m-d', strtotime("+" . $janjiBayar['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
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

                $renvisFilter = [
                    'id_contact' => $id_contact,
                    'filter' => 'Janji Bayar',
                    'nama' => $janjiBayar['nama'],
                    'type_renvis' => 'Janji Bayar',
                    'last_visit' => $last_visit,
                    'days' => $days,
                    'daysJatem' => $daysInvJatem,
                    'total_invoice' => $sisaHutang,
                    'is_new' => 0,
                ];

                // if ($days > $minDayCluster) {
                if (!$lastPayVisit) {
                    if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                        array_push($jadwalVisits, $renvisFilter);
                    }
                }
                // }
            }
        }
        ?>
        <?php
        // Filter 2 (Cluster & days 0 - 7)
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

            $is_new = 0;
            if ($renvi['type_renvis'] == 'jatem1') {
                if ($renvi['is_new'] == 1) {
                    $is_new = 1;
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
                'id_contact' => $renvi['id_contact'],
                'filter' => 'Cluster ' . $cluster . ', 0 & 7 Hari',
                'nama' => $renvi['nama'],
                'type_renvis' => $renvi['type_renvis'],
                'last_visit' => $last_visit,
                'days' => $days,
                'daysJatem' => $daysJatem,
                'total_invoice' => $total_invoice,
                'is_new' => $is_new,
                'hari_bayar' => $renvi['hari_bayar'],
                'hari_ini' => $dayName,
            ];

            // if ($id_contact == 5116) {
            //     echo json_encode($renvisFilter);
            // }

            if ($renvi['cluster'] == $cluster) {
                if (count($jadwalVisits) <= 14) {
                    // if ($days == 0 || $days >= 7) {
                    if ($days > $minDayCluster) {
                        if ($renvi['hari_bayar'] == 'bebas' || $renvi['hari_bayar'] == '-' || $renvi['hari_bayar'] == '-1') {
                            if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                    }

                    if ($renvi['hari_bayar'] == $dayName) {
                        if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                    }
                    // }

                    if ($is_new == 1) {
                        if ($renvi['hari_bayar'] == 'bebas' || $renvi['hari_bayar'] == '-' || $renvi['hari_bayar'] == '-1' || $renvi['hari_bayar'] == $dayName) {
                            if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                    }

                    if ($daysJatem == 0 || $daysJatem == 7) {
                        if ($renvi['hari_bayar'] == 'bebas' || $renvi['hari_bayar'] == '-' || $renvi['hari_bayar'] == '-1' || $renvi['hari_bayar'] == $dayName) {
                            if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                    }
                }
            }
            ?>
            <?php if ($renvi['cluster'] == $cluster): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= $renvi['nama'] ?></td>
                    <td>-</td>
                    <td class="text-center"><?= $renvi['type_renvis'] ?></td>
                    <td class="text-center"><?= $is_new == 0 ? $last_visit : 'Blm Visit' ?></td>
                    <td class="text-center"><?= $days ?></td>
                    <td class="text-center"><?= $daysJatem ?></td>
                    <td>Rp. <?= number_format($total_invoice, 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php
        // Filter 3 (Hari Bayar, Free Cluster & days 0 - 7)
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

            $is_new = 0;
            if ($renvi['type_renvis'] == 'jatem1') {
                if ($renvi['is_new'] == 1) {
                    $is_new = 1;
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
                'id_contact' => $renvi['id_contact'],
                'filter' => 'Cluster Lain di hari bayar ' . $renvi['hari_bayar'] . ',  0 & 7 Hari',
                'nama' => $renvi['nama'],
                'type_renvis' => $renvi['type_renvis'],
                'last_visit' => $last_visit,
                'days' => $days,
                'daysJatem' => $daysJatem,
                'total_invoice' => $total_invoice,
                'is_new' => $is_new,
            ];

            if ($id_contact == 5116) {
                echo json_encode($renvisFilter);
            }

            if (count($jadwalVisits) <= 14) {
                if ($renvi['cluster'] != 1) {
                    if ($renvi['hari_bayar'] == $dayName) {
                        // if ($days == 0 || $days >= 7) {
                        // if ($days > $minDayCluster) {
                        if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                        // }
                        // }
                    }
                }
            }
            ?>
        <?php endforeach; ?>
        <?php
        // Filter 4 (Toko yang akan passive)
        $id_city = $city['id_city'];
        $contactActives = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'active'])->result_array();

        foreach ($contactActives as $contactActive) {
            $id_contact = $contactActive['id_contact'];
            $lastOrder = $this->db->query("SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact")->row_array();

            if ($lastOrder != null) {
                $dateMin6Week = date('Y-m-d', strtotime("-6 week"));
                $dateMin2Month = date("Y-m-d", strtotime("-2 month"));
                $dateLastOrder = date("Y-m-d", strtotime($lastOrder['date_closing']));

                if ($dateLastOrder <= $dateMin6Week && $dateLastOrder >= $dateMin2Month) {
                    $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3') ORDER BY date_visit DESC LIMIT 1")->row_array();

                    $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                    $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                    $date1 = new DateTime(date("Y-m-d"));
                    $date2 = new DateTime($date_last_for_counter);
                    $days  = $date2->diff($date1)->format('%a');
                    $operan = "";
                    if ($date1 < $date2) {
                        $operan = "-";
                    }
                    $days = $operan . $days;

                    if (count($jadwalVisits) <= 14) {
                        $renvisFilter = [
                            'id_contact' => $id_contact,
                            'filter' => 'Toko akan pasif dalam 2 minggu',
                            'nama' => $contactActive['nama'],
                            'type_renvis' => 'Akan passive',
                            'last_visit' => $last_visit,
                            'days' => $days,
                            'daysJatem' => '-',
                            'total_invoice' => 0,
                            'is_new' => 0,
                        ];

                        // if ($days == 0 || $days >= 7) {
                        if ($days > $minDayCluster) {
                            if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                        // }
                    }
                }
            }
        }
        ?>
        <?php
        // Filter 5 (Toko data / baru)
        $id_city = $city['id_city'];
        $contactDatas = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'data'])->result_array();

        foreach ($contactDatas as $contactData) {
            if (count($jadwalVisits) <= 14) {
                $id_contact = $contactData['id_contact'];

                $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3') ORDER BY date_visit DESC LIMIT 1")->row_array();

                $date_last_for_counter = date('Y-m-d');
                $last_visit = date('d M Y');

                if ($rowLastVisit) {
                    $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                    $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));
                }

                $date1 = new DateTime(date("Y-m-d"));
                $date2 = new DateTime($date_last_for_counter);
                $days  = $date2->diff($date1)->format('%a');
                $operan = "";
                if ($date1 < $date2) {
                    $operan = "-";
                }
                $days = $operan . $days;

                $renvisFilter = [
                    'id_contact' => $id_contact,
                    'filter' => 'Toko Baru',
                    'nama' => $contactData['nama'],
                    'type_renvis' => 'Toko Baru',
                    'last_visit' => $last_visit,
                    'days' => $days,
                    'daysJatem' => '-',
                    'total_invoice' => 0,
                    'is_new' => 0,
                ];

                // if ($days == 0 || $days >= 7) {
                if ($days > $minDayCluster) {
                    if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                        array_push($jadwalVisits, $renvisFilter);
                    }
                }
                // }
            }
        }
        ?>
        <?php
        // Filter 6 (Toko passive)
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
                'id_contact' => $renvisPassive['id_contact'],
                'filter' => 'Passive',
                'nama' => $renvisPassive['nama'],
                'type_renvis' => $renvisPassive['type_renvis'],
                'last_visit' => $last_visit,
                'days' => $days,
                'daysJatem' => '-',
                'total_invoice' => 0,
                'is_new' => 0,
            ];

            if ($renvisPassive['cluster'] == $cluster) {
                if (count($jadwalVisits) <= 14) {
                    // if ($days == 0 || $days >= 7) {
                    if ($days > $minDayCluster) {
                        if (array_search($renvisPassive['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                    }
                    // }
                }
            }
        }
        ?>

        <?php
        // Filter 7 (Toko Aktif)
        $id_city = $city['id_city'];
        $contactDatas = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'active'])->result_array();

        foreach ($contactDatas as $contactData) {
            if (count($jadwalVisits) <= 14) {
                $id_contact = $contactData['id_contact'];

                $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();

                $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                $date1 = new DateTime(date("Y-m-d"));
                $date2 = new DateTime($date_last_for_counter);
                $days  = $date2->diff($date1)->format('%a');
                $operan = "";
                if ($date1 < $date2) {
                    $operan = "-";
                }
                $days = $operan . $days;

                $renvisFilter = [
                    'id_contact' => $id_contact,
                    'filter' => 'Toko Aktif',
                    'nama' => $contactData['nama'],
                    'type_renvis' => 'Toko Aktif',
                    'last_visit' => $last_visit,
                    'days' => $days,
                    'daysJatem' => '-',
                    'total_invoice' => 0,
                    'is_new' => 0,
                ];

                // if ($days == 0 || $days >= 7) {
                if ($days > $minDayCluster) {
                    if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                        array_push($jadwalVisits, $renvisFilter);
                    }
                }
                // }
            }
        }
        ?>
        <tr>
            <td colspan="8" class="border">Hasil Filter</td>
        </tr>
        <?php
        $noJadwal = 1;
        foreach ($jadwalVisits as $jadwalVisit): ?>
            <tr>
                <td class="text-center"><?= $noJadwal++; ?></td>
                <td><?= $jadwalVisit['nama'] ?></td>
                <td class="text-left"><?= $jadwalVisit['filter'] ?></td>
                <td class="text-center"><?= $jadwalVisit['type_renvis'] ?></td>
                <td class="text-center"><?= $jadwalVisit['is_new'] == 0 ? $jadwalVisit['last_visit'] : 'Blm Visit' ?></td>
                <td class="text-center"><?= $jadwalVisit['days'] ?></td>
                <td class="text-center"><?= $jadwalVisit['daysJatem'] ?></td>
                <td>Rp. <?= number_format($jadwalVisit['total_invoice'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>