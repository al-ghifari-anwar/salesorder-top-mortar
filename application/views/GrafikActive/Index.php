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
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Toko Aktif',
                    data: chartValues,
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    borderWidth: 1,
                    hoverBackgroundColor: '#218838',
                    hoverBorderColor: '#218838',
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'top',
                        color: '#495057',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value, context) {
                            return value;
                        }
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 500,
                    onProgress: function() {},
                    onComplete: function() {
                        var chartInstance = this.chart;
                        var ctx = chartInstance.ctx;

                        ctx.font = 'bold 12px Arial';
                        ctx.fillStyle = '#495057';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        this.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);

                            meta.data.forEach(function(bar, index) {
                                var value = dataset.data[index];

                                ctx.fillText(
                                    value,
                                    bar._model.x,
                                    bar._model.y - 5
                                );
                            });
                        });
                    }
                },
                plugins: {
                    datalabels: false
                },
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
                scales: {
                    xAxes: [{
                        barPercentage: 0.7,
                        categoryPercentage: 0.8,
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