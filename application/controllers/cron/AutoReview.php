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
        $allow = is_cli();
        $key = $this->input->get('k', true);
        $cfg_key = $this->config->item('auto_review_key', 'notification');
        if (!$allow && (empty($cfg_key) || $key !== $cfg_key)) {
            show_error('Unauthorized', 403);
            return;
        }

        // Cari pemesanan yang berstatus SUBMITTED (3), berdasar tanggal pemesanan (TANGGAL_PEMESANAN).
        // Aturan: user dapat mengisi ulasan mulai pada hari H = TANGGAL_PEMESANAN.
        // Jika pada H sampai H+3 tidak ada ulasan, maka otomatis diisi pada job ini.
        // Pertama: kirim notifikasi/email pada hari H (DATE == today) jika belum pernah dikirim.
        $today = date('Y-m-d');
        $todayRows = $this->db->select('p.ID_PEMESANAN, p.USERNAME')
            ->from('pemesanan p')
            ->where('p.STATUS', 3)
            ->where('DATE(p.TANGGAL_PEMESANAN)', $today)
            ->group_by('p.ID_PEMESANAN')
            ->get()
            ->result_array();

        foreach ($todayRows as $tr) {
            $pid = (int)$tr['ID_PEMESANAN'];
            $uname = $tr['USERNAME'] ?? '';
            if ($pid <= 0 || $uname === '') continue;

            // cek apakah notifikasi review untuk pemesanan ini sudah pernah dikirim
            $full_id = 'PMSN000' . $pid;
            $exists = $this->db->from('notifications')
                ->where('username', $uname)
                ->where('type', 'REVIEW_REQUEST')
                ->like('message', $full_id)
                ->limit(1)
                ->count_all_results() > 0;

            if (!$exists) {
                $this->notification_service->notifyReviewRequest($uname, $pid);
            }
        }

        // User punya waktu dari Hari H sampai H+3 untuk mengisi manual.
        // Maka, kita auto-fill yang sudah lewat dari H+3 (yaitu H+4 ke atas).
        // Jika hari ini tanggal 14, maka yang tanggal 10 (10,11,12,13) sudah habis waktunya.
        $cutoff_date = date('Y-m-d', strtotime('-4 days'));

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
            $username = $r['USERNAME'] ?? '';
            if ($id <= 0 || $username === '') continue;

            // ambil pemesanan detail (status harus SUBMITTED)
            $pes = $this->pemesanan_model->get_one_submitted_by_id_and_username($id, $username);
            if (empty($pes)) continue;

            // bangun title_key seperti di submit_ulasan()
            $nama_gedung = trim((string)($pes['NAMA_GEDUNG'] ?? ''));
            $tanggal = trim((string)($pes['TANGGAL_PEMESANAN'] ?? ''));
            $jam_mulai = trim((string)($pes['JAM_PEMESANAN'] ?? ''));
            $jam_selesai = trim((string)($pes['JAM_SELESAI'] ?? ''));
            $range = $jam_selesai ? ($jam_mulai . ' - ' . $jam_selesai) : $jam_mulai;
            $title_key = $nama_gedung . ' - ' . $tanggal . ' (' . $range . ')';

            // cek apakah sudah diulas
            if ($this->ulasan_model->exists_for_pemesanan($id, $username, $title_key)) continue;

            // simpan ulasan otomatis: rating 5, komentar kosong (tanpa komentar), APPROVED
            $insert = [
                'USERNAME' => $username,
                'RATING'   => 5,
                'TITLE'    => $title_key,
                'COMMENT'  => '',
                'STATUS'   => 'APPROVED',
                'CREATED_AT' => date('Y-m-d H:i:s')
            ];
            if ($this->db->field_exists('ID_PEMESANAN', 'ulasan')) {
                $insert['ID_PEMESANAN'] = $id;
            }

            $ok = $this->ulasan_model->insert_ulasan($insert);
            if ($ok) $inserted++;
        }

        echo "AutoReview finished. Inserted: " . $inserted . "\n";
    }
}
