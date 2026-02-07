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
                        <li class="breadcrumb-item"><a href="#"></a></li>
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
                            <div class="row">
                                <div class="col-12">
                                    <form action="<?= base_url('targetvisittagihan') ?>" method="POST">
                                        <div class="row">
                                            <label>Date:</label>
                                            <div class="form-group ml-3">
                                                <input type="date" class="form-control float-right" id="" name="date" value="<?= date('Y-m-d') ?>">
                                                <!-- /.input group -->
                                            </div>
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table-print" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kota</th>
                                        <th>Total Hutang Hari Ini</th>
                                        <th>Total Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalHutang = 0;
                                    $totalTertagih = 0;
                                    foreach ($jadwalVisits as $jadwalVisit): ?>
                                        <?php
                                        $totalHutang += $jadwalVisit['total_invoice'];
                                        $city = $this->MCity->getById($jadwalVisit['id_city']);

                                        $id_city = $jadwalVisit['id_city'];

                                        $seperateJadwals = $this->db->get_where('tb_jadwal_visit', ['DATE(date_jadwal_visit)' => $date, 'tb_jadwal_visit.id_city' => $id_city, 'total_invoice >' => 0])->result_array();

                                        // echo json_encode($seperateJadwals);
                                        // die;

                                        $total_payment = 0;
                                        foreach ($seperateJadwals as $seperateJadwal) {
                                            $id_contact = $seperateJadwal['id_contact'];

                                            $payment = $this->db->select('SUM(amount_payment) AS amount_payment')->join('tb_invoice', 'tb_invoice.id_invoice = tb_payment.id_invoice')->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan')->where('tb_surat_jalan.id_contact', $id_contact)->where('tb_invoice.status_invoice', 'waiting')->get('tb_payment')->row_array();

                                            // echo json_encode($payment);
                                            // die;

                                            if ($payment) {
                                                $total_payment += $payment['amount_payment'];
                                            }
                                        }

                                        $totalTertagih += $total_payment;
                                        ?>
                                        <tr>
                                            <td><?= $city['nama_city'] ?></td>
                                            <td class="text-right"><?= number_format($jadwalVisit['total_invoice'], 0, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($total_payment, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-right"><?= number_format($totalHutang, 0, ',', '.') ?></th>
                                        <th class="text-right"><?= number_format($totalTertagih, 0, ',', '.') ?></th>
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