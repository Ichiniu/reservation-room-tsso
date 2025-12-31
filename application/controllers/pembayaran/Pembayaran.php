<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pembayaran/pembayaran_model');
        $this->load->library('upload');
    }

public function upload_bukti()
{
    $id_pemesanan_raw = (int)$this->input->post('id_pemesanan_raw', TRUE);
    if ($id_pemesanan_raw <= 0) {
        show_error('ID Pemesanan tidak valid.');
        return;
    }

    // Ambil input user
    $atas_nama        = $this->input->post('atas_nama', TRUE);
    $tanggal_transfer = $this->input->post('tanggal_transfer', TRUE);
    $bank_pengirim    = $this->input->post('bank_pengirim', TRUE);

    $nominal_transfer = $this->input->post('nominal_transfer', TRUE);
    $nominal_transfer = preg_replace('/[^0-9]/', '', (string)$nominal_transfer);
    $nominal_transfer = (int)$nominal_transfer;

    if (empty($atas_nama) || empty($tanggal_transfer) || empty($bank_pengirim)) {
        show_error('Atas nama, tanggal transfer, dan bank pengirim wajib diisi.');
        return;
    }
    if ($nominal_transfer <= 0) {
        show_error('Nominal transfer tidak valid.');
        return;
    }

    // Ambil detail pemesanan + harga untuk hitung total (sekali query aja)
    $pesanan = $this->db->select("
            p.ID_PEMESANAN,
            p.TANGGAL_PEMESANAN,
            p.JUMLAH_CATERING,
            g.NAMA_GEDUNG,
            g.HARGA_SEWA,
            c.NAMA_PAKET,
            c.HARGA AS HARGA_CATERING_SATUAN
        ")
        ->from('pemesanan p')
        ->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
        ->join('catering c', 'c.ID_CATERING = p.ID_CATERING', 'left')
        ->where('p.ID_PEMESANAN', $id_pemesanan_raw)
        ->get()
        ->row();

    if (!$pesanan) {
        show_error('Data pemesanan tidak ditemukan.');
        return;
    }

    // Hitung TOTAL TAGIHAN
    $harga_sewa = !empty($pesanan->HARGA_SEWA) ? (int)$pesanan->HARGA_SEWA : 0;
    $harga_catering_satuan = !empty($pesanan->HARGA_CATERING_SATUAN) ? (int)$pesanan->HARGA_CATERING_SATUAN : 0;
    $jumlah_catering = !empty($pesanan->JUMLAH_CATERING) ? (int)$pesanan->JUMLAH_CATERING : 0;

    $total_tagihan = $harga_sewa + ($harga_catering_satuan * $jumlah_catering);

    if ($total_tagihan <= 0) {
        show_error('Total tagihan tidak valid (0). Cek HARGA_SEWA / HARGA catering di DB.');
        return;
    }

    // Upload bukti
    $upload_dir = FCPATH . 'assets/images/client-bukti-pembayaran/';
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0755, true);
    }

    $config = array(
        'upload_path'      => $upload_dir,
        'allowed_types'    => 'jpg|jpeg|png|pdf',
        'max_size'         => 5120,
        'file_name'        => 'bukti_' . $id_pemesanan_raw . '_' . date('Ymd_His'),
        'overwrite'        => false,
        'detect_mime'      => true,
        'file_ext_tolower' => true,
        'remove_spaces'    => true,
    );

    $this->upload->initialize($config);

    if (!$this->upload->do_upload('bukti')) {
        show_error(strip_tags($this->upload->display_errors()));
        return;
    }

    $upload_data   = $this->upload->data();
    $bukti_rel_path = 'assets/images/client-bukti-pembayaran/' . $upload_data['file_name'];

    // Kode pemesanan (kalau belum ada di sistem kamu, pakai default)
    $kode_pemesanan = 'PMSN000';

    // Data insert pembayaran (INI SATU-SATUNYA $data)
    $data = array(
        'ID_PEMESANAN_RAW'   => $id_pemesanan_raw,
        'KODE_PEMESANAN'     => $kode_pemesanan,
        'TANGGAL_PEMESANAN'  => $pesanan->TANGGAL_PEMESANAN,
        'NAMA_GEDUNG'        => $pesanan->NAMA_GEDUNG,
        'NAMA_PAKET'         => !empty($pesanan->NAMA_PAKET) ? $pesanan->NAMA_PAKET : '-',
        'TOTAL_TAGIHAN'      => $total_tagihan,

        'ATAS_NAMA_PENGIRIM' => $atas_nama,
        'TANGGAL_TRANSFER'   => $tanggal_transfer,
        'BANK_PENGIRIM'      => $bank_pengirim,
        'NOMINAL_TRANSFER'   => $nominal_transfer,

        'BUKTI_PATH'         => $bukti_rel_path,
        'BUKTI_NAME'         => $upload_data['file_name'],
        'BUKTI_MIME'         => $upload_data['file_type'],

        'STATUS_VERIF'       => 'PENDING',
    );

    // Simpan ke DB (transaksi)
    $this->db->trans_begin();

    $this->pembayaran_model->insert_pembayaran($data);
    $status_approve_paid = 2;

            $this->db->where('ID_PEMESANAN', $id_pemesanan_raw);
            $this->db->update('pemesanan', array('STATUS' => $status_approve_paid));



    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();

        // hapus file kalau DB gagal
        @unlink(FCPATH . $bukti_rel_path);

        $err = $this->db->error();
        $msg = isset($err['message']) ? $err['message'] : 'unknown';
        show_error('Gagal proses pembayaran: ' . $msg);
        return;
    }

    $this->db->trans_commit();
    redirect('home/pemesanan');
}
}