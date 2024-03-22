<?php
function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= "Laporan Stok " . $city['nama_city']  ?></title>
</head>

<body>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .border-r {
            border-right: 1px solid black;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            padding: 5px;
        }

        table {
            width: 100%;
        }

        .column {
            float: left;
            width: 50%;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-up {
            vertical-align: top;
        }

        .text-bot {
            vertical-align: bottom;
        }

        .page {
            height: 50%;
        }
    </style>
    <h3 class="text-center"><?= $this->session->userdata('nama_distributor') ?></h3>
    <h1 class="text-center">Laporan Stok <?= $city['nama_city'] ?></h1>
    <h4 class="text-center">Tanggal <?= date('d M, Y', strtotime($dates[0] . " 00:00:00")) . " - " . date('d M, Y', strtotime($dates[1] . " 23:59:59")) ?></h4>
    <table class="border">
        <tr>
            <th class="border">No</th>
            <th class="border">Nama</th>
            <th class="border">Kode Produk</th>
            <th class="border">Jumlah Awal</th>
            <th class="border">Pemasukan</th>
            <th class="border">Pengeluaran</th>
            <th class="border">Jumlah Akhir</th>
        </tr>
        <?php if ($produk != null) : ?>
            <?php
            $no = 1;
            foreach ($produk as $produk) : ?>
                <?php
                $id_produk = $produk['id_produk'];
                $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00"));
                $dateTo = date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59"));
                $id_city = $city['id_city'];

                // Pemasukan
                $pemasukan = $this->db->query("SELECT SUM(jml_stok) AS jml_stok FROM tb_stok WHERE id_produk = '$id_produk' AND created_at > '$dateFrom' AND created_at < '$dateTo'")->row_array();
                // Pengeluaran
                $pengeluaran = $this->db->query("SELECT SUM(qty_produk) AS qty_produk FROM tb_detail_surat_jalan JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan WHERE tb_detail_surat_jalan.id_produk = '$id_produk' AND date_closing > '$dateFrom' AND date_closing < '$dateTo' AND is_closing = 1")->row_array();
                // Jumlah Awal
                $jumlahAwal = $this->db->query("SELECT SUM(jml_stok) AS jml_stok FROM tb_stok WHERE id_produk = '$id_produk' AND created_at < '$dateFrom' ")->row_array();
                // Jumlah Akhir
                $jumlahAkhir = $this->db->query("SELECT SUM(jml_stok) AS jml_stok FROM tb_stok WHERE id_produk = '$id_produk' ")->row_array();
                $totalPengeluaran = $this->db->query("SELECT SUM(qty_produk) AS qty_produk FROM tb_detail_surat_jalan JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan WHERE tb_detail_surat_jalan.id_produk = '$id_produk' AND date_closing > '2024-02-16' ")->row_array();

                $valPemasukan = $pemasukan['jml_stok'] == null ? 0 : $pemasukan['jml_stok'];
                $valPengeluaran = $pengeluaran['qty_produk'] == null ? 0 : $pengeluaran['qty_produk'];
                $valJumlahAwal = $jumlahAwal['jml_stok'] == null ? 0 : $jumlahAwal['jml_stok'];
                $valJumlahAkhir = $jumlahAkhir['jml_stok'] - $totalPengeluaran['qty_produk'];
                ?>
                <tr>
                    <td class="text-center border-r"><?= $no++; ?></td>
                    <td class="text-left border-r"><?= $produk['nama_produk']; ?></td>
                    <td class="text-left border-r"><?= "-" ?></td>
                    <td class="text-center border-r"><?= $valJumlahAwal ?></td>
                    <td class="text-center border-r"><?= $valPemasukan ?></td>
                    <td class="text-center border-r"><?= $valPengeluaran ?></td>
                    <td class="text-center border-r"><?= $valPengeluaran ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>