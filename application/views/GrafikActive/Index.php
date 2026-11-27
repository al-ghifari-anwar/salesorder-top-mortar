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
                    <h1 class="m-0">Piutang Invoice</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Invoice</a></li>
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
                                    <form action="<?= base_url('grafikactive') ?>" method="GET" target="_blank">
                                        <div class="row">
                                            <label for="">Kota:</label>
                                            <div class="form-group">
                                                <select name="id_city" id="select2bs4" class="form-control select2bs4">
                                                    <option value="0">Semua Kota</option>
                                                    <?php foreach ($citys as $city): ?>
                                                        <option value="<?= $city['id_city'] ?>" <?= $id_city == $city['id_city'] ? 'selected' : '' ?>><?= $city['nama_city'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
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
                            <div class="chart" style="position: relative; height: 400px;">
                                <canvas id="activeStoreChart"></canvas>
                            </div>
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
    $(document).ready(function() {
        var chartLabels = <?= json_encode($chartLabels) ?>;
        var chartValues = <?= json_encode($chartValues) ?>;

        var ctx = document.getElementById('activeStoreChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Toko Aktif',
                    data: chartValues,
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: '#007bff',
                    borderWidth: 3,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    lineTension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.yLabel + ' Toko Aktif';
                        }
                    }
                },
                hover: {
                    mode: 'nearest',
                    intersect: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Jumlah Toko Aktif'
                        }
                    }]
                }
            }
        });
    });
</script>