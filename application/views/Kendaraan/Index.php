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
                    <h1 class="m-0">List Kendaraan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Kota</li>
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
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                Tambah Data
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kendaraan</th>
                                        <th>Nopol Kendaraan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($kendaraan as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama_kendaraan'] ?></td>
                                            <td><?= $data['nopol_kendaraan'] ?></td>
                                            <td>
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $data['id_kendaraan'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('delete-kendaraan/') . $data['id_kendaraan'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $data['id_kendaraan'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data Kota</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('update-kendaraan/') . $data['id_kendaraan'] ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Nama Kendaraan</label>
                                                                <input type="text" name="nama_kendaraan" id="" class="form-control" value="<?= $data['nama_kendaraan'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Nomor Polisi</label>
                                                                <input type="text" name="nopol_kendaraan" class="form-control" value="<?= $data['nopol_kendaraan'] ?>">
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

<div class="modal fade" id="modal-insert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Kendaraan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-kendaraan') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama Kendaraan</label>
                        <input type="text" name="nama_kendaraan" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Nomor Polisi</label>
                        <input type="text" name="nopol_kendaraan" class="form-control">
                    </div>
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->