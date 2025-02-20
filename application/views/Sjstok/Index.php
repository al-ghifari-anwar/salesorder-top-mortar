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
                                        <th>No Pengiriman</th>
                                        <th>Gudang</th>
                                        <th>Dibuat pada</th>
                                        <th>Tgl Pengiriman</th>
                                        <th>Diterima Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($sjstoks as $sjstok) : ?>
                                        <?php
                                        $id_gudang_stok = $sjstok['id_gudang_stok'];
                                        $gudangStok = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= 'SO-' . str_pad($sjstok['id_sj_stok'], 6, "0", STR_PAD_LEFT) ?></td>
                                            <td><?= $gudangStok['name_gudang_stok'] ?></td>
                                            <td><?= date('d F Y', strtotime($sjstok['created_at'])) ?></td>
                                            <td><?= date('d F Y', strtotime($sjstok['delivery_date'])) ?></td>
                                            <td><?= $sjstok['is_rechieved'] == 0 ? 'Belum' : date('d F Y', strtotime($sjstok['rechieved_date'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('sjstok/' . $sjstok['id_sj_stok']) ?>" class="btn bg-teal m-1" title="Detail"><i class="fas fa-eye"></i></a>
                                                <?php if ($sjstok['is_finished'] == 0): ?>
                                                    <a class="btn btn-primary m-1" data-toggle="modal" data-target="#modal-edit<?= $sjstok['id_sj_stok'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                    <a href="<?= base_url('sjstok/delete/') . $sjstok['id_sj_stok'] ?>" class="btn btn-danger m-1" title="Hapus"><i class="fas fa-trash"></i></a>
                                                <?php endif; ?>
                                                <?php if ($sjstok['is_finished'] == 1): ?>
                                                    <a href="<?= base_url('sjstok/print/' . $sjstok['id_sj_stok']) ?>" class="btn bg-maroon m-1" title="Print" target="__blank"><i class="fas fa-print"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-edit<?= $sjstok['id_sj_stok'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Data Kota</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('sjstok/update/') . $sjstok['id_sj_stok'] ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Nama</label>
                                                                <input type="text" name="name_sjstok_stok" id="" class="form-control" value="<?= $sjstok['name_sjstok_stok'] ?>">
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
                <form action="<?= base_url('sjstok/create') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Gudang Tujuan</label>
                        <select name="id_gudang_stok" id="" class="form-control select2bs4">
                            <option value="0">=== Pilih Gudang ===</option>
                            <?php foreach ($gudangs as $gudang): ?>
                                <option value="<?= $gudang['id_gudang_stok'] ?>"><?= $gudang['name_gudang_stok'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tgl Pengiriman</label>
                        <input type="date" name="delivery_date" class="form-control">
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