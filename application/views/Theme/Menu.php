<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <?php if ($this->session->userdata('level_user') != null) : ?>
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="index3.html" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link">Contact</a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <!-- Notifications Dropdown Menu -->
                    <!-- <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li> -->
                    <!-- Menu Dropdown Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-cog"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                            <span class="dropdown-header">Menu</span>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('logout') ?>" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                            <i class="fas fa-th-large"></i>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="<?= base_url('assets/img/logo_retina.png') ?>" alt="Logo" class="brand-image" style="opacity: .8">
                <span class="brand-text font-weight-light">.</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <a href="#" class="d-block"><?= $this->session->userdata('full_name') ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <?php if ($this->session->userdata('level_user') != null) : ?>
                        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item">
                                <a href="<?= base_url() ?>" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>
                                        Dashboard
                                    </p>
                                </a>
                            </li>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'sales') : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('renvis') ?>" class="nav-link">
                                        <i class="nav-icon fas fa-calendar-plus"></i>
                                        <p>
                                            Rencana Visit
                                            <!-- <span class="right badge badge-danger">New</span> -->
                                        </p>
                                    </a>
                                </li>
                            <?php endif ?>
                            <?php if ($this->session->userdata('level_user') == 'superadmin') : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('distributor') ?>" class="nav-link">
                                        <i class="nav-icon fas fa-warehouse"></i>
                                        <p>
                                            Distributor
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <!-- <li class="nav-header">Data</li> -->
                            <?php if ($this->session->userdata('level_user') == 'marketing') : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('marketing') ?>" class="nav-link">
                                        <i class="nav-icon fas fa-shopping-bag"></i>
                                        <p>
                                            Marketing
                                            <!-- <span class="right badge badge-danger">New</span> -->
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'superadmin' || $this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance' || $this->session->userdata('level_user') == 'salesleader') : ?>
                                <?php if ($this->session->userdata('level_user') != 'salesleader') : ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon fas fa-database"></i>
                                            <p>
                                                Data
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="<?= base_url('city') ?>" class="nav-link">
                                                    <i class="fas fa-map-marker-alt nav-icon"></i>
                                                    <p>Kota</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('kendaraan') ?>" class="nav-link">
                                                    <i class="fas fa-truck nav-icon"></i>
                                                    <p>Kendaraan</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('produk') ?>" class="nav-link">
                                                    <i class="fas fa-shopping-basket nav-icon"></i>
                                                    <p>Produk</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('toko') ?>" class="nav-link">
                                                    <i class="fas fa-store nav-icon"></i>
                                                    <p>Toko</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon fas fa-file"></i>
                                            <p>
                                                Surat Jalan
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="<?= base_url('surat-jalan') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-th"></i>
                                                    <p>
                                                        Surat Jalan
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('sj-not-closing') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-times-circle"></i>
                                                    <p>
                                                        Belum Closing
                                                        <?php
                                                        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
                                                        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
                                                        $sjNotClosing = $this->db->get_where("tb_surat_jalan", ['is_closing' => 0, 'tb_city.id_distributor' => $this->session->userdata('id_distributor')]);
                                                        ?>
                                                        <span class="right badge badge-danger"><?= $sjNotClosing->num_rows() ?></span>
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon fas fa-file-invoice"></i>
                                            <p>
                                                Invoice
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="<?= base_url('invoice') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-file-invoice"></i>
                                                    <p>
                                                        Invoice
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('rep-invoice/0') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-file-archive"></i>
                                                    <p>
                                                        Rekap Invoice
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>
                                            Sales
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('visit') ?>" class="nav-link">
                                                <i class="nav-icon fas fa-location-arrow"></i>
                                                <p>
                                                    Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rencana-visit') ?>" class="nav-link">
                                                <i class="nav-icon fas fa-map-marked"></i>
                                                <p>
                                                    Rencana Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rekap-sales') ?>" class="nav-link">
                                                <i class="nav-icon fab fa-paypal"></i>
                                                <p>
                                                    Fee
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rekap-status') ?>" class="nav-link">
                                                <i class="nav-icon fas fa-info-circle"></i>
                                                <p>
                                                    Rekap Detail Status
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php if ($this->session->userdata('level_user') == 'salesleader') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('lap-kurir') ?>" class="nav-link">
                                            <i class="nav-icon fas fa-truck-moving"></i>
                                            <p>
                                                Laporan Kurir
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('stok') ?>" class="nav-link">
                                        <i class="nav-icon fas fa-truck-loading"></i>
                                        <p>
                                            Laporan Stok
                                            <!-- <span class="right badge badge-danger">New</span> -->
                                        </p>
                                    </a>
                                </li>
                                <?php if ($this->session->userdata('id_distributor') != '10') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('voucher') ?>" class="nav-link">
                                            <i class="nav-icon fas fa-ticket-alt"></i>
                                            <p>
                                                Voucher
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if ($this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'admin_c') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('penjualan') ?>" class="nav-link">
                                            <i class="nav-icon fas fa-archive"></i>
                                            <p>
                                                Penjualan
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon fas fa-file"></i>
                                            <p>
                                                Piutang
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="<?= base_url('piutang') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                                    <p>
                                                        Piutang
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('jatuh-tempo') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                                    <p>
                                                        Piutang Jatuh Tempo
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'superadmin' || $this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'finance') : ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>
                                            Pembayaran
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">

                                        <li class="nav-item">
                                            <a href="<?= base_url('payment') ?>" class="nav-link">
                                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                                <p>
                                                    Rincian Pembayaran
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('payment?hist=1') ?>" class="nav-link">
                                                <i class="nav-icon fas fa-store-alt"></i>
                                                <p>
                                                    Histori Toko
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <?php if ($this->session->userdata('level_user') == 'finance') : ?>
                                            <li class="nav-item">
                                                <a href="<?= base_url('payment-transit') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-money-bill-wave-alt"></i>
                                                    <p>
                                                        Pembayaran Transit
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>

                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>