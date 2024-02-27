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
                    <h1 class="m-0">Laporan Kurir <?= $city['nama_city'] ?> Hari Ini</h1>
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
                                <form action="<?= base_url('lap-absen/' . $id_city . "/" . 'courier') ?>" method="POST" target="__blank">
                                    <div class="row">
                                        <?php
                                        $month = date("m")
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
                                        <div class="form-group ml-3">
                                            <button type="submit" class="btn btn-primary">Cetak Absen</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kurir</th>
                                        <th>Gudang</th>
                                        <th>Jarak</th>
                                        <th>Jam - Tanggal</th>
                                        <th>Laporan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($visit as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['full_name'] ?></td>
                                            <td><?= $data['nama_gudang'] ?></td>
                                            <td><?= $data['distance_visit'] ?></td>
                                            <td><?= date("H:i - d M Y", strtotime($data['date_visit'])) ?></td>
                                            <td><?= $data['laporan_visit'] ?></td>
                                            <td>
                                                <a href="<?= base_url('approve-visit/' . $data['id_visit'] . '/' . $id_city) ?>" class="btn btn-success" title="Approve"><i class="fas fa-check-circle"></i></a>
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