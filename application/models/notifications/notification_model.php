<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model
{
    public function count_unread($username, $type = null)
    {
        $this->db->from('notifications');
        $this->db->where('username', $username);
        $this->db->where('read_at IS NULL', null, false);
        if ($type) $this->db->where('type', $type);
        return (int) $this->db->count_all_results();
    }

    public function latest_unread($username, $type = null, $limit = 1)
    {
        $this->db->from('notifications');
        $this->db->where('username', $username);
        $this->db->where('read_at IS NULL', null, false);
        if ($type) $this->db->where('type', $type);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function mark_read($username, $type = null)
    {
        $this->db->set('read_at', date('Y-m-d H:i:s'));
        $this->db->where('username', $username);
        $this->db->where('read_at IS NULL', null, false);
        if ($type) $this->db->where('type', $type);
        return $this->db->update('notifications');
    }
}