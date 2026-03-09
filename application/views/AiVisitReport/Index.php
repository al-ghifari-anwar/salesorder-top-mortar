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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <form action="<?= base_url('ai-visitreport') ?>" method="GET">
                                        <div class="row">
                                            <label>Date range:</label>
                                            <div class="form-group ml-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control float-right" id="reservation" name="daterange" value="<?= date("m/d/Y", strtotime($dateFrom)) . " - " .  date("m/d/Y", strtotime($dateTo)) ?>">
                                                </div>
                                            </div>
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Toko</th>
                                        <th>Sales</th>
                                        <th>Laporan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($aiVisitReports as $aiVisitReport) : ?>
                                        <?php
                                        $id_visit = $aiVisitReport['id_visit'];
                                        $contact = $this->MContact->getById($aiVisitReport['id_contact']);
                                        $user = $this->MUser->getById($aiVisitReport['id_user']);
                                        $visit = $this->db->get_where('tb_visit', ['id_visit' => $id_visit])->row_array();
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td>
                                                <h5 class="font-weight-bold"><?= $contact['nama'] ?></h5>

                                                <span><?= $contact['nomorhp'] ?> | <?= $contact['nama_city'] ?></span>
                                            </td>
                                            <td>
                                                <h6 class="font-weight-bold"><?= $user ? $user['full_name'] : '-' ?></h6>
                                            </td>
                                            <td><?= $visit ? $visit['laporan_visit'] : '-' ?></td>
                                            <td>
                                                <a href="<?= base_url('ai-visitreport/detail/') . $aiVisitReport['id_ai_reportvisit'] ?>" class="btn btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
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