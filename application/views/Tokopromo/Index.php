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
                    <h1 class="m-0"><?= $title ?> - <?= $contact['nama'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
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
                        <div class="card-body">
                            <form action="<?= base_url('tokopromo/verify/') . $contact['id_contact'] ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="">Nama Lengkap</label>
                                    <input type="text" name="nama" id="" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Nomor HP Aktif (WhatsApp)</label>
                                    <input type="text" name="nomorhp" id="" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Lahir (Opsional)</label>
                                    <input type="date" name="tgl_lahir" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat (Opsional)</label>
                                    <textarea name="address" class="form-control" id="" cols="30" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Jabatan</label>
                                    <select name="id_catcus" id="select2bs4" class="form-control select2bs4">
                                        <?php foreach ($catcus as $catcus) : ?>
                                            <option value="<?= $catcus['id_catcus'] ?>"><?= $catcus['name_catcus'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Nota</label>
                                    <input type="file" name="foto_nota" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Nominal Pembelian</label>
                                    <input type="text" name="nominal" id="" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim</button>
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