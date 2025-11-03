<body class="hold-transition sidebar-mini">
    <div id="loading-screen">
        <div class="loader"></div>
        <br>
        <div class="row">
            <div class="col">
                <h5>&nbsp;&nbsp;Sedang mengambil data...</h5>
            </div>
        </div>
    </div>

    <?php
    $getCompany = $this->db->get_where('tb_company', ['id_distributor' => $this->session->userdata('id_distributor')])->row_array();
    ?>
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <?php if ($this->session->userdata('level_user') != null) : ?>
                <!-- https://dev-order.topmortarindonesia.com/ -->
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <!-- <li class="nav-item d-none d-sm-inline-block">
                        <a href="index3.html" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link">Contact</a>
                    </li> -->
                </ul>

                <?php if (base_url() == "https://dev-order.topmortarindonesia.com/" || base_url() == "http://localhost/salesorder/"): ?>
                    <a href="#" class="btn btn-warning"><i class="fas fa-exclamation-triangle"></i>&nbsp; Development Site. Semua data hanya dummy. <b>[Versi Database: 5 Juli 2025]</b></a>
                <?php endif; ?>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">

                    <!-- Tagihan Dropdown Menu -->
                    <?php if ($this->session->userdata('level_user') == 'finance'): ?>
                        <li class="nav-item dropdown">
                            <?php
                            $tagihans = $this->db->get_where('tb_tagihan', ['id_distributor' => $this->session->userdata('id_distributor'), 'status_tagihan' => 'Waiting'])->result_array();

                            $countTagihan = count($tagihans);
                            ?>
                            <a class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span class="badge badge-danger navbar-badge"><?= $countTagihan ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                                <span class="dropdown-header">Tagihan Anda</span>
                                <div class="dropdown-divider"></div>
                                <?php foreach ($tagihans as $tagihan): ?>
                                    <a href="<?= base_url('tagihan/print/') . $tagihan['id_tagihan'] ?>" class="dropdown-item" target="_blank">
                                        <i class="fas fa-file-invoice-dollar mr-2"></i> #<?= $tagihan['no_tagihan'] ?>
                                        <span class="float-right text-muted text-sm"><?= date("d F Y", strtotime($tagihan['date_tagihan'])) ?></span>
                                    </a>
                                <?php endforeach; ?>
                                <div class="dropdown-divider"></div>
                                <a href="<?= base_url('tagihan') ?>" class="dropdown-item dropdown-footer">Lihat Semua Tagihan</a>
                            </div>
                        </li>
                    <?php endif; ?>
                    <!-- Menu Top Seller -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-shopping-basket"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                            <span class="dropdown-header">Menu Setting Top Seller</span>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('topseller/setting/global') ?>" class="dropdown-item">
                                <i class="fas fa-cogs mr-2"></i> Global
                            </a>
                            <a href="<?= base_url('topseller/setting/discount') ?>" class="dropdown-item">
                                <i class="fas fa-percentage mr-2"></i> Diskon Aplikasi
                            </a>
                        </div>
                    </li>

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

                </ul>
            <?php endif; ?>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-light-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url() ?>" class="brand-link">
                <?php if ($this->session->userdata('id_user') != null): ?>
                    <img src="<?= $getCompany ? base_url('assets/img/company_img/') . $getCompany['img_company'] :
                                    '-' ?>" alt="Logo" class="brand-image" style="opacity: .8">
                <?php endif; ?>
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
                                <a href="<?= base_url() ?>" class="nav-link <?= $menu == 'Dashboard' ? 'active' : '' ?>">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>
                                        Dashboard
                                    </p>
                                </a>
                            </li>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance' || $this->session->userdata('level_user') == 'marketing'): ?>
                                <!-- <li class="nav-item">
                                    <a href="<?= base_url('topevent') ?>" class="nav-link">
                                        <i class="nav-icon fas fa-trophy"></i>
                                        <p>
                                            Event Lomba
                                        </p>
                                    </a>
                                </li> -->
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'scanner'): ?>
                                <li class="nav-item <?= $menuGroup == 'ScanQrUangTunai' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'ScanQrUangTunai' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-qrcode"></i>
                                        <p>
                                            Scan QR Uang Tunai
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('scanqrsak') ?>" class="nav-link <?= $menu == 'Scanqrsak' ? 'active' : '' ?>">
                                                <i class="fas fa-qrcode nav-icon"></i>
                                                <p>Scan</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance'): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('logbca') ?>" class="nav-link <?= $menu == 'LogBCA' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-money-bill-wave"></i>
                                        <p>
                                            Log BCA
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'stok'): ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-box"></i>
                                        <p>
                                            Stok
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('sjstok') ?>" class="nav-link">
                                                <i class="fas fa-truck-loading nav-icon"></i>
                                                <p>Tambah Stok</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('stok') ?>" class="nav-link">
                                                <i class="nav-icon fas fa-file"></i>
                                                <p>
                                                    Laporan Stok
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'finance'): ?>
                                <li class="nav-item <?= $menuGroup == 'Setting' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Setting' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-cog"></i>
                                        <p>
                                            Setting
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('company') ?>" class="nav-link <?= $menu == 'Company' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-building"></i>
                                                <p>
                                                    Company Data
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('qontak') ?>" class="nav-link <?= $menu == 'Qontak' ? 'active' : '' ?>">
                                                <i class="nav-icon fab fa-whatsapp"></i>
                                                <p>
                                                    Qontak
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item <?= $menuGroup == 'Data' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Data' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-database"></i>
                                        <p>
                                            Data
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('satuan') ?>" class="nav-link <?= $menu == 'Satuan' ? 'active' : '' ?>">
                                                <i class="fas fa-layer-group nav-icon"></i>
                                                <p>Satuan</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('city') ?>" class="nav-link <?= $menu == 'Kota' ? 'active' : '' ?>">
                                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                                <p>Kota</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('kendaraan') ?>" class="nav-link <?= $menu == 'Kendaraan' ? 'active' : '' ?>">
                                                <i class="fas fa-truck nav-icon"></i>
                                                <p>Kendaraan</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('masterproduk') ?>" class="nav-link <?= $menu == 'MasterProduk' ? 'active' : '' ?>">
                                                <i class="fas fa-tag nav-icon"></i>
                                                <p>Master Produk</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('gudang') ?>" class="nav-link <?= $menu == 'Gudang' ? 'active' : '' ?>">
                                                <i class="fas fa-warehouse nav-icon"></i>
                                                <p>Gudang</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('produk') ?>" class="nav-link <?= $menu == 'ProdukPerKota' ? 'active' : '' ?>">
                                                <i class="fas fa-shopping-basket nav-icon"></i>
                                                <p>Produk Per Kota</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('toko') ?>" class="nav-link <?= $menu == 'Toko' ? 'active' : '' ?>">
                                                <i class="fas fa-store nav-icon"></i>
                                                <p>Toko</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('tukang') ?>" class="nav-link <?= $menu == 'Tukang' ? 'active' : '' ?>">
                                                <i class="fas fa-user-astronaut nav-icon"></i>
                                                <p>Tukang</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item <?= $menuGroup == 'Stok' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Stok' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-box"></i>
                                        <p>
                                            Stok
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('sjstok') ?>" class="nav-link <?= $menu == 'Sjstok' ? 'active' : '' ?>">
                                                <i class="fas fa-truck-loading nav-icon"></i>
                                                <p>Tambah Stok</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('stok') ?>" class="nav-link <?= $menu == 'Stok' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file"></i>
                                                <p>
                                                    Laporan Stok
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'salesleader') : ?>
                                <li class="nav-item <?= $menuGroup == 'InputRenvi' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'InputRenvi' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-calendar-plus"></i>
                                        <p>
                                            Input Rencana Visit
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('renvis') ?>" class="nav-link <?= $menu == 'Renvis' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-calendar-plus"></i>
                                                <p>
                                                    Rencana Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('renvimg') ?>" class="nav-link <?= $menu == 'Renvimg' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-calendar-plus"></i>
                                                <p>
                                                    Renvi MG
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('absen') ?>" class="nav-link <?= $menu == 'Absen' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-calendar-check"></i>
                                        <p>
                                            Absen Harian
                                            <!-- <span class="right badge badge-danger">New</span> -->
                                        </p>
                                    </a>
                                </li>
                            <?php endif ?>

                            <?php if ($this->session->userdata('level_user') == 'superadmin') : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('distributor') ?>" class="nav-link <?= $menu == 'Distributor' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-warehouse"></i>
                                        <p>
                                            Distributor
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance') : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('prioritystore') ?>" class="nav-link <?= $menu == 'Priority' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-medal"></i>
                                        <p>
                                            Top Mortar Priority
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance') : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('tokopromostore') ?>" class="nav-link <?= $menu == 'TokoPromo' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-medal"></i>
                                        <p>
                                            Top Mortar Toko Promo
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'finance' || $this->session->userdata('level_user') == 'marketing') : ?>
                                <li class="nav-item <?= $menuGroup == 'TopSeller' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'TopSeller' ? 'active' : '' ?>">
                                        <i class="nav-icon fab fa-google-play"></i>
                                        <p>
                                            Top Seller
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('merchandise') ?>" class="nav-link <?= $menu == 'MerchPoin' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-box"></i>
                                                <p>
                                                    Merchandise Poin
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('akunseller') ?>" class="nav-link <?= $menu == 'AkunSeller' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-user-check"></i>
                                                <p>
                                                    Akun Top Seller
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('akunseller/penukaran') ?>" class="nav-link <?= $menu == 'Penukaran' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-ticket-alt"></i>
                                                <p>
                                                    Rekap Penukaran
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('akunseller/datatukang') ?>" class="nav-link <?= $menu == 'ValidasiTukang' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-user-shield"></i>
                                                <p>
                                                    Validasi Data Tukang
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('promoseller') ?>" class="nav-link <?= $menu == 'Promo' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file-image"></i>
                                                <p>
                                                    Banner Promo
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('kontenseller') ?>" class="nav-link <?= $menu == 'Konten' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-images"></i>
                                                <p>
                                                    Banner Konten
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('sebarvctukang') ?>" class="nav-link <?= $menu == 'Sebarvctukang' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-ticket-alt"></i>
                                                <p>
                                                    Sebar Voucher Tukang
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance' || $this->session->userdata('level_user') == 'salesleader'): ?>
                                <li class="nav-item <?= $menuGroup == 'Analisa' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Analisa' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-chart-line"></i>
                                        <p>
                                            Analisa
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('analisa/passive') ?>" class="nav-link <?= $menu == 'PassiveRutin' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-money-check"></i>
                                                <p>
                                                    Passive Bayar Rutin
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('analisa/order5') ?>" class="nav-link <?= $menu == 'Passive5' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-history"></i>
                                                <p>
                                                    Passive Order > 5
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('analisa/active') ?>" class="nav-link <?= $menu == 'ActiveRutin' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-money-check"></i>
                                                <p>
                                                    Active Bayar Rutin
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('scoring') ?>" class="nav-link <?= $menu == 'Scoring' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-percentage"></i>
                                                <p>
                                                    Scoring Toko
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('badscore') ?>" class="nav-link <?= $menu == 'Badscore' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-store-alt-slash"></i>
                                                <p>
                                                    Toko Skor Rendah
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('notif') ?>" class="nav-link <?= $menu == 'Notif' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-store-alt-slash"></i>
                                                <p>
                                                    Laporan Toko Passive
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('report-active-zero') ?>" class="nav-link <?= $menu == 'ReportActiveZero' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-money-check"></i>
                                                <p>
                                                    Toko Pasif Penjualan Rp 0
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item <?= $menuGroup == 'AnalisaTukang' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'AnalisaTukang' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-user-astronaut"></i>
                                        <p>
                                            Analisa Tukang
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('analisatukang/user') ?>" class="nav-link <?= $menu == 'SPG' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-user-cog"></i>
                                                <p>
                                                    Admin Lapangan / SPG
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('analisatukang/laporan') ?>" class="nav-link <?= $menu == 'Rekap' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-chart-area"></i>
                                                <p>
                                                    Rekap Target Tukang
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('id_distributor') == 4) : ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('proyek') ?>" class="nav-link">
                                        <i class="nav-icon fas fa-project-diagram"></i>
                                        <p>
                                            Proyek
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <!-- <li class="nav-header">Data</li> -->
                            <?php if ($this->session->userdata('level_user') == 'marketing' || $this->session->userdata('level_user') == 'admin') : ?>
                                <li class="nav-item <?= $menuGroup == 'Marketing' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Marketing' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-shopping-bag"></i>
                                        <p>
                                            Marketing
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item ">
                                            <a href="<?= base_url('kontenmsg') ?>" class="nav-link <?= $menu == 'Kontenmsg' ? 'active' : '' ?>">
                                                <i class="fas fa-user-astronaut nav-icon"></i>
                                                <p>Konten</p>
                                            </a>
                                        </li>
                                        <li class="nav-item ">
                                            <a href="<?= base_url('marketing/tukang') ?>" class="nav-link <?= $menu == 'BlastTukang' ? 'active' : '' ?>">
                                                <i class="fas fa-user-astronaut nav-icon"></i>
                                                <p>Blast Tukang</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('marketing/rekap/tukang') ?>" class="nav-link <?= $menu == 'RekapBlast' ? 'active' : '' ?>">
                                                <i class="fas fa-list-alt nav-icon"></i>
                                                <p>Rekap Blast Tukang</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('qrsak') ?>" class="nav-link <?= $menu == 'Qrsak' ? 'active' : '' ?>">
                                                <i class="fas fa-qrcode nav-icon"></i>
                                                <p>QR dalam Sak</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('confirmqrsak') ?>" class="nav-link <?= $menu == 'Confirmqrsak' ? 'active' : '' ?>">
                                                <i class="fas fa-qrcode nav-icon"></i>
                                                <p>Scan QR Pertama</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'admin') : ?>
                                <?php if ($this->session->userdata('id_distributor') == '1') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('switchtf') ?>" class="nav-link <?= $menu == 'Switchtf' ? 'active' : '' ?>">
                                            <i class="nav-icon fas fa-money-bill-wave"></i>
                                            <p>
                                                Switch Auto Transfer
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'superadmin' || $this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'finance' || $this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'courier') : ?>

                                <li class="nav-item <?= $menuGroup == 'SJ' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'SJ' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>
                                            Surat Jalan
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('apporder') ?>" class="nav-link <?= $menu == 'Apporder' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-shopping-bag"></i>
                                                <p>
                                                    Pesanan Top Seller
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('surat-jalan') ?>" class="nav-link  <?= $menu == 'SJ' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-th"></i>
                                                <p>
                                                    Surat Jalan
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('sj-not-closing') ?>" class="nav-link <?= $menu == 'NotClosing' ? 'active' : '' ?>">
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

                                <li class="nav-item  <?= $menuGroup == 'Invoice' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link  <?= $menuGroup == 'Invoice' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-file-invoice"></i>
                                        <p>
                                            Invoice
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('invoice') ?>" class="nav-link  <?= $menu == 'Invoice' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file-invoice"></i>
                                                <p>
                                                    Invoice
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('invoice/sent') ?>" class="nav-link  <?= $menu == 'SentInvoice' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-train"></i>
                                                <p>
                                                    Sent Invoice
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rep-invoice/0') ?>" class="nav-link  <?= $menu == 'RekapInvoice' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file-archive"></i>
                                                <p>
                                                    Rekap Invoice
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('invoicecod/waiting/') ?>" class="nav-link  <?= $menu == 'CodBlmLunas' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file-archive"></i>
                                                <p>
                                                    Invoice COD Belum Lunas
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item <?= $menuGroup == 'Visit' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Visit' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-map-marked"></i>
                                        <p>
                                            Visit
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('feerenvi') ?>" class="nav-link <?= $menu == 'TotalRenvi' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-user-clock"></i>
                                                <p>
                                                    Total Renvi
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('cusvisit') ?>" class="nav-link <?= $menu == 'CusVisit' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-business-time"></i>
                                                <p>
                                                    Customer Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('manualvisit') ?>" class="nav-link <?= $menu == 'ManualVisit' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-map-marker"></i>
                                                <p>
                                                    Manual Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('jadwalvisit') ?>" class="nav-link <?= $menu == 'JadwalVisit' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-calendar"></i>
                                                <p>
                                                    Jadwal Visit
                                                    <span class="right badge badge-success">New</span>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('tokonocluster') ?>" class="nav-link <?= $menu == 'TokoNoCluster' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-map-marked"></i>
                                                <p>
                                                    Toko Tanpa Cluster
                                                    <span class="right badge badge-success">New</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item <?= $menuGroup == 'Sales' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Sales' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>
                                            Sales
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('checklist-visit') ?>" class="nav-link <?= $menu == 'ChecklistVisit' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-question"></i>
                                                <p>
                                                    Checklist Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('visit') ?>" class="nav-link <?= $menu == 'Visit' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-location-arrow"></i>
                                                <p>
                                                    Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rencana-visit') ?>" class="nav-link <?= $menu == 'RencanaVisit' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-map-marked"></i>
                                                <p>
                                                    Rencana Visit
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rekap-sales') ?>" class="nav-link <?= $menu == 'Fee' ? 'active' : '' ?>">
                                                <i class="nav-icon fab fa-paypal"></i>
                                                <p>
                                                    Fee
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rekap-status') ?>" class="nav-link <?= $menu == 'RekapStatus' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-info-circle"></i>
                                                <p>
                                                    Rekap Detail Status
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('rekaptokobaru') ?>" class="nav-link <?= $menu == 'RekapTokoBaru' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-newspaper"></i>
                                                <p>
                                                    Rekap Toko Baru
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php if ($this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'admin_c') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('lap-kurir') ?>" class="nav-link <?= $menu == 'LapKurir' ? 'active' : '' ?>">
                                            <i class="nav-icon fas fa-truck-moving"></i>
                                            <p>
                                                Laporan Kurir
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($this->session->userdata('id_distributor') != '10') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('voucher') ?>" class="nav-link <?= $menu == 'Voucher' ? 'active' : '' ?>">
                                            <i class="nav-icon fas fa-ticket-alt"></i>
                                            <p>
                                                Voucher
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if ($this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'admin' || $this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'finance') : ?>
                                    <li class="nav-item">
                                        <a href="<?= base_url('penjualan') ?>" class="nav-link <?= $menu == 'Penjualan' ? 'active' : '' ?>">
                                            <i class="nav-icon fas fa-archive"></i>
                                            <p>
                                                Penjualan
                                                <!-- <span class="right badge badge-danger">New</span> -->
                                            </p>
                                        </a>
                                    </li>

                                    <li class="nav-item <?= $menuGroup == 'Piutang' ? 'menu-open' : '' ?>">
                                        <a href="#" class="nav-link <?= $menuGroup == 'Piutang' ? 'active' : '' ?>">
                                            <i class="nav-icon fas fa-file"></i>
                                            <p>
                                                Piutang
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="<?= base_url('rekap-piutang') ?>" class="nav-link <?= $menu == 'RekapPiutang' ? 'active' : '' ?>">
                                                    <i class="nav-icon fas fa-paperclip"></i>
                                                    <p>
                                                        Rekap
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('piutang') ?>" class="nav-link <?= $menu == 'Piutang' ? 'active' : '' ?>">
                                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                                    <p>
                                                        Piutang
                                                        <!-- <span class="right badge badge-danger">New</span> -->
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= base_url('jatuh-tempo') ?>" class="nav-link <?= $menu == 'PiutangJatem' ? 'active' : '' ?>">
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
                                <li class="nav-item <?= $menuGroup == 'Pembayaran' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'Pembayaran' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>
                                            Pembayaran
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">

                                        <li class="nav-item">
                                            <a href="<?= base_url('payment') ?>" class="nav-link <?= $menu == 'RincianPembayaran' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                                <p>
                                                    Rincian Pembayaran
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= base_url('payment?hist=1') ?>" class="nav-link <?= $menu == 'HistoryPembayaran' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-store-alt"></i>
                                                <p>
                                                    Histori Toko
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                        <?php if ($this->session->userdata('level_user') == 'finance' || $this->session->userdata('level_user') == 'finance_c') : ?>
                                            <li class="nav-item">
                                                <a href="<?= base_url('payment-transit') ?>" class="nav-link <?= $menu == 'Transit' ? 'active' : '' ?>">
                                                    <i class="nav-icon fas fa-money-bill-wave-alt"></i>
                                                    <p>
                                                        Pembayaran Transit
                                                    </p>
                                                </a>
                                            </li>
                                            <!-- <li class="nav-item">
                                                <a href="<?= base_url('autotransfertest') ?>" class="nav-link">
                                                    <i class="nav-icon fas fa-money-bill-wave-alt"></i>
                                                    <p>
                                                        Laporan Test Auto Transfer
                                                    </p>
                                                </a>
                                            </li> -->
                                        <?php endif; ?>
                                    </ul>
                                </li>

                            <?php endif; ?>
                            <?php if ($this->session->userdata('level_user') == 'sales' || $this->session->userdata('level_user') == 'penagihan'): ?>
                                <li class="nav-item <?= $menuGroup == 'ChecklistRenvi' ? 'menu-open' : '' ?>">
                                    <a href="#" class="nav-link <?= $menuGroup == 'ChecklistRenvi' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-file"></i>
                                        <p>
                                            Checklist Renvi
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="<?= base_url('checklistrenvi') ?>" class="nav-link <?= $menu == 'ChecklistRenvi' ? 'active' : '' ?>">
                                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                                <p>
                                                    Renvi
                                                    <!-- <span class="right badge badge-danger">New</span> -->
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= base_url('jadwalvisit') ?>" class="nav-link <?= $menu == 'JadwalVisit' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-calendar"></i>
                                        <p>
                                            Jadwal Visit
                                            <span class="right badge badge-success">New</span>
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>