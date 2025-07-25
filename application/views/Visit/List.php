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
                    <h1 class="m-0">List Visit <?= $city['nama_city'] ?> Hari Ini</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Visit</a></li>
                        <li class="breadcrumb-item active"><?= $city['nama_city'] ?></li>
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
                                    <form action="<?= base_url('lap-absen/' . $id_city . "/" . 'sales') ?>" method="POST" target="__blank">
                                        <div class="row">
                                            <!-- <label>Date range:</label>
                                            <div class="form-group ml-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                    </div>
                                                    <?php
                                                    $dateFrom = date("m/d/Y", strtotime("26th day of previous month"));
                                                    $dateTo = date("m/d/Y", strtotime("25th day of current month"));
                                                    ?>
                                                    <input type="text" class="form-control float-right" id="reservation" name="date_range">
                                                </div>
                                            </div> -->
                                            <?php
                                            $user = $this->db->get_where('tb_user', ['id_city' => $id_city, 'level_user' => 'sales'])->result_array();
                                            ?>
                                            <label for="">Bulan:</label>
                                            <div class="form-group">
                                                <select name="bulan" id="select2bs4" class="form-control select2bs4">
                                                    <option value="1">Januari</option>
                                                    <option value="2">Februari</option>
                                                    <option value="3">Maret</option>
                                                    <option value="4">April</option>
                                                    <option value="5">Mei</option>
                                                    <option value="6">Juni</option>
                                                    <option value="7">Juli</option>
                                                    <option value="8">Agustus</option>
                                                    <option value="9">September</option>
                                                    <option value="10">Oktober</option>
                                                    <option value="11">November</option>
                                                    <option value="12">Desember</option>
                                                </select>
                                            </div>
                                            <!-- <label for="">Sales:</label>
                                            <div class="form-group">
                                                <select name="id_user" id="select2bs4" class="form-control select2bs4">
                                                    <option value="0">Semua</option>
                                                    <?php foreach ($user as $user) : ?>
                                                        <option value="<?= $user['id_user'] ?>"><?= $user['full_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div> -->
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
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
                                        <th>Sales</th>
                                        <th>Toko</th>
                                        <th>Jarak</th>
                                        <th>Jam - Tanggal</th>
                                        <th>Laporan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($visit as $data) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $data['full_name'] ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td><?= $data['distance_visit'] ?></td>
                                            <td><?= date("H:i - d M Y", strtotime($data['date_visit'])) ?></td>
                                            <td><?= normalizer_normalize($data['laporan_visit'], Normalizer::FORM_KC) ?> <?= $data['pay_date'] != null ? ' - Tanggal dijanjikan: ' . date("d F Y", strtotime($data['pay_date'])) : ''  ?></td>
                                            <td>
                                                <a href="#" class="btn btn-primary" title="Detail" data-toggle="modal" data-target="#modal-detail<?= $data['id_visit'] ?>"><i class="fas fa-eye"></i></a>
                                                <?php if ($data['is_approved'] == 0): ?>
                                                    <a href="<?= base_url('approve-visit/' . $data['id_visit']) ?>" class="btn btn-success" title="Approve" data-toggle="modal" data-target="#modal-approve<?= $data['id_visit'] ?>"><i class="fas fa-check-circle"></i></a>
                                                <?php endif; ?>
                                                <?php if ($this->session->userdata('id_distributor') == 4): ?>
                                                    <?php if ($this->session->userdata('id_user') == '72' || $this->session->userdata('id_user') == '121' || $this->session->userdata('id_user') == '124'): ?>
                                                        <a href="#" class="btn btn-primary mx-1" title="Approve" data-toggle="modal" data-target="#modal-approve2<?= $data['id_visit'] ?>"><i class="fas fa-check-circle"></i></a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-approve<?= $data['id_visit'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Approval</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('approve-visit/' . $data['id_visit'] . "/" . $id_city) ?>" method="POST">
                                                            <?php if ($this->session->userdata('id_distributor') == 4): ?>
                                                                <div class="form-group">
                                                                    <label for="">Proyek</label>
                                                                    <select name="id_proyek" id="" class="form-control select2bs4">
                                                                        <?php foreach ($proyeks as $proyek): ?>
                                                                            <option value="<?= $proyek['id_proyek'] ?>"><?= $proyek['name_proyek'] . " - " . $proyek['alamat_proyek'] ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="form-group">
                                                                <label for="">Pesan Approve</label>
                                                                <textarea name="approve_message" id="" cols="30" rows="5" class="form-control"></textarea>
                                                            </div>
                                                            <button class="btn btn-primary float-right">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Approve 2 -->
                                        <div class="modal fade" id="modal-approve2<?= $data['id_visit'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Approval</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('approve-visit2/' . $data['id_visit'] . "/" . $id_city) ?>" method="POST">
                                                            <?php if ($this->session->userdata('id_distributor') == 4): ?>
                                                                <div class="form-group">
                                                                    <label for="">Proyek</label>
                                                                    <select name="id_proyek" id="" class="form-control select2bs4">
                                                                        <?php foreach ($proyeks as $proyek): ?>
                                                                            <option value="<?= $proyek['id_proyek'] ?>"><?= $proyek['name_proyek'] . " - " . $proyek['alamat_proyek'] ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="form-group">
                                                                <label for="">Pesan Approve</label>
                                                                <textarea name="approve_message_2" id="" cols="30" rows="5" class="form-control"></textarea>
                                                            </div>
                                                            <button class="btn btn-primary float-right">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal Detail -->
                                        <div class="modal fade" id="modal-detail<?= $data['id_visit'] ?>">
                                            <?php
                                            $id_visit = $data['id_visit'];
                                            $this->db->group_by('tb_visit_answer.text_question');
                                            $getVisitQuestion = $this->db->get_where('tb_visit_answer', ['id_visit' => $id_visit])->result_array();
                                            ?>
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Detail Checklist Visit</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php
                                                        $noDetail = 1;
                                                        foreach ($getVisitQuestion as $visitQuestion): ?>
                                                            <?php
                                                            $text_question = $visitQuestion['text_question'];
                                                            $getAnswers = $this->db->get_where('tb_visit_answer', ['id_visit' => $id_visit, 'text_question' => $text_question])->result_array();
                                                            ?>
                                                            <h6><?= $noDetail++ ?>. <?= $visitQuestion['text_question'] ?></h6>
                                                            <?php foreach ($getAnswers as $answer): ?>
                                                                <ul>
                                                                    <p class=""><?= $answer['text_answer'] ?></p>
                                                                </ul>
                                                            <?php endforeach; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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