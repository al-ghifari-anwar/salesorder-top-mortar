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
                    <h1 class="m-0">List Invoice <?= $city['nama_city'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Invoice</a></li>
                        <li class="breadcrumb-item active"><?= $city['nama_city'] ?></li>
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
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Invoice</th>
                                        <th>No DO</th>
                                        <th>Termin</th>
                                        <th>Jatem</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($invoice as $data) : ?>
                                        <?php
                                        if ($data['is_cod'] == 0) {
                                            // Set Jatem
                                            $date_jatem = date('Y-m-d H:i:s', strtotime("+" . $data['termin_payment'] . " days", strtotime($data['date_invoice'])));
                                            // Count Days
                                            $jatuhTempo = date('d M Y', strtotime("+" . $data['termin_payment'] . " days", strtotime($data['date_invoice'])));
                                            $date1 = new DateTime(date("Y-m-d"));
                                            $date2 = new DateTime($jatuhTempo);
                                            $days  = $date2->diff($date1)->format('%a');
                                        } else {
                                            $date_jatem = date("Y-m-d H:i:s", strtotime($data['date_invoice']));
                                            $days = "COD";
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['no_invoice'] ?></td>
                                            <td><?= $data['no_surat_jalan'] ?></td>
                                            <td><?= $days ?></td>
                                            <td><?= $date_jatem ?></td>
                                            <td>Rp. <?= number_format($data['total_invoice'], 0, ',', '.') ?></td>
                                            <td>
                                                <a href="<?= base_url('print-invoice/' . $data['id_invoice']) ?>" class="btn btn-success" title="Hapus" target="__blank"><i class="fas fa-print"></i></a>
                                                <a href="<?= base_url('invoice/change' . $data['id_invoice']) ?>" class="btn btn-success" title="Ubah" target="__blank"><i class="fas fa-print"></i></a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>
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