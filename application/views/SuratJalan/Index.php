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
                    <h1 class="m-0">List Surat Jalan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Surat Jalan</a></li>
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
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Surat Jalan</th>
                                        <th>Toko</th>
                                        <th>Nomor HP</th>
                                        <th>Kota</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($suratjalan as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['no_surat_jalan'] ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['nama_city'] ?></td>
                                            <td>
                                                <a href="<?= base_url('surat-jalan/') . $data['id_surat_jalan'] ?>" class="btn btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
                                                <a href="<?= base_url('delete-suratjalan/') . $data['id_surat_jalan'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
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
                <h4 class="modal-title">Tambah Data Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-suratjalan') ?>" method="POST">
                    <div class="row">
                        <div class="col-6">
                            <input type="text" name="no_surat_jalan" class="form-control" value="<?= "DO-" . rand(10000000, 99999999) ?>" hidden>
                            <div class="form-group">
                                <label for="">Toko</label>
                                <select class="form-control select2bs4" name="id_contact" style="width: 100%;" id="select2bs4">
                                    <option value="0">--- PLEASE SELECT STORE ---</option>
                                    <?php foreach ($toko as $data) : ?>
                                        <option value="<?= $data['id_contact'] ?>" shiptoname="<?= $data['store_owner'] ?>" shipaddress="<?= $data['address'] ?>" shipphone="<?= $data['nomorhp'] ?>"><?= $data['nama'] . " - " . $data['nomorhp'] . " - " . $data['store_owner'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group" hidden>
                                <label for="">Order Number</label>
                                <input type="text" name="order_number" class="form-control" value="0">
                            </div>
                            <!-- <div class="mt-3">
                        <label for=""><b>Shipping Detail:</b></label>
                    </div> -->
                            <div class="form-group">
                                <label for="">Ship To Name</label>
                                <input type="text" name="ship_to_name" class="form-control" id="ship_to_name">
                            </div>
                            <div class="form-group">
                                <label for="">Ship To Address</label>
                                <textarea name="ship_to_address" id="ship_to_address" cols="30" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Ship To Phone</label>
                                <input type="text" name="ship_to_phone" id="ship_to_phone" class="form-control" placeholder="628xxx">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Kurir</label>
                                <select class="form-control select2bs4" name="id_courier" style="width: 100%;">
                                    <?php foreach ($kurir as $data) : ?>
                                        <option value="<?= $data['id_user'] ?>"><?= $data['full_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Kendaraan</label>
                                <select class="form-control select2bs4" name="id_kendaraan" style="width: 100%;">
                                    <?php foreach ($kendaraan as $dataKend) : ?>
                                        <option value="<?= $dataKend['id_kendaraan'] ?>"><?= $dataKend['nama_kendaraan'] . " - " . $dataKend['nopol_kendaraan'] ?></option>
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
    document.getElementById("select2bs4").onchange = function() {
        console.log(this.options[this.selectedIndex].getAttribute("shiptoname"));
        console.log(this.options[this.selectedIndex].getAttribute("shipaddress"));
        document.getElementById('ship_to_name').value = this.options[this.selectedIndex].getAttribute("shiptoname");
        document.getElementById('ship_to_phone').value = this.options[this.selectedIndex].getAttribute("shipphone");
        document.getElementById('ship_to_address').textContent = this.options[this.selectedIndex].getAttribute("shipaddress");
    };
</script>