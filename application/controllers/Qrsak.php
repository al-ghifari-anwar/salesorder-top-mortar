<?php

class Qrsak extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test()
    {
        $qty_qrsak = 48;

        $data['title'] = 'Cetak QR sebanyak ' . $qty_qrsak;
        $data['qty_qrsak'] = $qty_qrsak;

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A3',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'shrink_tables_to_fit' => 0
        ]);
        $html = $this->load->view('Qrsak/Test', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
