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
                                        <th>Kurir</th>
                                        <th>Tgl. SJ</th>
                                        <th>Closing</th>
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
                                            <td><?= $data['full_name'] ?></td>
                                            <td><?= date("d M Y", strtotime($data['dalivery_date'])) ?></td>
                                            <td>
                                                <?php if ($data['is_closing'] == 0) : ?>
                                                    <i class="fas fa-times-circle"></i>
                                                <?php endif; ?>
                                                <?php if ($data['is_closing'] == 1) : ?>
                                                    <a href="https://saleswa.topmortarindonesia.com/img/<?= $data['proof_closing'] ?>" target="__blank"><?= date("d M Y", strtotime($data['date_closing'])) ?> <i class="fas fa-external-link-alt"></i></a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('surat-jalan/') . $data['id_surat_jalan'] ?>" class="btn btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
                                                <?php if ($data['is_closing'] == 0) : ?>
                                                    <a href="<?= base_url('print-suratjalan/') . $data['id_surat_jalan'] ?>" class="btn btn-success" title="Cetak" target="__blank"><i class="fas fa-print"></i></a>
                                                    <?php if ($this->session->userdata('level_user') == 'finance'): ?>
                                                        <a href="<?= base_url('delete-suratjalan/') . $data['id_surat_jalan'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                                    <?php endif; ?>
                                                    <?php if ($data['is_cod'] == 1) : ?>
                                                        <a href="<?= base_url('print-tempinv/') . $data['id_surat_jalan'] ?>" class="btn btn-warning" title="Print Invoice COD" target="__blank"><i class="fas fa-print"></i></a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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
    <div class="modal-dialog modal-lg">
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
                                        <?php
                                        $id_contact = $data['id_contact'];
                                        $lastInv = $this->db->query("SELECT MAX(id_invoice) AS id_invoice, MAX(date_invoice) AS date_invoice FROM tb_invoice JOIN tb_surat_jalan ON tb_invoice.id_surat_jalan = tb_surat_jalan.id_surat_jalan WHERE tb_surat_jalan.id_contact = '$id_contact' AND status_invoice = 'waiting'")->row_array();

                                        if ($lastInv['date_invoice'] != null) {
                                            $jatuhTempo = date('d M Y', strtotime("+" . $data['termin_payment'] . " days", strtotime($lastInv['date_invoice'])));
                                            $date1 = new DateTime(date("Y-m-d"));
                                            $date2 = new DateTime($jatuhTempo);
                                            $days  = $date2->diff($date1)->format('%a');
                                            $operan = "";
                                            if ($date1 < $date2) {
                                                $operan = "-";
                                            }
                                            $daysWithOperan = $operan . $days;
                                        } else {
                                            $daysWithOperan = 0;
                                        }
                                        ?>
                                        <option value="<?= $data['id_contact'] ?>" shiptoname="<?= $data['nama'] ?>" shipaddress="<?= $data['address'] ?>" shipphone="<?= $data['nomorhp'] ?>" reputation="<?= $data['reputation'] ?>" jatemdays="<?= $daysWithOperan ?>" iscod="<?= $data['termin_payment'] ?>"><?= $data['nama'] . " - " . $data['nomorhp'] . " - " . $data['store_owner'] ?></option>
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
                                        <option value="<?= $data['id_user'] ?>"><?= $data['full_name'] . " - " . $data['nama_city'] ?></option>
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
                            <div class="form-group">
                                <label for="">Pembayaran COD</label>
                                <input type="checkbox" name="is_cod" id="is_cod" class="form-check-control">
                            </div>
                            <div class="form-group" id="alert-bad" hidden>
                                <div class="alert alert-danger fade show" role="alert">
                                    <strong>Status Toko Tidak Bagus</strong>
                                </div>
                            </div>
                            <div class="form-group" id="alert-good" hidden>
                                <div class="alert alert-success fade show" role="alert">
                                    <strong>Status Toko Bagus</strong>
                                </div>
                            </div>
                            <div class="form-group" id="alert-jatem" hidden>
                                <div class="alert alert-warning fade show" role="alert">
                                    <strong>Toko masih memiliki tanggungan jatuh tempo</strong>
                                </div>
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