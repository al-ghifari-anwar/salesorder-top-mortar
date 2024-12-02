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
                    <h1 class="m-0">List Kota</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Kota</li>
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
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('company/update/') . $this->session->userdata('id_distributor') ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="">Nama Perusahaan</label>
                                    <input type="text" class="form-control" name="name_company" value="<?= $company['name_company'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat</label>
                                    <textarea class="form-control" name="address_company"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Nomor HP</label>
                                    <input type="text" class="form-control" name="phone_company" value="<?= $company['phone_company'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Norek</label>
                                    <input type="text" class="form-control" name="norek_company" value="<?= $company['norek_company'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Logo</label>
                                    <input type="file" class="form-control" name="img_company">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Save</button>
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