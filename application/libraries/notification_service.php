<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('email');
        $this->CI->load->database();
        // tetap pakai group "notification"
        $this->CI->load->config('notification', true);
    }

    private function insertNotif($username, $type, $title, $message, $url)
    {
        $this->CI->db->insert('notifications', [
            'username'   => $username,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'url'        => $url,
            'created_at' => date('Y-m-d H:i:s'),
            'read_at'    => null,
            'emailed_at' => null,
        ]);
        return (int)$this->CI->db->insert_id();
    }

    private function markEmailed($id)
    {
        if ($id > 0) {
            $this->CI->db->where('id', $id)->update('notifications', [
                'emailed_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function getUserEmail($username)
    {
        $u = $this->CI->db->get_where('user', ['USERNAME' => $username])->row_array();
        return ($u && !empty($u['EMAIL'])) ? $u['EMAIL'] : null;
    }

    private function sendEmail($to, $subject, $html)
    {
        $smtp     = $this->CI->config->item('smtp', 'notification');
        $from     = $this->CI->config->item('mail_from', 'notification');
        $fromName = $this->CI->config->item('mail_from_name', 'notification');

        if (is_array($smtp) && !empty($smtp)) {
            $this->CI->email->initialize($smtp); // ✅ WAJIB
        }

        $this->CI->email->clear(true);
        $this->CI->email->from($from, $fromName);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($html);

        $ok = (bool)$this->CI->email->send();

        if (!$ok) {
            log_message('error', 'EMAIL FAIL: ' . $this->CI->email->print_debugger(['headers', 'subject']));
        }

        return $ok;
    }


    public function notifyUser($username, $type, $title, $message, $url, $sendEmail = true)
    {
        if (empty($username)) return;

        $notifId = $this->insertNotif($username, $type, $title, $message, $url);

        if (!$sendEmail) return;

        $to = $this->getUserEmail($username);
        if (!$to) return;

        $link = site_url($url);
        $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) .
            '<br><br>Silakan cek: <a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a>';

        if ($this->sendEmail($to, $title, $html)) {
            $this->markEmailed($notifId);
        }
    }

    public function notifyAdmin($type, $title, $message, $url, $sendEmail = true)
    {
        $notifId = $this->insertNotif('admin', $type, $title, $message, $url);

        if (!$sendEmail) return;

        // ✅ ambil dari config group notification
        $emails = $this->CI->config->item('admin_emails', 'notification');
        if (!is_array($emails) || empty($emails)) return;

        $link = site_url($url);
        $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) .
            '<br><br>Link: <a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</a>';

        $sent = false;
        foreach ($emails as $to) {
            if (!empty($to) && $this->sendEmail($to, $title, $html)) {
                $sent = true;
            }
        }

        if ($sent) $this->markEmailed($notifId);
    }

    public function get_unread($username, $types = [], $limit = 10)
    {
        $this->CI->db->from('notifications');
        $this->CI->db->where('username', $username);
        $this->CI->db->where('read_at IS NULL', null, false);
        if (!empty($types)) $this->CI->db->where_in('type', $types);
        $this->CI->db->order_by('id', 'DESC');
        $this->CI->db->limit((int)$limit);
        return $this->CI->db->get()->result_array();
    }

    public function count_unread($username, $types = [])
    {
        $this->CI->db->from('notifications');
        $this->CI->db->where('username', $username);
        $this->CI->db->where('read_at IS NULL', null, false);
        if (!empty($types)) $this->CI->db->where_in('type', $types);
        return (int)$this->CI->db->count_all_results();
    }
}
