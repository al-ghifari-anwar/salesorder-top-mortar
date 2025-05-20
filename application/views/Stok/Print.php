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
    <title><?= "Laporan Stok " . $gudang['name_gudang_stok']  ?></title>
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
    <h1 class="text-center">Laporan Stok <?= $gudang['name_gudang_stok'] ?></h1>
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
        <?php if ($masterProduks != null) : ?>
            <?php
            $no = 1;
            foreach ($masterProduks as $masterProduk) : ?>
                <?php
                $id_master_produk = $masterProduk['id_master_produk'];
                $dateFrom = date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00"));
                $dateTo = date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59"));
                $id_gudang_stok = $gudang['id_gudang_stok'];

                // Pemasukan
                // $pemasukan = $this->db->query("SELECT SUM(jml_stok) AS jml_stok FROM tb_stok WHERE id_produk = '$id_produk' AND created_at > '$dateFrom' AND created_at < '$dateTo'")->row_array();
                $this->db->select('SUM(jml_stok) AS jml_stokIn');
                $getStokIn = $this->db->get_where('tb_stok', ['id_gudang_stok' => $id_gudang_stok, 'id_master_produk' => $id_master_produk, 'status_stok' => 'in', 'created_at >' => $dateFrom, 'created_at <' => $dateTo])->row_array();

                // Pengeluaran
                // $pengeluaran = $this->db->query("SELECT SUM(qty_produk) AS qty_produk FROM tb_detail_surat_jalan JOIN tb_surat_jalan ON tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan WHERE tb_detail_surat_jalan.id_produk = '$id_produk' AND date_closing > '$dateFrom' AND date_closing < '$dateTo' AND is_closing = 1")->row_array();
                $getStokData = $this->db->get_where('tb_stok', ['id_gudang_stok' => $id_gudang_stok, 'id_master_produk' => $id_master_produk])->row_array();

                $this->db->where('id_master_produk', $id_master_produk);
                $this->db->where("id_city IN (SELECT id_city FROM tb_city tc WHERE id_gudang_stok = $id_gudang_stok)", NULL, FALSE);
                $getProdukDatas = $this->db->get('tb_produk')->result_array();

                // echo $this->db->last_query();
                // echo json_encode($getProdukDatas);
                // die;

                if ($getProdukDatas != null) {
                    $idProduks = array();
                    foreach ($getProdukDatas as $getProdukData) {
                        $idProduks[] = $getProdukData['id_produk'];
                    }

                    $this->db->select('SUM(qty_produk) AS jml_stokOut');
                    $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
                    $this->db->where_in('id_produk', $idProduks);
                    $getStokOut = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.date_closing >' => $dateFrom, 'tb_surat_jalan.date_closing <' => $dateTo, 'tb_surat_jalan.is_closing' => 1])->row_array();
                } else {
                    $getStokOut = ['jml_stokOut' => 0];
                }

                $dateCutoff = date("Y-m-d H:i:s", strtotime("2025-02-20 00:00:00"));

                // Jumlah Awal
                $this->db->select('SUM(jml_stok) AS jml_stokAwal');
                $jumlahAwal = $this->db->get_where('tb_stok', ['id_master_produk' => $id_master_produk, 'created_at <=' => $dateFrom, 'created_at >=' => $dateCutoff, 'id_gudang_stok' => $id_gudang_stok])->row_array();

                $this->db->where('id_master_produk', $id_master_produk);
                $this->db->where("id_city IN (SELECT id_city FROM tb_city WHERE id_gudang_stok = $id_gudang_stok)", NULL, FALSE);
                $getProdukDatasAwal = $this->db->get('tb_produk')->result_array();

                // echo json_encode($this->db->last_query());
                // die;

                if ($getProdukDatasAwal != null) {
                    $idProduks = array();
                    foreach ($getProdukDatasAwal as $getProdukDataAwal) {
                        $idProduks[] = $getProdukDataAwal['id_produk'];
                    }

                    $this->db->select('SUM(qty_produk) AS jml_stokOut');
                    $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
                    $this->db->where_in('id_produk', $idProduks);
                    $jumlahAwalPengeluaran = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing <' => $dateFrom, 'tb_surat_jalan.is_closing >=' => $dateCutoff, 'tb_surat_jalan.is_closing' => 1])->row_array();

                    echo json_encode($this->db->last_query());
                    die;
                } else {
                    $jumlahAwalPengeluaran = ['jml_stokOut' => 0];
                }

                // Jumlah Akhir
                $this->db->select('SUM(jml_stok) AS jml_stokAkhir');
                $jumlahAkhir = $this->db->get_where('tb_stok', ['id_master_produk' => $id_master_produk, 'id_gudang_stok' => $id_gudang_stok])->row_array();

                $jmlAwal = $jumlahAwal['jml_stokAwal'] != null ? $jumlahAwal['jml_stokAwal'] : 0;
                $jmlKluarAwal = $jumlahAwalPengeluaran['jml_stokOut'] != null ? $jumlahAwalPengeluaran['jml_stokOut'] : 0;
                $valPemasukan = $getStokIn['jml_stokIn'];
                $valPengeluaran = $getStokOut['jml_stokOut'];
                $valJumlahAwal = $jmlAwal - $jmlKluarAwal;
                $valJumlahAkhir = ($valJumlahAwal + $getStokIn['jml_stokIn']) - $getStokOut['jml_stokOut'];
                ?>
                <tr>
                    <td class="text-center border-r"><?= $no++; ?></td>
                    <td class="text-left border-r"><?= $masterProduk['name_master_produk']; ?> (<?= $id_master_produk ?>)</td>
                    <td class="text-left border-r"><?= "-" ?></td>
                    <td class="text-center border-r"><?= $valJumlahAwal ?>(<?= $jmlAwal . "|" . $jmlKluarAwal ?>) </td>
                    <td class="text-center border-r"><?= $valPemasukan ?></td>
                    <td class="text-center border-r"><?= $valPengeluaran ?></td>
                    <td class="text-center border-r"><?= $valJumlahAkhir ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>