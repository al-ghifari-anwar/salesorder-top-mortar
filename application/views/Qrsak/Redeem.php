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
                        <li class="breadcrumb-item"><a href="#"><?= $menuGroup ?></a></li>
                        <li class="breadcrumb-item active"><?= $menu ?></li>
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
                            <!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                Set Value berdasarkan No Batch
                            </button> -->
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>No Batch</th>
                                        <th>Value</th>
                                        <th>Aktif</th>
                                        <th>Confirm</th>
                                        <th>Redeem</th>
                                        <th>Tgl Redeem</th>
                                        <th>Nama</th>
                                        <th>Nomor HP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($qrsak_details as $qrsak_detail) : ?>
                                        <?php
                                        $id_qrsak_detail = $qrsak_detail['id_qrsak_detail'];
                                        // $user = $this->MUser->getById($qrsak_detail['created_user']);
                                        $qrsakRedeem = $this->db->get_where('tb_qrsak_redeem', ['id_qrsak_detail' => $id_qrsak_detail])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= str_replace('https://qrpromo.topmortarindonesia.com/redeem/', '', $qrsak_detail['code_qrsak_detail']) ?></td>
                                            <td><?= $qrsak_detail['batch_qrsak_detail'] ?></td>
                                            <td><?= $qrsak_detail['value_qrsak_detail'] ?></td>
                                            <td><?= $qrsak_detail['is_active'] == 1 ? 'YES' : 'NO' ?></td>
                                            <td><?= $qrsak_detail['is_confirm'] == 1 ? 'YES' : 'NO' ?></td>
                                            <td><?= $qrsak_detail['is_redeemed'] == 1 ? 'YES' : 'NO' ?></td>
                                            <td><?= $qrsak_detail['redeemed_date'] == null ? '-' : date('d M Y', strtotime($qrsak_detail['redeemed_date'])) ?></td>
                                            <td><?= $qrsakRedeem ? $qrsakRedeem['name_qrsak_redeem'] : '' ?></td>
                                            <td><?= $qrsakRedeem ? $qrsakRedeem['phone_qrsak_redeem'] : '' ?></td>
                                        </tr>
                                        <div class="modal fade" id="value-modal<?= $qrsak_detail['id_qrsak_detail'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Value <?= str_replace('https://qrpromo.topmortarindonesia.com/redeem/', '', $qrsak_detail['code_qrsak_detail']) ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('qrsak/insert-value') ?>" method="POST">
                                                            <input type="hidden" value="<?= $qrsak['id_qrsak'] ?>" name="id_qrsak">
                                                            <input type="hidden" value="<?= $qrsak_detail['id_qrsak_detail'] ?>" name="id_qrsak_detail">
                                                            <div class="form-group">
                                                                <label for="">Value (Rupiah)</label>
                                                                <input type="number" name="value_qrsak_detail" id="" class="form-control">
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
                <h4 class="modal-title">Tambah Value <?= $title ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('qrsak/insert-value-batch') ?>" method="POST">
                    <input type="hidden" value="<?= $qrsak['id_qrsak'] ?>" name="id_qrsak">
                    <div class="form-group">
                        <label for="">No Batch</label>
                        <select name="batch_qrsak_detail" id="" class="form-control select2bs4">
                            <option value="">--- Pilih No Batch ---</option>
                            <?php foreach ($qrsak_batchs as $qrsak_batch): ?>
                                <option value="<?= $qrsak_batch['batch_qrsak_detail'] ?>"><?= $qrsak_batch['batch_qrsak_detail'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Value (Rupiah)</label>
                        <input type="number" name="value_qrsak_detail" id="" class="form-control">
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