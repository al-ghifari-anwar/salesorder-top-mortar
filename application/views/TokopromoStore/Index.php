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
                    <h1 class="m-0"><?= $title ?> - <?= $city['nama_city'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('prioritystore') ?>">Toko Prioritas</a></li>
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
                            <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-add">Tambah Toko Promo</a>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Toko</th>
                                        <th>Status</th>
                                        <th>Reputation</th>
                                        <th>Kuota</th>
                                        <th>QR</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($contactPriors as $data) : ?>
                                        <?php
                                        $id_contact = $data['id_contact'];
                                        $getCountVoucher = $this->db->get_where('tb_voucher_tukang', ['id_contact' => $id_contact])->num_rows();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td><?= $data['quota_priority'] - $getCountVoucher ?></td>
                                            <td>
                                                <a href="<?= base_url('assets/img/qr/') .  $data['qr_toko'] ?>" target="__blank">
                                                    <img src="<?= base_url('assets/img/qr/') .  $data['qr_toko'] ?>" alt="" class="img-fluid" width="100">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('tokopromostore/delete/') . $data['id_contact'] ?>" class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data?')"><i class="fas fa-trash"></i></a>
                                                <a href="<?= base_url('tokopromostore/penukaran/') . $data['id_contact'] ?>" class="btn btn-success"><i class="fas fa-ticket-alt"></i>&nbsp;List Penukaran</a>
                                            </td>
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

<div class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Toko Promo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('tokopromostore/add') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Toko</label>
                        <select name="id_contact" id="select2bs4" class="form-control select2bs4">
                            <?php foreach ($contacts as $contact) : ?>
                                <option value="<?= $contact['id_contact'] ?>"><?= $contact['nama'] . " - " . $contact['nomorhp'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>