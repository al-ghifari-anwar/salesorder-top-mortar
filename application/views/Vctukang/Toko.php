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
                    <h1 class="m-0">Voucher Tukang - <?= $contact['nama'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Voucher</li>
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
                <div class="col-7">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-center">Claim Voucher Tukang</h5>
                            <form action="<?= base_url('vctukang/claim') ?>" method="POST">
                                <div class="form-group">
                                    <label for="">Nomor Seri</label>
                                    <input type="text" name="no_seri" id="" class="form-control" value="<?= $tukang['nomorhp'] ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Tukang</label>
                                    <input type="text" name="nama_tukang" id="" class="form-control" value="<?= $tukang['nama'] ?>" readonly>
                                </div>
                                <hr>
                                <h6 class="text-center">Informasi Rekening Toko</h6>
                                <div class="form-group">
                                    <label for="">Nama Pemilik Rekening</label>
                                    <input type="text" name="to_name" id="" class="form-control" value="">
                                </div>
                                <div class="form-group">
                                    <label for="">Bank</label>
                                    <select name="id_bank" id="" class="form-control">
                                        <?php foreach ($banks as $bank) : ?>
                                            <option value="<?= $bank['id_bank'] ?>"><?= $bank['nama_bank'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Nomor Rekening</label>
                                    <input type="number" name="to_account" id="" class="form-control" value="">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Claim</button>
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