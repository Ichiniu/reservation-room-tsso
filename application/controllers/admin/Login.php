<?php
/**
* 
*/
class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}
	
	function index() {
		$this->load->view('admin/admin_login');
	}

	public function is_sign_in() {
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
	
		$username = $this->input->post('username');
		$password = $this->input->post('password');
	
		if ($this->form_validation->run() == FALSE) {
	
			$data['error'] = "Username dan Password harus diisi!";
			$this->load->view('admin/admin_login', $data);
	
		} else {
	
			if ($username == 'admin' && $password == 'admin') {
	
				$session = array(
					'username' => $username,
					'logged_in' => TRUE,
					'session_id' => session_id()
				);
	
				$this->session->set_userdata($session);
				redirect('admin/dashboard');
	
			} else {
	
				$data['error'] = "USERNAME / PASSWORD YANG ANDA MASUKAN SALAH!";
				$this->load->view('admin/admin_login', $data);
			}
		}
	}
	function log_out() {
		$this->session->sess_destroy();
		redirect(base_url());
	}
}