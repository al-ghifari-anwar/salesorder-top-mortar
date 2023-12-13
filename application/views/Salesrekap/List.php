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
                    <h1 class="m-0">Rekap Sales <?= $city['nama_city'] ?> Hari Ini</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Visit</a></li>
                        <li class="breadcrumb-item active"><?= $city['nama_city'] ?></li>
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
                                <div class="col-12">
                                    <form action="<?= base_url('print-rekap-sales') ?>" method="POST" target="__blank">
                                        <div class="row">
                                            <!-- <label>Date range:</label>
                                            <div class="form-group ml-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control float-right" id="reservation" name="date_range">
                                                </div>
                                            </div> -->
                                            <?php
                                            $user = $this->db->get_where('tb_user', ['id_city' => $id_city, 'level_user' => 'sales'])->result_array();
                                            $month = date("m");
                                            ?>
                                            <label for="">Bulan:</label>
                                            <div class="form-group">
                                                <select name="bulan" id="select2bs4" class="form-control select2bs4">
                                                    <option value="1" <?= $month == 1 ? 'selected' : '' ?>>Januari</option>
                                                    <option value="2" <?= $month == 2 ? 'selected' : '' ?>>Februari</option>
                                                    <option value="3" <?= $month == 3 ? 'selected' : '' ?>>Maret</option>
                                                    <option value="4" <?= $month == 4 ? 'selected' : '' ?>>April</option>
                                                    <option value="5" <?= $month == 5 ? 'selected' : '' ?>>Mei</option>
                                                    <option value="6" <?= $month == 6 ? 'selected' : '' ?>>Juni</option>
                                                    <option value="7" <?= $month == 7 ? 'selected' : '' ?>>Juli</option>
                                                    <option value="8" <?= $month == 8 ? 'selected' : '' ?>>Agustus</option>
                                                    <option value="9" <?= $month == 9 ? 'selected' : '' ?>>September</option>
                                                    <option value="10" <?= $month == 10 ? 'selected' : '' ?>>Oktober</option>
                                                    <option value="11" <?= $month == 11 ? 'selected' : '' ?>>November</option>
                                                    <option value="12" <?= $month == 12 ? 'selected' : '' ?>>Desember</option>
                                                </select>
                                            </div>
                                            <label for="">Sales:</label>
                                            <!-- <input type="text" value="0" name="id_user" hidden> -->
                                            <div class="form-group">
                                                <select name="id_user" id="select2bs4" class="form-control select2bs4">
                                                    <!-- <option value="0">Semua</option> -->
                                                    <?php foreach ($user as $user) : ?>
                                                        <option value="<?= $user['id_user'] ?>"><?= $user['full_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <input type="text" value="<?= $id_city ?>" name="id_city" hidden>
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