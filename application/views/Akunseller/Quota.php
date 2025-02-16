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
                    <?php
                    $id_contact = $contact['id_contact'];
                    $getCountVoucher = $this->db->get_where('tb_voucher_tukang', ['id_contact' => $id_contact, 'is_claimed' => 1])->num_rows();
                    ?>
                    <h1 class="m-0"><?= $title ?> - <?= $contact['nama'] ?> [QUOTA: <?= $contact['quota_priority'] - $getCountVoucher ?>]</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('prioritystore') ?>">Toko Seller</a></li>
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
                            <a href="#" class="btn btn-danger float-right mx-1" data-toggle="modal" data-target="#modal-quota-min">Kurangi Quota Toko</a>
                            <a href="#" class="btn btn-primary float-right mx-1" data-toggle="modal" data-target="#modal-quota">Tambah Quota Toko</a>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Quota</th>
                                        <th>Tgl</th>
                                        <th>User / Author</th>
                                        <!-- <th>Aksi</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($quotas as $data) : ?>
                                        <?php
                                        $id_user = $data['id_user'];

                                        $user = $this->db->get_where('tb_user', ['id_user' => $id_user])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['val_quota_toko'] ?></td>
                                            <td><?= date("d F Y - H:i", strtotime($data['created_at'])) ?></td>
                                            <td><?= $user['full_name'] ?></td>
                                            <!-- <td>
                                                <a href="<?= base_url('assets/img/qr/') .  $data['qr_toko'] ?>" target="__blank">
                                                    <img src="<?= base_url('assets/img/qr/') .  $data['qr_toko'] ?>" alt="" class="img-fluid" width="100">
                                                </a>
                                            </td> -->
                                            <!-- <td>
                                                <a href="<?= base_url('akunseller/penukaran/store/') . $data['id_contact'] ?>" class="btn btn-success m-1"><i class="fas fa-ticket-alt"></i>&nbsp;&nbsp;List Penukaran</a>
                                                <a href="<?= base_url('akunseller/quota/') . $data['id_contact'] ?>" class="btn btn-primary m-1"><i class="fas fa-clock"></i>&nbsp;&nbsp;History Quota</a>
                                            </td> -->
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
                    <input type="text" value="<?= $contact['id_contact'] ?>" name="id_contact" hidden>
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

<div class="modal fade" id="modal-quota-min">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kurangi Quota Toko</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('akunseller/minquota') ?>" method="POST">
                    <input type="text" value="<?= $contact['quota_priority'] ?>" name="quota_priority" hidden>
                    <input type="text" value="<?= $contact['id_contact'] ?>" name="id_contact" hidden>
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