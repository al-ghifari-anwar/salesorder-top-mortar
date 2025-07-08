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
                    <h1 class="m-0">Voucher</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Voucher</li>
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
                            <?php if ($this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'marketing') : ?>
                                <button type="button" class="btn btn-primary float-right mx-1" data-toggle="modal" data-target="#modal-insert">
                                    Tambah Voucher
                                </button>
                            <?php endif; ?>
                            <!-- <a class="btn btn-success float-right mx-1" data-toggle="modal" data-target="#modal-laporan">
                                Laporan Voucher
                            </a> -->
                            <a href="<?= base_url('vc-penerima/') . $id_city  ?>" class="btn btn-primary float-right mx-1" target="__blank">
                                Laporan Penerima
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="table-print" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Voucher</th>
                                        <th>Tanggal</th>
                                        <th>Expired Date</th>
                                        <th>Is Active</th>
                                        <th>Toko</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($voucher as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['no_voucher'] ?></td>
                                            <td><?= date("d M Y", strtotime($data['date_voucher'])) ?></td>
                                            <td><?= date("d M Y", strtotime($data['exp_date'])) ?></td>
                                            <td>
                                                <?php
                                                $dateNow = date('Y-m-d');
                                                $dateExp = date("Y-m-d", strtotime($data['exp_date']));
                                                ?>
                                                <?php
                                                if ($data['is_claimed'] == 0) {
                                                    if ($dateExp >= $dateNow) {
                                                ?>
                                                        <p class="text-success">YES</p>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <p class="text-danger">NO</p>
                                                    <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <p class="text-danger">NO</p>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['type_voucher'] ?></td>
                                            <td>
                                                <?php
                                                $dateNow = date('Y-m-d');
                                                $dateExp = date("Y-m-d", strtotime($data['exp_date']));
                                                ?>
                                                <?php if ($data['is_claimed'] == 1) { ?>
                                                    <?= $data['is_claimed'] == 0 ? 'Not Claimed' : 'Claimed' ?>
                                                <?php } else { ?>
                                                    <?php if ($dateExp >= $dateNow) { ?>
                                                        <?= 'Not Claimed' ?>
                                                    <?php } else { ?>
                                                        <?= 'Expired' ?>
                                                    <?php } ?>
                                                <?php } ?>
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

<div class="modal fade" id="modal-insert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Voucher Toko</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('reg-voucher/' . $id_city) ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama Toko</label>
                        <select name="id_contact" id="select2bs41" class="select2bs41">
                            <?php foreach ($contact as $contact) : ?>
                                <option value="<?= $contact['id_contact'] ?>"><?= $contact['nama'] . " - " . $contact['store_status'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Jumlah Voucher</label>
                        <input type="number" name="jml_voucher" class="form-control">
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

<div class="modal fade" id="modal-laporan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Laporan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('lap-voucher/' . $id_city) ?>" method="POST" target="__blank">
                    <div class="form-group">
                        <label for="">Berdasarkan</label>
                        <select name="berdasarkan" id="select2bs4" class="select2bs4">
                            <option value="belum-terima">Toko yang belum menerima voucher</option>
                            <option value="expired">Toko dengan voucher expired</option>
                            <option value="claimed">Voucher yang sudah di-claim</option>
                            <option value="not-claimed">Voucher belum di-claim</option>
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