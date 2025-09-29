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
                    <h1 class="m-0">List Toko</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Toko</li>
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
                            <form action="" method="POST">
                                <div class="row">
                                    <label for="">Kota</label>
                                    <div class="form-group ml-3">
                                        <select name="id_city" id="select2bs4" class="form-control select2bs4">
                                            <?php if ($this->session->userdata('level_user') != 'admin_c') : ?>
                                                <option value="0">Semua</option>
                                            <?php endif; ?>
                                            <?php foreach ($city as $city) : ?>
                                                <option value="<?= $city['id_city'] ?>"><?= $city['nama_city'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <label for="">Status</label>
                                    <div class="form-group ml-3">
                                        <select name="status" id="select2bs4" class="form-control select2bs4">
                                            <option value="data">Data (Kuning)</option>
                                            <option value="active">Aktif (Hijau)</option>
                                            <option value="passive">Pasif (Abu-abu)</option>
                                        </select>
                                    </div>
                                    <div class="form-group ml-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                            <?php if ($this->session->userdata('id_distributor') == 4): ?>
                                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                    Tambah Data
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <table id="table-print" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Toko</th>
                                        <th>Pemilik</th>
                                        <th>Nomor HP</th>
                                        <th>Nomor HP 2</th>
                                        <th>Tgl Lahir</th>
                                        <th>Kota</th>
                                        <th>Maps</th>
                                        <th>Alamat</th>
                                        <th>Status</th>
                                        <th>Promo</th>
                                        <th>Termin</th>
                                        <th>Reputation</th>
                                        <th>Payment Method</th>
                                        <th>Mingguan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($toko as $data) : ?>
                                        <?php
                                        $id_promo = $data['id_promo'];
                                        $getPromo = $this->db->get_where('tb_promo', ['id_promo' => $id_promo])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['store_owner'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['nomorhp_2'] ?></td>
                                            <td><?= $data['tgl_lahir'] ?></td>
                                            <td><?= $data['nama_city'] ?></td>
                                            <td><?= $data['maps_url'] ?></td>
                                            <td><?= $data['address'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $getPromo['nama_promo'] ?></td>
                                            <td><?= $data['termin_payment'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td><?= $data['payment_method'] ?></td>
                                            <td><?= $data['tagih_mingguan'] == 1 ? 'Yes' : 'No' ?></td>
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
                <h4 class="modal-title">Tambah Toko</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-toko') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" name="nama" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Nomor HP</label>
                        <input type="text" name="nomorhp" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Kota</label>
                        <select name="id_city" id="" class="form-control select2bs4">
                            <?php foreach ($cities as $cityToko): ?>
                                <option value="<?= $cityToko['id_city'] ?>"><?= $cityToko['nama_city'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="text" value="30" name="termin_payment" hidden>
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>