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
                        <li class="breadcrumb-item"><a href="#"><?= $menuGroup ?></a></li>
                        <li class="breadcrumb-item active"><?= $menu ?></li>
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
                <div class="col-8">
                    <div class="card">
                        <div class="card-header">

                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('scanqrsak/scan') ?>" method="post">
                                <!-- <div class="row">
                                    <div class="col">
                                        <label for="">Jumlah Sak dalam 1 batch</label>
                                        <input type="number" name="jmlSak" class="form-control" value="25">
                                    </div>
                                    <div class="col">
                                        <label for="">No batch awal</label>
                                        <input type="text" name="batch_qrsak_detail" class="form-control">
                                    </div>
                                </div> -->
                                <!-- <br> -->
                                <div class="form-group">
                                    <label for="">No Batch</label>
                                    <input type="text" name="batch_qrsak_detail" id="" class="form-control" value="<?= $lastBatch ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">QR</label>
                                    <input type="text" name="code_qrsak_detail" id="" class="form-control" autofocus>
                                </div>
                                <div class="form-group">
                                    <button type="submit"></button>
                                </div>
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

<div class="modal fade" id="modal-insert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data <?= $title ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('qrsak/create') ?>" method="POST">
                    <div class="form-group">
                        <label for="">Jumlah Halaman (1 Halaman berisi 48 QR)</label>
                        <input type="number" name="jml_page" id="" class="form-control">
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