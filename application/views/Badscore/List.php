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
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Reputation</th>
                                        <th>Approved</th>
                                        <th>Status Approval</th>
                                        <th>Skor Payment</th>
                                        <th>Skor Terakhir</th>
                                        <th>Tgl</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($contacts as $data) : ?>
                                        <?php
                                        $id_contact = $data['id_contact'];
                                        $id_bad_score = $data['id_bad_score'];
                                        $badscore = $this->db->get_where('tb_bad_score', ['id_bad_score' => $id_bad_score])->row_array();

                                        // Get Score
                                        $curl = curl_init();

                                        curl_setopt_array($curl, array(
                                            CURLOPT_URL => 'https://order.topmortarindonesia.com/scoring/combine/' . $id_contact,
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

                                        $resultScore = json_decode($response, true);
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?> (<?= $data['id_contact'] ?>)</td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td><?= $badscore['is_approved'] == 1 ? 'Ya' : 'Tidak' ?></td>
                                            <td><?= $badscore['type_approval'] ?></td>
                                            <td><?= $resultScore['payment'] ?></td>
                                            <td><?= $badscore['last_score'] ?></td>
                                            <td><?= date("d F Y - H:i", strtotime($badscore['created_at'])) ?></td>
                                            <td>
                                                <?php if ($badscore['is_approved'] == 0): ?>
                                                    <a href="<?= base_url('badscore/approve/' . $data['id_contact']) . '/' . $id_city ?>" class="btn btn-danger mr-1"><i class="fas fa-store-slash"></i> Approve</a>
                                                <?php endif ?>
                                                <?php if ($badscore['is_approved'] == 1): ?>
                                                    <a href="<?= base_url('badscore/tampilkan/' . $data['id_contact']) . '/' . $id_city ?>" class="btn btn-primary mr-1"><i class="fas fa-eye"></i> Tampilkan Lagi</a>
                                                <?php endif ?>
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