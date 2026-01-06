<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Notification_model');
    }

    public function unread_count()
    {
        $username = $this->session->userdata('username');
        if (!$username) show_error('Unauthorized', 401);

        $type = $this->input->get('type', true); // contoh: pemesanan
        $count = $this->Notification_model->count_unread($username, $type);
        $latest = $this->Notification_model->latest_unread($username, $type, 1);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'count' => $count,
                'latest' => $latest ? $latest[0] : null,
            ]));
    }

    public function mark_read()
    {
        $username = $this->session->userdata('username');
        if (!$username) show_error('Unauthorized', 401);

        $type = $this->input->post('type', true); // 'pemesanan'
        $this->Notification_model->mark_read($username, $type);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true]));
    }
}