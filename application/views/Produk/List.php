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
                    <h1 class="m-0">List Produk</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Produk</a></li>
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
                                        <th>Satuan</th>
                                        <th>Harga Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($produk as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama_produk'] ?></td>
                                            <td><?= $data['name_satuan'] ?></td>
                                            <td>Rp. <?= number_format($data['harga_produk'], 0, ',', '.') ?></td>
                                            <td>
                                                <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $data['id_produk'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                <a href="#" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                                <!-- <a class="btn btn-success" data-toggle="modal" data-target="#modal-stok<?= $data['id_produk'] ?>" title="Tambah Stok"><i class="fas fa-archive"></i></a> -->
                                                <a href="<?= base_url('produk/' . $city['id_city'] . "/" . $data['id_produk']) ?>" class="btn btn-info" title="Tambah Stok" target="__blank"><i class="fas fa-eye"></i>&nbsp;Lihat Stok</a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $data['id_produk'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data Produk</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('update-produk/') . $data['id_produk'] ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Nama Produk</label>
                                                                <input type="text" name="nama_produk" id="" class="form-control" value="<?= $data['nama_produk'] ?>">
                                                            </div>
                                                            <label for="">Satuan</label>
                                                            <select name="id_satuan" id="" class="form-control select2bs4">
                                                                <?php foreach ($satuans as $satuan): ?>
                                                                    <option value="<?= $satuan['id_satuan'] ?>" <?= $satuan['id_satuan'] == $data['id_satuan'] ? 'selected' : '' ?>><?= $satuan['name_satuan'] ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <input type="text" name="id_city" value="<?= $city['id_city'] ?>" hidden>
                                                            <div class="form-group">
                                                                <label for="">Harga Produk</label>
                                                                <input type="text" name="harga_produk" class="form-control" value="<?= $data['harga_produk'] ?>">
                                                            </div>
                                                            <button class="btn btn-primary float-right">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="modal-stok<?= $data['id_produk'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Tambah Stok Produk</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('insert-stok/') . $data['id_produk'] ?>" method="POST">
                                                            <input type="text" name="id_city" value="<?= $city['id_city'] ?>" hidden>
                                                            <div class="form-group">
                                                                <label for="">Jumlah</label>
                                                                <input type="number" name="jml_stok" class="form-control" value="1">
                                                            </div>
                                                            <input type="text" name="id_city" value="<?= $city['id_city'] ?>" hidden>
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
                <h4 class="modal-title">Tambah Data Produk</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-produk') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama Produk</label>
                        <input type="text" name="nama_produk" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Satuan</label>
                        <select name="id_satuan" id="" class="form-control select2bs4">
                            <?php foreach ($satuans as $satuan): ?>
                                <option value="<?= $satuan['id_satuan'] ?>"><?= $satuan['name_satuan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="text" name="id_city" value="<?= $city['id_city'] ?>" hidden>
                    <div class="form-group">
                        <label for="">Harga Produk</label>
                        <input type="text" name="harga_produk" class="form-control">
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