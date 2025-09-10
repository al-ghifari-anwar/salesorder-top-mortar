<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use claviska\SimpleImage;

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

        $images = array();

        for ($i = 0; $i < $qty_qrsak; $i++) {
            $qrImage = $this->testImage($i);

            array_push($images, $qrImage);
        }

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

        $data['images'] = $images;

        $html = $this->load->view('Qrsak/Test', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function testImage($number)
    {
        // Read Logo File
        $logoPath = FCPATH . "./assets/img/logo_retina.png";
        $logoImageReader = new SimpleImage();
        $logoImageReader->fromFile($logoPath)->bestFit(100, 100);
        // Next, create a slightly larger image,
        // fill it with a rounded white square,
        // and overlay the resized logo
        $logoImageBuilder = new SimpleImage();
        $logoImageBuilder->fromNew(110, 110)->roundedRectangle(0, 0, 210, 210, 10, 'white', 'filled')->overlay($logoImageReader);

        $logoData = $logoImageBuilder->toDataUri('image/png', 100);

        // Generate QR
        $image_name = 'QRSAK_' . $number . '_' . date("YmdHis") . '.png'; //buat name dari qr code sesuai dengan nim

        $qrContent = base_url('qrsak/') . md5("Top" . md5(uniqid() . time()));

        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrContent)
            ->size(500)
            ->logoPath($logoData)
            ->logoResizeToWidth(100)
            ->encoding(new Encoding('ISO-8859-1'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->build()
            ->saveToFile(FCPATH . "./assets/img/qrsak/" . $image_name);

        $qrPath = FCPATH . "./assets/img/qrsak/" . $image_name;
        $qrImageLoader = new SimpleImage();
        $qrImageLoader->fromFile($qrPath)->resize(400, 400);

        // Set Frame
        $frameBuilder = new SimpleImage();
        $frameBuilder->fromFile(FCPATH . "./assets/img/qrsak/frame.jpg")
            ->autoOrient()
            ->overlay($qrImageLoader, 'center', 1, 0, 0)
            ->toFile(FCPATH . "./assets/img/qrsak/framed_" . $image_name, 'image/png');

        $result = [
            'img_name' => $image_name,
            'img_framed_name' => 'framed_' . $image_name,
            'qr_content' => $qrContent,
        ];

        return $result;
    }
}
