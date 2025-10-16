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
                        <!-- <li class="breadcrumb-item"><a href="#">Master</a></li>
                        <li class="breadcrumb-item active">Kota</li> -->
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
                            <!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-insert">
                                Tambah Data
                            </button> -->
                        </div>
                        <div class="card-body">
                            <table id="table-no-paging" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAll"></th>
                                        <th>No</th>
                                        <th>Toko</th>
                                        <th>Kategori</th>
                                        <th>Jatem</th>
                                        <th>Hari</th>
                                        <th>Umur Hutang</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($renvis as $renvi): ?>
                                        <?php
                                        $id_contact = $renvi['id_contact'];

                                        $created_at = date('Y-m-d', strtotime($renvi['created_at']));

                                        $date1 = new DateTime(date("Y-m-d"));
                                        $date2 = new DateTime($created_at);
                                        $days  = $date2->diff($date1)->format('%a');
                                        $operan = "";
                                        if ($date1 < $date2) {
                                            $operan = "-";
                                        }
                                        $days = $operan . $days;

                                        // Jatem Days
                                        $date1jatem = new DateTime(date("Y-m-d"));
                                        $date2jatem = new DateTime($renvi['jatem']);
                                        $daysJatem  = $date2jatem->diff($date1jatem)->format('%a');
                                        $operanJatem = "";
                                        if ($date1jatem < $date2jatem) {
                                            $operanJatem = "-";
                                        }
                                        $daysJatem = $operanJatem . $daysJatem;

                                        // Invoice
                                        $id_invoice = $renvi['id_invoice'];
                                        $invoices = $this->MInvoice->getByIdInvoiceWaiting($id_invoice);

                                        $total_invoice = 0;

                                        foreach ($invoices as $invoice) {
                                            $id_invoice = $invoice['id_invoice'];
                                            // Jatem Days
                                            $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                                            $dateInv1 = new DateTime(date("Y-m-d"));
                                            $dateInv2 = new DateTime($jatemInv);
                                            $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                                            $operanInvJatem = "";
                                            if ($dateInv1 < $dateInv2) {
                                                $operanInvJatem = "-";
                                            }
                                            $daysInvJatem = $operanInvJatem . $daysInvJatem;

                                            $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                                            $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];

                                            if ($renvi['type_renvis'] != 'tagih_mingguan') {
                                                if ($daysInvJatem > 0) {
                                                    $total_invoice += $sisaHutang;
                                                }
                                            } else {
                                                if ($daysInvJatem < 0) {
                                                    $total_invoice += $sisaHutang;
                                                }
                                            }
                                        }

                                        if ($total_invoice == 0) {
                                            $invoices = $this->MInvoice->getByIdContactWaiting($id_contact);
                                            foreach ($invoices as $invoice) {
                                                $id_invoice = $invoice['id_invoice'];
                                                // Jatem Days
                                                $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                                                $dateInv1 = new DateTime(date("Y-m-d"));
                                                $dateInv2 = new DateTime($jatemInv);
                                                $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                                                $operanInvJatem = "";
                                                if ($dateInv1 < $dateInv2) {
                                                    $operanInvJatem = "-";
                                                }
                                                $daysInvJatem = $operanInvJatem . $daysInvJatem;

                                                $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                                                $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];

                                                if ($renvi['type_renvis'] != 'tagih_mingguan') {
                                                    if ($daysInvJatem > 0) {
                                                        $total_invoice += $sisaHutang;
                                                    }
                                                } else {
                                                    if ($daysInvJatem < 0) {
                                                        $total_invoice += $sisaHutang;
                                                    }
                                                }
                                            }
                                        }

                                        ?>
                                        <tr>
                                            <td><input type="checkbox" class="checkItem" value="<?= $renvi['id_renvis_jatem'] ?>"></td>
                                            <td><?= $no++; ?></td>
                                            <td><?= $renvi['nama'] ?></td>
                                            <td><?= $renvi['type_renvis'] ?></td>
                                            <td><?= $renvi['jatuh_tempo'] ?></td>
                                            <td><?= $days ?></td>
                                            <td><?= $daysJatem ?></td>
                                            <td>Rp. <?= number_format($total_invoice, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <button id="proses" class="btn btn-primary float-right">Proses</button>
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

<div class="modal fade" id="modal-insert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Kota</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('insert-city') ?>" method="POST">

                    <button class="btn btn-primary float-right">Simpan</button>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
    $("#proses").click(function() {
        var selected = [];
        $(".checkItem:checked").each(function() {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            alert("Pilih minimal 1 data");
            return;
        }

        // Buat form dinamis dan submit ke controller
        var form = $('<form>', {
            'method': 'POST',
            'action': "<?= site_url('checklistrenvi/proses') ?>",
            'target': '_blank'
        });

        $.each(selected, function(i, val) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'ids[]',
                'value': val
            }));
        });

        $('body').append(form);
        form.submit();
    });
</script>