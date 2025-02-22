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
                    <h1 class="m-0"><?= $title ?> </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('akunseller') ?>">Top Seller</a></li>
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
                            <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-quota">Tambah Quota Toko</a>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Toko</th>
                                        <th>Status</th>
                                        <th>Reputation</th>
                                        <th>Tgl Join</th>
                                        <th>Program</th>
                                        <th>Kuota</th>
                                        <th>Kota</th>
                                        <th>Status Top Seller</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($contactPriors as $data) : ?>
                                        <?php
                                        $id_contact = $data['id_contact'];
                                        $getCountVoucher = $this->db->get_where('tb_voucher_tukang', ['id_contact' => $id_contact, 'is_claimed' => 1])->num_rows();

                                        $this->db->order_by('created_at', 'DESC');
                                        $getTglJoin = $this->db->get_where('tb_otp_toko', ['id_contact' => $id_contact], 1)->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td><?= ($getTglJoin) ? date('d F Y', strtotime($getTglJoin['created_at'])) : '-' ?></td>
                                            <td>
                                                <?php if ($data['is_priority'] == 1 && $data['is_tokopromo'] == 0) {
                                                    echo "Priority";
                                                } else if ($data['is_priority'] == 1 && $data['is_tokopromo'] == 1) {
                                                    echo "Toko Promo";
                                                } else if ($data['is_priority'] == 0 && $data['is_tokopromo'] == 0) {
                                                    if ($data['quota_priority'] == 0) {
                                                        echo "Default";
                                                    } else if ($data['quota_priority'] > 0) {
                                                        echo "Non Aktif";
                                                    }
                                                } ?>
                                            </td>
                                            <td><?= $data['quota_priority'] - $getCountVoucher ?></td>
                                            <td><?= $data['nama_city'] ?></td>
                                            <td>
                                                <?php if ($data['topseller_active'] == 1) {
                                                    echo "Aktif";
                                                } else if ($data['topseller_active'] == 0) {
                                                    echo "Non Aktif";
                                                }  ?>
                                            </td>
                                            <!-- <td>
                                                <a href="<?= base_url('assets/img/qr/') .  $data['qr_toko'] ?>" target="__blank">
                                                    <img src="<?= base_url('assets/img/qr/') .  $data['qr_toko'] ?>" alt="" class="img-fluid" width="100">
                                                </a>
                                            </td> -->
                                            <td>
                                                <a href="<?= base_url('akunseller/penukaran/store/') . $data['id_contact'] ?>" class="btn btn-success m-1"><i class="fas fa-ticket-alt"></i>&nbsp;&nbsp;List Penukaran</a>
                                                <a href="<?= base_url('akunseller/quota/') . $data['id_contact'] ?>" class="btn btn-primary m-1"><i class="fas fa-clock"></i>&nbsp;&nbsp;History Quota</a>
                                                <?php if ($data['topseller_active'] == 1): ?>
                                                    <a href="<?= base_url('akunseller/nonactive/') . $data['id_contact'] ?>" class="btn btn-danger m-1"><i class="fas fa-stop-circle"></i></a>
                                                <?php endif; ?>
                                                <?php if ($data['topseller_active'] == 0): ?>
                                                    <a href="<?= base_url('akunseller/active/') . $data['id_contact'] ?>" class="btn bg-teal m-1"><i class="fas fa-play-circle"></i></a>
                                                <?php endif; ?>
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

<div class="modal fade" id="modal-quota">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Quota Toko</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('akunseller/addquota') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Toko</label>
                        <select name="id_contact" id="" class="form-control select2bs4">
                            <option value="0">Pilih Toko</option>
                            <?php foreach ($contactPriors as $contactPrior): ?>
                                <option value="<?= $contactPrior['id_contact'] ?>"><?= $contactPrior['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Quota</label>
                        <input type="number" name="val_quota_toko" class="form-control" value="1">
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