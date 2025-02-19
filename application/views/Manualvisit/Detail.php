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
                <div class="col-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('manualvisit/insert') ?>" method="POST">
                                <input type="hidden" value="<?= $id_city ?>" name="id_city">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Toko</label>
                                            <select name="id_contact" id="" class="form-control select2bs4">
                                                <option value="0">--- Pilih Toko ---</option>
                                                <?php foreach ($contacts as $contact) : ?>
                                                    <option value="<?= $contact['id_contact'] ?>"><?= $contact['nama'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">User</label>
                                            <select name="id_user" id="" class="form-control select2bs4">
                                                <option value="0">--- Pilih User / Sales ---</option>
                                                <?php foreach ($users as $user) : ?>
                                                    <option value="<?= $user['id_user'] ?>"><?= $user['full_name'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Nominal Pembayaran</label>
                                            <input type="number" name="pay_value" class="form-control" required>
                                            <input type="text" name="is_pay" value="pay" hidden>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Type</label>
                                            <select name="source_visit" id="" class="form-control select2bs4">
                                                <option value="jatem1">Jatem 0-7 Hari</option>
                                                <option value="jatem2">Jatem 8-15 Hari</option>
                                                <option value="jatem3">Jatem +16 Hari</option>
                                                <option value="weekly">Mingguan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Keterangan Visit Manual</label>
                                            <textarea name="laporan_visit" id="" cols="30" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="manual_user" value="<?= $this->session->userdata('id_user') ?>" hidden>
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