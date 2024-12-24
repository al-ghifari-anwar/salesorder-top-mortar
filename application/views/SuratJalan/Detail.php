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
            <?php if ($vouchers != null) : ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i></strong> Toko ini memiliki kode voucher, claim melalui opsi "Gunakan Voucher" ketika menambah produk
                </div>
            <?php endif ?>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $suratjalan['no_surat_jalan'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Surat Jalan</a></li>
                        <li class="breadcrumb-item"><?= $suratjalan['no_surat_jalan'] ?></li>
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
                                    <h5><b>Shipping Detail:</b></h5>
                                    <h6>Ship To Name: <?= $suratjalan['ship_to_name'] ?></h6>
                                    <h6>Ship To Address: <?= $suratjalan['ship_to_address'] ?></h6>
                                    <h6>Ship To Phone: <?= $suratjalan['ship_to_phone'] ?></h6>
                                </div>
                                <div class="col-3">
                                    <h5><b>Courier Detail:</b></h5>
                                    <h6>Name: <?= $suratjalan['full_name'] ?></h6>
                                    <h6>Kendaraan: <?= $suratjalan['nama_kendaraan'] ?></h6>
                                    <h6>No. Polisi: <?= $suratjalan['nopol_kendaraan'] ?></h6>
                                </div>
                                <div class="col-1">
                                    <?php if ($detail == null) { ?>
                                        <a href="<?= base_url('finish-suratjalan/') . $suratjalan['id_surat_jalan'] ?>" class="btn btn-secondary disabled">Finish</a>
                                    <?php } else if ($detail != null && $suratjalan['is_finished'] == 0) { ?>
                                        <a href="<?= base_url('finish-suratjalan/') . $suratjalan['id_surat_jalan'] ?>" class="btn btn-success">Finish</a>
                                    <?php } ?>

                                    <?php if ($suratjalan['is_finished'] == 1) : ?>
                                        <a href="<?= base_url('finish-suratjalan/') . $suratjalan['id_surat_jalan'] ?>" class="btn btn-secondary disabled">Finish</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <?php if ($suratjalan['is_finished'] == 0) : ?>
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                    Tambah Produk
                                </button>
                                <?php if ($vouchers != null) : ?>
                                    <button type="button" class="btn btn-warning float-right mr-3" data-toggle="modal" data-target="#modal-insert-vc">
                                        Tambah Produk Voucher
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>QTY</th>
                                        <th>Is Free / Retur</th>
                                        <?php if ($suratjalan['is_finished'] == 0) : ?>
                                            <th>Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($detail as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama_produk'] ?> <b><?= $data['no_voucher'] == null ? '' : '(' . $data['no_voucher'] . ')' ?></b></td>
                                            <td><?= $data['price'] ?></td>
                                            <td><?= $data['qty_produk'] ?></td>
                                            <td><?= $data['is_bonus'] == 1 || $data['is_bonus'] == 2 ? 'Yes' : 'No' ?></td>
                                            <?php if ($suratjalan['is_finished'] == 0) : ?>
                                                <td>
                                                    <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $data['id_detail_surat_jalan'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                    <a href="<?= base_url('delete-detsuratjalan/') . $data['id_detail_surat_jalan'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $data['id_detail_surat_jalan'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('update-detsuratjalan/') . $data['id_detail_surat_jalan'] ?>" method="POST">
                                                            <input type="text" name="id_detail_surat_jalan" class="form-control" value="<?= $data['id_detail_surat_jalan'] ?>" hidden>
                                                            <input type="text" name="id_surat_jalan" class="form-control" value="<?= $data['id_surat_jalan'] ?>" hidden>
                                                            <div class="form-group">
                                                                <label for="">Produk</label>
                                                                <select class="form-control select2bs4" name="id_produk" style="width: 100%;">
                                                                    <?php foreach ($produk as $dataProduk) : ?>
                                                                        <option value="<?= $dataProduk['id_produk'] ?>"><?= $dataProduk['nama_produk'] . " - " . "Rp. " . number_format($dataProduk['harga_produk'], 0, ',', '.')  ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">QTY</label>
                                                                <input type="number" name="qty_produk" class="form-control" value="<?= $data['qty_produk'] ?>">
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
                <h4 class="modal-title">Tambah Produk Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-detsuratjalan') ?>" method="POST">
                    <input type="text" name="id_surat_jalan" class="form-control" value="<?= $suratjalan['id_surat_jalan'] ?>" hidden>
                    <div class="form-group">
                        <label for="">Produk</label>
                        <select class="form-control select2bs4 id_produk" name="id_produk" style="width: 100%;">
                            <?php foreach ($produk as $data) : ?>
                                <option value="<?= $data['id_produk'] ?>" price="<?= $data['harga_produk'] ?>"><?= $data['nama_produk'] . " - " . "Rp. " . number_format($data['harga_produk'], 0, ',', '.')  ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">QTY</label>
                        <input type="number" name="qty_produk" class="form-control" id="qty_produk">
                    </div>
                    <div class="form-group">
                        <label for="">Harga</label>
                        <input type="number" name="harga_produk" class="form-control" id="harga_produk">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <?php if ($vouchers != null) : ?>
                                <div class="col-5 mr-auto">
                                    <!-- <label for="">Gunakan Voucher</label> -->
                                    <input type="checkbox" name="is_voucher" id="is_voucher" hidden>
                                </div>
                            <?php endif; ?>
                            <div class="col-3 ml-auto">
                                <label for="">Is Retur?</label>
                                <input type="checkbox" name="is_retur" id="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php if ($vouchers != null) : ?>
                            <?php
                            $vouchersStr = "";
                            foreach ($vouchers as $voucher) {
                                $vouchersStr .= $voucher['no_voucher'] . ",";
                            }
                            ?>
                            <!-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle"></i></strong> Toko ini memiliki <?= count($vouchers) ?> kode voucher. (<?= $vouchersStr ?>)
                            </div> -->
                            <input type="text" value="<?= $vouchersStr ?>" name="no_vouchers" hidden>
                        <?php endif; ?>
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

<div class="modal fade" id="modal-insert-vc">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Produk Voucher</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-detsuratjalan') ?>" method="POST">
                    <input type="text" name="id_surat_jalan" class="form-control" value="<?= $suratjalan['id_surat_jalan'] ?>" hidden>
                    <div class="form-group">
                        <label for="">Produk</label>
                        <select class="form-control select2bs4" name="id_produk" style="width: 100%;">
                            <?php foreach ($produk as $data) : ?>
                                <option value="<?= $data['id_produk'] ?>"><?= $data['nama_produk'] . " - " . "Rp. " . number_format($data['harga_produk'], 0, ',', '.')  ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">QTY</label>
                        <input type="number" name="qty_produk" class="form-control" id="qty_produk">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <?php if ($vouchers != null) : ?>
                                <div class="col-5 mr-auto">
                                    <!-- <label for="">Gunakan Voucher</label> -->
                                    <input type="checkbox" name="is_voucher" id="is_voucher" checked hidden>
                                </div>
                            <?php endif; ?>
                            <div class="col-3 ml-auto">
                                <!-- <label for="">Is Retur?</label> -->
                                <input type="checkbox" name="is_retur" id="" hidden>
                            </div>
                            <input type="number" value="<?= count($vouchers) ?>" hidden name="jml_voucher">
                        </div>
                    </div>
                    <div class="form-group">
                        <?php if ($vouchers != null) : ?>
                            <?php
                            $vouchersStr = "";
                            foreach ($vouchers as $voucher) {
                                $vouchersStr .= $voucher['no_voucher'] . ",";
                            }
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle"></i></strong> Toko ini memiliki <?= count($vouchers) ?> kode voucher. (<?= $vouchersStr ?>)
                            </div>
                            <input type="text" value="<?= $vouchersStr ?>" name="no_vouchers" hidden>
                        <?php endif; ?>
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

<script>
    document.getElementById("select2bs4").onchange = function() {
        //Print data toko di field
        console.log(this.options[this.selectedIndex].getAttribute("price"));
        document.getElementById('harga_produk').textContent = this.options[this.selectedIndex].getAttribute("price");
    };
</script>