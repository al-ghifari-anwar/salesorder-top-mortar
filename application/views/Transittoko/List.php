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
                                        <th>Toko</th>
                                        <th>Nomor HP</th>
                                        <th>Alamat</th>
                                        <th>Sales</th>
                                        <th>Cutoff<br>Terakhir</th>
                                        <th>Jml Visit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($transits as $data) : ?>
                                        <?php
                                        $contact = $this->MContact->getById($data['id_contact']);

                                        $lastVisit = $this->db->where('id_contact', $data['id_contact'])->order_by('date_visit', 'DESC')->get('tb_visit')->row_array();

                                        $user = $this->db->where('id_user', $lastVisit['id_user'])->get('tb_user')->row_array();

                                        $cutoffVisit = $this->db->where('id_contact', $data['id_contact'])->order_by('created_at', 'DESC')->get('tb_cutoff_visit')->row_array();


                                        $visits = $this->db->select('COUNT(*) AS jml_visit')->where('id_contact', $data['id_contact'])->where('id_user', $user['id_user'])->get('tb_visit')->row_array();

                                        if ($cutoffVisit) {
                                            $visits = $this->db->select('COUNT(*) AS jml_visit')->where('id_contact', $data['id_contact'])->where('id_user', $user['id_user'])->where('DATE(date_visit) >', $cutoffVisit['date_cutoff_visit'])->get('tb_visit')->row_array();
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $contact['nama'] ?></td>
                                            <td><?= $contact['nomorhp'] ?></td>
                                            <td><?= $contact['address'] ?></td>
                                            <td><?= $user['full_name'] ?></td>
                                            <td><?= $cutoffVisit ? date('d F Y', strtotime($cutoffVisit['date_cutoff_visit'])) : "Blm ada cutoff" ?></td>
                                            <td><?= $visits['jml_visit'] ?></td>
                                            <td>
                                                <a href="<?= base_url('transittoko/approve/' . $data['id_transit_toko']) ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i>&nbsp; Masukkan Kota X</a>
                                                <a href="<?= base_url('transittoko/kembalikan/' . $data['id_transit_toko']) ?>" class="btn btn-primary" title="Ubah"><i class="fas fa-recycle"></i>&nbsp; Kembalikan Ke Asal</a>
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