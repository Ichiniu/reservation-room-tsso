<?php

class Login extends CI_Controller
{

	/**
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('login');
	}

	public function sign_in()
	{
		$username = (string)$this->input->post('username', TRUE);
		$password = (string)$this->input->post('password', TRUE);

		// 1. Ambil data user berdasarkan username (case-sensitive BINARY)
		$user = $this->db->query(
			"SELECT * FROM user WHERE BINARY USERNAME = ?",
			[$username]
		)->row();

		if ($user) {
			$authenticated = false;

			// 2. Cek apakah password sudah ter-hash (hash password_hash selalu dimulai dengan '$')
			if (strpos((string)$user->PASSWORD, '$') === 0) {
				if (password_verify($password, $user->PASSWORD)) {
					$authenticated = true;
				}
			} else {
				// 3. Fallback: Cek plain-text (koneksi lama)
				if ($password === $user->PASSWORD) {
					$authenticated = true;
					// Update ke hash agar selanjutnya aman
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$this->db->where('USERNAME', $username);
					$this->db->update('user', ['PASSWORD' => $new_hash]);
				}
			}

			if ($authenticated) {
				$session_data = [
					'username'    => $user->USERNAME,
					'foto_profil' => $user->FOTO_PROFIL ?? '',
					'logged_in'   => TRUE,
					'session_id'  => session_id()
				];
				$this->session->set_userdata($session_data);
				$this->session->set_flashdata('flash_msg', 'Selamat datang, ' . $user->USERNAME . '! Login berhasil.');
				$this->session->set_flashdata('flash_type', 'success');
				redirect('home/' . $user->USERNAME . '/');
				return;
			}
		}

		$this->session->set_flashdata('flash_msg', 'Login Gagal. Username atau Password salah.');
		$this->session->set_flashdata('flash_type', 'error');
		redirect('login');
	}
}
