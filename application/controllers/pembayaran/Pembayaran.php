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

    // Ambil detail pemesanan (buat isi kolom pembayaran yg wajib)
   // Ambil detail pemesanan berdasarkan ID numeric
$pesanan = $this->db->select('p.ID_PEMESANAN, p.TANGGAL_PEMESANAN, p.ID_GEDUNG, p.ID_CATERING,
                              g.NAMA_GEDUNG, c.NAMA_PAKET')
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

    $upload_data = $this->upload->data();

    // Path yang disimpan ke DB (disarankan RELATIVE path)
    $bukti_rel_path = 'assets/images/client-bukti-pembayaran/' . $upload_data['file_name'];

    // Total tagihan dari form (sudah kamu set dari TOTAL + pajak)
 // Ambil detail pemesanan + harga untuk hitung total
$pesanan = $this->db->select('p.ID_PEMESANAN, p.TANGGAL_PEMESANAN, p.JUMLAH_CATERING,
                              g.NAMA_GEDUNG, g.HARGA_SEWA,
                              c.NAMA_PAKET, c.HARGA AS HARGA_CATERING_SATUAN')
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

$harga_sewa = (int)$pesanan->HARGA_SEWA;
$harga_catering_satuan = !empty($pesanan->HARGA_CATERING_SATUAN) ? (int)$pesanan->HARGA_CATERING_SATUAN : 0;
$jumlah_catering = !empty($pesanan->JUMLAH_CATERING) ? (int)$pesanan->JUMLAH_CATERING : 0;

$harga_catering_total = $harga_catering_satuan * $jumlah_catering;
$total_tagihan = $harga_sewa + $harga_catering_total;

// nominal transfer dari input user
$nominal_transfer = (int)$this->input->post('nominal_transfer', TRUE);

$data = array(
    'ID_PEMESANAN_RAW'   => $id_pemesanan_raw,
    'KODE_PEMESANAN'     => 'PMSN000',
    'TANGGAL_PEMESANAN'  => $pesanan->TANGGAL_PEMESANAN,
    'NAMA_GEDUNG'        => $pesanan->NAMA_GEDUNG,
    'NAMA_PAKET'         => !empty($pesanan->NAMA_PAKET) ? $pesanan->NAMA_PAKET : '-',

    // INI yang benar:
    'TOTAL_TAGIHAN'      => $total_tagihan,
    'NOMINAL_TRANSFER'   => $nominal_transfer,

    'ATAS_NAMA_PENGIRIM' => $this->input->post('atas_nama', TRUE),
    'TANGGAL_TRANSFER'   => $this->input->post('tanggal_transfer', TRUE),
    'BANK_PENGIRIM'      => $this->input->post('bank_pengirim', TRUE),

    'BUKTI_PATH'         => $bukti_rel_path,
    'BUKTI_NAME'         => $upload_data['file_name'],
    'BUKTI_MIME'         => $upload_data['file_type'],

    'STATUS_VERIF'       => 'PENDING',
);


    // KODE_PEMESANAN: kalau view/DB kamu pakai prefix "PMSN000", isi itu
    // (kalau di V_PEMESANAN ada kolom KODE_PEMESANAN, pakai dari sana)
    $kode_pemesanan = !empty($pesanan->KODE_PEMESANAN) ? $pesanan->KODE_PEMESANAN : 'PMSN000';

    $data = array(
        'ID_PEMESANAN_RAW'   => $id_pemesanan_raw,
        'KODE_PEMESANAN'     => $kode_pemesanan,
        'TANGGAL_PEMESANAN'  => $pesanan->TANGGAL_PEMESANAN,
        'NAMA_GEDUNG'        => $pesanan->NAMA_GEDUNG,
        'NAMA_PAKET'         => !empty($pesanan->NAMA_PAKET) ? $pesanan->NAMA_PAKET : '-',
        'TOTAL_TAGIHAN'      => $total,

        'ATAS_NAMA_PENGIRIM' => $this->input->post('atas_nama', TRUE),
        'TANGGAL_TRANSFER'   => $this->input->post('tanggal_transfer', TRUE),
        'BANK_PENGIRIM'      => $this->input->post('bank_pengirim', TRUE),
        'NOMINAL_TRANSFER'   => $total,

        'BUKTI_PATH'         => $bukti_rel_path,
        'BUKTI_NAME'         => $upload_data['file_name'],
        'BUKTI_MIME'         => $upload_data['file_type'],

        'STATUS_VERIF'       => 'PENDING',
        // kolom tujuan punya default, tapi kalau mau isi juga boleh:
        // 'BANK_TUJUAN' => 'BCA', 'NO_REKENING_TUJUAN' => '1234567890', 'ATAS_NAMA_TUJUAN' => 'Tiga Serangkai Smart Office'
    );

    $this->db->trans_begin();

    // insert ke tabel pembayaran
    $this->pembayaran_model->insert_pembayaran($data);

    // update status pemesanan -> 2 (APPROVE & PAID)
    $this->db->where('ID_PEMESANAN', $id_pemesanan_raw);
    $this->db->update('pemesanan', array('STATUS' => 2));

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $err = $this->db->error();
        $msg = isset($err['message']) ? $err['message'] : 'unknown';
        show_error('Gagal proses pembayaran: ' . $msg);
        return;
    }

    $this->db->trans_commit();
    redirect('home/pemesanan');
}

}
