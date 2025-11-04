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
                        <li class="breadcrumb-item"><a href="#">Invoice</a></li>
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
                                    <form action="<?= base_url('cusvisit/print/') . $id_city ?>" method="GET" target="_blank">
                                        <div class="row">
                                            <label>Date range:</label>
                                            <div class="form-group ml-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control float-right" id="reservation" name="date_range" value="<?= date("m/01/Y") . " - " .  date("m/d/Y") ?>">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                            <!-- <label for="">Toko:</label>
                                            <div class="form-group">
                                                <select name="id_contact" id="select2bs41" class="form-control select2bs41">
                                                    <option value="0">Semua</option>
                                                    <?php foreach ($toko as $toko) : ?>
                                                        <option value="<?= $toko['id_contact'] ?>"><?= $toko['nama'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <label for="">Kota:</label>
                                            <div class="form-group">
                                                <select name="id_city" id="select2bs4" class="form-control select2bs4">
                                                    <?php if ($this->session->userdata('level_user') != 'admin_c') : ?>
                                                        <option value="0">Semua</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($city as $city) : ?>
                                                        <option value="<?= $city['id_city'] ?>"><?= $city['nama_city'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div> -->
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- <div class="col-5">
                                    <a href="<?= base_url('report') ?>" class="btn btn-primary float-right">Semua</a>
                                </div> -->
                            </div>
                        </div>
                        <div class="card-body">

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