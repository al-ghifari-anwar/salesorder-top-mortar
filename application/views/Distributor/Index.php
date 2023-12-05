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
                    <h1 class="m-0">List Distributor</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Distributor</li>
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
                                        <th>Nama Distributor</th>
                                        <th>Nomor HP</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($dst as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama_distributor'] ?></td>
                                            <td><?= $data['nomorhp_distributor'] ?></td>
                                            <td>
                                                <a class="btn btn-success" data-toggle="modal" data-target="#modal-user-dist<?= $data['id_distributor'] ?>" title="Tambah Akun Admin"><i class="fas fa-user"></i></a>
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $data['id_distributor'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('delete-dist/') . $data['id_distributor'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $data['id_distributor'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data Distributor</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('update-dist/') . $data['id_distributor'] ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Nama Distributor</label>
                                                                <input type="text" name="nama_distributor" id="" class="form-control" value="<?= $data['nama_distributor'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Nomor HP</label>
                                                                <input type="text" name="nomorhp_distributor" class="form-control" value="<?= $data['nomorhp_distributor'] ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Alamat</label>
                                                                <textarea name="alamat_distributor" id="" cols="30" rows="3" class="form-control"><?= $data['alamat_distributor'] ?></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Jenis</label>
                                                                <select name="jenis_distributor" id="" class="form-control">
                                                                    <option value="dist">Distributor</option>
                                                                    <option value="pusat">Pusat</option>
                                                                </select>
                                                            </div>
                                                            <button class="btn btn-primary float-right">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="modal-user-dist<?= $data['id_distributor'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Tambah Admin Distributor</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('add-dist-user') ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Username</label>
                                                                <input type="text" name="username" id="" class="form-control" placeholder="Huruf kecil tanpa spasi. Contoh: user1">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Nama Lengkap</label>
                                                                <input type="text" name="full_name" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Password (Minimal 8 Karakter)</label>
                                                                <input type="password" name="password" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Nomor HP</label>
                                                                <input type="text" name="phone_user" class="form-control">
                                                            </div>
                                                            <input type="text" name="id_distributor" value="<?= $data['id_distributor'] ?>" hidden>
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
                <h4 class="modal-title">Tambah Data Distributor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-dist') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama Distributor</label>
                        <input type="text" name="nama_distributor" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Nomor HP</label>
                        <input type="text" name="nomorhp_distributor" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Alamat</label>
                        <textarea name="alamat_distributor" id="" cols="30" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis</label>
                        <select name="jenis_distributor" id="" class="form-control">
                            <option value="dist">Distributor</option>
                            <option value="pusat">Pusat</option>
                        </select>
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