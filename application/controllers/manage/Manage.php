<?php

class Manage extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library(['session']);

		$method = $this->router->fetch_method();
		if ($method !== 'index' && !$this->session->userdata('manage_logged_in')) {
			redirect('manage');
		}
	}

	function index()
	{
		if ($this->session->userdata('manage_logged_in')) {
			redirect('manage/dashboard');
		}

		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);

		if ($username && $password) {
			$user = $this->db->get_where('admins', [
				'username' => $username,
				'role'     => 'MANAGE'
			])->row();

			if ($user && password_verify($password, $user->password)) {
				$session = array(
					'username'         => $user->username, // Legacy support
					'manage_username'  => $user->username,
					'manage_nama'      => $user->nama_lengkap,
					'manage_logged_in' => TRUE,
					'session_id'       => session_id()
				);
				$this->session->set_userdata($session);
				redirect('manage/dashboard');
				return;
			} else {
				$data['error'] = "Username / Password salah!";
				$this->load->view('manage/manage_login', $data);
				return;
			}
		}

		$this->load->view('manage/manage_login');
	}

	function log_out()
	{
		$this->session->unset_userdata([
			'username',
			'manage_username',
			'manage_nama',
			'manage_logged_in',
			'session_id'
		]);
		redirect('manage');
	}

	function dashboard()
	{
		$this->load->view('manage/dashboard');
	}

	function all_export_to_pdf()
	{
		$this->load->helper('warsito_pdf_helper');
		$this->load->model('gedung/gedung_model');
		$data['row'] = $this->gedung_model->laporan_perawatan_keseluruhan();
		$object = $this->load->view('manage/pdf_report_all', $data, true);
		$filename = 'Report.pdf';
		generate_pdf($object, $filename, true);
	}

	function laporan_kegiatan()
	{
		$this->load->model('gedung/gedung_model');
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$submit = $this->input->get('submit');
		if (!empty($submit)) {
			$data['start_date'] = $start_date;
			$data['end_date'] = $end_date;
			$data['report'] = $this->gedung_model->jadwal_gedung($start_date, $end_date);
			$this->load->view('manage/report_kegiatan_periodic', $data);
		} else {
			$this->load->view('manage/report_kegiatan');
		}
	}

	function kegiatan_export_pdf($start_date, $end_date)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('warsito_pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->jadwal_gedung($start_date, $end_date);
		$object = $this->load->view('manage/pdf_report_kegiatan', $data, true);
		$filename = "Report Kegiatan.pdf";
		generate_pdf($object, $filename, true);
	}
}
