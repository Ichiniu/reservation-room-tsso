<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model untuk kebutuhan ulasan berbasis pemesanan.
 * - Hanya pemesanan dengan STATUS = 3 (SUBMITTED) yang boleh diulas.
 * - Join ke tabel `gedung` untuk mendapatkan nama gedung.
 */
class Pemesanan_model extends CI_Model
{
    const STATUS_SUBMITTED = 3;

    /**
     * Format TIME "HH:MM:SS" -> "HH:MM"
     * (PHP 5 friendly)
     */
    private function time_hm($time)
    {
        $t = trim((string)$time);
        if ($t === '') return '';
        return (strlen($t) >= 5) ? substr($t, 0, 5) : $t;
    }

    /**
     * Ambil daftar pemesanan milik user dengan STATUS = 3 (SUBMITTED).
     * Return fields:
     * - ID_PEMESANAN, TANGGAL_PEMESANAN, JAM_PEMESANAN, JAM_SELESAI, ID_GEDUNG, NAMA_GEDUNG
     */
    public function get_submitted_by_username($username)
    {
        $username = trim((string)$username);
        if ($username === '') return array();

        $rows = $this->db
            ->select('p.ID_PEMESANAN, p.TANGGAL_PEMESANAN, p.JAM_PEMESANAN, p.JAM_SELESAI, p.ID_GEDUNG, g.NAMA_GEDUNG')
            ->from('pemesanan p')
            ->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
            ->where('p.USERNAME', $username)
            ->where('p.STATUS', self::STATUS_SUBMITTED)
            ->order_by('p.TANGGAL_PEMESANAN', 'DESC')
            ->order_by('p.JAM_PEMESANAN', 'DESC')
            ->get()
            ->result_array();

        // Rapikan jam jadi HH:MM supaya siap dipakai untuk label dropdown
        for ($i = 0; $i < count($rows); $i++) {
            $rows[$i]['JAM_PEMESANAN'] = $this->time_hm(isset($rows[$i]['JAM_PEMESANAN']) ? $rows[$i]['JAM_PEMESANAN'] : '');
            $rows[$i]['JAM_SELESAI']   = $this->time_hm(isset($rows[$i]['JAM_SELESAI']) ? $rows[$i]['JAM_SELESAI'] : '');
        }

        return $rows;
    }

    /**
     * Ambil satu pemesanan status SUBMITTED (3) milik user.
     * Return: array atau null
     */
    public function get_one_submitted_by_id_and_username($id_pemesanan, $username)
    {
        $username = trim((string)$username);
        if ($username === '') return null;

        $row = $this->db
            ->select('p.ID_PEMESANAN, p.TANGGAL_PEMESANAN, p.JAM_PEMESANAN, p.JAM_SELESAI, p.ID_GEDUNG, g.NAMA_GEDUNG')
            ->from('pemesanan p')
            ->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
            ->where('p.ID_PEMESANAN', (int)$id_pemesanan)
            ->where('p.USERNAME', $username)
            ->where('p.STATUS', self::STATUS_SUBMITTED)
            ->limit(1)
            ->get()
            ->row_array();

        if (!$row) return null;

        // Rapikan jam jadi HH:MM
        $row['JAM_PEMESANAN'] = $this->time_hm(isset($row['JAM_PEMESANAN']) ? $row['JAM_PEMESANAN'] : '');
        $row['JAM_SELESAI']   = $this->time_hm(isset($row['JAM_SELESAI']) ? $row['JAM_SELESAI'] : '');

        return $row;
    }
}
