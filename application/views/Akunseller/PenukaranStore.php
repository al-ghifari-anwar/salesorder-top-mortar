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
                    <h1 class="m-0"><?= $title ?> - <?= $contact['nama'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('prioritystore') ?>">Toko Prioritas</a></li>
                        <li class="breadcrumb-item active"><?= $contact['nama'] ?></li>
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
                            <!-- <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-add">Tambah Toko Prioritas</a> -->
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tukang</th>
                                        <th>Tanggal Claim</th>
                                        <th>Nominal</th>
                                        <th>Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($vouchers as $data) : ?>
                                        <?php
                                        $id_tukang = $data['id_tukang'];
                                        $tukang = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang]);
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= ($tukang) ? $tukang['nama'] : '-' ?></td>
                                            <td><?= $data['is_claimed'] == 0 ? 'GAGAL' : date("d F Y", strtotime($data['claim_date'])) ?></td>
                                            <td><?= $data['nominal_pembelian'] ?></td>
                                            <td>
                                                <a href="<?= base_url('assets/img/img_nota/') .  $data['nota_pembelian'] ?>" target="__blank">
                                                    <img src="<?= base_url('assets/img/img_nota/') .  $data['nota_pembelian'] ?>" alt="" class="img-fluid" width="100">
                                                </a>
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