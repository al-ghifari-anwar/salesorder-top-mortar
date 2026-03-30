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
                    <h1 class="m-0"><?= $title . " " . $city['nama_city'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"></a></li>
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
                                <div class="col-7">

                                </div>
                                <div class="col-5">
                                    <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#add-modal">Tambah Renvi</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Filter</th>
                                        <th>Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($jadwalVisitTambahans as $jadwalVisitTambahan) : ?>
                                        <?php
                                        $contact = $this->MContact->getById($jadwalVisitTambahan['id_contact']);
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $contact['nama'] ?></td>
                                            <td><?= $jadwalVisitTambahan['filter_jadwal_visit'] ?></td>
                                            <td><?= $jadwalVisitTambahan['kategori_jadwal_visit'] ?></td>
                                            <td>
                                                <a href="<?= base_url('add-jadwalvisit/delete/') . $jadwalVisitTambahan['id_jadwal_visit'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
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

<div class="modal fade" id="add-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Renvi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('add-jadwalvisit/create') ?>" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="id_city" value="<?= $city['id_city'] ?>">
                        <label for="">Renvi</label>
                        <select name="jadwalVisit" id="" class="select2bs4">
                            <option value="0">=== Pilih Renvi ===</option>
                            <?php foreach ($jadwalVisits as $jadwalVisit): ?>
                                <?php
                                $jadwalVisitData = [
                                    'id_city' => $city['id_city'],
                                    'id_contact' => $jadwalVisit['id_contact'],
                                    'cluster_jadwal_visit' => $cluster,
                                    'date_jadwal_visit' => date('Y-m-d'),
                                    'filter_jadwal_visit' => $jadwalVisit['filter'],
                                    'kategori_jadwal_visit' => $jadwalVisit['type_renvis'],
                                    'is_new' => $jadwalVisit['is_new'],
                                    'last_visit' => $jadwalVisit['last_visit'],
                                    'days_jadwal_visit' => $jadwalVisit['days'],
                                    'total_invoice' => $jadwalVisit['total_invoice'],
                                    'is_tambahan' => 1,
                                ];

                                $contact = $this->MContact->getById($jadwalVisit['id_contact']);
                                ?>
                                <option value="<?= htmlspecialchars(json_encode($jadwalVisitData)) ?>"><?= $contact['nama'] ?> - <?= $jadwalVisit['type_renvis'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
    </div>
</aside>
<!-- /.control-sidebar -->