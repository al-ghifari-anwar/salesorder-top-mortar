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
                        <li class="breadcrumb-item"><a href="#"><?= $menuGroup ?></a></li>
                        <li class="breadcrumb-item active"><?= $menu ?></li>
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
                                        <th>Foto</th>
                                        <th>Nama</th>
                                        <th>Harga (Poin)</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($merchandises as $merchandise) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <img src="<?= $merchandise['img_merchandise'] ?>" alt="" width="100">
                                            </td>
                                            <td><?= $merchandise['name_merchandise'] ?></td>
                                            <td><?= $merchandise['price_merchandise'] ?></td>
                                            <td><?= $merchandise['desc_merchandise'] ?></td>
                                            <td>
                                                <a href="#" class="btn btn-success m-1" data-toggle="modal" data-target="#modal-edit<?= $merchandise['id_merchandise'] ?>"><i class="fas fa-edit"></i></a>
                                                <a href="<?= base_url('merchandise/delete/' . $merchandise['id_merchandise']) ?>" class="btn btn-danger m-1"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $merchandise['id_merchandise'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Tambah Data <?= $title ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('merchandise/update/' . $merchandise['id_merchandise']) ?>" method="POST" enctype="multipart/form-data">
                                                            <div class="form-group">
                                                                <label for="">Nama Merchandise</label>
                                                                <input type="text" name="name_merchandise" id="" class="form-control" value="<?= $merchandise['name_merchandise'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Harga (Poin)</label>
                                                                <input type="number" name="price_merchandise" id="" class="form-control" value="<?= $merchandise['price_merchandise'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Deskripsi</label>
                                                                <textarea name="desc_merchandise" id="" cols="30" rows="5" class="form-control"><?= $merchandise['desc_merchandise'] ?></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Foto</label>
                                                                <input type="file" name="img_merchandise" id="" class="form-control">
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
                <h4 class="modal-title">Tambah Data <?= $title ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('merchandise/create') ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="">Nama Merchandise</label>
                        <input type="text" name="name_merchandise" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Harga (Poin)</label>
                        <input type="number" name="price_merchandise" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Deskripsi</label>
                        <textarea name="desc_merchandise" id="" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Foto</label>
                        <input type="file" name="img_merchandise" id="" class="form-control">
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