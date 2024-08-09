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
                    <h1 class="m-0">Marketing</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Marketing</li>
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
                <div class="col-6 col-md-3 col-lg-3">
                    <!-- small box -->
                    <a href="#" data-toggle="modal" data-target="#modal-insert">
                        <div class="bg-light border border-primary" style="height: 18   rem;">
                            <div class="card-body">
                                <div class="row my-auto">
                                    <h1 class="text-center mx-auto"><i class="fas fa-plus"></i></h1>
                                </div>
                                <h3 class="text-center">Tambah Konten</h3>
                            </div>
                        </div>
                    </a>
                </div>
                <?php foreach ($marketing as $marketing) : ?>
                    <div class="col-6 col-md-3 col-lg-3">
                        <!-- small box -->
                        <div class="card">
                            <img src="<?= base_url('assets/img/content_img/') . $marketing['image_marketing_message'] ?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?= $marketing['nama_marketing_message'] ?></h5>
                                <br>
                                <p class="card-text">Minggu Ke <?= $marketing['week_marketing_message'] ?></p>
                                <a href="<?= base_url('delete-marketing/') . $marketing['id_marketing_message'] ?>" class="btn btn-danger">Delete</a>
                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-edit">Edit</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-edit">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?= base_url('update-marketing/') . $marketing['id_marketing_message'] ?>" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="">Nama Kontenr</label>
                                            <input type="text" name="nama_marketing_message" class="form-control" value="<?= $marketing['nama_marketing_message'] ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Template Qontak</label>
                                            <select name="template_id" id="" class="form-control select2bs4">
                                                <?php foreach ($templates as $template) : ?>
                                                    <option value="<?= $template['id'] ?>"><?= $template['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Image</label>
                                            <input type="file" name="image_marketing_message" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Body</label>
                                            <textarea name="body_marketing_message" id="" cols="30" rows="5" class="form-control"><?= $marketing['body_marketing_message'] ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Minggu Ke</label>
                                            <input type="number" name="week_marketing_message" class="form-control" value="<?= $marketing['week_marketing_message'] ?>">
                                        </div>
                                        <input type=" text" value="data" name="target_status" hidden>
                                        <button class="btn btn-primary float-right">Simpan</button>
                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                <?php endforeach; ?>
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
                <!-- <h4 class="modal-title">Tambah Data Kota</h4> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-marketing') ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="">Nama Kontenr</label>
                        <input type="text" name="nama_marketing_message" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Template Qontak</label>
                        <select name="template_id" id="select2bs4" class="select2bs4">
                            <?php foreach ($templates as $template) : ?>
                                <option value="<?= $template['id'] ?>"><?= $template['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Image</label>
                        <input type="file" name="image_marketing_message" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Body</label>
                        <textarea name="body_marketing_message" id="" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Minggu Ke</label>
                        <input type="number" name="week_marketing_message" class="form-control">
                    </div>
                    <input type="text" value="data" name="target_status" hidden>
                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->