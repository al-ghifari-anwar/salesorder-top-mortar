<?php

class Tebusmurah extends CI_Controller
{
    public function index()
    {
        $data['contactTebusmurahs'] = $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city')->where('tb_contact.store_status', 'active')->where('tb_contact.id_contact IN (SELECT id_contact FROM tb_surat_jalan WHERE is_tebus_murah = 1)', null, false)->order_by('tb_city.nama_city', 'ASC')->get('tb_contact')->result_array();
        // $data = 1;

        json_encode($data);
        die;
        // $html = $this->load->view('Notif/PrintPassive', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Tebusmurah/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
