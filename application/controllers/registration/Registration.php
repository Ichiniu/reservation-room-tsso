<?php

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Output $output
 * @property User_model $user_model
 */
class Registration extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
	}

	function index()
	{
		$this->load->view('/registration/daftar');
	}

	function add_user()
	{
		$this->load->model('user/user_model');

		// ambil input dasar
		$username = trim($this->input->post('username', true));
		$nama_lengkap = trim($this->input->post('nama_lengkap', true));
		$password = $this->input->post('password', true);
		$email = trim($this->input->post('email', true));
		$alamat = trim($this->input->post('alamat', true));
		$no_telepon = trim($this->input->post('no_telepon', true));
		$dob = $this->input->post('dob', true);

		// ✅ perusahaan logic
		$perusahaan = $this->input->post('perusahaan', true); // INTERNAL / EKSTERNAL
		$nama_perusahaan = null;
		$departemen = null;

		if ($perusahaan === 'INTERNAL') {
			$departemen = trim($this->input->post('departemen', true));

			if ($departemen === '') {
				echo "Departemen wajib dipilih untuk INTERNAL";
				$this->output->set_header('refresh:2; url=' . site_url("/registration"));
				return;
			}

			// set otomatis biar ga NULL
			$nama_perusahaan = 'PT Tiga Serangkai Pustaka Mandiri';
		} elseif ($perusahaan === 'EKSTERNAL') {
			$nama_perusahaan = trim($this->input->post('nama_perusahaan', true));

			if ($nama_perusahaan === '') {
				echo "Nama perusahaan wajib diisi untuk EKSTERNAL";
				$this->output->set_header('refresh:2; url=' . site_url("/registration"));
				return;
			}

			$departemen = null;
		} else {
			echo "Perusahaan wajib dipilih";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}

		// ✅ data insert (samakan dengan nama kolom DB kamu)
		$data = array(
			'USERNAME'        => $username,
			'NAMA_LENGKAP'    => $nama_lengkap,
			'perusahaan'      => $perusahaan,
			'nama_perusahaan' => $nama_perusahaan,
			'departemen'      => $departemen,
			'PASSWORD'        => $password,
			'EMAIL'           => $email,
			'ALAMAT'          => $alamat,
			'NO_TELEPON'      => $no_telepon,
			'TANGGAL_LAHIR'   => $dob,
		);

		// cek username sudah ada
		$this->db->select('USERNAME');
		$this->db->where('USERNAME', $data['USERNAME']);
		$result = $this->db->get('user');

		if ($result->num_rows() > 0) {
			echo "Username sudah ada";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}

		// insert
		$this->user_model->insert($data);

		echo "Registrasi Berhasil";
		$this->output->set_header('refresh:2; url=' . site_url("/login"));
	}
}
