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
                        <li class="breadcrumb-item"><a href="#">AI Integration</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
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
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="font-weight-bold"><?= $contact['nama'] ?></h5>
                            <span><?= $contact['nomorhp'] ?> | <?= $contact['nama_city'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="font-weight-bold"><?= $user ? $user['full_name'] : '-' ?></h5>
                            <hr>
                            Laporan:
                            <p class="bg-light p-2 rounded-sm"><?= $visit ? $visit['laporan_visit'] : '-' ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="font-weight-bold">Analisa AI <span class="right badge bg-purple"><?= $aiVisitReport['model_ai'] ?></span></h5>
                            <hr>
                            <b>Analisis:</b>
                            <p class="bg-light p-2 rounded-sm"><?= $aiVisitReport['analisis_spv'] ?></p>
                            <hr>
                            <b>Saran Strategi:</b>
                            <p class="bg-light p-2 rounded-sm"><?= $aiVisitReport['saran_strategi'] ?></p>
                            <hr>
                            <b>Rekomendasi WA:</b>
                            <p class="bg-light p-2 rounded-sm"><?= $aiVisitReport['rekomendasi_wa'] ?></p>
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
                <h4 class="modal-title">Tambah Data Kota</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-city') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Nama Kota</label>
                        <input type="text" name="nama_city" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Kode Kota</label>
                        <input type="text" name="kode_city" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Gudang</label>
                        <select name="id_gudang_stok" id="" class="select2bs4">
                            <option value="0">=== Pilih Gudang ===</option>
                            <?php foreach ($gudangs as $gudang): ?>
                                <option value="<?= $gudang['id_gudang_stok'] ?>"><?= $gudang['name_gudang_stok'] ?></option>
                            <?php endforeach; ?>
                        </select>
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