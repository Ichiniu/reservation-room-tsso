<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pembayaran/pembayaran_model');
        $this->load->library('upload');
    }

    public function index()
    {
        // wajib POST dari form
        $id_pemesanan_raw = (int)$this->input->post('id_pemesanan_raw', TRUE);
        if ($id_pemesanan_raw <= 0) {
            show_error('ID Pemesanan tidak valid.');
            return;
        }

        $upload_path = FCPATH . 'assets/images/client-bukti-pembayaran/';
        $image_path  = base_url('assets/images/client-bukti-pembayaran/');

        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png',
            'max_size'      => 2048,
            'file_name'     => 'client-trf_' . date('dmY_His'),
            'overwrite'     => false,
        ];
        $this->upload->initialize($config);

        // input file di view kamu namanya: bukti
        if (!$this->upload->do_upload('bukti')) {
            show_error(strip_tags($this->upload->display_errors()));
            return;
        }

        $upload_data = $this->upload->data();

        $data = [
            'ID_PEMESANAN'     => $id_pemesanan_raw,
            'ATAS_NAMA'        => $this->input->post('atas_nama', TRUE),
            'NOMINAL_TRANSFER' => (int)$this->input->post('nominal_transfer', TRUE),
            'BANK_PENGIRIM'    => $this->input->post('bank_pengirim', TRUE),
            'TANGGAL_TRANSFER' => $this->input->post('tanggal_transfer', TRUE),
            'FLAG'             => 0,
            'PATH'             => $image_path,
            'IMG_NAME'         => $upload_data['file_name'],
        ];

        $this->pembayaran_model->insert_pembayaran($data);

        // debug kalau insert gagal
        if ($this->db->affected_rows() <= 0) {
            $err = $this->db->error();
            show_error('Gagal insert pembayaran: ' . ($err['message'] ?? 'unknown'));
            return;
        }

        redirect('home/pemesanan');
    }
}
