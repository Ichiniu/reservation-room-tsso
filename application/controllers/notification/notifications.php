<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('notification_service');
        $this->load->model('notification/notification_model', 'notif_model');
    }



    /**
     * Map query "type" dari frontend -> daftar tipe notifikasi yang disimpan di DB
     * type frontend: pemesanan | transaksi | all
     */
    private function mapTypes($type)
    {
        $type = strtolower(trim((string)$type));
        if ($type === 'pemesanan') return ['USER_PEMESANAN'];
        if ($type === 'transaksi') return ['USER_TRANSAKSI'];
        if ($type === 'all' || $type === '') return ['USER_PEMESANAN', 'USER_TRANSAKSI'];
        if (preg_match('/^USER_/i', $type)) return [$type];
        return ['USER_PEMESANAN', 'USER_TRANSAKSI'];
    }

    public function unread_count()
    {
        header('Content-Type: application/json; charset=utf-8');

        $username = (string)$this->session->userdata('username');
        if ($username === '') {
            $username = (string)$this->session->userdata('admin_username');
        }

        if ($username === '') {
            echo json_encode(['ok' => false, 'count' => 0]);
            return;
        }

        // Tipe yang dianggap "unread" untuk badge utama (Desktop Notif)
        $types = (strtolower($username) === 'admin')
            ? ['ADMIN_INBOX', 'ADMIN_TRANSAKSI']
            : ['USER_PEMESANAN', 'USER_TRANSAKSI', 'REVIEW_REQUEST'];

        try {
            $count = $this->notification_service->count_unread($username, $types);

            echo json_encode([
                'ok' => true,
                'count' => (int)$count, // JS di navbar.php pakai "count"
                'unread_count' => (int)$count
            ]);
        } catch (Throwable $e) {
            log_message('error', 'unread_count error: ' . $e->getMessage());
            echo json_encode([
                'ok' => false,
                'count' => 0,
                'message' => 'Server error'
            ]);
        }
    }




    public function mark_read()
    {
        header('Content-Type: application/json; charset=utf-8');

        $username = (string)$this->session->userdata('username');
        if ($username === '') return;

        $notif_id = $this->input->post('id');
        $type_req = $this->input->post('type');

        try {
            if (!empty($notif_id)) {
                $ok = $this->notif_model->mark_read_by_id((int)$notif_id);
            } elseif (!empty($type_req)) {
                $types = $this->mapTypes($type_req);
                if (strtolower($type_req) === 'transaksi') {
                    $types[] = 'REVIEW_REQUEST';
                    // Juga bersihkan flag legacy di tabel pembayaran
                    $this->load->model('gedung/gedung_model');
                    $this->gedung_model->clear_transaksi_flag($username);
                }

                $this->db->where('username', $username)
                    ->where_in('type', $types)
                    ->where('read_at IS NULL', null, false)
                    ->update('notifications', ['read_at' => date('Y-m-d H:i:s')]);
                $ok = true;
            } else {
                echo json_encode(['ok' => false, 'message' => 'No ID or Type']);
                return;
            }

            echo json_encode(['ok' => (bool)$ok]);
        } catch (Throwable $e) {
            log_message('error', 'mark_read error: ' . $e->getMessage());
            echo json_encode(['ok' => false, 'message' => 'Server error']);
        }
    }

}
