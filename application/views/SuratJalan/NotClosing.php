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
                    <h1 class="m-0">List Surat Jalan Yang Belum Closing</h1>
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
                                        <!-- <th>Aksi</th> -->
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
                                            <td><?= date("d M Y - H:i", strtotime($data['dalivery_date'])) ?></td>
                                            <td>
                                                <?php if ($data['is_closing'] == 0) : ?>
                                                    <i class="fas fa-times-circle"></i>
                                                <?php endif; ?>
                                                <?php if ($data['is_closing'] == 1) : ?>
                                                    <a href="https://saleswa.topmortarindonesia.com/img/<?= $data['proof_closing'] ?>" target="__blank"><?= date("d M Y", strtotime($data['date_closing'])) ?> <i class="fas fa-external-link-alt"></i></a>
                                                <?php endif; ?>
                                            </td>
                                            <!-- <td>
                                                <a href="<?= base_url('surat-jalan/') . $data['id_surat_jalan'] ?>" class="btn btn-primary" title="Detail"><i class="fas fa-eye"></i></a>
                                                <?php if ($data['is_closing'] == 0) : ?>
                                                    <a href="<?= base_url('delete-suratjalan/') . $data['id_surat_jalan'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                                <?php endif; ?>
                                            </td> -->
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