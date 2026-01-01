<?php

/**
 * 
 */
class Home extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$session_id = $this->session->userdata('username');
		if (empty($session_id)) {
			redirect(site_url() . '/login');
		}
	}

	public function index()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');

		// --- 1. Ambil data dasar seperti biasa ---
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$data['res']  = $this->gedung_model->get_all();

		// --- 2. Ambil filter tanggal & jam dari QUERY STRING (GET) ---
		// contoh URL: /home?tanggal=2025-01-10&jam=09:00
		$tanggal = $this->input->get('tanggal');
		$jam     = $this->input->get('jam');

		$data['tanggal_filter'] = $tanggal;
		$data['jam_filter']     = $jam;

		// --- 3. Hitung ketersediaan per gedung ---
		// Pakai fungsi check_date() yang sudah ada (cek apakah tanggal tsb sudah dibooking)
		$availability = [];

		if (!empty($tanggal)) {
			// kalau ada filter tanggal (jam opsional dulu), cek satu-satu
			foreach ($data['res'] as $row) {
				// get_all() kamu mengembalikan array, jadi akses pakai $row['ID_GEDUNG']
				$id_gedung = $row['ID_GEDUNG'];

				// check_date() akan mengembalikan jumlah booking di tanggal tsb
				// kalau > 0 berarti SUDAH dibooking (tidak available)
				$exist = $this->check_date($tanggal, $id_gedung);

				// true  => tersedia
				// false => sudah dibooking
				$availability[$id_gedung] = ($exist == 0);
			}
		}

		$data['availability'] = $availability;

		// --- 4. Kirim ke view ---
		$this->load->view('/home/home_screen', $data);
	}
	// batas gpt

	public function cancel_order($id_pemesanan)
	{
		$this->load->model('gedung/gedung_model');
		$temp_id = "PMSN000" . $id_pemesanan;
		$tanggal_pesan = $this->gedung_model->get_detail_pesanan($temp_id)->TANGGAL_PEMESANAN;
		$min_refund = date('Y-m-d', time());
		$perbedaan = date_diff(new DateTime($tanggal_pesan), new DateTime($min_refund));
		$c_perbedaan = $perbedaan->format('%d');
		if ($c_perbedaan > 7) {
			$data = array('STATUS' => 3);
			$this->gedung_model->cancel_order($id_pemesanan, $data);
		} else {
			$data = array('STATUS' => 4);
			$this->gedung_model->cancel_order($id_pemesanan, $data);
		}
		$jadwal = array('FINAL_STATUS' => 2);
		$this->gedung_model->delete_jadwal($id_pemesanan, $jadwal);
		redirect('home/pemesanan');
	}

	public function jadwal_gedung()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');

		// semua jadwal yang CONFIRMED
		$data['jadwal'] = $this->gedung_model->jadwal_gedung();
		$data['flag']   = $this->gedung_model->get_pemesanan_flag($username);

		$this->load->view('gedung/jadwal_gedung', $data);
	}


	public function jadwal_per_periode($start_date, $end_date)
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['jadwal'] = $this->gedung_model->jadwal_gedung($start_date, $end_date);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('gedung/jadwal_gedung_per_periode', $data);
	}

	public function view_catering()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_menu_catering();
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('gedung/view_catering', $data);
	}

	function check_date($date, $id_gedung)
	{
		$this->load->model('gedung/gedung_model');
		$data = $this->gedung_model->check_date($date, $id_gedung);
		return $data;
	}

	public function order_gedung($id_gedung)
	{
		$username = $this->session->userdata('username');
		$this->load->model('catering/catering_model');
		$this->load->model('gedung/gedung_model');
		$gedung['hasil'] = $this->gedung_model->get_gedung_name($id_gedung);
		$data['res'] = $this->catering_model->get_catering_name();
		$data['email'] = $this->gedung_model->get_email_address($username);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$hasil = array_merge($gedung, $data);
		$this->load->view('gedung/order_gedung', $hasil);
	}

	public function pemesanan()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_pemesanan($username);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$data['no_data'] = "Data Kosong";
		$data['rows'] = $this->gedung_model->count_pemesanan($username);
		$this->load->view('home/pemesanan', $data);
	}

	public function pembayaran()
	{
		$this->load->model('gedung/gedung_model');
		$username = $this->session->userdata('username');
		$data['res'] = $this->gedung_model->user_detail_pembayaran($username);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/pembayaran', $data);
	}

	public function detail_pemesanan($id_pemesanan)
	{
		$username = $this->session->userdata('username');
		$temp_id = substr($id_pemesanan, 7);
		$this->load->model('gedung/gedung_model');
		$this->gedung_model->update_user_flag($temp_id);
		$data['result'] = $this->gedung_model->get_detail_pesanan($id_pemesanan);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$u = $this->db->select('NAMA_LENGKAP')
			->from('user')
			->where('USERNAME', $username)
			->get()
			->row();

		$data['nama_lengkap_user'] = $u ? $u->NAMA_LENGKAP : $username;
		$this->load->view('home/detail_pemesanan', $data);
	}

	public function upload_proposal($id_pemesanan = null)
	{
		$this->load->helper(['form']);
		$this->load->model('gedung/gedung_model');

		$username = $this->session->userdata('username');
		if (!$username) show_404(); // atau redirect login

		// ambil ID dari segment atau POST
		$id = $id_pemesanan ?: (int)$this->input->post('id_pemesanan');
		if ($id <= 0) show_404();

		// pastikan order ini milik user yg login
		if (!$this->gedung_model->is_order_owner($id, $username)) show_404();

		// ✅ WAJIB UPLOAD: kalau kosong, balikin user
		if (empty($_FILES['proposal']['name'])) {
			$this->session->set_flashdata('upload_error', 'Proposal wajib diupload (PDF/DOC/DOCX).');
			redirect('home/home/validasi_upload/' . $id); // <-- GANTI sesuai URL halaman upload kamu
			return;
		}

		$upload_path = FCPATH . 'assets/user-proposal/';          // path fisik
		$public_path = base_url('assets/user-proposal/') . '/';   // url publik

		// ✅ pastikan folder ada
		if (!is_dir($upload_path)) {
			@mkdir($upload_path, 0775, true);
		}

		$file_name = $username . "_" . date('dmY_His');

		$config = [
			'upload_path'   => $upload_path,
			'allowed_types' => 'pdf|doc|docx',
			'max_size'      => 800, // KB
			'file_name'     => $file_name,
			'overwrite'     => false,
		];

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('proposal')) {
			$this->session->set_flashdata('upload_error', strip_tags($this->upload->display_errors()));
			redirect('home/home/validasi_upload/' . $id); // <-- GANTI sesuai URL halaman upload kamu
			return;
		}

		$upload_data = $this->upload->data();
		$doc_name    = $upload_data['file_name'];

		$data = [
			'ID_PEMESANAN'    => $id,
			'PATH'            => $public_path,
			'FILE_NAME'       => $doc_name,
			'DESKRIPSI_ACARA' => $this->input->post('deskripsi-acara', TRUE),
		];

		// update kalau sudah ada, insert kalau belum
		if ($this->gedung_model->proposal_exists($id)) {
			$this->gedung_model->update_proposal($id, $data);
		} else {
			$this->gedung_model->upload_proposal($data);
		}

		$this->load->view('home/success_page');
	}


	public function order()
	{
		$this->load->model('gedung/gedung_model');

		$tanggal_pesan = $this->input->post('tgl_pesan', TRUE);
		$id_gedung     = $this->uri->segment(4);
		$username      = $this->session->userdata('username');

		$tipe_jam = $this->input->post('tipe_jam', TRUE);
		if (empty($tipe_jam)) $tipe_jam = 'CUSTOM';

		$paket = array(
			'HALF_DAY_PAGI'  => array('08:00', '12:00'),
			'HALF_DAY_SIANG' => array('13:00', '16:00'),
			'FULL_DAY'       => array('08:00', '17:00'),
		);

		if ($tipe_jam === 'CUSTOM') {
			$jam_mulai   = $this->input->post('jam_pesan', TRUE);
			$jam_selesai = $this->input->post('jam_selesai', TRUE);

			if (empty($jam_mulai) || empty($jam_selesai)) {
				$this->session->set_flashdata('error', 'Jam mulai dan jam selesai wajib diisi.');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}
		} elseif (isset($paket[$tipe_jam])) {
			$jam_mulai   = $paket[$tipe_jam][0];
			$jam_selesai = $paket[$tipe_jam][1];
		} else {
			show_error('Tipe jam tidak valid');
			return;
		}

		if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
			$this->session->set_flashdata('error', 'Jam mulai harus lebih kecil dari jam selesai.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		$min_pesan = date('Y-m-d', strtotime("+10 day"));

		if ($tanggal_pesan < $min_pesan) {
			$this->load->view('errors/pemesanan_alert', array(
				'tgl_pesan' => $tanggal_pesan,
				'min_pesan' => $min_pesan
			));
			return;
		}
		// ✅ cek bentrok untuk jadwal yang sudah "terkunci" (PENDING / CONFIRMED)
		if ($this->gedung_model->has_locked_conflict($id_gedung, $tanggal_pesan, $jam_mulai, $jam_selesai)) {
			$this->session->set_flashdata(
				'error',
				'Maaf, jadwal tersebut sudah dipesan dan sudah ada pembayaran (menunggu verifikasi / sudah dikonfirmasi). Silakan cek pada halaman Jadwal Gedung.'
			);
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}


		// ✅ cek bentrok harus pakai JAM
		$exist = $this->gedung_model->check_date($tanggal_pesan, $id_gedung, $jam_mulai, $jam_selesai);
		if ($exist > 0) {
			$this->session->set_flashdata('error', 'Tanggal/gedung sudah terbooking.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ✅ REQUEST_ID stabil
		$request_id = $this->input->post('request_id', TRUE);
		if (empty($request_id)) {
			$request_id = sha1(
				session_id() . '|' . $username . '|' . $id_gedung . '|' .
					$tanggal_pesan . '|' . $jam_mulai . '|' . $jam_selesai . '|' . $tipe_jam
			);
		}

		$data = array(
			'USERNAME'         => $username,
			'TANGGAL_PEMESANAN' => $tanggal_pesan,
			'JAM_PEMESANAN'     => $jam_mulai,
			'JAM_SELESAI'       => $jam_selesai,
			'TIPE_JAM'          => $tipe_jam,
			'EMAIL'             => $this->input->post('email', TRUE),
			'ID_CATERING'       => $this->input->post('catering', TRUE),
			'ID_GEDUNG'         => $id_gedung,
			'JUMLAH_CATERING'   => $this->input->post('jumlah-porsi', TRUE),
			'STATUS'            => 0,
			'REQUEST_ID'        => $request_id,
		);

		$id_pemesanan = $this->gedung_model->insert_pemesanan($data);

		if ($id_pemesanan === false) {
			$row = $this->db->get_where('pemesanan', array('REQUEST_ID' => $request_id))->row_array();
			if (!empty($row)) {
				redirect('home/confirm-order/' . (int)$row['ID_PEMESANAN']);
				return;
			}
			show_error('Gagal membuat pemesanan. Silakan coba lagi.');
			return;
		}

		redirect('home/confirm-order/' . $id_pemesanan);
	}



	public function edit_data($user)
	{
		$this->load->model('user/user_model');
		$this->load->view('/home/edit_data');
		$data = array(
			'password' => $password = $this->input->post('password'),
			'email' =>  $email = $this->input->post('email')
		);
		if (isset($_POST['password'])) {
			$this->user_model->update_data($user, $data);
			echo "<script> alert('Data Diperbarui'); </script>";
			redirect('/home/home/dashboard/' . $user . '/', 'refresh');
		}
	}

	public function sort_by_name()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->sort_by_name();
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('/home/home_screen', $data);
	}

	public function sort_by_capacity()
	{
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->sort_by_capacity();
		$username = $this->session->userdata('username');
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('/home/home_screen', $data);
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url());
	}

	public function gedung_details($id_gedung)
	{
		$this->load->model('gedung/gedung_model');
		$id_gedung = $this->uri->segment(3);
		$gallery['gallery'] = $this->gedung_model->get_gedung_img($id_gedung);
		$details['result'] = $this->gedung_model->gedung_details($id_gedung);
		$username = $this->session->userdata('username');
		$flag['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$data = array_merge($gallery, $details, $flag);
		$this->load->view('gedung/gedung_details', $data);
	}

	public function search_gedung($nama_gedung)
	{
		$this->load->helper('form');
		$nama_gedung = $this->input->get('search_gedung');
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->search_gedung($nama_gedung);
		$username = $this->session->userdata('username');
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/search_gedung', $data);
	}

	public function confirm_order($id_pemesanan)
	{
		$this->load->model('gedung/gedung_model');

		$username = $this->session->userdata('username');
		if (!$username) show_404();

		$hasil['res'] = $this->gedung_model->get_order_by_id_user($id_pemesanan, $username);
		if (empty($hasil['res'])) show_404();

		$hasil['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/confirm_order', $hasil);
	}
}
