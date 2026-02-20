<?php

/**
 * 
 */
class Catering_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	function get_catering_name()
	{
		$sql = "SELECT ID_CATERING, NAMA_PAKET FROM CATERING WHERE IS_ACTIVE = 1";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	function get_all()
	{
		$sql = "SELECT * FROM CATERING ORDER BY ID_CATERING DESC";
		$query = $this->db->query($sql);
		$hasil = $query->result_array();
		return $hasil;
	}

	function get_by_id($id_catering)
	{
		$id_catering = (int)$id_catering;
		$query = $this->db->get_where('catering', array('ID_CATERING' => $id_catering), 1);
		return $query->row_array();
	}

	function add_catering($data)
	{
		$this->db->insert('catering', $data);
	}

	function update_catering($id_catering, $data)
	{
		$id_catering = (int)$id_catering;
		$this->db->where('ID_CATERING', $id_catering);
		return $this->db->update('catering', $data);
	}

	function delete_catering($id_catering)
	{
		$id_catering = (int)$id_catering;
		$this->db->where('ID_CATERING', $id_catering);
		return $this->db->delete('catering');
	}

	public function get_catering_full()
	{
		return $this->db
			->select('ID_CATERING, NAMA_PAKET, HARGA, MIN_PAX, JENIS, MENU_JSON')
			->from('catering')
			->order_by('JENIS', 'ASC')
			->order_by('HARGA', 'ASC')
			->get()
			->result_array();
	}

	/**
	 * Hanya ambil catering yang aktif (IS_ACTIVE = 1)
	 */
	function get_active_only()
	{
		$sql = "SELECT * FROM CATERING WHERE IS_ACTIVE = 1 ORDER BY ID_CATERING DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * Toggle status aktif/nonaktif catering
	 */
	function toggle_status($id_catering)
	{
		$id_catering = (int)$id_catering;
		$row = $this->get_by_id($id_catering);
		if (!$row) return false;

		$new_status = (isset($row['IS_ACTIVE']) && (int)$row['IS_ACTIVE'] === 1) ? 0 : 1;
		$this->db->where('ID_CATERING', $id_catering);
		return $this->db->update('catering', array('IS_ACTIVE' => $new_status));
	}
}
