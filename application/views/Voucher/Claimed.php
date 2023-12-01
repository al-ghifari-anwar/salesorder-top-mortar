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
                            <h5>Selamat <?= $toko['nama'] ?>, anda mendapatkan <?= $claimed['actual_vouchers'] ?> point voucher yang dapat ditukarkan dengan <?= $claimed['actual_vouchers'] ?> Thinbed</h5>
                            <br>
                            <h5>Ketuk claim dibawah dan hadiah akan dikirim bersamaan dengan order berikutnya (Min 10 Sak)</h5>
                            <form action="<?= base_url('claimed') ?>" method="POST">
                                <div class="form-group">
                                    <input type="text" name="id_contact" id="" class="form-control" placeholder="Contoh: 123456, 789123" value="<?= $claimed['id_contact'] ?>" hidden>
                                    <input type="text" names="point_vouchers" value="<?= $claimed['actual_vouchers'] ?>" hidden>
                                    <input type="text" name="voucher_ori" value="<?= $claimed['voucher_ori'] ?>">
                                </div>
                                <button class="btn btn-primary float-right">Claim</button>
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