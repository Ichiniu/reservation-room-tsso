<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ulasan_model extends CI_Model
{

    /**
     * Cache hasil pengecekan kolom agar tidak hit DB berulang.
     * @var bool|null
     */
    private $has_id_pemesanan_col = null;

    /**
     * Cek apakah tabel `ulasan` sudah punya kolom ID_PEMESANAN.
     * Kalau belum, sistem tetap jalan dengan fallback (pakai USERNAME + TITLE).
     */
    private function ulasan_has_id_pemesanan()
    {
        if ($this->has_id_pemesanan_col === null) {
            $this->has_id_pemesanan_col = $this->db->field_exists('ID_PEMESANAN', 'ulasan');
        }
        return (bool)$this->has_id_pemesanan_col;
    }


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

    /**
     * Cek apakah pemesanan sudah pernah diulas.
     * - Jika tabel ulasan punya kolom ID_PEMESANAN, cek by ID.
     * - Jika belum, fallback: cek by USERNAME + TITLE (string label pemesanan).
     */
    public function exists_for_pemesanan($id_pemesanan, $username, $title_key)
    {
        if ($this->ulasan_has_id_pemesanan()) {
            return $this->db->where('ID_PEMESANAN', (int)$id_pemesanan)
                ->from('ulasan')
                ->count_all_results() > 0;
        }

        $username = trim((string)$username);
        $title_key = trim((string)$title_key);
        if ($username === '' || $title_key === '') return false;

        return $this->db->where('USERNAME', $username)
            ->where('TITLE', $title_key)
            ->from('ulasan')
            ->count_all_results() > 0;
    }

    /**
     * Ambil daftar TITLE yang sudah diulas oleh user (dipakai untuk filter dropdown).
     */
    public function get_reviewed_titles_by_username($username)
    {
        $username = trim((string)$username);
        if ($username === '') return array();

        $rows = $this->db->select('TITLE')
            ->from('ulasan')
            ->where('USERNAME', $username)
            ->get()
            ->result_array();

        $out = array();
        foreach ($rows as $r) {
            if (!empty($r['TITLE'])) $out[] = $r['TITLE'];
        }
        return $out;
    }

    /**
     * Ambil daftar ID_PEMESANAN yang sudah diulas (hanya jika kolom tersedia).
     */
    public function get_reviewed_id_pemesanan_by_username($username)
    {
        if (!$this->ulasan_has_id_pemesanan()) return array();

        $username = trim((string)$username);
        if ($username === '') return array();

        $rows = $this->db->select('ID_PEMESANAN')
            ->from('ulasan')
            ->where('USERNAME', $username)
            ->get()
            ->result_array();

        $out = array();
        foreach ($rows as $r) {
            $out[] = (int)$r['ID_PEMESANAN'];
        }
        return $out;
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
