<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AutoReview extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ulasan/Ulasan_Model', 'ulasan_model');
        $this->load->model('pemesanan/Pemesanan_Model', 'pemesanan_model');
        $this->load->library('notification_service');
        $this->load->database();
    }

    /**
     * Run via CLI: php index.php cron/autoreview run
     * Or via web with secret key: /cron/autoreview/run?k=SECRET
     */
    public function run()
    {
        // protect: allow CLI or valid key
        $allow = $this->input->is_cli_request();
        $key = $this->input->get('k', true);
        $cfg_key = $this->config->item('auto_review_key', 'notification');
        if (!$allow && (empty($cfg_key) || $key !== $cfg_key)) {
            show_error('Unauthorized', 403);
            return;
        }

        // Cari pemesanan yang berstatus SUBMITTED (3), berdasar tanggal pemesanan (TANGGAL_PEMESANAN).
        // Aturan: user dapat mengisi ulasan mulai pada hari H = TANGGAL_PEMESANAN.
        // Jika pada H sampai H+3 tidak ada ulasan, maka otomatis diisi pada job ini.
        // Jadi kita pilih pemesanan dengan DATE(TANGGAL_PEMESANAN) <= (today - 3 days).
        $cutoff_date = date('Y-m-d', strtotime('-3 days'));

        $rows = $this->db->select('p.ID_PEMESANAN, p.USERNAME')
            ->from('pemesanan p')
            ->where('p.STATUS', 3)
            ->where('DATE(p.TANGGAL_PEMESANAN) <=', $cutoff_date)
            ->group_by('p.ID_PEMESANAN')
            ->get()
            ->result_array();

        $inserted = 0;
        foreach ($rows as $r) {
            $id = (int)$r['ID_PEMESANAN'];
            $username = isset($r['USERNAME']) ? $r['USERNAME'] : '';
            if ($id <= 0 || $username === '') continue;

            // ambil pemesanan detail (status harus SUBMITTED)
            $pes = $this->pemesanan_model->get_one_submitted_by_id_and_username($id, $username);
            if (empty($pes)) continue;

            // bangun title_key seperti di submit_ulasan()
            $nama_gedung = isset($pes['NAMA_GEDUNG']) ? trim((string)$pes['NAMA_GEDUNG']) : '';
            $tanggal = isset($pes['TANGGAL_PEMESANAN']) ? trim((string)$pes['TANGGAL_PEMESANAN']) : '';
            $jam_mulai = isset($pes['JAM_PEMESANAN']) ? trim((string)$pes['JAM_PEMESANAN']) : '';
            $jam_selesai = isset($pes['JAM_SELESAI']) ? trim((string)$pes['JAM_SELESAI']) : '';
            $range = $jam_selesai ? ($jam_mulai . ' - ' . $jam_selesai) : $jam_mulai;
            $title_key = $nama_gedung . ' - ' . $tanggal . ' (' . $range . ')';

            // cek apakah sudah diulas
            if ($this->ulasan_model->exists_for_pemesanan($id, $username, $title_key)) continue;

            // simpan ulasan otomatis: rating 5, komentar '-' (atau kosong), APPROVED
            $insert = array(
                'USERNAME' => $username,
                'RATING'   => 5,
                'TITLE'    => $title_key,
                'COMMENT'  => '-',
                'STATUS'   => 'APPROVED',
                'CREATED_AT' => date('Y-m-d H:i:s')
            );
            if ($this->db->field_exists('ID_PEMESANAN', 'ulasan')) {
                $insert['ID_PEMESANAN'] = $id;
            }

            $ok = $this->ulasan_model->insert_ulasan($insert);
            if ($ok) $inserted++;
        }

        echo "AutoReview finished. Inserted: " . $inserted . "\n";
    }
}
