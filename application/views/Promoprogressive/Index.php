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
                                        <th>Nama Promo</th>
                                        <th>Kelipatan</th>
                                        <th>Bonus</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($promos as $promo) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $promo['name_promo_progressive'] ?></td>
                                            <td><?= $promo['kelipatan_promo_progressive'] ?></td>
                                            <td><?= $promo['bonus_promo_progressive'] ?></td>
                                            <td>
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $promo['id_promo_progressive'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('promoprogressive/delete/') . $promo['id_promo_progressive'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $promo['id_promo_progressive'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data Promo</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('promoprogressive/update/') . $promo['id_promo_progressive'] ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Nama Promo</label>
                                                                <input type="text" name="name_promo_progressive" id="" class="form-control" value="<?= $promo['name_promo_progressive'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Kelipatan</label>
                                                                <input type="number" name="kelipatan_promo_progressive" class="form-control" value="<?= $promo['kelipatan_promo_progressive'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Bonus</label>
                                                                <input type="number" name="bonus_promo_progressive" class="form-control" value="<?= $promo['bonus_promo_progressive'] ?>">
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
                <h4 class="modal-title">Tambah Data Promo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('promoprogressive/create') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama Promo</label>
                        <input type="text" name="name_promo_progressive" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Kelipatan</label>
                        <input type="number" name="kelipatan_promo_progressive" class="form-control" value="1">
                    </div>
                    <div class="form-group">
                        <label for="">Bonus</label>
                        <input type="number" name="bonus_promo_progressive" class="form-control" value="1">
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