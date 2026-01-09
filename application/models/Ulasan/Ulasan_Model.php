<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ulasan_model extends CI_Model
{


    public function get_approved($limit = 20)
    {
        $this->db->where('STATUS', 'APPROVED');
        $this->db->order_by('CREATED_AT', 'DESC');
        $q = $this->db->get('ulasan', $limit);
        return $q->result_array();
    }

    public function insert_ulasan($data)
    {
        return $this->db->insert('ulasan', $data);
    }

    public function get_summary_approved()
    {
        $q1 = $this->db->select('COUNT(*) AS total, AVG(RATING) AS avg_rating', false)
            ->where('STATUS', 'APPROVED')
            ->get('ulasan')
            ->row_array();

        $q2 = $this->db->select('RATING, COUNT(*) AS cnt', false)
            ->where('STATUS', 'APPROVED')
            ->group_by('RATING')
            ->get('ulasan')
            ->result_array();

        $dist = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
        foreach ($q2 as $row) {
            $r = (int)$row['RATING'];
            $dist[$r] = (int)$row['cnt'];
        }

        return array(
            'total' => (int)$q1['total'],
            'avg' => ($q1['avg_rating'] ? round((float)$q1['avg_rating'], 1) : 0),
            'dist' => $dist
        );
    }
    public function exists_by_username($username)
    {
        $username = trim((string)$username);
        if ($username === '') return false;

        return $this->db->where('USERNAME', $username)
            ->from('ulasan')
            ->count_all_results() > 0;
    }
}
