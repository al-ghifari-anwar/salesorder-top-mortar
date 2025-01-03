<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Alert!</strong> <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('failed')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Alert!</strong> <?= $this->session->flashdata('failed') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $title ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><?= $title ?></a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-center">Rekap Piutang Per <?= date("d F Y") ?></h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kota</th>
                                        <th>0 - 7 Hari</th>
                                        <th>8 - 15 Hari</th>
                                        <th>16+</th>
                                        <th>Piutang</th>
                                        <th>Prosentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $totalAll0to7 = 0;
                                    $totalAll8to15 = 0;
                                    $totalAll16 = 0;
                                    $totalAllPiutang = 0;
                                    foreach ($city as $data): ?>
                                        <?php
                                        $id_city = $data['id_city'];

                                        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
                                        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
                                        $getInvoice = $this->db->get_where('tb_invoice', ['status_invoice' => 'waiting', 'tb_contact.id_city' => $id_city])->result_array();

                                        $totalPiutang = 0;
                                        foreach ($getInvoice as $invoiceTotal) {
                                            $id_invoice = $invoiceTotal['id_invoice'];
                                            $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment, SUM(potongan_payment) AS potongan_payment, SUM(adjustment_payment) AS adjustment_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                                            $sisaHutang = $invoiceTotal['total_invoice'] - ($payment['amount_payment'] + $payment['potongan_payment'] + $payment['adjustment_payment']);
                                            $totalPiutang += $sisaHutang;
                                            $totalAllPiutang += $sisaHutang;
                                        }

                                        $total0to7 = 0;
                                        $total8to15 = 0;
                                        $total16 = 0;
                                        foreach ($getInvoice as $invoice) {
                                            $id_invoice = $invoice['id_invoice'];
                                            // Calculate Hari
                                            $jatuhTempo = date('d M Y', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                                            $date1 = new DateTime(date("Y-m-d"));
                                            $date2 = new DateTime($jatuhTempo);
                                            $days  = $date2->diff($date1)->format('%a');
                                            $operan = "";
                                            if ($date1 < $date2) {
                                                $operan = "-";
                                            }
                                            $days = $operan . $days;

                                            // $id_invoice = $invoice['id_invoice'];
                                            // $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                                            // $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];
                                            // $totalStore1 += $sisaHutang;

                                            if ($days >= "0" && $days <= "7") {
                                                $this->db->select("SUM(amount_payment) AS amount_total");
                                                $getPayment = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice])->row_array();

                                                $sisaHutang = $invoice['total_invoice'] - $getPayment['amount_total'];

                                                if ($invoice['total_invoice'] > 0) {
                                                    $total0to7 += $sisaHutang;
                                                    $totalAll0to7 += $sisaHutang;
                                                }
                                            } else if ($days > "7" && $days <= "15") {
                                                $this->db->select("SUM(amount_payment) AS amount_total");
                                                $getPayment = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice])->row_array();

                                                $sisaHutang = $invoice['total_invoice'] - $getPayment['amount_total'];

                                                if ($invoice['total_invoice'] > 0) {
                                                    $total8to15 += $sisaHutang;
                                                    $totalAll8to15 += $sisaHutang;
                                                }
                                            } else if ($days > "15") {
                                                $this->db->select("SUM(amount_payment) AS amount_total");
                                                $getPayment = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice])->row_array();

                                                $sisaHutang = $invoice['total_invoice'] - $getPayment['amount_total'];

                                                if ($invoice['total_invoice'] > 0) {
                                                    $total16 += $sisaHutang;
                                                    $totalAll16 += $sisaHutang;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama_city'] ?></td>
                                            <td>Rp. <?= number_format($total0to7, 0, ',', '.') ?></td>
                                            <td>Rp. <?= number_format($total8to15, 0, ',', '.') ?></td>
                                            <td>Rp. <?= number_format($total16, 0, ',', '.') ?></td>
                                            <td>Rp. <?= number_format($totalPiutang, 0, ',', '.') ?></td>
                                            <td>
                                                <?php if ($total16 > 0): ?>
                                                    <?= number_format(($total16 / $totalPiutang) * 100, 2, '.', ',') ?>%
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th>Rp. <?= number_format($totalAll0to7, 0, ',', '.') ?></th>
                                        <th>Rp. <?= number_format($totalAll8to15, 0, ',', '.') ?></th>
                                        <th>Rp. <?= number_format($totalAll16, 0, ',', '.') ?></th>
                                        <th>Rp. <?= number_format($totalAllPiutang, 0, ',', '.') ?></th>
                                        <th>
                                            <?php if ($totalAll16 > 0): ?>
                                                <?= number_format(($totalAll16 / $totalAllPiutang) * 100, 2, '.', ',') ?>%
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
    </div>
</aside>
<!-- /.control-sidebar -->