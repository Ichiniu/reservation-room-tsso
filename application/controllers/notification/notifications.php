<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // pakai alias biar jelas & gak salah kapital
        $this->load->model('notification/notification_model', 'notif');
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
        $username = $this->session->userdata('username');
        if (!$username) show_error('Unauthorized', 401);

        $typeParam = $this->input->get('type', true); // pemesanan | transaksi | all
        $types     = $this->mapTypes($typeParam);

        // asumsi model kamu mendukung parameter array types (sesuai Notification_service)
        $count  = $this->notif->count_unread($username, $types);
        $latest = $this->notif->latest_unread($username, $types, 1);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'count'  => (int)$count,
                'latest' => !empty($latest) ? $latest[0] : null,
            ]));
    }

    public function mark_read()
    {
        $username = $this->session->userdata('username');
        if (!$username) show_error('Unauthorized', 401);

        $typeParam = $this->input->post('type', true); // pemesanan | transaksi | all
        $types     = $this->mapTypes($typeParam);

        $this->notif->mark_read($username, $types);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true]));
    }
}
