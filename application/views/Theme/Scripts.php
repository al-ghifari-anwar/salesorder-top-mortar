<!-- ============ 2) Wadah panel chat ============ -->
<div
    id="haloai-embed"
    style="position: fixed; bottom: 24px; right: 24px; z-index: 2147483001; display: none; width: 400px; height: 620px; max-width: calc(100vw - 32px); max-height: calc(100vh - 48px);"></div>

<!-- ============ 3) Loader HaloAI ============ -->
<script
    src="https://www.haloai.co.id/embed/client.js"
    data-channel-id="01a0ec4e-d7a1-75cb-bb06-e9846fb14fcd"
    async></script>

<!-- REQUIRED SCRIPTS -->

<!-- LIVE CHAT TRIAL -->
<!-- ============ 4) Perekat tombol <-> chat ============ -->
<script>
    (() => {
        const channelId = '01a0ec4e-d7a1-75cb-bb06-e9846fb14fcd';
        const containerId = 'haloai-embed';
        const buttonId = 'live-chat-button';

        // Token identitas dari link WhatsApp. Kosong untuk pengunjung biasa —
        // mereka tetap melihat pre-chat form seperti sebelumnya.
        const identityToken = new URLSearchParams(window.location.search).get('identity') || '';

        // Kalau pengunjung datang membawa token, chat dibuka otomatis: dia sudah
        // menyatakan niat chat waktu mengklik link, jadi jangan suruh klik lagi.
        const autoOpen = identityToken.length > 0;

        const openChat = (api) => {
            const button = document.getElementById(buttonId);
            const container = document.getElementById(containerId);
            if (!button || !container) {
                return;
            }

            button.style.display = 'none';
            container.style.display = 'block';

            const searchParams = {};
            if (identityToken) {
                searchParams.identity = identityToken;
            }

            api
                .openChatUi(channelId, {
                    containerId,
                    backgroundColor: '#ffffff',
                    searchParams
                })
                .catch((error) => {
                    console.error('HaloAI Embed: gagal membuka chat.', error);
                    container.style.display = 'none';
                    button.style.display = '';
                });
        };

        const attach = (api) => {
            if (!api || typeof api.openChatUi !== 'function') {
                return;
            }
            const button = document.getElementById(buttonId);
            if (!button || button.dataset.haloaiMounted === 'true') {
                return;
            }
            button.dataset.haloaiMounted = 'true';

            button.addEventListener('click', (e) => {
                e.preventDefault();
                openChat(api);
            });

            if (autoOpen) {
                openChat(api);
            }
        };

        window.addEventListener('message', (event) => {
            const allowedOrigins = [window.location.origin, 'https://haloai.co.id', 'https://www.haloai.co.id'];
            if (!allowedOrigins.includes(event.origin) || !event.data) {
                return;
            }
            const button = document.getElementById(buttonId);
            const container = document.getElementById(containerId);

            if (event.data.type === 'haloai:close' && button && container) {
                container.style.display = 'none';
                button.style.display = '';
            }

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
<!-- <script src="<?= base_url('assets') ?>/plugins/jquery/jquery.min.js"></script> -->
<!-- Bootstrap 4 -->
<script src="<?= base_url('assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets') ?>/dist/js/adminlte.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?= base_url('assets') ?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url('assets') ?>/plugins/select2/js/select2.full.min.js"></script>
<!-- InputMask -->
<script src="<?= base_url('assets') ?>/plugins/moment/moment.min.js"></script>
<script src="<?= base_url('assets') ?>/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('assets') ?>/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- date-range-picker -->
<script src="<?= base_url('assets') ?>/plugins/daterangepicker/daterangepicker.js"></script>
<!-- bs-custom-file-input -->
<script src="<?= base_url('assets') ?>/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- ChartJS -->
<script src="<?= base_url('assets') ?>/plugins/chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
<!-- Monaco Editor -->
<!-- <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs/loader.js"></script> -->

<script>
    // Tooltip
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(function() {
        bsCustomFileInput.init();
    });
    $(function() {
        $('#table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

    $('#table-no-paging').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    });

    $("#table-print").DataTable({
        "lengthMenu": [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        "paging": true,
        "responsive": true,
        "lengthChange": true,
        "autoWidth": true,
        'footer': true,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#table-print_wrapper .col-md-6:eq(0)');

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
    $('#select2bs4').one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', 'Search...');
    });
    //Initialize Select2 Elements
    $('.select2bs41').select2({
        theme: 'bootstrap4'
    })
    $('#select2bs41').one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', 'Search...');
    });
    //Date range picker
    $('#reservation').daterangepicker()
</script>
<!-- Loading -->
<script>
    $(document).ready(function() {
        $("#loading-screen").fadeOut();

        $("a").on("click", function(e) {
            let target = $(this).attr("href");

            if (
                !target ||
                target === "#" ||
                target.startsWith("javascript") ||
                $(this).attr("data-toggle") === "modal" ||
                $(this).attr("data-target") ||
                $(this).attr("target") === "_blank" ||
                $(this).attr("target") === "__blank"
            ) {
                return;
            }

            // Cek apakah link valid dan bukan '#' atau JavaScript void
            if (target && target !== "#" && !target.startsWith("javascript") && !target.includes('#')) {
                e.preventDefault(); // Cegah perpindahan halaman langsung

                $("#loading-screen").fadeIn(); // Tampilkan loading screen

                setTimeout(function() {
                    window.location.href = target; // Setelah delay, pindah halaman
                }, 300);
            }
        });
    });
    // Solusi untuk masalah "Back" browser: hilangkan loading saat halaman ditampilkan dari cache
    $(window).on("pageshow", function(event) {
        if (event.originalEvent.persisted) {
            console.log("Back button ditekan, menghilangkan loading...");
            $("#loading-screen").fadeOut();
        }
    });
</script>
<!-- Chart Score -->

</body>

</html>