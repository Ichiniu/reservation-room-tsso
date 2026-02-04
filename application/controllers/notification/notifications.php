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

        // deteksi role
        $role = $this->session->userdata('admin_username') ? 'admin' : 'user';

        // sesuaikan type sesuai sistemmu
        $types = ($role === 'admin')
            ? ['ADMIN_INBOX']
            : ['USER_INBOX'];

        try {
            $count = $this->notification_service->count_unread($role, $types);

            echo json_encode([
                'ok' => true,
                'unread_count' => (int)$count
            ]);
        } catch (Throwable $e) {
            log_message('error', 'unread_count error: ' . $e->getMessage());
            echo json_encode([
                'ok' => false,
                'message' => 'Server error'
            ]);
        }
    }



    public function mark_read()
    {
        header('Content-Type: application/json; charset=utf-8');

        $notif_id = $this->input->post('id'); // pastikan JS kirim "id"
        if (empty($notif_id)) {
            echo json_encode([
                'ok' => false,
                'message' => 'ID tidak valid'
            ]);
            $notif_id = $this->input->post('id');
            if (empty($notif_id)) $notif_id = $this->input->post('notif_id');
            if (empty($notif_id)) $notif_id = $this->input->post('notification_id');

            // BONUS: kalau ternyata kamu kirim lewat GET (kadang JS begitu)
            if (empty($notif_id)) $notif_id = $this->input->get('id');
            if (empty($notif_id)) $notif_id = $this->input->get('notif_id');

            return;
        }

        try {
            $ok = $this->notif_model->mark_read_by_id((int)$notif_id);

            echo json_encode([
                'ok' => (bool)$ok
            ]);
        } catch (Throwable $e) {
            log_message('error', 'mark_read error: ' . $e->getMessage());
            echo json_encode([
                'ok' => false,
                'message' => 'Server error'
            ]);
        }
    }
}
