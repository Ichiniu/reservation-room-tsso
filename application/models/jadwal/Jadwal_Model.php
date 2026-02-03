<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_model extends CI_Model
{
     public function get_jadwal_by_date($id_gedung, $date)
    {
        // Sesuaikan nama tabel/kolom milikmu
        // Contoh tabel: pemesanan_ruangan
        // kolom: ID_GEDUNG, TGL_PESAN, JAM_PESAN, JAM_SELESAI, STATUS

        return $this->db->select('JAM_PESAN, JAM_SELESAI, STATUS, KEPERLUAN')
            ->from('pemesanan_ruangan')
            ->where('ID_GEDUNG', (int)$id_gedung)
            ->where('TGL_PESAN', $date)
            ->where_in('STATUS', ['PROCESS', 'APPROVE', 'SUBMITTED']) // sesuaikan status di sistemmu
            ->order_by('JAM_PESAN', 'ASC')
            ->get()
            ->result_array();
    }
}