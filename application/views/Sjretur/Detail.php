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
                    <h1 class="m-0"><?= $sjretur['no_sjretur'] . str_pad($sjretur['id_sjretur'], 5, "0", STR_PAD_LEFT) ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><?= $title ?></a></li>
                        <li class="breadcrumb-item"><?= $sjretur['no_sjretur'] . str_pad($sjretur['id_sjretur'], 5, "0", STR_PAD_LEFT) ?></li>
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
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <h5><b>Customer Detail:</b></h5>
                                    <h6>Toko: <?= $toko['nama'] ?></h6>
                                    <h6>Nomor HP: <?= $toko['nomorhp'] ?></h6>
                                </div>
                                <div class="col-4">
                                    <h5><b>Gudang Retur:</b></h5>
                                    <h6><?= $gudang['name_gudang_stok'] ?></h6>
                                </div>
                                <div class="col-1">
                                    <?php if ($sjreturdetails == null) { ?>
                                        <a href="<?= base_url('sjretur/finish/') . $sjretur['id_sjretur'] ?>" class="btn btn-secondary disabled">Finish</a>
                                    <?php } else if ($sjreturdetails != null) { ?>
                                        <?php if ($sjretur['is_finished'] == 0): ?>
                                            <a href="<?= base_url('sjretur/finish/') . $sjretur['id_sjretur'] ?>" class="btn btn-success">Finish</a>
                                        <?php endif; ?>
                                        <?php if ($sjretur['is_finished'] == 1) : ?>
                                            <a href="<?= base_url('sjretur/finish/') . $sjretur['id_sjretur'] ?>" class="btn btn-secondary disabled">Finish</a>
                                        <?php endif; ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <?php if ($sjretur['is_finished'] == 0) : ?>
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                    Tambah Produk
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>QTY</th>
                                        <?php if ($sjretur['is_finished'] == 0) : ?>
                                            <th>Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($sjreturdetails as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama_produk'] ?> </td>
                                            <td><?= $data['qty_sjretur_detail'] ?></td>
                                            <?php if ($sjretur['is_finished'] == 0) : ?>
                                                <td>
                                                    <a href="<?= base_url('sjretur/detail/delete/') . $data['id_sjretur_detail'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                                </td>
                                            <?php endif; ?>
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

<div class="modal fade" id="modal-insert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Produk Retur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('sjretur/detail/create') ?>" method="POST">
                    <input type="text" name="id_city" class="form-control" value="<?= $toko['id_city'] ?>" hidden>
                    <input type="text" name="id_sjretur" class="form-control" value="<?= $sjretur['id_sjretur'] ?>" hidden>
                    <div class="form-group">
                        <label for="">Produk</label>
                        <select class="form-control select2bs4 id_produk" id="id_produk" name="id_detail_surat_jalan" style="width: 100%;">
                            <option value="0">=== PILIH PRODUK ===</option>
                            <?php foreach ($sjdetails as $data) : ?>
                                <option value="<?= $data['id_detail_surat_jalan'] ?>" price="<?= $data['harga_produk'] ?>"><?= $data['nama_produk'] . " (" . $data['qty_produk'] . " " . $data['name_satuan'] . ")"  ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Jumlah Retur</label>
                        <input type="number" name="qty_sjretur_detail" class="form-control">
                    </div>
                    <!-- <div class="form-group">
                        <label for="">Harga</label>
                        <input type="number" name="harga_produk" class="form-control" id="harga_produk">
                    </div> -->
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<script>
    document.getElementById("id_produk").onchange = function() {
        //Print data toko di field
        console.log(this.options[this.selectedIndex].getAttribute("price"));
    };
</script>