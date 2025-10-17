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
        $this->load->model('MQrsak');
        $this->load->model('MQrsakDetail');
        $this->load->model('MUser');
    }

    public function index()
    {
        $data['title'] = 'Program QR Uang Tunai';
        $data['menuGroup'] = 'Marketing';
        $data['menu'] = 'Program QR Uang Tunai';

        $data['qrsaks'] = $this->MQrsak->get();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Qrsak/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $jmlPage = $post['jml_page'];
        $jmlQr = $jmlPage * 48;

        $path = FCPATH . 'assets/pdf/qrsak/';

        $filename = 'qrsak_' . date('Ymd_His') . '_' . uniqid() . '.pdf';
        $filepath = $path . $filename;

        $data['title'] = 'QR#' . $filename;

        $qrsakData = [
            'id_distributor' => $this->session->userdata('id_distributor'),
            'code_qrsak' => 'qrsak_' . date('Ymd_His') . '_' . uniqid(),
            'qty_qrsak' => $jmlQr,
            'pdf_qrsak' => $filename,
            'created_user' => $this->session->userdata('id_user')
        ];

        $save = $this->MQrsak->create($qrsakData);

        if (!$save) {
            $this->session->set_flashdata('failed', "Gagal menyimpan data!");
            redirect('qrsak');
        } else {
            $id_qrsak = $this->db->insert_id();

            $images = array();

            for ($i = 0; $i < $jmlQr; $i++) {
                $qrImage = $this->generateImage($i);

                $qrsakDetailData = [
                    'id_qrsak' => $id_qrsak,
                    'code_qrsak_detail' => $qrImage['qr_content'],
                    'batch_qrsak_detail' => '',
                ];

                $this->MQrsakDetail->create($qrsakDetailData);

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

            $html = $this->load->view('Qrsak/Print', $data, true);
            $mpdf->AddPage('L');
            $mpdf->WriteHTML($html);
            $mpdf->Output($filepath, \Mpdf\Output\Destination::FILE);
            // $mpdf->Output();

            foreach ($images as $image) {
                unlink(FCPATH . 'assets/img/qrsak/' . $image['img_name']);
                unlink(FCPATH . 'assets/img/qrsak/' . $image['img_framed_name']);
            }

            // redirect(base_url('assets/pdf/qrsak/' . $filename));
            $this->session->set_flashdata('success', "Berhasil membuat QR!");
            redirect('qrsak');
        }
    }

    public function generateImage($code)
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
        $image_name = 'QRSAK_' . $code . '_' . date("YmdHis") . '.png'; //buat name dari qr code sesuai dengan nim

        $qrContent = 'https://qrpromo.topmortarindonesia.com/redeem/' . md5("Top" . md5(uniqid() . time()));

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
