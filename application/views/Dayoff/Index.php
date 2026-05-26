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
                    <h1 class="m-0"><?= $title ?> </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('target-visit') ?>"><?= $title ?></a></li>
                        <li class="breadcrumb-item"><?= $title ?></li>
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
                            <a href="#" data-toggle="modal" data-target="#modal-insert" class="btn btn-primary float-right">Tambah Data</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>User</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dayoffs as $dayoff): ?>
                                        <tr>
                                            <td><?= date('d F Y', strtotime($dayoff['date_day_off'])) ?></td>
                                            <td><?= $dayoff['full_name'] != null ? $dayoff['full_name'] : 'Semua' ?></td>
                                            <td><?= $dayoff['desc_day_off'] ?></td>
                                            <td>
                                                <a href="<?= base_url('dayoff/delete/' . $dayoff['id_day_off']) ?>" class="btn btn-danger m-1"><i class="fas fa-trash"></i></a>
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
                <h4 class="modal-title">Tambah Day Off</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('dayoff/create') ?>" method="POST">
                    <div class="form-group">
                        <label for="">User</label>
                        <select name="id_user" id="" class="form-control select2bs4">
                            <option value="0">Semua User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id_user'] ?>"><?= $user['full_name'] . ' - ' . $user['nama_city'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <input type="date" name="date_day_off" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Jumlah Pengurangan</label>
                        <input type="number" name="jml_day_off" class="form-control" value="10">
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <textarea name="desc_day_off" id="" cols="30" rows="3" class="form-control"></textarea>
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
    document.getElementById("select2bs4").onchange = function() {
        //Print data toko di field
        console.log(this.options[this.selectedIndex].getAttribute("shiptoname"));
        console.log(this.options[this.selectedIndex].getAttribute("shipaddress"));
        document.getElementById('ship_to_name').value = this.options[this.selectedIndex].getAttribute("shiptoname");
        document.getElementById('ship_to_phone').value = this.options[this.selectedIndex].getAttribute("shipphone");
        document.getElementById('ship_to_address').textContent = this.options[this.selectedIndex].getAttribute("shipaddress");

        // Notif reputasi toko
        var rep = this.options[this.selectedIndex].getAttribute("reputation");
        if (rep == 'good') {
            document.getElementById('alert-good').hidden = false;
            document.getElementById('alert-bad').hidden = true;
        } else {
            document.getElementById('alert-good').hidden = true;
            document.getElementById('alert-bad').hidden = false;
        }

        var daysJatem = this.options[this.selectedIndex].getAttribute("jatemdays");
        if (daysJatem > 7) {
            document.getElementById('alert-jatem').hidden = false;
        } else {
            document.getElementById('alert-jatem').hidden = true;
        }

        var isCod = this.options[this.selectedIndex].getAttribute("iscod");
        if (isCod < 0) {
            document.getElementById('is_cod').checked = true;
        } else {
            document.getElementById('is_cod').checked = false;
        }
    };
</script>