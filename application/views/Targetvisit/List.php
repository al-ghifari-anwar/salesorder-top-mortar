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
                    <h1 class="m-0"><?= $title ?> - <?= $city['nama_city'] ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('target-visit') ?>"><?= $title ?></a></li>
                        <li class="breadcrumb-item"><?= $title ?> - <?= $city['nama_city'] ?></li>
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
                                <div class="col-8">
                                    <form action="<?= base_url("target-visit/print") ?>" method="GET" target="_blank">
                                        <div class="row">
                                            <div class="col-3">
                                                <select name="id_user" id="" class="form-control select2bs4">
                                                    <?php foreach ($users as $user): ?>
                                                        <option value="<?= $user['id_user'] ?>"><?= $user['full_name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group ml-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="far fa-calendar-alt"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" class="form-control float-right" id="reservation" name="daterange" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <button type="submit" class="btn btn-primary float-left">
                                                    Filter
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
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