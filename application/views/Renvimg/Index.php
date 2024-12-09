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
                    <h1 class="m-0">List Rencana Visit MG</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Rencana Visit MG</a></li>
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
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                Tambah Data
                            </button>
                            <!-- <a href="<?= base_url('renvis/print/') . $id_city ?>" class="btn btn-success mx-3 float-right" target="__blank">
                                Cetak Data
                            </a> -->
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Toko</th>
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Reputasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($renvimg as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td>
                                                <a href="<?= base_url('renvimg/delete/' . $data['id_contact']) ?>" class="btn btn-danger"><i class="fas fa-trash"></i></a>
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
                <h4 class="modal-title">Tambah Data Rencana Visit</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('renvimg/insert') ?>" method="POST">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Toko</label>
                                <select class="form-control select2bs4" name="id_contact" style="width: 100%;" id="select2bs4">
                                    <option value="0">--- PLEASE SELECT STORE ---</option>
                                    <?php foreach ($toko as $data) : ?>
                                        <option value="<?= $data['id_contact'] ?>"><?= $data['nama'] . " - " . $data['nomorhp'] . " - " . $data['store_owner'] . " - " . $data['store_status'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
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
<script>
    // function getToko(selectObject) {
    //     var value = selectObject.shiptoname;
    //     console.log(value);
    // }
    // document.getElementById("select2bs4").onchange = function() {
    //Print data toko di field
    // console.log(this.options[this.selectedIndex].getAttribute("shiptoname"));
    // console.log(this.options[this.selectedIndex].getAttribute("shipaddress"));
    // document.getElementById('ship_to_name').value = this.options[this.selectedIndex].getAttribute("shiptoname");
    // document.getElementById('ship_to_phone').value = this.options[this.selectedIndex].getAttribute("shipphone");
    // document.getElementById('ship_to_address').textContent = this.options[this.selectedIndex].getAttribute("shipaddress");

    // Notif reputasi toko
    //     var rep = this.options[this.selectedIndex].getAttribute("reputation");
    //     if (rep == 'good') {
    //         document.getElementById('alert-good').hidden = false;
    //         document.getElementById('alert-bad').hidden = true;
    //     } else {
    //         document.getElementById('alert-good').hidden = true;
    //         document.getElementById('alert-bad').hidden = false;
    //     }

    //     var daysJatem = this.options[this.selectedIndex].getAttribute("jatemdays");
    //     if (daysJatem > 7) {
    //         document.getElementById('alert-jatem').hidden = false;
    //     } else {
    //         document.getElementById('alert-jatem').hidden = true;
    //     }
    // };
</script>