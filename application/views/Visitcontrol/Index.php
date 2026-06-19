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
                        <li class="breadcrumb-item"><a href="#"></a></li>
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
                                <div class="col-12">
                                    <form action="<?= base_url('visitcontrol') ?>" method="GET">
                                        <div class="row">
                                            <label>Date:</label>
                                            <div class="form-group ml-3">
                                                <input type="date" class="form-control float-right" id="" name="date" value="<?= date('Y-m-d', strtotime($date)) ?>">
                                                <!-- /.input group -->
                                            </div>
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Toko</th>
                                        <th>Kota</th>
                                        <th>Sales</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($visitcontrols as $visitcontrol): ?>
                                        <?php
                                        $id_contact = $visitcontrol['id_contact'];
                                        $id_user = $visitcontrol['id_user'];

                                        $contact = $this->db->join('tb_city', 'tb_city.id_city=tb_contact.id_city')->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();

                                        $user = $this->db->get_where('tb_user', ['id_user' => $id_user])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $contact['nama']; ?></td>
                                            <td><?= $contact['nama_city']; ?></td>
                                            <td><?= $user['full_name']; ?></td>
                                            <td><?= $visitcontrol['reason_visit_control']; ?></td>
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