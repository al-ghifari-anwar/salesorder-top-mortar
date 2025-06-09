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
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
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
                                        <th>Nama Produk</th>
                                        <th>Stok Minimum</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($minimums as $minimum) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $minimum['name_master_produk'] ?></td>
                                            <td><?= $minimum['minimum_stok'] ?></td>
                                            <td>
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $minimum['id_minimum_stok'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('minimumstok/delete/') . $minimum['id_minimum_stok'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $minimum['id_minimum_stok'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('minimumstok/update/') ?>" method="POST">
                                                            <input type="hidden" name="id_gudang_stok" value="<?= $gudang['id_gudang_stok'] ?>">
                                                            <input type="hidden" name="id_minimum_stok" value="<?= $minimum['id_minimum_stok'] ?>">
                                                            <input type="hidden" name="old_id_master_produk" value="<?= $minimum['id_master_produk'] ?>">
                                                            <div class="form-group">
                                                                <label for="">Produk</label>
                                                                <select name="id_master_produk" id="" class="form-control select2bs4">
                                                                    <?php foreach ($produks as $produk): ?>
                                                                        <option value="<?= $produk['id_master_produk'] ?>" <?= $minimum['id_master_produk'] == $produk['id_master_produk'] ? 'selected' : '' ?>><?= $produk['name_master_produk'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Stok Minimal</label>
                                                                <input type="numeric" name="minimum_stok" id="" class="form-control" value="<?= $minimum['minimum_stok'] ?>">
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
                <h4 class="modal-title">Tambah Data</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('minimumstok/create') ?>" method="POST">
                    <input type="hidden" name="id_gudang_stok" value="<?= $gudang['id_gudang_stok'] ?>">
                    <div class="form-group">
                        <label for="">Produk</label>
                        <select name="id_master_produk" id="" class="form-control select2bs4">
                            <?php foreach ($produks as $produk): ?>
                                <option value="<?= $produk['id_master_produk'] ?>"><?= $produk['name_master_produk'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Stok Minimal</label>
                        <input type="numeric" name="minimum_stok" id="" class="form-control" value="1">
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