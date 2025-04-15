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
                            <div class="row">
                                <div class="col-10">

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table-print" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tukang</th>
                                        <th>Is Demo?</th>
                                        <th>Ditambah Oleh</th>
                                        <th>Skill</th>
                                        <th>Nomor HP</th>
                                        <th>Alamat</th>
                                        <th>Kota</th>
                                        <th>Tgl Lahir</th>
                                        <th>Tgl Masuk</th>
                                        <th>Validasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($tukangs as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td title="<?= $data['nama'] ?>"><?= $data['nama'] ?></td>
                                            <td><?= $data['is_demo'] == 1 ? 'Yes' : 'No' ?></td>
                                            <td>
                                                <?php
                                                if ($data['is_self'] == 1) {
                                                    echo "Mandiri";
                                                } else {
                                                    if ($data['id_user_post'] != 0) {
                                                        $id_user = $data['id_user_post'];
                                                        $user = $this->db->get_where('tb_user', ['id_user' => $id_user])->row_array();
                                                        echo '[' . $user['level_user'] . '] ' . $user['full_name'];
                                                    } else {
                                                        if ($data['id_contact_post'] != 0) {
                                                            $id_contact = $data['id_contact_post'];
                                                            $contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();
                                                            echo '[Toko] ' . $contact['nama'];
                                                        } else {
                                                            echo "Not Set";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?= $data['nama_skill'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td title="<?= $data['address'] ?>"><?= substr($data['address'], 0, 20) . '...' ?></td>
                                            <td><?= $data['nama_city'] ?></td>
                                            <td><?= $data['tgl_lahir'] == '0000-00-00' ? "" : date("d F Y", strtotime($data['tgl_lahir'])) ?></td>
                                            <td><?= $data['created_at'] == '0000-00-00' ? "" : date("d F Y", strtotime($data['created_at'])) ?></td>
                                            <td>
                                                <a href="#" class="btn btn-primary m-1" data-toggle="modal" data-target="#modal-validate<?= $data['id_tukang'] ?>">Demo&nbsp;<i class="fas fa-recycle"></i></a>
                                                <a href="<?= base_url('akunseller/deletetukang/') . $data['id_tukang'] ?>" class="btn btn-danger m-1"><i class="fas fa-trash"></i></a>
                                            </td>
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
                                                        <form action="<?= base_url('tukang/demo/' . $data['id_tukang']) ?>" method="POST">
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