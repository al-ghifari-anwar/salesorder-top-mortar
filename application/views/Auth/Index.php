<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> - PT Top Mortar</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>PT</b> Top Mortar</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Login</p>
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


                <form action="<?= base_url('login') ?>" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" name="username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- /.social-auth-links -->

                <!-- <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p>
                <p class="mb-0">
                    <a href="register.html" class="text-center">Register a new membership</a>
                </p> -->
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <button
        id="live-chat-button"
        type="button"
        aria-label="Chat dengan Top Mortar"
        style="position: fixed; bottom: 40px; right: 40px; z-index: 2147483000; cursor: pointer; border: none; border-radius: 18px; padding: 0; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; box-shadow: 0 25px 50px -12px rgba(15, 118, 110, 0.45); overflow: hidden; background: #ffffff;">
        <img
            src="https://www.haloai.co.id/haloai/halo-ai-icon-contact.webp"
            alt="Chat"
            style="width: 100%; height: 100%; object-fit: contain; padding: 12px;" />
    </button>

    <!-- ============ 1) Wadah panel chat ============ -->
    <div
        id="haloai-embed"
        style="position: fixed; bottom: 24px; right: 24px; z-index: 2147483001; display: none; width: 400px; height: 620px; max-width: calc(100vw - 32px); max-height: calc(100vh - 48px);"></div>

    <!-- ============ 2) Loader HaloAI (cukup SATU kali per halaman) ============ -->
    <script
        src="https://www.haloai.co.id/embed/client.js"
        data-channel-id="01a0ec4e-d7a1-75cb-bb06-e9846fb14fcd"
        async></script>

    <!-- ============ 3) Perekat tombol <-> chat ============ -->
    <script>
        (() => {
            const channelId = '01a0ec4e-d7a1-75cb-bb06-e9846fb14fcd';
            const containerId = 'haloai-embed';
            const buttonId = 'live-chat-button';
            const welcomeMessage = '';

            const attach = (api) => {
                if (!api || typeof api.openChatUi !== 'function') {
                    return;
                }
                const button = document.getElementById(buttonId);
                const container = document.getElementById(containerId);
                if (!button || !container || button.dataset.haloaiMounted === 'true') {
                    return;
                }
                button.dataset.haloaiMounted = 'true';

                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    button.style.display = 'none';
                    container.style.display = 'block';
                    api
                        .openChatUi(channelId, {
                            containerId,
                            backgroundColor: '#ffffff',
                            searchParams: {
                                welcomeMessage
                            },
                        })
                        .catch((error) => {
                            console.error('HaloAI Embed: gagal membuka chat.', error);
                            container.style.display = 'none';
                            button.style.display = '';
                        });
                });
            };

            window.addEventListener('message', (event) => {
                const allowedOrigins = [window.location.origin, 'https://haloai.co.id', 'https://www.haloai.co.id'];
                if (!allowedOrigins.includes(event.origin) || !event.data) {
                    return;
                }
                const button = document.getElementById(buttonId);
                const container = document.getElementById(containerId);

                // Pengunjung menutup panel dari dalam iframe.
                if (event.data.type === 'haloai:close' && button && container) {
                    container.style.display = 'none';
                    button.style.display = '';
                }

                // Channel sedang tidak tersedia -> sembunyikan tombolnya.
                if (event.data.type === 'haloai:channel-unavailable' && event.data.channelId === channelId) {
                    if (button) {
                        button.style.display = 'none';
                        button.setAttribute('aria-hidden', 'true');
                    }
                    if (container) {
                        container.style.display = 'none';
                    }
                }
            });

            if (window.HaloAI?.ready) {
                window.HaloAI.ready.then(attach).catch((error) => {
                    console.error('HaloAI Embed: ready promise rejected.', error);
                });
            } else {
                window.addEventListener('haloai:ready', (event) => attach(event?.detail), {
                    once: true
                });
            }
        })();
    </script>


    <!-- jQuery -->
    <script src="<?= base_url('assets') ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets') ?>/dist/js/adminlte.min.js"></script>
</body>

</html>