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
                        <li class="breadcrumb-item"><a href="#"><?= $title ?></a></li>
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
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Order</th>
                                        <th>Toko</th>
                                        <th>Nomor HP</th>
                                        <th>Kota</th>
                                        <th>Tgl</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($apporders as $apporder): ?>
                                        <?php
                                        $getApporder = $this->MApporder->getById($apporder['id_apporder']);
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $apporder['id_apporder'] ?></td>
                                            <td><?= $apporder['nama'] ?></td>
                                            <td><?= $apporder['nomorhp'] ?></td>
                                            <td><?= $apporder['nama_city'] ?></td>
                                            <td><?= date('d M Y - H:i', strtotime($getApporder['created_at'])) ?></td>
                                            <td>
                                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-detail<?= $apporder['id_apporder'] ?>"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php foreach ($apporders as $apporder): ?>
                                <?php
                                // Get Score from API
                                // Get Score
                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => 'https://order.topmortarindonesia.com/scoring/combine/' . $apporder['id_contact'],
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_HTTPHEADER => array(
                                        'Cookie: ci_session=2scmao9aquusdrn7rm2i7vkrifkamkld'
                                    ),
                                ));

                                $response = curl_exec($curl);

                                curl_close($curl);

                                $resScore = json_decode($response, true);
                                ?>
                                <!-- Modal detail -->
                                <div class="modal fade" id="modal-detail<?= $apporder['id_apporder'] ?>">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Detail Pesanan dan Toko</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <b>Detail Toko</b>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table">
                                                                    <tr>
                                                                        <th>Nama Toko</th>
                                                                        <td>:</td>
                                                                        <td><?= $apporder['nama'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Nomor Hp</th>
                                                                        <td>:</td>
                                                                        <td><?= $apporder['nomorhp'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Owner</th>
                                                                        <td>:</td>
                                                                        <td><?= $apporder['store_owner'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Status</th>
                                                                        <td>:</td>
                                                                        <td><?= $apporder['store_status'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Reputasi</th>
                                                                        <td>:</td>
                                                                        <td><?= $apporder['reputation'] ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Skor</th>
                                                                        <td>:</td>
                                                                        <td><?= $resScore['total'] ?>%</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <?php
                                                        $apporderDetails = $this->MApporderDetail->getByIdApporder($apporder['id_apporder']);

                                                        ?>
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <b>Detail Pesanan</b>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table">
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Qty</th>
                                                                    </tr>
                                                                    <?php foreach ($apporderDetails as $apporderDetail): ?>
                                                                        <tr>
                                                                            <td><?= $apporderDetail['nama_produk'] ?></td>
                                                                            <td><?= $apporderDetail['qty_apporder_detail'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <a href="<?= base_url('apporder/approve/') . $apporder['id_apporder'] ?>" class="btn btn-success float-right ml-3"><i class="fas fa-check"></i>&nbsp; Terima Pesanan</a>
                                                <a href="#" class="btn btn-danger float-right ml-3"><i class="fas fa-times"></i>&nbsp; Tolak Pesanan</a>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
                            <?php endforeach; ?>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-suratjalan') ?>" method="POST">


                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
</script>