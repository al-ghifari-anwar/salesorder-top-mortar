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
                    <h1 class="m-0"><?= $title ?> </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('prioritystore') ?>">Toko Seller</a></li>
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
                    <div class="row">
                        <?php foreach ($contactPriors as $contact): ?>
                            <?php
                            $store_location = explode(',', $contact['maps_url']);
                            $lat_store = $store_location[0];
                            $long_store = $store_location[1];
                            $link_maps = "https://www.google.com/maps/place/" . $contact['maps_url'];

                            $distance = $this->MContact->vincentyGreatCircleDistance($loc_user['lat'], $loc_user['long'], $lat_store, $long_store)
                            ?>
                            <?php if ($distance <= 2000): ?>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <a href="<?= $link_maps ?>" class="text-decoration-none" target="_blank">
                                        <div class="info-box shadow-none">
                                            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-store"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text"><?= $contact['nama'] ?></span>
                                                <span class="info-box-number">
                                                    Jarak: <?= number_format($distance, 2, ',', '.') ?> Meter
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
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