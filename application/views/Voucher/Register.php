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
                    <h1 class="m-0">Tambah Voucher</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Tambah Voucher</a></li>
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
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                Lihat Semua Voucher
                            </button> -->
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('reg-voucher/' . $id_city) ?>" method="POST">
                                <input type="text" name="no_surat_jalan" class="form-control" value="<?= "DO-" . rand(10000000, 99999999) ?>" hidden>
                                <div class="form-group">
                                    <label for="">Toko</label>
                                    <select class="form-control select2bs4" name="id_contact" style="width: 100%;" id="select2bs4">
                                        <option value="0">--- PLEASE SELECT STORE ---</option>
                                        <?php foreach ($store as $data) : ?>
                                            <option value="<?= $data['id_contact'] ?>" shiptoname="<?= $data['nama'] ?>" shipaddress="<?= $data['address'] ?>" shipphone="<?= $data['nomorhp'] ?>"><?= $data['nama'] . " - " . $data['nomorhp'] . " - " . $data['store_owner'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Nomor Voucher</label>
                                    <input type="text" name="no_voucher" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Point Voucher</label>
                                    <input type="number" name="point_voucher" id="" class="form-control" value="1">
                                </div>
                                <button class="btn btn-primary float-right">Simpan</button>
                            </form>
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