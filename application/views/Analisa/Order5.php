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
                    <h1 class="m-0"><?= $title . " " . $selected_city['nama_city'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Kota</li>
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
                            <div class="row">
                                <div class="col-10">
                                    <form action="<?= base_url('analisa/passive') ?>" method="POST">
                                        <div class="row">
                                            <label for="">Kota: </label>
                                            <div class="form-group ml-3">
                                                <select name="id_city" id="select2bs4" class="form-control select2bs4">
                                                    <?php if ($this->session->userdata('level_user') != 'admin_c') : ?>
                                                        <option value="0">Semua</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($citys as $city) : ?>
                                                        <option value="<?= $city['id_city'] ?>" <?= $city['id_city'] == $selected_city['id_city'] ? 'selected' : '' ?>><?= $city['nama_city'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Lihat</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- <div class="col-5">
                                    <a href="<?= base_url('report') ?>" class="btn btn-primary float-right">Semua</a>
                                </div> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Nomor HP</th>
                                        <th>Status</th>
                                        <th>Kota</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($contacts as $contact) : ?>
                                        <?php if ($contact['store_status'] == 'passive'): ?>
                                            <?php
                                            $count_late_payment = 0;
                                            $invoices = $this->MInvoice->getByIdContactNoMerch($contact['id_contact']);
                                            $payments = null;
                                            foreach ($invoices as $invoice) {
                                                $payments = $this->MPayment->getByIdInvoiceOnly($invoice['id_invoice']);

                                                $jatuhTempo = date('Y-m-d', strtotime("+" . $contact['termin_payment'] . " days", strtotime($invoice['date_invoice'])));

                                                foreach ($payments as $payment) {
                                                    $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                                                    if ($datePayment > $jatuhTempo) {
                                                        $count_late_payment += 1;
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php if ($payments): ?>
                                                <?php if ($count_late_payment == 0): ?>
                                                    <?php if (count($invoices) >= 5): ?>
                                                        <tr>
                                                            <td><?= $no++; ?></td>
                                                            <td><?= $contact['nama'] ?></td>
                                                            <td><?= $contact['address'] ?></td>
                                                            <td><?= $contact['nomorhp'] ?></td>
                                                            <td><?= $contact['store_status'] ?></td>
                                                            <td><?= $contact['nama_city'] ?></td>
                                                            <td>
                                                                <!-- <a class="btn btn-primary" data-toggle="modal" data-target="#modal-edit<?= $contact['id_city'] ?>" title="Edit"><i class="fas fa-pen"></i></a>
                                                            <a href="<?= base_url('delete-city/') . $contact['id_city'] ?>" class="btn btn-danger" title="Hapus"><i class="fas fa-trash"></i></a> -->
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
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