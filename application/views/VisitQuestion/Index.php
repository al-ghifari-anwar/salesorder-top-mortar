<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('failed')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i></strong> &nbsp; <?= $this->session->flashdata('failed') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-check-circle"></i></strong> &nbsp; <?= $this->session->flashdata('success') ?>
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
                    <!-- <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol> -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a class="btn btn-primary float-right" data-toggle="modal" data-target="#add-modal">Tambah Data</a>
                        </div>
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pertanyaan</th>
                                        <th>Jenis</th>
                                        <th>Opsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($questions as $question) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $question['text_question'] ?></td>
                                            <td><?= $question['answer_type'] ?></td>
                                            <td><?= $question['answer_option'] ?></td>
                                            <td>
                                                <a href="#" class="btn btn-success" data-toggle="modal" data-target="#edit-modal<?= $question['id_visit_question'] ?>"><i class="fas fa-pen"></i></a>
                                                <a href="<?= base_url('checklist-visit/delete/') . $question['id_visit_question'] ?>" class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="edit-modal<?= $question['id_visit_question'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?= base_url('checklist-visit/update/') . $question['id_visit_question'] ?>" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="">Pertanyaan</label>
                                                                <input type="text" class="form-control" name="text_question" value="<?= $question['text_question'] ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Jenis</label>
                                                                <select name="answer_type" id="" class="form-control select2bs4">
                                                                    <option value="text" <?= $question['answer_type'] == 'text' ? 'selected' : '' ?>>Text</option>
                                                                    <option value="date" <?= $question['answer_type'] == 'date' ? 'selected' : '' ?>>Date</option>
                                                                    <option value="checkbox" <?= $question['answer_type'] == 'checkbox' ? 'selected' : '' ?>>Checkbox (Pilihan Ganda Bisa Pilih Lebih Dari 1)</option>
                                                                    <option value="radio" <?= $question['answer_type'] == 'radio' ? 'selected' : '' ?>>Radio (Pilihan Ganda Hanya Bisa Pilih Satu)</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Opsi Jawaban (Untuk Pilihan Ganda) (Pisahkan Dengan Koma)</label>
                                                                <input type="text" class="form-control" name="answer_option" value="<?= $question['answer_option'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Modal -->
<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('checklist-visit/add') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Pertanyaan</label>
                        <input type="text" class="form-control" name="text_question" required>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis</label>
                        <select name="answer_type" id="" class="form-control select2bs4">
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                            <option value="checkbox">Checkbox (Pilihan Ganda Bisa Pilih Lebih Dari 1)</option>
                            <option value="radio">Radio (Pilihan Ganda Hanya Bisa Pilih Satu)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Opsi Jawaban (Untuk Pilihan Ganda) (Pisahkan Dengan Koma)</label>
                        <input type="text" class="form-control" name="answer_option">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>