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
                                <div class="col-6">
                                    <h6>No Pengiriman: <b><?= 'SO-' . str_pad($sjstok['id_sj_stok'], 6, "0", STR_PAD_LEFT) ?></b></h6>
                                    <h6>Gudang: <b><?= $gudang['name_gudang_stok'] ?></b></h6>
                                    <?php
                                    $gudangCity = '';
                                    $noGudangCity = 1;
                                    foreach ($citys as $city) {
                                        if ($noGudangCity == 1) {
                                            $gudangCity .= $city['nama_city'];
                                        } else {
                                            $gudangCity .= ', ' . $city['nama_city'];
                                        }
                                        $noGudangCity++;
                                    }
                                    ?>
                                    <h6>Kota: <b><?= $gudangCity ?></b></h6>
                                </div>
                                <div class="col-6">
                                    <?php if ($sjstok['is_rechieved'] == 0): ?>
                                        <button type="button" class="btn btn-success float-right btn-lg" data-toggle="modal" data-target="#modal-insert">
                                            Konfirmasi
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Dikirim</th>
                                        <th>Diterima</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($detailSjstoks as $detailSjstok) : ?>
                                        <?php
                                        $id_master_produk = $detailSjstok['id_master_produk'];
                                        $detailMasterProduk = $this->db->get_where('tb_master_produk', ['id_master_produk' => $id_master_produk])->row_array();

                                        $id_satuan = $detailMasterProduk['id_satuan'];
                                        $satuan = $this->db->get_where('tb_satuan', ['id_satuan' => $id_satuan])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $detailMasterProduk['name_master_produk'] ?></td>
                                            <td><?= $detailSjstok['qty_detail_sj_stok'] . ' ' . $satuan['name_satuan'] ?></td>
                                            <td><?= $detailSjstok['qty_rechieved'] . ' ' . $satuan['name_satuan'] ?></td>
                                            <td>
                                                <?php if ($sjstok['is_rechieved'] == 0): ?>
                                                    <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $detailSjstok['id_detail_sj_stok'] ?>" title="Adjust"><i class="fas fa-edit"></i>&nbsp;&nbsp;Adjustment</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $detailSjstok['id_detail_sj_stok'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('sjstok/rechieved/update/') . $detailSjstok['id_detail_sj_stok'] ?>" method="POST">
                                                            <input type="text" name="id_sj_stok" value="<?= $sjstok['id_sj_stok'] ?>" hidden>
                                                            <div class="form-group">
                                                                <label for="">Produk</label>
                                                                <input type="text" class="form-control" value="<?= $detailMasterProduk['name_master_produk'] ?>" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">QTY</label>
                                                                <input type="number" name="qty_rechieved" class="form-control" value="<?= $detailSjstok['qty_detail_sj_stok'] ?>">
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
                <form action="<?= base_url('sjstok/rechieved/save/' . $sjstok['id_sj_stok']) ?>" method="POST">
                    <input type="text" name="id_sj_stok" value="<?= $sjstok['id_sj_stok'] ?>" hidden>
                    <div class="form-group">
                        <label for="">Nama Penerima</label>
                        <input type="text" class="form-control" name="rechieved_name">
                    </div>
                    <div class="form-group">
                        <label for="">Nomor HP Penerima</label>
                        <input type="text" class="form-control" name="rechieved_phone">
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