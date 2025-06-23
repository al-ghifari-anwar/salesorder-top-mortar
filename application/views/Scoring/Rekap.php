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
    <title><?= $title  ?></title>
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

        .text-success {
            color: green;
        }

        .text-teal {
            color: teal;
        }

        .text-danger {
            color: red;
        }

        .text-blue {
            color: blue;
        }

        .text-purple {
            color: purple;
        }
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center"><?= $title ?></h1>
    <table class="border">
        <tr>
            <th class="border">Nama</th>
            <th class="border">Nomor HP</th>
            <th class="border">Alamat</th>
            <th class="border">Status</th>
            <th class="border">Jml Inv</th>
            <th class="border">Jml Inv Sehat</th>
            <th class="border">Jml Inv Trlmbt</th>
            <th class="border">Score Invoice</th>
            <th class="border">Score 3 Inv Trakhir</th>
            <th class="border">Score Frequency</th>
            <th class="border">Score Order</th>
            <th class="border">Score Total</th>
        </tr>
        <?php foreach ($contacts as $contact): ?>
            <?php
            $id_contact = $contact['id_contact'];
            // Get Score
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://order.topmortarindonesia.com/scoring/combine/' . $id_contact,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: ci_session=2scmao9aquusdrn7rm2i7vkrifkamkld'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $totalScore = json_decode($response, true);

            // if (isset($res['total'])) {
            // $totalScore = $res['total'];

            // All Invoices
            $count_late_payment = 0;
            $invoices = $this->MInvoice->getByIdContactNoMerch($contact['id_contact']);
            $payments = null;
            $array_scoring = array();
            foreach ($invoices as $invoice) {
                $id_surat_jalan = $invoice['id_surat_jalan'];
                $payments = $this->MPayment->getLastByIdInvoiceOnly($invoice['id_invoice']);

                $sj = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();

                $jatuhTempo = date('Y-m-d', strtotime("+" . $contact['termin_payment'] . " days", strtotime($invoice['date_invoice'])));

                // if ($jatuhTempo <= date("Y-m-d")) {
                if ($payments) {

                    foreach ($payments as $payment) {
                        $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                        if ($datePayment > $jatuhTempo) {
                            $count_late_payment += 1;
                            $date1 = new DateTime($datePayment);
                            $date2 = new DateTime($jatuhTempo);
                            $days  = $date2->diff($date1)->format('%a');

                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'late',
                                'days_late' => $days,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100 - $days,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        } else {
                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'good',
                                'days_late' => 0,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        }
                    }
                } else {
                    $dateNow = date("Y-m-d");
                    if ($dateNow > $jatuhTempo) {
                        $count_late_payment += 1;
                        $date1 = new DateTime($dateNow);
                        $date2 = new DateTime($jatuhTempo);
                        $days  = $date2->diff($date1)->format('%a');

                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'late',
                            'days_late' => $days,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $dateNow,
                            'percent_score' => 100 - $days,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    } else {
                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'good',
                            'days_late' => 0,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $dateNow,
                            'percent_score' => 100,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    }
                }
                // }
            }

            // Last Invoices
            $last_invoices = $this->MInvoice->getLast3ByIdContactNoMerch($contact['id_contact']);
            $last_payments = null;
            $last_array_scoring = array();
            foreach ($last_invoices as $last_invoice) {
                $id_surat_jalan = $last_invoice['id_surat_jalan'];
                $payments = $this->MPayment->getLastByIdInvoiceOnly($last_invoice['id_invoice']);

                $sj = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();

                $jatuhTempo = date('Y-m-d', strtotime("+" . $contact['termin_payment'] . " days", strtotime($last_invoice['date_invoice'])));

                // if ($jatuhTempo <= date("Y-m-d")) {

                if ($payments) {

                    foreach ($payments as $payment) {
                        $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                        if ($datePayment > $jatuhTempo) {
                            // $count_late_payment += 1;
                            $date1 = new DateTime($datePayment);
                            $date2 = new DateTime($jatuhTempo);
                            $days  = $date2->diff($date1)->format('%a');

                            $scoreData = [
                                'id_invoice' => $last_invoice['id_invoice'],
                                'no_invoice' => $last_invoice['no_invoice'],
                                'status' => 'late',
                                'days_late' => $days,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100 - $days,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $last_invoice['date_invoice'],
                            ];

                            array_push($last_array_scoring, $scoreData);
                        } else {
                            $scoreData = [
                                'id_invoice' => $last_invoice['id_invoice'],
                                'no_invoice' => $last_invoice['no_invoice'],
                                'status' => 'good',
                                'days_late' => 0,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $last_invoice['date_invoice'],
                            ];

                            array_push($last_array_scoring, $scoreData);
                        }
                    }
                } else {
                    $dateNow = date("Y-m-d");
                    if ($dateNow > $jatuhTempo) {
                        // $count_late_payment += 1;
                        $date1 = new DateTime($dateNow);
                        $date2 = new DateTime($jatuhTempo);
                        $days  = $date2->diff($date1)->format('%a');

                        $scoreData = [
                            'id_invoice' => $last_invoice['id_invoice'],
                            'no_invoice' => $last_invoice['no_invoice'],
                            'status' => 'late',
                            'days_late' => $days,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $dateNow,
                            'percent_score' => 100 - $days,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $last_invoice['date_invoice'],
                        ];

                        array_push($last_array_scoring, $scoreData);
                    } else {
                        $scoreData = [
                            'id_invoice' => $last_invoice['id_invoice'],
                            'no_invoice' => $last_invoice['no_invoice'],
                            'status' => 'good',
                            'days_late' => 0,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $dateNow,
                            'percent_score' => 100,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $last_invoice['date_invoice'],
                        ];

                        array_push($last_array_scoring, $scoreData);
                    }
                }
                // }
            }

            // Scoring System
            $count_invoice = count($array_scoring);
            if ($count_invoice == 0) {
                $count_invoice = 1;
            }
            $total_score = 0;
            foreach ($array_scoring as $scoring) {
                $total_score += $scoring['percent_score'];
            }
            $val_scoring = number_format($total_score / $count_invoice, 2, '.', '.');
            if ($val_scoring > 90 && $val_scoring <= 100) {
                $color_text = 'text-success';
            } else if ($val_scoring > 80 && $val_scoring <= 90) {
                $color_text = 'text-teal';
            } else if ($val_scoring <= 80) {
                $color_text = 'text-danger';
            }

            // Last 3 Scoring System
            $last_count_invoice = count($last_array_scoring);
            if ($last_count_invoice == 0) {
                $last_count_invoice = 1;
            }
            $last_total_score = 0;
            foreach ($last_array_scoring as $scoring) {
                $last_total_score += $scoring['percent_score'];
            }
            $last_val_scoring = number_format($last_total_score / $last_count_invoice, 2, '.', '.');
            if ($last_val_scoring > 90 && $last_val_scoring <= 100) {
                $color_text = 'text-success';
            } else if ($last_val_scoring > 80 && $last_val_scoring <= 90) {
                $color_text = 'text-teal';
            } else if ($last_val_scoring <= 80) {
                $color_text = 'text-danger';
            }

            if ($val_scoring > 100) {
                $val_scoring = 100;
            } else if ($val_scoring <= 100 && $val_scoring > 0) {
                $val_scoring = $val_scoring;
            } else if ($val_scoring < 0) {
                $val_scoring = 0;
            }

            if ($last_val_scoring > 100) {
                $last_val_scoring = 100;
            } else if ($last_val_scoring <= 100 && $last_val_scoring > 0) {
                $last_val_scoring = $last_val_scoring;
            } else if ($last_val_scoring < 0) {
                $last_val_scoring = 0;
            }
            ?>
            <?php if (count($array_scoring) > 0): ?>
                <tr>
                    <td class="border"><?= $contact['nama'] ?></td>
                    <td class="border"><?= $contact['nomorhp'] ?></td>
                    <td class="border"><?= $contact['address'] ?></td>
                    <td class="border"><?= $contact['store_status'] ?></td>
                    <td class="border"><?= $count_invoice ?></td>
                    <td class="border text-blue"><?= $count_invoice - $count_late_payment ?></td>
                    <td class="border text-purple"><?= $count_late_payment ?></td>
                    <td class="border <?= $color_text ?>"><?= $val_scoring  ?></td>
                    <td class="border <?= $color_text ?>"><?= $last_val_scoring ?></td>
                    <td class="border"><?= $totalScore['frequency'] ?></td>
                    <td class="border"><?= $totalScore['order'] ?></td>
                    <td class="border"><?= $totalScore['total'] ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</body>

</html>