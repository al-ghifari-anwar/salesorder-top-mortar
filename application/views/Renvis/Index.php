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
                    <h1 class="m-0">List Rencana Visit</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Rencana Visit</a></li>
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
                                        <th>Nama Toko</th>
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Reputasi</th>
                                        <th>Tgl Ditambah</th>
                                        <th>Counter</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($renvis as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $data['store_status'] ?></td>
                                            <td><?= $data['reputation'] ?></td>
                                            <td><?= date("d F, Y", strtotime($data['created_at'])) ?></td>
                                            <td>
                                                <?php
                                                $date1 = new DateTime(date("Y-m-d"));
                                                $date2 = new DateTime(date("Y-m-d", strtotime($data['created_at'])));
                                                $days  = $date2->diff($date1)->format('%a');
                                                $operan = "";
                                                if ($date1 < $date2) {
                                                    $operan = "";
                                                }
                                                $daysWithOperan = $operan . $days;
                                                echo $daysWithOperan . " Hari";
                                                ?>
                                            </td>
                                            <td></td>
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
                <form action="<?= base_url('insert-renvis') ?>" method="POST">
                    <div class="row">
                        <div class="col-12">
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
                                        <option value="<?= $data['id_contact'] ?>" shiptoname="<?= $data['nama'] ?>" shipaddress="<?= $data['address'] ?>" shipphone="<?= $data['nomorhp'] ?>" reputation="<?= $data['reputation'] ?>" jatemdays="<?= $daysWithOperan ?>"><?= $data['nama'] . " - " . $data['nomorhp'] . " - " . $data['store_owner'] ?></option>
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
    };
</script>