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

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
	}

	public function index()
	{
		$this->load->view('/registration/daftar');
	}

	public function add_user()
	{
		$this->load->model('user/user_model');

		// ambil input dasar
		$username = trim((string)$this->input->post('username', true));
		$nama_lengkap = trim((string)$this->input->post('nama_lengkap', true));
		$password = $this->input->post('password', true);
		$email = trim((string)$this->input->post('email', true));
		$alamat = trim((string)$this->input->post('alamat', true));
		$no_telepon = trim((string)$this->input->post('no_telepon', true));
		$dob = $this->input->post('dob', true);

		//  perusahaan logic
		$perusahaan = $this->input->post('perusahaan', true); // INTERNAL / EKSTERNAL
		$nama_perusahaan = null;
		$departemen = null;

		if ($perusahaan === 'INTERNAL') {
			$departemen = trim((string)$this->input->post('departemen', true));

			if ($departemen === '') {
				echo "Departemen wajib dipilih untuk INTERNAL";
				$this->output->set_header('refresh:2; url=' . site_url("/registration"));
				return;
			}

			// set otomatis biar ga NULL
			$nama_perusahaan = 'PT Tiga Serangkai Pustaka Mandiri';
		} elseif ($perusahaan === 'EKSTERNAL') {
			$nama_perusahaan = trim((string)$this->input->post('nama_perusahaan', true));

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

		// data insert (samakan dengan nama kolom DB kamu)
		$data = [
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
		];

		// ===== server-side validation =====
		// email must be valid and use gmail domain
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "Format email tidak valid";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}
		if (!preg_match('/@gmail(\.[a-z]{2,})?$/i', $email)) {
			echo "Email harus menggunakan domain @gmail";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}

		// phone: must be digits only and have 11-13 digits
		if (!preg_match('/^\d{11,14}$/', $no_telepon)) {
			echo "No telepon harus 11 sampai 13 digit angka (hanya angka diperbolehkan)";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}
		// normalize phone in data (safe)
		$data['NO_TELEPON'] = $no_telepon;

		if (!empty($dob)) {
			try {
				$birth = new DateTime($dob);
				$now = new DateTime();
				$age = $now->diff($birth)->y;
				if ($age < 18) {
					echo "Usia minimal 18 tahun";
					$this->output->set_header('refresh:2; url=' . site_url("/registration"));
					return;
				}
			} catch (Exception $e) {
				echo "Format tanggal lahir tidak valid";
				$this->output->set_header('refresh:2; url=' . site_url("/registration"));
				return;
			}
		} else {
			echo "Tanggal lahir wajib diisi";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}

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
