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
                        <li class="breadcrumb-item active"><?= $title ?></li>
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
                                    <form action="<?= base_url('scoring/' . $city['id_city']) ?>" method="POST">
                                        <div class="row">
                                            <label for="">Toko: </label>
                                            <div class="form-group ml-3">
                                                <select name="id_contact" id="select2bs4" class="form-control select2bs4">
                                                    <?php foreach ($contacts as $contact) : ?>
                                                        <option value="<?= $contact['id_contact'] ?>" <?= $contact['id_contact'] == $selected_contact['id_contact'] ? 'selected' : '' ?>><?= $contact['nama'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group ml-3">
                                                <button type="submit" class="btn btn-primary">Lihat Skor</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- <div class="col-5">
                                    <a href="<?= base_url('report') ?>" class="btn btn-primary float-right">Semua</a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <?php if ($is_score == 1): ?>
                <?php
                $count_late_payment = 0;
                $invoices = $this->MInvoice->getByIdContactNoMerch($selected_contact['id_contact']);
                $payments = null;
                $array_scoring = array();
                foreach ($invoices as $invoice) {
                    $payments = $this->MPayment->getLastByIdInvoiceOnly($invoice['id_invoice']);

                    $jatuhTempo = date('Y-m-d', strtotime("+" . $selected_contact['termin_payment'] . " days", strtotime($invoice['date_invoice'])));

                    foreach ($payments as $payment) {
                        $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                        if ($datePayment > $jatuhTempo) {
                            $count_late_payment += 1;
                            $date1 = new DateTime($datePayment);
                            $date2 = new DateTime($jatuhTempo);
                            $days  = $date2->diff($date1)->format('%a');

                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'late',
                                'days_late' => $days,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100 - $days
                            ];

                            array_push($array_scoring, $scoreData);
                        } else {
                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'good',
                                'days_late' => 0,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100
                            ];

                            array_push($array_scoring, $scoreData);
                        }
                    }
                }
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                $count_invoice = count($array_scoring);
                                if ($count_invoice == 0) {
                                    $count_invoice = 1;
                                }
                                $total_score = 0;
                                foreach ($array_scoring as $scoring) {
                                    $total_score += $scoring['percent_score'];
                                }
                                $val_scoring = number_format($total_score / $count_invoice, 2, '.', '.');
                                if ($val_scoring >= 91 && $val_scoring <= 100) {
                                    $color_text = 'text-success';
                                } else if ($val_scoring >= 81 && $val_scoring <= 90) {
                                    $color_text = 'text-teal';
                                } else if ($val_scoring <= 80) {
                                    $color_text = 'text-danger';
                                }
                                ?>
                                <div class="row">
                                    <div class="col-6">
                                        <!-- DONUT CHART -->
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Skor</h3>
                                            </div>
                                            <div class="card-body">
                                                <h1 class="text-center <?= $color_text ?>"><?= $val_scoring ?>%</h1>
                                                <!-- <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas> -->
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Detail</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Jml Invoice Keseluruhan</th>
                                                        <td><?= $count_invoice ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jml Invoice Sehat</th>
                                                        <td><?= $count_invoice - $count_late_payment ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jml Invoice Terlambat</th>
                                                        <td><?= $count_late_payment ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Detail Invoice Terlambat</h3>
                                            </div>
                                            <div class="card-body">
                                                <table class="table-bordered table">
                                                    <?php foreach ($array_scoring as $scoringDetail): ?>
                                                        <?php if ($scoringDetail['status'] == 'late'): ?>
                                                            <tr>
                                                                <th colspan="2">INVOICE #<?= $scoringDetail['no_invoice'] ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Jatuh Tempo</td>
                                                                <td><?= $scoringDetail['date_jatem'] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Payment</td>
                                                                <td><?= $scoringDetail['date_payment'] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Jml Hari Terlambat</td>
                                                                <td><?= $scoringDetail['days_late'] ?></td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(function() {
                                        var score = <?= $val_scoring ?>
                                        //-------------
                                        //- DONUT CHART -
                                        //-------------
                                        // Get context with jQuery - using jQuery's .get() method.
                                        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
                                        var donutData = {
                                            datasets: [{
                                                data: [score, 100 - score],
                                                backgroundColor: ['#00a65a', '#d2d6de', '#d2d6de', '#d2d6de', '#d2d6de', '#d2d6de'],
                                            }]
                                        }
                                        var donutOptions = {
                                            maintainAspectRatio: false,
                                            responsive: true,
                                            legend: {
                                                display: true,
                                                position: 'bottom',
                                                align: 'center',
                                                labels: {
                                                    boxWidth: 15
                                                }
                                            },
                                            plugins: {
                                                labels: {
                                                    render: 'value',
                                                    precision: 0,
                                                    fontSize: 14,
                                                    fontColor: '#fff',
                                                    fontStyle: 'normal',
                                                }
                                            },
                                            animations: {
                                                duration: 1000,
                                                duration: 5000,
                                                easing: 'linear',
                                                from: 1,
                                                to: 0,
                                                loop: true
                                            }
                                        }
                                        //Create pie or douhnut chart
                                        // You can switch between pie and douhnut using the method below.
                                        new Chart(donutChartCanvas, {
                                            type: 'pie',
                                            data: donutData,
                                            options: donutOptions
                                        })
                                    })
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->