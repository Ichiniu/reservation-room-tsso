<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'form_validation']);
	}

	public function index()
	{
		$this->load->view('admin/admin_login');
	}

	public function is_sign_in()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);

		if ($this->form_validation->run() === FALSE) {
			$data['error'] = "Username dan Password harus diisi!";
			$this->load->view('admin/admin_login', $data);
			return;
		}

		// Query database (tabel admins baru)
		$admin = $this->db->get_where('admins', [
			'username' => $username,
			'role'     => 'ADMIN'
		])->row();

		if ($admin && password_verify($password, $admin->password)) {
			$this->session->set_userdata([
				'admin_username'   => $admin->username,
				'admin_nama'       => $admin->nama_lengkap,
				'admin_logged_in'  => TRUE,
				'admin_session_id' => session_id()
			]);
			redirect('admin/dashboard');
			return;
		}

		$data['error'] = "USERNAME / PASSWORD YANG ANDA MASUKAN SALAH!";
		$this->load->view('admin/admin_login', $data);
	}

	public function log_out()
	{
		$this->session->unset_userdata(['admin_username', 'admin_logged_in', 'admin_session_id']);
		redirect('admin');
	}
}
