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
                                            <td><?= $jadwalVisitTambahan['filter_step_renvi'] ?></td>
                                            <td>
                                                <a href="<?= base_url('add-advancedvisit/delete/') . $jadwalVisitTambahan['id_step_renvi'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
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
                <h4 class="modal-title">Tambah Advanced Visit</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('add-advancedvisit/create') ?>" method="POST">
                    <input type="hidden" name="id_city" value="<?= $city['id_city'] ?>">
                    <div class="form-group">
                        <label for="">Toko</label>
                        <select name="id_contact" id="id_contact" class="select2bs4">
                            <option value="0">=== Pilih Toko ===</option>
                            <?php foreach ($contacts as $contact): ?>
                                <option value="<?= $contact['id_contact'] ?>" data-status="<?= $contact['store_status'] ?>"><?= $contact['nama'] . " | Cluster: " . $contact['cluster'] . " | Status: " . $contact['store_status'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Kategori Visit</label>
                        <select name="source_step_renvi" id="source_step_renvi" class="form-control">

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
<script>
    $(document).ready(function() {
        $('#id_contact').on('change', function() {
            let status = $(this).find(':selected').data('status');
            let kategori = $('#source_step_renvi');

            // Reset pilihan kategori
            kategori.empty();
            kategori.append('<option value="">=== Pilih Kategori Visit ===</option>');

            if (status === 'data') {
                kategori.append('<option value="data">Toko Baru</option>');
            } else if (status === 'active') {
                kategori.append('<option value="active">Toko Aktif</option>');
                kategori.append('<option value="jatem1">Tagihan Jatem</option>');
            } else if (status === 'passive') {
                kategori.append('<option value="passive">Toko Pasif</option>');
                kategori.append('<option value="jatem1">Tagihan Jatem</option>');
            }

            // Refresh Select2
            kategori.val('').trigger('change');
        });
    });
</script>