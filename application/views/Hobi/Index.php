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
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <style>
        .tree-level-0 {
            border-left: 5px solid #007bff;
        }

        .tree-level-1 {
            border-left: 5px solid #28a745;
        }

        .tree-level-2 {
            border-left: 5px solid #fd7e14;
        }

        .tree-level-3 {
            border-left: 5px solid #dc3545;
        }

        .tree-level-4 {
            border-left: 5px solid #6f42c1;
        }
    </style>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">Tambah Data</a>
                        </div>
                        <div class="card-body">
                            <?php foreach ($hobis as $hobi): ?>
                                <?php
                                $level = substr_count($hobi['path_hobi'], '/') - 1;

                                ?>
                                <div class="card tree-level-<?= $level % 5 ?> shadow-sm mb-2" style="margin-left: <?= $level * 35 ?>px;">

                                    <div class="card-body py-2">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div>

                                                <?php for ($i = 0; $i < $level; $i++): ?>
                                                    <span class="text-muted mr-1">│</span>
                                                <?php endfor; ?>

                                                <?php if ($level > 0): ?>
                                                    <span class="text-muted">├──</span>
                                                <?php endif; ?>

                                                <strong><?= $hobi['name_hobi'] ?></strong>

                                            </div>

                                            <div>

                                                <button
                                                    class="btn btn-xs btn-success"
                                                    data-toggle="modal"
                                                    data-target="#modal-edit<?= $hobi['id_hobi'] ?>">

                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </button>

                                                <button
                                                    class="btn btn-xs btn-primary"
                                                    data-toggle="modal"
                                                    data-target="#modal-insert<?= $hobi['id_hobi'] ?>">

                                                    <i class="fas fa-plus"></i>
                                                    Sub
                                                </button>

                                                <button
                                                    class="btn btn-xs btn-danger"
                                                    data-toggle="modal"
                                                    data-target="#modal-delete<?= $hobi['id_hobi'] ?>">

                                                    <i class="fas fa-trash"></i>
                                                    Hapus
                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="modal fade" id="modal-delete<?= $hobi['id_hobi'] ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?= base_url('hobi/delete') ?>" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="id_hobi" value="<?= $hobi['id_hobi'] ?>">
                                                    <input type="hidden" name="path_hobi" value="<?= $hobi['path_hobi'] ?>">
                                                    Apakah akan hapus data?
                                                    <button class="btn btn-danger float-right">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
                                <div class="modal fade" id="modal-insert<?= $hobi['id_hobi'] ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?= base_url('hobi/create') ?>" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="id_parent_hobi" value="<?= $hobi['id_hobi'] ?>">
                                                    <input type="hidden" name="path_hobi" value="<?= $hobi['path_hobi'] ?>">
                                                    <div class="form-group">
                                                        <label for="">Hobi</label>
                                                        <input type="text" name="name_hobi" class="form-control">
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
                                <div class="modal fade" id="modal-edit<?= $hobi['id_hobi'] ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?= base_url('hobi/update/') . $hobi['id_hobi'] ?>" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="id_parent_hobi" value="<?= $hobi['id_hobi'] ?>">
                                                    <input type="hidden" name="path_hobi" value="<?= $hobi['path_hobi'] ?>">
                                                    <div class="form-group">
                                                        <label for="">Hobi</label>
                                                        <input type="text" name="name_hobi" class="form-control" value="<?= $hobi['name_hobi'] ?>">
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
                            <?php endforeach; ?>
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
                <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('hobi/create') ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_parent_hobi" value="0">
                    <input type="hidden" name="path_hobi" value="">
                    <div class="form-group">
                        <label for="">Hobi</label>
                        <input type="text" name="name_hobi" class="form-control">
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