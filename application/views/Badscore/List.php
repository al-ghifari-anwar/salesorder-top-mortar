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
                        <!-- <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Toko</li> -->
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
                        </div>
                        <div class="card-body">
                            <table id="table-print" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Toko</th>
                                        <th>Pemilik</th>
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Reputation</th>
                                        <th>Score Pembayaran</th>
                                        <th>Score Freq</th>
                                        <th>Score Jml Order</th>
                                        <th>Total Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($contacts as $data) : ?>
                                        <?php
                                        $id_contact = $data['id_contact'];
                                        $id_promo = $data['id_promo'];
                                        $getPromo = $this->db->get('tb_promo', ['id_promo' => $id_promo])->row_array();

                                        // Get Score
                                        $scoreRes = $this->arrayCombineScoring($id_contact);
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['store_owner'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td><?= $scoreRes['payment'] ?></td>
                                            <td><?= $scoreRes['frequency'] ?></td>
                                            <td><?= $scoreRes['order'] ?></td>
                                            <td><?= number_format($scoreRes['total'], 2, '.', ',') ?></td>
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