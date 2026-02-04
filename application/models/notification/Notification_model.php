<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model
{
    private $table = 'notifications';

    public function create($data)
    {
        $row = array(
            'username'   => $data['username'],
            'type'       => $data['type'],
            'title'      => $data['title'],
            'message'    => $data['message'],
            'url'        => $data['url'],
            'created_at' => date('Y-m-d H:i:s'),
            'read_at'    => null,
            'emailed_at' => null,
        );
        $this->db->insert($this->table, $row);
        return (int)$this->db->insert_id();
    }

    public function exists_recent($username, $type, $url, $minutes = 10)
    {
        $since = date('Y-m-d H:i:s', time() - ((int)$minutes * 60));
        $q = $this->db->from($this->table)
            ->where('username', $username)
            ->where('type', $type)
            ->where('url', $url)
            ->where('created_at >=', $since)
            ->limit(1)
            ->get();
        return $q->num_rows() > 0;
    }

    public function mark_emailed($id)
    {
        $this->db->where('id', (int)$id)->update($this->table, array(
            'emailed_at' => date('Y-m-d H:i:s')
        ));
    }

    // ====== wrapper supaya controller bisa panggil count_unread() ======
    public function count_unread($username, $typesOrLike = null)
    {
        return $this->unread_count($username, $typesOrLike);
    }

    public function unread_count($username, $typesOrLike = null)
    {
        $this->db->from($this->table);
        $this->db->where('username', $username);
        $this->db->where('read_at IS NULL', null, false);

        if (!empty($typesOrLike)) {
            if (is_array($typesOrLike)) {
                $this->db->where_in('type', $typesOrLike);
            } else {
                $this->db->like('type', $typesOrLike, 'after');
            }
        }

        return (int)$this->db->count_all_results();
    }

    public function latest_unread($username, $typesOrLike = null, $limit = 5)
    {
        $this->db->from($this->table);
        $this->db->where('username', $username);
        $this->db->where('read_at IS NULL', null, false);

        if (!empty($typesOrLike)) {
            if (is_array($typesOrLike)) {
                $this->db->where_in('type', $typesOrLike);
            } else {
                $this->db->like('type', $typesOrLike, 'after');
            }
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit((int)$limit);
        return $this->db->get()->result_array();
    }

    // ====== wrapper supaya controller bisa panggil mark_read() ======
    public function mark_read($username, $typesOrLike = null)
    {
        // kosong / all -> mark semua notif user
        if (empty($typesOrLike)) {
            $this->db->where('username', $username);
            $this->db->where('read_at IS NULL', null, false);
            $this->db->update($this->table, array(
                'read_at' => date('Y-m-d H:i:s')
            ));
            return;
        }

        $this->mark_read_by_type($username, $typesOrLike);
    }

    public function mark_read_by_type($username, $typesOrLike)
    {
        $this->db->where('username', $username);

        if (!empty($typesOrLike)) {
            if (is_array($typesOrLike)) {
                $this->db->where_in('type', $typesOrLike);
            } else {
                $this->db->like('type', $typesOrLike, 'after');
            }
        }

        $this->db->where('read_at IS NULL', null, false);
        $this->db->update($this->table, array(
            'read_at' => date('Y-m-d H:i:s')
        ));
    }
    public function mark_emailed_by_id($id)
    {
        if (empty($id)) return false;

        $this->db->set('emailed_at', date('Y-m-d H:i:s'));
        $this->db->where('id', (int)$id);
        $this->db->where('emailed_at IS NULL', null, false);

        return $this->db->update('notifications');
    }


    public function get_unread_since($username, $types = array(), $since_id = 0, $limit = 20)
    {
        $this->db->from($this->table);
        $this->db->where('username', $username);
        $this->db->where('read_at IS NULL', null, false);

        if (!empty($types)) $this->db->where_in('type', $types);

        $since_id = (int)$since_id;
        if ($since_id > 0) $this->db->where('id >', $since_id);

        $this->db->order_by('id', 'ASC');
        $this->db->limit((int)$limit);

        return $this->db->get()->result_array();
    }
}
