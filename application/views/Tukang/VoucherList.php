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
                            <a href="" class="btn btn-primary float-right" data-toggle="modal" data-target="#add-modal">Tambah Voucher</a>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tukang</th>
                                        <th>Is Demo?</th>
                                        <th>Skill</th>
                                        <th>Nomor HP</th>
                                        <th>Tgl</th>
                                        <th>Expired</th>
                                        <th>Status</th>
                                        <!-- <th>Validasi</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($vctukangs as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['is_demo'] == 1 ? 'Yes' : 'No' ?></td>
                                            <td><?= $data['nama_skill'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['created_at'] == '0000-00-00' ? "" : date("d F Y", strtotime($data['created_at'])) ?></td>
                                            <td><?= $data['created_at'] == '0000-00-00' ? "" : date("d F Y", strtotime($data['exp_at'])) ?></td>
                                            <td><?= $data['is_claimed'] == 1 ? 'Claimed' : 'Belum Claim' ?></td>
                                            <!-- <td>
                                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-validate<?= $data['id_tukang'] ?>">Atur Demo&nbsp;<i class="fas fa-recycle"></i></a>
                                                <a href="<?= base_url('akunseller/deletetukang/') . $data['id_tukang'] ?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                            </td> -->
                                        </tr>
                                        <div class="modal fade" id="modal-validate<?= $data['id_tukang'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Atur Tukang Demo</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('akunseller/validasi/' . $data['id_tukang']) ?>" method="POST">
                                                            <div class="form-group">
                                                                <label for="">Pilih</label>
                                                                <select name="is_demo" id="" class="form-control select2bs4">
                                                                    <option value="1">Jadikan Demo</option>
                                                                    <option value="0">Keluarkan Dari Demo</option>
                                                                </select>
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

<div class="modal fade" id="add-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Voucher Tukang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('sebarvctukang/create/' . $city['id_city']) ?>" method="POST">
                    <div class="form-group">
                        <label for="">Pilih Tukang</label>
                        <select name="id_tukang" id="select2bs41" class="select2bs41">
                            <?php foreach ($tukangs as $tukang) : ?>
                                <?php
                                $id_tukang = $tukang['id_tukang'];
                                $getVoucher = $this->db->get_where('tb_voucher_tukang', ['id_tukang' => $id_tukang, 'type_voucher' => 'digi_voucher'])->row_array();
                                ?>
                                <option value="<?= $tukang['id_tukang'] ?>"><?= $getVoucher == null ? '[Belum Sebar] ' : '[Sdh Sebar] ' ?><?= $tukang['nama'] . " - " . $tukang['nomorhp'] ?></option>
                            <?php endforeach; ?>
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