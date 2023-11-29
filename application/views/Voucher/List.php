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
                    <h1 class="m-0">Voucher</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Voucher</li>
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
                            <!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                Tambah Data
                            </button> -->
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Voucher</th>
                                        <th>Tanggal</th>
                                        <th>Toko</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($voucher as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['no_voucher'] ?></td>
                                            <td><?= date("d M Y", strtotime($data['date_voucher'])) ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['is_claimed'] == 0 ? 'Not Claimed' : 'Claimed' ?></td>
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