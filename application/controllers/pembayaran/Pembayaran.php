<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembayaran extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pembayaran/pembayaran_model');
        $this->load->library('upload');
    }

    public function upload_bukti()
    {
        $id_pemesanan_raw = (int) $this->input->post('id_pemesanan_raw', TRUE);
        if ($id_pemesanan_raw <= 0) {
            show_error('ID Pemesanan tidak valid.');
            return;
        }

        // Ambil detail pemesanan + harga + perusahaan user
        $pesanan = $this->db->select("
            p.ID_PEMESANAN,
            p.USERNAME,
            p.TANGGAL_PEMESANAN,
            p.JUMLAH_CATERING,
            u.perusahaan,
            g.NAMA_GEDUNG,
            g.HARGA_SEWA,
            c.NAMA_PAKET,
            c.HARGA AS HARGA_CATERING_SATUAN
        ")
            ->from('pemesanan p')
            ->join('user u', 'u.USERNAME = p.USERNAME', 'left')
            ->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
            ->join('catering c', 'c.ID_CATERING = p.ID_CATERING', 'left')
            ->where('p.ID_PEMESANAN', $id_pemesanan_raw)
            ->get()
            ->row();

        if (!$pesanan) {
            show_error('Data pemesanan tidak ditemukan.');
            return;
        }

        // ===== RULE DISKON =====
        // (FIX PHP 5.x: hilangkan operator ??)
        $perusahaan_val = (isset($pesanan->perusahaan) && $pesanan->perusahaan !== null) ? $pesanan->perusahaan : '';
        $perusahaan = strtoupper(trim((string) $perusahaan_val));
        $is_internal = ($perusahaan === 'INTERNAL');

        $harga_sewa = !empty($pesanan->HARGA_SEWA) ? (int) $pesanan->HARGA_SEWA : 0;
        if ($is_internal) {
            $harga_sewa = 0; // INTERNAL => gedung gratis
        }

        $harga_catering_satuan = !empty($pesanan->HARGA_CATERING_SATUAN) ? (int) $pesanan->HARGA_CATERING_SATUAN : 0;
        $jumlah_catering = !empty($pesanan->JUMLAH_CATERING) ? (int) $pesanan->JUMLAH_CATERING : 0;

        $total_tagihan = $harga_sewa + ($harga_catering_satuan * $jumlah_catering);

        // ===== AUTO CONFIRM kalau total tagihan 0 =====
        if ($total_tagihan === 0) {

            // cegah dobel insert
            $exists = $this->db->select('ID_PEMBAYARAN')
                ->from('pembayaran')
                ->where('ID_PEMESANAN_RAW', $id_pemesanan_raw)
                ->limit(1)
                ->get()
                ->row();

            if (!$exists) {
                $data_free = array(
                    'ID_PEMESANAN_RAW'   => $id_pemesanan_raw,
                    'KODE_PEMESANAN'     => 'PMSN000',
                    'TANGGAL_PEMESANAN'  => $pesanan->TANGGAL_PEMESANAN,
                    'NAMA_GEDUNG'        => $pesanan->NAMA_GEDUNG,
                    'NAMA_PAKET'         => !empty($pesanan->NAMA_PAKET) ? $pesanan->NAMA_PAKET : '-',
                    'TOTAL_TAGIHAN'      => 0,

                    // kolom tujuan transfer (boleh dihapus kalau mau pakai default DB, tapi ini bikin rapi di admin)
                    'BANK_TUJUAN'        => 'BCA',
                    'NO_REKENING_TUJUAN' => '1234567890',
                    'ATAS_NAMA_TUJUAN'   => 'Tiga Serangkai Smart Office',

                    // dummy pengirim
                    'ATAS_NAMA_PENGIRIM' => 'INTERNAL (AUTO)',
                    'TANGGAL_TRANSFER'   => date('Y-m-d'),
                    'BANK_PENGIRIM'      => '-',
                    'NOMINAL_TRANSFER'   => 0,

                    // WAJIB NOT NULL di tabel kamu
                    'BUKTI_PATH'         => '-',
                    'BUKTI_NAME'         => '-',
                    'BUKTI_MIME'         => NULL, // ini boleh NULL

                    'STATUS_VERIF'       => 'CONFIRMED',
                    'CATATAN_ADMIN'      => 'AUTO: INTERNAL - Langsung confirmed (gratis)',
                    'CONFIRMED_AT'       => date('Y-m-d H:i:s'),
                );

                $this->db->trans_begin();

                $this->pembayaran_model->insert_pembayaran($data_free);

                // status pemesanan jadi confirmed
                $this->db->where('ID_PEMESANAN', $id_pemesanan_raw);
                $this->db->update('pemesanan', array('STATUS' => 3));

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $err = $this->db->error();
                    $msg = isset($err['message']) ? $err['message'] : 'unknown';
                    show_error('Gagal auto-confirm internal: ' . $msg);
                    return;
                }

                $this->db->trans_commit();
            }

            redirect('home/pemesanan');
            return;
        }

        // ===== Kalau masih ada tagihan, lanjut flow upload bukti normal =====
        $atas_nama        = $this->input->post('atas_nama', TRUE);
        $tanggal_transfer = $this->input->post('tanggal_transfer', TRUE);
        $bank_pengirim    = $this->input->post('bank_pengirim', TRUE);

        $nominal_transfer = $this->input->post('nominal_transfer', TRUE);
        $nominal_transfer = preg_replace('/[^0-9]/', '', (string) $nominal_transfer);
        $nominal_transfer = (int) $nominal_transfer;

        if (empty($atas_nama) || empty($tanggal_transfer) || empty($bank_pengirim)) {
            show_error('Atas nama, tanggal transfer, dan bank pengirim wajib diisi.');
            return;
        }
        if ($nominal_transfer <= 0) {
            show_error('Nominal transfer tidak valid.');
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

        $upload_data    = $this->upload->data();
        $bukti_rel_path = 'assets/images/client-bukti-pembayaran/' . $upload_data['file_name'];

        $data = array(
            'ID_PEMESANAN_RAW'   => $id_pemesanan_raw,
            'KODE_PEMESANAN'     => 'PMSN000',
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

        $this->db->trans_begin();

        $this->pembayaran_model->insert_pembayaran($data);

        // status setelah user upload bukti (menunggu verifikasi admin)
        $this->db->where('ID_PEMESANAN', $id_pemesanan_raw);
        $this->db->update('pemesanan', array('STATUS' => 2));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
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