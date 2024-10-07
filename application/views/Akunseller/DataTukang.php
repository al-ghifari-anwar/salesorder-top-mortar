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
                        <li class="breadcrumb-item"><a href="#">Top Seller</a></li>
                        <li class="breadcrumb-item active">Data Tukang</li>
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
                                <div class="col-10">

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tukang</th>
                                        <th>Skill</th>
                                        <th>Nomor HP</th>
                                        <th>Alamat</th>
                                        <th>Kota</th>
                                        <th>Tgl Lahir</th>
                                        <th>Validasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($vouchers as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['nama_skill'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['address'] ?></td>
                                            <td><?= $data['nama_city'] ?></td>
                                            <td><?= $data['tgl_lahir'] == '0000-00-00' ? "" : date("d F Y", strtotime($data['tgl_lahir'])) ?></td>
                                            <td>
                                                <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal-validate<?= $data['id_tukang'] ?>">Valid&nbsp;<i class="fas fa-check-circle"></i></a>
                                                <a href="<?= base_url('akunseller/deletetukang/') . $data['id_tukang'] ?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-validate<?= $data['id_tukang'] ?>">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Validasi Data Tukang</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('akunseller/validasi/' . $data['id_tukang']) ?>" method="POST">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="">Nama</label>
                                                                        <input type="text" class="form-control" value="<?= $data['nama'] ?>" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="">Nomor HP</label>
                                                                        <input type="text" class="form-control" value="<?= $data['nomorhp'] ?>" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="">Kota</label>
                                                                        <input type="text" class="form-control" value="<?= $data['nama_city'] ?>" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="">Skill</label>
                                                                        <input type="text" class="form-control" value="<?= $data['nama_skill'] ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="">Tgl Lahir</label>
                                                                        <input type="date" class="form-control" name="tgl_lahir" value="<?= $data['tgl_lahir'] ?>">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="">Alamat</label>
                                                                        <textarea name="address" id="" cols="30" rows="3" class="form-control"><?= $data['address'] ?></textarea>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="">Kategori</label>
                                                                        <select name="id_catcus" id="" class="form-control select2bs4">
                                                                            <?php foreach ($catcuss as $catcus): ?>
                                                                                <option value="<?= $catcus['id_catcus'] ?>" <?= $data['id_catcus'] == $catcus['id_catcus'] ? 'selected' : '' ?>><?= $catcus['name_catcus'] ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-primary float-right">Validasi</button>
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