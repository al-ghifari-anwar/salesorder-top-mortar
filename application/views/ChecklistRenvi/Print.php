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
                    if ($daysInvJatem > 0) {
                        $total_invoice += $sisaHutang;
                    }
                } else {
                    if ($daysInvJatem < 0) {
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
                        if ($daysInvJatem > 0) {
                            $total_invoice += $sisaHutang;
                        }
                    } else {
                        if ($daysInvJatem < 0) {
                            $total_invoice += $sisaHutang;
                        }
                    }
                }
            }
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= $renvi['nama'] ?></td>
                <td class="text-center"><?= $renvi['type_renvis'] ?></td>
                <td class="text-center"><?= $renvi['jatuh_tempo'] ?></td>
                <td class="text-center"><?= $days ?></td>
                <td class="text-center"><?= $daysJatem ?></td>
                <td>Rp. <?= number_format($total_invoice, 0, ',', '.') ?></td>
                <td class="text-center"><?= $renvi['selected'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>