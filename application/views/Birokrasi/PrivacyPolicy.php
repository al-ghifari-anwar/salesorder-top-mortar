<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Kebijakan privasi aplikasi MyTop Sales oleh PT. TOP MORTAR INDONESIA." />
    <meta name="robots" content="index,follow" />
    <title>Kebijakan Privasi — MyTop Sales</title>
    <style>
        :root {
            color-scheme: light;
            font: 16px/1.65 system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #20252b;
            background: #f5f7fa;
        }

        body {
            margin: 0;
        }

        header {
            background: #123c68;
            color: #fff;
            padding: 2.5rem max(1.25rem, calc((100% - 820px)/2));
        }

        main {
            box-sizing: border-box;
            max-width: 820px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px #172b4d12;
        }

        h1,
        h2 {
            line-height: 1.25;
        }

        h1 {
            margin: 0;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
        }

        h2 {
            margin-top: 2rem;
            color: #123c68;
            font-size: 1.25rem;
        }

        a {
            color: #0759a5;
        }

        .meta {
            margin: .5rem 0 0;
        }

        footer {
            max-width: 820px;
            margin: 0 auto;
            padding: 0 1.25rem 2rem;
            color: #59636e;
            font-size: .9rem;
        }

        @media (max-width: 600px) {
            main {
                margin: 1rem;
                padding: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Kebijakan Privasi MyTop Sales</h1>
        <p class="meta">Berlaku mulai 21 November 2026 · Dikelola oleh PT. TOP MORTAR INDONESIA</p>
    </header>
    <main>
        <p>Kebijakan ini menjelaskan cara PT. TOP MORTAR INDONESIA memproses data melalui MyTop Sales untuk operasional sales, kurir, pengiriman, dan absensi.</p>

        <h2>Data yang diproses</h2>
        <ul>
            <li><strong>Data akun pegawai:</strong> username atau ID pengguna, nama, nomor telepon, distributor, kota, peran, dan informasi profil yang disediakan perusahaan.</li>
            <li><strong>Kredensial login:</strong> username dan password yang digunakan untuk masuk ke layanan autentikasi perusahaan. Jangan membagikan password kepada petugas dukungan.</li>
            <li><strong>Lokasi presisi:</strong> digunakan untuk fitur peta, pencarian toko/basecamp, absensi, dan laporan kunjungan. Tracking berulang di latar belakang digunakan selama jam kerja ketika status absensi aktif, dari absen masuk sampai absen pulang.</li>
            <li><strong>Data operasional:</strong> toko/pelanggan, kunjungan, produk, invoice, voucher, surat jalan, pengiriman, dan laporan sesuai fungsi dan peran pengguna.</li>
            <li><strong>Konten pengguna:</strong> pesan teks, media chat, foto bukti pengiriman, dan catatan dalam laporan/checklist.</li>
            <li><strong>Data teknis:</strong> perusahaan menyatakan tidak membuat log teknis aplikasi. Firebase, Google Maps, perangkat, atau penyedia hosting dapat memproses data operasional/diagnostik sesuai fungsi layanan mereka.</li>
        </ul>

        <h2>Tujuan dan izin</h2>
        <p>Data digunakan untuk autentikasi, administrasi pegawai, absensi, validasi lokasi, penugasan kunjungan/pengiriman, bukti serah-terima, invoice, komunikasi operasional, peta, dukungan, dan keamanan layanan. Anda dapat mengatur atau mencabut izin lokasi, kamera, dan media di pengaturan perangkat. Fitur yang memerlukan izin tersebut mungkin tidak tersedia jika izin ditolak.</p>

        <h2>Penyedia layanan dan lokasi pemrosesan</h2>
        <ul>
            <li>Backend/API dan server media perusahaan menggunakan layanan IDCloudHost; lokasi server yang diinformasikan perusahaan adalah Singapura.</li>
            <li>Firebase Realtime Database berada di region <code>us-central1</code>, Amerika Serikat. Layanan Firebase/Google lain digunakan sesuai fitur aplikasi.</li>
            <li>Google Maps/Places digunakan untuk peta dan pencarian lokasi.</li>
            <li>Tim backend internal mengelola integrasi backend. Perhitungan rute dapat menggunakan OSRM (Open Source Routing Machine), perangkat lunak open source berlisensi BSD 2-Clause.</li>
        </ul>
        <p>Atribusi perangkat lunak routing: “© OSRM. OSRM is free, open source, and available under the very permissive (simplified) 2-clause BSD license.” Atribusi ini menjelaskan lisensi perangkat lunak, bukan pemilik domain atau operator server endpoint.</p>
        <p>Pemrosesan oleh penyedia layanan dapat berlangsung di negara tempat infrastrukturnya berada. Informasi mengenai lokasi server perusahaan dan Firebase di atas berdasarkan konfigurasi yang disampaikan perusahaan.</p>

        <h2>Penyimpanan, retensi, dan penghapusan</h2>
        <p>Informasi backend yang disampaikan perusahaan menyebut data lokasi, absensi, foto bukti, chat, laporan, dan pengiriman dapat disimpan tanpa batas waktu yang ditentukan. Arsip yang wajib disimpan berdasarkan persetujuan Finance/legal disimpan di perusahaan di Indonesia.</p>
        <p>Jika akun dihapus, row akun dan identifier personal seperti <code>id_user</code> serta nama dihapus atau dianonimkan secara permanen dari record terkait. Record operasional yang dipertahankan tidak lagi memiliki relasi database ke identitas pegawai; pada tampilan, pembuat record dapat ditunjukkan sebagai “Anonim”.</p>
        <p>Anda dapat menghapus akun dari Profil Pengguna di aplikasi. Jika tidak dapat masuk, ajukan melalui <a href="https://order.topmortarindonesia.com/account-deletion/">halaman penghapusan akun</a> atau email <a href="mailto:topmortardev@gmail.com">topmortardev@gmail.com</a>. Permintaan akan diverifikasi. Target layanan yang diusulkan adalah konfirmasi dalam 3 hari kerja dan penyelesaian dalam 30 hari kalender setelah verifikasi. Jika ada kendala atau record yang wajib disimpan, kami akan menyampaikan alasannya dan memberi pembaruan.</p>

        <h2>Keamanan dan hak Anda</h2>
        <p>Perusahaan menerapkan koneksi HTTPS untuk komunikasi aplikasi dan mengelola akses ke layanan backend bersama penyedia hosting. Anda dapat menghubungi kami untuk pertanyaan atau permintaan terkait data pribadi. Kami akan memproses permintaan sesuai hukum yang berlaku dan kewajiban penyimpanan yang sah.</p>

        <h2>Pengelola dan kontak</h2>
        <address>
            PT. TOP MORTAR INDONESIA<br />
            Pergudangan Bizpoint Block F No 50, Cikupa, Tangerang<br />
            Email: <a href="mailto:topmortardev@gmail.com">topmortardev@gmail.com</a>
        </address>
        <p>Kebijakan ini dapat diperbarui ketika fitur, penyedia, atau praktik pemrosesan berubah. Versi terbaru tersedia di URL halaman ini.</p>
    </main>
    <footer><a href="https://order.topmortarindonesia.com/account-deletion/">Informasi dan permintaan penghapusan akun</a></footer>
</body>

</html>