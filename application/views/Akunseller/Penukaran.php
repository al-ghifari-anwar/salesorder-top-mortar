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
                        <li class="breadcrumb-item"><a href="#">Top Seller</a></li>
                        <li class="breadcrumb-item active">Penukaran</li>
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
                                    <form action="<?= base_url('akunseller/penukaran') ?>" method="POST">
                                        <div class="row">
                                            <label>Date range:</label>
                                            <div class="form-group ml-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control float-right" id="reservation" name="date_range">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                            <!-- <label for="">Kota:</label>
                                            <div class="form-group">
                                                <select name="id_city" id="select2bs4" class="form-control select2bs4">
                                                    <?php if ($this->session->userdata('level_user') != 'admin_c') : ?>
                                                        <option value="0">Semua</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($city as $city) : ?>
                                                        <option value="<?= $city['id_city'] ?>"><?= $city['nama_city'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div> -->
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tukang</th>
                                        <th>Skill</th>
                                        <th>Nomor HP</th>
                                        <th>Toko</th>
                                        <th>Kota</th>
                                        <th>Tanggal Claim</th>
                                        <th>Nominal</th>
                                        <th>Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($vouchers as $data) : ?>
                                        <?php
                                        $id_contact = $data['id_contact'];
                                        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
                                        $getContact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['nama_skill'] ?></td>
                                            <td><?= $data['nomorhp'] ?></td>
                                            <td><?= $getContact['nama'] ?></td>
                                            <td><?= $getContact['nama_city'] ?></td>
                                            <td><?= date("d F Y", strtotime($data['claim_date'])) ?></td>
                                            <td><?= $data['nominal_pembelian'] ?></td>
                                            <td>
                                                <a href="<?= base_url('assets/img/img_nota/') .  $data['nota_pembelian'] ?>" target="__blank">
                                                    <img src="<?= base_url('assets/img/img_nota/') .  $data['nota_pembelian'] ?>" alt="" class="img-fluid" width="100">
                                                </a>
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