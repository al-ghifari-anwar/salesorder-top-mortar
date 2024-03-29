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
                    <h1 class="m-0">Claim Voucher</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- <li class="breadcrumb-item"><a href="#">Tambah Voucher</a></li> -->
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
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-tutor">
                                Tutorial
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('claim') ?>" method="POST">
                                <div class="form-group">
                                    <label for="">Nomor Voucher </label>
                                    <input type="text" name="no_voucher1" id="" class="form-control mb-3" placeholder="Voucher 1" autocomplete="off" pattern="\d*" maxlength="5">
                                    <input type="text" name="no_voucher2" id="" class="form-control mb-3" placeholder="Voucher 2" autocomplete="off" pattern="\d*" maxlength="5">
                                    <input type="text" name="no_voucher3" id="" class="form-control mb-3" placeholder="Voucher 3" autocomplete="off" pattern="\d*" maxlength="5">
                                    <input type="text" name="no_voucher4" id="" class="form-control mb-3" placeholder="Voucher 4" autocomplete="off" pattern="\d*" maxlength="5">
                                    <input type="text" name="no_voucher5" id="" class="form-control mb-3" placeholder="Voucher 5" autocomplete="off" pattern="\d*" maxlength="5">
                                </div>
                                <button class="btn btn-primary float-right">Cek</button>
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