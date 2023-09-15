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
                    <h1 class="m-0">Pembayaran Transit</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Pembayaran</a></li>
                        <li class="breadcrumb-item active">Transit</li>
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
                                        <th>Nominal</th>
                                        <th>Tanggal</th>
                                        <th>Remark</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($payment as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= number_format($data['amount_payment'], 0, '.', ',') ?></td>
                                            <td><?= date("d M Y", strtotime($data['date_payment'])) ?></td>
                                            <td><?= $data['remark_payment'] ?></td>
                                            <td>
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $data['id_payment'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('delete-payment/') . $data['id_payment'] ?>" class="btn btn-danger"><i class="fas fa-remove-format"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $data['id_payment'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Assign Invoice</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('update-payment/') . $data['id_payment'] ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Invoice</label>
                                                                <select class="form-control select2bs4" name="id_invoice" style="width: 100%;" id="select2bs4<?= $data['id_payment'] ?>">
                                                                    <option value="0">--- PLEASE SELECT INVOICE ---</option>
                                                                    <?php foreach ($invoice as $dataInv) : ?>
                                                                        <option value="<?= $dataInv['id_invoice'] ?>"><?= $dataInv['no_invoice'] . " - " . $dataInv['nama'] . " - " . $dataInv['nama_city'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <button class="btn btn-primary float-right">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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