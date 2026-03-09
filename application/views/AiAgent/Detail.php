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
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="font-weight-bold"><?= $aiAgent['name_ai_agent'] ?></h5>
                            <small>Last Update: <?= date('d M Y', strtotime($aiAgent['updated_at'])) ?></small>
                            <br>
                            <small>Updated By: <?= $user['full_name'] ?></small>
                            <br>
                            <span class="badge bg-purple"><?= $aiAgent['model_ai_agent'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3">
                                    <h3 class="card-title">AI Specs</h3>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="ai-tabs-setting-tab" data-toggle="pill" href="#ai-tabs-setting" role="tab" aria-controls="ai-tabs-setting" aria-selected="true">AI Settings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">Profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-two-messages-tab" data-toggle="pill" href="#custom-tabs-two-messages" role="tab" aria-controls="custom-tabs-two-messages" aria-selected="false">Messages</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-two-settings-tab" data-toggle="pill" href="#custom-tabs-two-settings" role="tab" aria-controls="custom-tabs-two-settings" aria-selected="false">Settings</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="ai-tabs-setting" role="tabpanel" aria-labelledby="ai-tabs-setting-tab">
                                    <div class="row">
                                        <div class="col-6">
                                            <form action="<?= base_url('ai-agent/update') ?>" method="post">
                                                <div class="form-group">
                                                    <label for="">Nama Agent</label>
                                                    <input type="text" name="name_ai_agent" id="" class="form-control" value="<?= $aiAgent['name_ai_agent'] ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Agent Usage</label>
                                                    <select name="usage_ai_agent" id="" class="form-control" disabled>
                                                        <option value="Sales Visit Summary">Sales Visit Summary</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Model</label>
                                                    <select name="model_ai_agent" id="" class="form-control">
                                                        <option value="gpt-5" <?= $aiAgent['model_ai_agent'] == 'gpt-5' ? 'selected' : '' ?>>gpt-5</option>
                                                        <option value="gpt-4o-mini" <?= $aiAgent['model_ai_agent'] == 'gpt-4o-mini' ? 'selected' : '' ?>>gpt-4o-mini</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Temperature </label>
                                                    <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="right" title="Temperatur mempengaruhi kepribadian AI. Dari patuh ke kreatif. Range 0 - 1"></i>
                                                    <input type="number" id="" class="form-control" value="<?= $aiAgent['temperature_ai_agent'] ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Max Output Token</label>
                                                    <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="right" title="Jumlah maximal token output yang dipakai oleh AI"></i>
                                                    <input type="number" id="" class="form-control" value="<?= $aiAgent['max_output_token_ai_agent'] ?>">
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                                    Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel" aria-labelledby="custom-tabs-two-messages-tab">
                                    Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel" aria-labelledby="custom-tabs-two-settings-tab">
                                    Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
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