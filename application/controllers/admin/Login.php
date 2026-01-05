<?php

/**
 * 
 */
class Login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'form_validation']);
	}

	function index()
	{
		$this->load->view('admin/admin_login');
	}

	public function is_sign_in()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);

		if ($this->form_validation->run() == FALSE) {
			$data['error'] = "Username dan Password harus diisi!";
			$this->load->view('admin/admin_login', $data);
			return;
		}

		if ($username === 'admin' && $password === 'admin') {

			$this->session->set_userdata([
				'admin_username'   => $username,
				'admin_logged_in'  => TRUE,
				'admin_session_id' => session_id()
			]);

			redirect('admin/dashboard');
			return;
		}

		$data['error'] = "USERNAME / PASSWORD YANG ANDA MASUKAN SALAH!";
		$this->load->view('admin/admin_login', $data);
	}
}

function log_out()
	{
		// Jangan sess_destroy() karena itu akan ngehapus session user juga
		$this->session->unset_userdata(['admin_username', 'admin_logged_in', 'admin_session_id']);
		redirect(base_url('admin'));
	}
}
