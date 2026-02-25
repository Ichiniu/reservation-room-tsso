<?php

/**
 * 
 */
class Pembayaran_Model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function insert_pembayaran($data)
	{
		$this->db->insert('pembayaran', $data);
	}
}
