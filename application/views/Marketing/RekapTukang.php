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
                        <li class="breadcrumb-item active">Marketing</li>
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
                            <form action="<?= base_url('marketing/rekap/tukang') ?>" method="POST">
                                <div class="row">
                                    <label>Date range:</label>
                                    <div class="form-group ml-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right" id="reservation" name="daterange">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <div class="form-group ml-3">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Terkirim Pada</th>
                                        <th>Konten</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($watzap_tukangs as $watzap_tukang): ?>
                                        <?php
                                        $id_tukang = $watzap_tukang['id_tukang'];
                                        $id_marketing_message = $watzap_tukang['id_marketing_message'];

                                        $tukang = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();

                                        $marketing_message = $this->db->get_where('tb_marketing_message', ['id_marketing_message' => $id_marketing_message])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $tukang['nama'] ?></td>
                                            <td><?= $tukang['nomorhp'] ?></td>
                                            <td><?= $watzap_tukang['status_watzap_tukang'] ?></td>
                                            <td><?= $watzap_tukang['send_at'] != null ? date("d F Y", strtotime($watzap_tukang['send_at'])) : "-" ?></td>
                                            <td>
                                                <img src="<?= base_url('assets/img/content_img/' . $marketing_message['image_marketing_message']) ?>" class="img-thumbnail" width="200">
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

<div class="modal fade" id="modal-insert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-marketing') ?>" method="POST" enctype="multipart/form-data">
                    <input type="text" name="target_marketing_message" value="tukang" hidden>
                    <div class="form-group">
                        <label for="">Nama Konten</label>
                        <input type="text" name="nama_marketing_message" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Image</label>
                        <input type="file" name="image_marketing_message" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label for="">Body</label>
                        <textarea name="body_marketing_message" id="" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                    <input type="text" value="data" name="target_status" hidden>
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->