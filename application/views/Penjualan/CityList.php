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
                    <h1 class="m-0"></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Penjualan</li>
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
                    <form action="<?= base_url('penjualan') ?>" method="POST">
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
                            <div class="form-group ml-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <?php
                $totalQty = 0;
                foreach ($items as $item) {
                    $totalQty += $item['qty_produk'];
                }
                ?>
                <?php
                foreach ($city as $data) :
                    if ($dates) {
                        $itemsCity = $this->MDetailSuratJalan->getSoldItemsByDate($data['id_city'], date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")));
                    } else {
                        $itemsCity = $this->MDetailSuratJalan->getSoldItems($data['id_city']);
                    }
                ?>
                    <div class="col-lg-3 col-6">
                        <?php
                        $totalItemCity = 0;
                        foreach ($itemsCity as $itemsCity) {
                            $totalItemCity += $itemsCity['qty_produk'];
                        }
                        ?>
                        <!-- small box -->
                        <div class="small-box bg-light">
                            <div class="inner">
                                <h3><?= $data['nama_city'] ?></h3>

                                <p><?= $data['kode_city'] ?></p>
                                <h5>Total Penjualan: <?= $totalItemCity ?></h5>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="<?= base_url('penjualan/') . $data['id_city'] ?>" class="small-box-footer">Buka <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="row">
                <div class="col-lg-12 col-12">
                    <!-- small box -->
                    <div class="small-box bg-light">
                        <div class="inner">
                            <p style="font-size: 20pt;"><b>TOTAL KESELURUHAN</b></p>

                            <p><?= $totalQty ?> Sak</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <!-- <a href="<?= base_url('penjualan-detail/') . $data['id_produk'] ?>" class="small-box-footer">Buka <i class="fas fa-arrow-circle-right"></i></a> -->
                    </div>
                </div>
                <div class="row">
                    <?php
                    $totalQty = 0;
                    foreach ($items as $data) :
                        $totalQty += $data['qty_produk'];
                    ?>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-light">
                                <div class="inner">
                                    <p style="font-size: 20pt;"><b><?= str_replace('TOP MORTAR', '', $data['nama_produk']) ?></b></p>

                                    <p><?= $data['qty_produk'] ?> Sak</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <!-- <a href="<?= base_url('penjualan-detail/') . $data['id_produk'] ?>" class="small-box-footer">Buka <i class="fas fa-arrow-circle-right"></i></a> -->
                            </div>
                        </div>
                    <?php endforeach; ?>
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