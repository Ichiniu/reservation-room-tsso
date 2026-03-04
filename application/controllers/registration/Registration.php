<?php

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Output $output
 * @property CI_Session $session
 * @property User_model $user_model
 */
class Registration extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('/registration/daftar');
	}

	public function add_user()
	{
		$this->load->model('user/user_model');

		// ambil input dasar
		$username     = trim((string)$this->input->post('username', true));
		$nama_lengkap = trim((string)$this->input->post('nama_lengkap', true));
		$password     = $this->input->post('password', true);
		$email        = trim((string)$this->input->post('email', true));
		$alamat       = trim((string)$this->input->post('alamat', true));
		$no_telepon   = trim((string)$this->input->post('no_telepon', true));
		$dob          = $this->input->post('dob', true);

		//  perusahaan logic
		$perusahaan      = $this->input->post('perusahaan', true); // INTERNAL / EKSTERNAL
		$nama_perusahaan = null;
		$departemen      = null;

		if ($perusahaan === 'INTERNAL') {
			$departemen = trim((string)$this->input->post('departemen', true));

			if ($departemen === '') {
				$this->session->set_flashdata('flash_msg', 'Departemen wajib dipilih untuk INTERNAL.');
				$this->session->set_flashdata('flash_type', 'error');
				redirect('/registration');
				return;
			}

			// set otomatis biar ga NULL
			$nama_perusahaan = 'PT Tiga Serangkai Pustaka Mandiri';
		} elseif ($perusahaan === 'EKSTERNAL') {
			$nama_perusahaan = trim((string)$this->input->post('nama_perusahaan', true));

			if ($nama_perusahaan === '') {
				$this->session->set_flashdata('flash_msg', 'Nama perusahaan wajib diisi untuk EKSTERNAL.');
				$this->session->set_flashdata('flash_type', 'error');
				redirect('/registration');
				return;
			}

			$departemen = null;
		} else {
			$this->session->set_flashdata('flash_msg', 'Perusahaan wajib dipilih.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// data insert (samakan dengan nama kolom DB kamu)
		$data = [
			'USERNAME'        => $username,
			'NAMA_LENGKAP'    => $nama_lengkap,
			'perusahaan'      => $perusahaan,
			'nama_perusahaan' => $nama_perusahaan,
			'departemen'      => $departemen,
			'PASSWORD'        => password_hash((string)$password, PASSWORD_DEFAULT),
			'EMAIL'           => $email,
			'ALAMAT'          => $alamat,
			'NO_TELEPON'      => $no_telepon,
			'TANGGAL_LAHIR'   => $dob,
		];

		// ===== server-side validation =====
		// email must be valid and use gmail domain
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->session->set_flashdata('flash_msg', 'Format email tidak valid.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}
		if (!preg_match('/@gmail(\.[a-z]{2,})?$/i', $email)) {
			$this->session->set_flashdata('flash_msg', 'Email harus menggunakan domain @gmail.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// phone: must be digits only and have 11-13 digits
		if (!preg_match('/^\d{11,14}$/', $no_telepon)) {
			$this->session->set_flashdata('flash_msg', 'No telepon harus 11 sampai 13 digit angka (hanya angka diperbolehkan).');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}
		// normalize phone in data (safe)
		$data['NO_TELEPON'] = $no_telepon;

		if (!empty($dob)) {
			try {
				$birth = new DateTime($dob);
				$now   = new DateTime();
				$age   = $now->diff($birth)->y;
				if ($age < 18) {
					$this->session->set_flashdata('flash_msg', 'Usia minimal 18 tahun.');
					$this->session->set_flashdata('flash_type', 'error');
					redirect('/registration');
					return;
				}
			} catch (Exception $e) {
				$this->session->set_flashdata('flash_msg', 'Format tanggal lahir tidak valid.');
				$this->session->set_flashdata('flash_type', 'error');
				redirect('/registration');
				return;
			}
		} else {
			$this->session->set_flashdata('flash_msg', 'Tanggal lahir wajib diisi.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// cek username sudah ada (case-insensitive: 'admin' = 'ADMIN')
		$this->db->select('USERNAME');
		$this->db->where('LOWER(USERNAME) = ' . $this->db->escape(strtolower($data['USERNAME'])), null, false);
		$result = $this->db->get('user');

		if ($result->num_rows() > 0) {
			$this->session->set_flashdata('flash_msg', 'Username sudah digunakan. Silakan pilih username lain.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// cek nama_lengkap sudah ada (case-insensitive: 'wahyu' = 'WAHYU')
		$this->db->select('NAMA_LENGKAP');
		$this->db->where('LOWER(NAMA_LENGKAP) = ' . $this->db->escape(strtolower($data['NAMA_LENGKAP'])), null, false);
		$result_nama = $this->db->get('user');

		if ($result_nama->num_rows() > 0) {
			echo "Nama lengkap sudah terdaftar";
			$this->output->set_header('refresh:2; url=' . site_url("/registration"));
			return;
		}

		// insert
		$this->user_model->insert($data);

		$this->session->set_flashdata('flash_msg', 'Registrasi berhasil! Silakan login.');
		$this->session->set_flashdata('flash_type', 'success');
		redirect('/login');
	}

	/**
	 * AJAX endpoint — cek ketersediaan username / nama_lengkap
	 * GET/POST: /registration/check_availability?field=username&value=xxx
	 * Response: JSON { "available": true|false }
	 */
	public function check_availability()
	{
		$this->output->set_content_type('application/json');

		$field = $this->input->get_post('field', true);
		$value = trim((string)$this->input->get_post('value', true));

		// Hanya izinkan field yang diperbolehkan
		$allowed = ['username' => 'USERNAME', 'nama_lengkap' => 'NAMA_LENGKAP'];

		if (!array_key_exists($field, $allowed) || $value === '') {
			echo json_encode(['available' => true]);
			return;
		}

		$col = $allowed[$field];
		// Gunakan LOWER() + escape() agar case-insensitive dan aman dari SQL injection
		$this->db->select($col);
		$this->db->where("LOWER({$col}) = " . $this->db->escape(strtolower($value)), null, false);
		$result = $this->db->get('user');

		echo json_encode(['available' => $result->num_rows() === 0]);
	}
}
