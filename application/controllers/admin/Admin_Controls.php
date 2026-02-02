<?php

/**
 * 
 */
class Admin_Controls extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		// wajib load session kalau belum
		$this->load->library('session');

		// cek login admin
		if ($this->session->userdata('admin_logged_in') !== TRUE) {
			redirect('admin'); // balik ke login admin
			return;
		}
	}



	function index()
	{
		$this->load->view('admin/home');
	}

	function tambah_gedung()
	{
		$this->load->helper(['form', 'url']);
		$this->load->model('gedung/gedung_model');

		$submit = $this->input->post('submit');

		// untuk view badge
		$data_view['result'] = $this->gedung_model->get_pending_transaction();

		if (empty($submit)) {
			$this->load->view('admin/tambah_gedung', $data_view);
			return;
		}

		// 1) INSERT GEDUNG (SEKALI SAJA)
		$data_gedung = array(
			'NAMA_GEDUNG'      => $this->input->post('nama_gedung'),
			'KAPASITAS'        => $this->input->post('kapasitas_gedung'),
			'ALAMAT'           => $this->input->post('alamat_gedung'),
			'DESKRIPSI_GEDUNG' => $this->input->post('deskripsi_gedung'),
			'fasilitas'        => $this->input->post('fasilitas_gedung'), // ✅ TAMBAH
			'HARGA_SEWA'       => $this->input->post('harga_sewa')
		);

		$this->gedung_model->insert_gedung($data_gedung);

		// ambil ID terakhir dengan aman
		$id_gedung_obj = $this->gedung_model->get_last_id_gedung();
		$id_gedung = $id_gedung_obj ? (int)$id_gedung_obj->ID_GEDUNG : 0;

		if ($id_gedung <= 0) {
			show_error('Gagal menyimpan data gedung.');
			return;
		}

		// 2) SETUP UPLOAD (GAMBAR OPSIONAL)
		$path = "./assets/images/gedung/";
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'jpg|png';
		$config['max_size']      = 500;
		$config['max_width']     = 500;
		$config['max_height']    = 500;

		$this->load->library('upload');
		$this->upload->initialize($config);

		$base_url = base_url();
		$img_path = $base_url . "assets/images/gedung/";

		foreach ($_FILES as $key => $value) {
			if (empty($value['name'])) continue; // skip kalau kosong

			if (!$this->upload->do_upload($key)) {
				// kalau upload 1 gambar gagal, lanjut gambar lainnya
				// kamu bisa simpan error ke flashdata kalau mau
				continue;
			}

			$files = $this->upload->data();

			$img_data = array(
				'ID_GEDUNG'   => $id_gedung,
				'NAMA_GEDUNG' => $this->input->post('nama_gedung'),
				'PATH'        => $img_path,
				'IMG_NAME'    => $files['file_name']
			);

			$this->gedung_model->insert_gedung_img($img_data);
		}

		redirect('admin/gedung');
	}


	function rekap_aktivitas()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$this->load->view('admin/rekap_aktivitas', $data);
	}

	function rekap_aktivitas_det($tanggal_awal, $tanggal_akhir)
	{
		$first_date = $this->input->get('start_date');
		$second_date = $this->input->get('end_date');
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['hasil'] = $this->gedung_model->jadwal_gedung($first_date, $second_date);
		$data['first_period'] = $first_date;
		$data['last_period'] = $second_date;
		$this->load->view('admin/rekap_aktivitas_det', $data);
	}

	function rekap_pembayaran()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$this->load->view('admin/rekap_pembayaran', $data);
	}

	function rekap_pembayaran_det($tanggal_awal, $tanggal_akhir)
	{
		$this->load->model('gedung/gedung_model');
		$tanggal_awal = $this->input->get('start_date');
		$tanggal_akhir = $this->input->get('end_date');
		$data['start_date'] = $tanggal_awal;
		$data['end_date'] = $tanggal_akhir;
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['row'] = $this->gedung_model->laporan_perawatan_periodic($tanggal_awal, $tanggal_akhir);
		$this->load->view('admin/rekap_pembayaran_det', $data);
	}

	function rekap_transaksi()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$this->load->view('admin/rekap_transaksi', $data);
	}

	function rekap_transaksi_det($tanggal_awal, $tanggal_akhir)
	{
		$this->load->model('gedung/gedung_model');
		$tanggal_awal = $this->input->get('start_date');
		$tanggal_akhir = $this->input->get('end_date');
		$data['start_date'] = $tanggal_awal;
		$data['end_date'] = $tanggal_akhir;
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['row'] = $this->gedung_model->laporan_pembayaran_periodic($tanggal_awal, $tanggal_akhir);
		$this->load->view('admin/rekap_transaksi_det', $data);
	}

	function transaksi_export_pdf($start_date, $end_date)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('warsito_pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->laporan_pembayaran_periodic($start_date, $end_date);
		$object = $this->load->view('admin/pdf_report_transaksi', $data, true);
		$filename = "Report Transaksi.pdf";
		generate_pdf($object, $filename, true);
	}
	public function detail_pemesanan($id_pemesanan)
	{
		$this->load->model('gedung/gedung_model');

		$data['result'] = $this->gedung_model->get_detail_pesanan($id_pemesanan);

		if (!$data['result']) {
			show_error('Data pemesanan tidak ditemukan');
			return;
		}

		// optional biar view aman
		$data['user_username'] = !empty($data['result']->USERNAME) ? $data['result']->USERNAME : '';
		$data['user_email']    = !empty($data['result']->EMAIL) ? $data['result']->EMAIL : '';
		$data['nama_lengkap_user'] = !empty($data['result']->NAMA_LENGKAP) ? $data['result']->NAMA_LENGKAP : $data['user_username'];
		$data['proposal_details'] = null;

		$this->load->view('admin/Detail_Pemesanan', $data);
	}



	function pembayaran()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['pembayaran'] = $this->gedung_model->get_all_pembayaran();
		$data['notifs_admin_trx'] = $this->db->order_by('id', 'DESC')
			->limit(10)
			->like('type', 'ADMIN_TRANSAKSI_', 'after')
			->get_where('notifications', [
				'username' => 'admin',
				'read_at'  => null
			])
			->result_array();

		$this->load->view('admin/pembayaran', $data);
	}

	function delete_jadwal($id_gedung)
	{
		$this->load->model('gedung/gedung_model');
		$data = array(
			'FINAL_STATUS' => 2
		);
		$this->gedung_model->delete_jadwal($id_gedung, $data);
		redirect('admin/dashboard');
	}

	function pemesanan2()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['pemesanan'] = $this->gedung_model->get_all_pemesanan();
		$this->load->view('admin/pemesanan_2', $data);
	}

	function read_transaction($id_pembayaran)
	{
		$id_pembayaran = (int)$id_pembayaran;

		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();

		// INI YANG BENAR (karena di model kamu adanya get_details_transaction)
		$data['details'] = $this->gedung_model->get_details_transaction($id_pembayaran);

		if (!$data['details']) {
			show_error('Data pembayaran tidak ditemukan');
			return;
		}

		// pakai nama file view yang ada: Detail_Pembayaran.php
		$this->load->view('admin/Detail_Pembayaran', $data);
	}


	function transaksi()
	{
		$this->load->model('gedung/gedung_model');
		$data['pemesanan'] = $this->gedung_model->get_all_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['notifs_admin_inbox'] = $this->db->order_by('id', 'DESC')
			->limit(10)
			->like('type', 'ADMIN_INBOX_', 'after')
			->get_where('notifications', [
				'username' => 'admin',
				'read_at'  => null
			])
			->result_array();

		$this->load->view('admin/pemesanan', $data);
	}

	public function detail_transaksi($id_pemesanan)
	{
		$this->load->helper('date');
		$this->load->model('gedung/gedung_model');
		$this->load->library('notification_service');

		$temp_id = (int) preg_replace('/\D+/', '', (string) $id_pemesanan);
		if ($temp_id <= 0) {
			show_404();
			return;
		}

		// ambil pemesan (untuk notif user)
		$ps = $this->db->get_where('pemesanan', array('ID_PEMESANAN' => $temp_id))->row_array();
		$username_user = (!empty($ps) && isset($ps['USERNAME'])) ? $ps['USERNAME'] : null;

		// === PROSES POST DULU ===
		if ($this->input->method(TRUE) === 'POST') {
			$status  = (int) $this->input->post('status-proposal');
			$remarks = trim((string) $this->input->post('remarks', TRUE));

			// URL langsung ke detail pemesanan
			$detailUrl = 'home/pemesanan/details/PMSN000' . $temp_id;

			// TERIMA PROPOSAL -> PROPOSAL APPROVE (1)
			if ($status === 1) {
				$this->gedung_model->update_transaksi($temp_id, 1, '');

				// badge lama user
				$this->gedung_model->mark_flag_unread('PMSN000' . $temp_id);

				// notif + email user
				if (!empty($username_user)) {
					$this->notification_service->notifyUser(
						$username_user,
						'USER_PEMESANAN',
						'Proposal disetujui',
						'Proposal untuk pesanan PMSN000' . $temp_id . ' telah disetujui. Silakan lanjut ke transaksi/pembayaran.',
						$detailUrl,
						true
					);
				}

				redirect('admin/transaksi');
				return;
			}

			// TOLAK PROPOSAL -> REJECTED (4)
			if ($status === 4) {
				if ($remarks === '') {
					show_error('Catatan/remarks wajib diisi saat menolak proposal.');
					return;
				}

				$this->gedung_model->update_transaksi($temp_id, 4, $remarks);

				$this->gedung_model->mark_flag_unread('PMSN000' . $temp_id);

				if (!empty($username_user)) {
					$this->notification_service->notifyUser(
						$username_user,
						'USER_PEMESANAN',
						'Proposal ditolak',
						'Proposal untuk pesanan PMSN000' . $temp_id . ' ditolak. Catatan: ' . $remarks,
						$detailUrl,
						true
					);
				}

				redirect('admin/transaksi');
				return;
			}

			show_error('Status tidak valid.');
			return;
		}

		// === BARU LOAD DATA UNTUK VIEW (GET) ===
		$data = array();
		$data['details'] = $this->gedung_model->get_proposal_by_id($temp_id);
		$data['hasil']   = $this->gedung_model->get_detail_pesanan($id_pemesanan);
		$data['result']  = $this->gedung_model->get_pending_transaction();

		$this->load->view('admin/detail_transaksi', $data);
	}


	function send_mail($to_email, $pesan)
	{
		$from_email = "Admin Pembayaran";
		$this->load->library('email');
		$this->email->from('no-reply@domain.com', 'Booking Smarts');
		$this->email->to($to_email);
		$this->email->subject('Deadline Pembayaran Reservasi Gedung');
		$this->email->set_mailtype('html');
		$this->email->message($pesan);
		$this->email->send();
		if (!$this->email->send()) {
			echo $this->email->print_debugger();
		}
	}

	function download_proposal($id_pemesanan)
	{
		$this->load->helper('download');
		$this->load->model('gedung/gedung_model');

		$temp_id = (int) preg_replace('/\D+/', '', (string)$id_pemesanan);
		if ($temp_id <= 0) show_404();

		$data = $this->gedung_model->get_proposal_by_id($temp_id);
		if (!$data || empty($data->FILE_NAME)) {
			show_error('Proposal belum diupload.');
			return;
		}

		// Lebih aman ambil dari file system (bukan URL)
		$fullpath = FCPATH . 'assets/user-proposal/' . $data->FILE_NAME;
		if (!is_file($fullpath)) {
			show_error('File proposal tidak ditemukan di server.');
			return;
		}

		force_download($data->FILE_NAME, file_get_contents($fullpath));
	}


	function update_transaksi($id_pemesanan)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('form');
		$temp_id = (int) preg_replace('/\D+/', '', (string)$id_pemesanan); // ambil angka saja
		if ($temp_id <= 0) show_404();
	}

	function tambah_catering($id_catering = null)
	{
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('catering/catering_model');
		$this->load->model('gedung/gedung_model');

		// ===== Templates (buat tombol "Template" di form admin) =====
		$templates = array(
			'PAKET_RASA_1' => "{\n  \"categories\": [\n    {\n      \"key\": \"nasi_mie\",\n      \"label\": \"Menu Nasi & Mie\",\n      \"choose\": 2,\n      \"note\": \"Bebas memilih 2 macam\",\n      \"items\": [\n        \"Nasi Putih\",\n        \"Nasi Goreng Sosis\",\n        \"Nasi Goreng Bawang\",\n        \"Nasi Goreng Tradisional\",\n        \"Mie Goreng Jawa\",\n        \"Mie Goreng\",\n        \"Bihun Goreng\",\n        \"Oseng Soun\"\n      ]\n    },\n    {\n      \"key\": \"ayam\",\n      \"label\": \"Menu Ayam\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Ayam Bakar Mbe\",\n        \"Ayam Bakar Rujak\",\n        \"Ayam Bakar Kacang\",\n        \"Ayam Bakar Balado\",\n        \"Ayam Gor Kalasan\",\n        \"Ayam Gor Renyah\",\n        \"Ayam Kremes\",\n        \"Semur Ayam\"\n      ]\n    },\n    {\n      \"key\": \"lauk_pendamping\",\n      \"label\": \"Lauk Pendamping\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Tempe Crispy\",\n        \"Tempe Bacem\",\n        \"Bakwan Jagung\",\n        \"Tahu Crispy\",\n        \"Tahu Bacem\",\n        \"Bakwan Sayur\"\n      ]\n    },\n    {\n      \"key\": \"sayur\",\n      \"label\": \"Aneka Sayur\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Gulai Daun Singkong\",\n        \"Sayur Kare\",\n        \"Sambal Goreng Jipan\",\n        \"Sayur Ndeso\",\n        \"Sayur Lodeh\",\n        \"Buncis Asem Pedes\",\n        \"Cah Sayuran\",\n        \"Cap Jay Jawa\",\n        \"Cah Toge Ikan Asin\",\n        \"Orak Arik Sayuran\",\n        \"Tumis Kcg Panjang\",\n        \"Jamur Kembang Kol\",\n        \"Tumis Sawi Sendok\",\n        \"Tumis Buncis Wortel\",\n        \"Tumis Toge Tahu\",\n        \"Daun Pepaya Teri\",\n        \"Tumis Jamur\",\n        \"Pencok Sayur\",\n        \"Tumis Pare\",\n        \"Kembang Pepaya\"\n      ]\n    },\n    {\n      \"key\": \"es\",\n      \"label\": \"Aneka Es\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Es Campur\",\n        \"Es Buah\",\n        \"Es Cendol\",\n        \"Es Teler\"\n      ]\n    },\n    {\n      \"key\": \"pudding\",\n      \"label\": \"Aneka Pudding\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Puding Buah\",\n        \"Puding Coklat\",\n        \"Puding Yogurt\",\n        \"Rainbow Puding\"\n      ]\n    }\n  ]\n}",
			'PAKET_RASA_2' => "{\n  \"categories\": [\n    {\n      \"key\": \"nasi_mie\",\n      \"label\": \"Menu Nasi & Mie\",\n      \"choose\": 2,\n      \"note\": \"Bebas memilih 2 macam\",\n      \"items\": [\n        \"Nasi Putih\",\n        \"Nasi Goreng Sosis\",\n        \"Nasi Goreng Bawang\",\n        \"Nasi Goreng Tradisional\",\n        \"Mie Goreng Jawa\",\n        \"Mie Goreng\",\n        \"Bihun Goreng\",\n        \"Oseng Soun\"\n      ]\n    },\n    {\n      \"key\": \"ayam\",\n      \"label\": \"Menu Ayam\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Ayam Bakar Mbe\",\n        \"Ayam Bakar Rujak\",\n        \"Ayam Bakar Kacang\",\n        \"Ayam Bakar Balado\",\n        \"Ayam Gor Kalasan\",\n        \"Ayam Gor Renyah\",\n        \"Ayam Kremes\",\n        \"Semur Ayam\"\n      ]\n    },\n    {\n      \"key\": \"lauk_pendamping\",\n      \"label\": \"Lauk Pendamping\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Tempe Crispy\",\n        \"Tempe Bacem\",\n        \"Bakwan Jagung\",\n        \"Tahu Crispy\",\n        \"Tahu Bacem\",\n        \"Bakwan Sayur\"\n      ]\n    },\n    {\n      \"key\": \"sayur\",\n      \"label\": \"Aneka Sayur\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Gulai Daun Singkong\",\n        \"Sayur Kare\",\n        \"Sambal Goreng Jipan\",\n        \"Sayur Ndeso\",\n        \"Sayur Lodeh\",\n        \"Buncis Asem Pedes\",\n        \"Cah Sayuran\",\n        \"Cap Jay Jawa\",\n        \"Cah Toge Ikan Asin\",\n        \"Orak Arik Sayuran\",\n        \"Tumis Kcg Panjang\",\n        \"Jamur Kembang Kol\",\n        \"Tumis Sawi Sendok\",\n        \"Tumis Buncis Wortel\",\n        \"Tumis Toge Tahu\",\n        \"Daun Pepaya Teri\",\n        \"Tumis Jamur\",\n        \"Pencok Sayur\",\n        \"Tumis Pare\",\n        \"Kembang Pepaya\"\n      ]\n    },\n    {\n      \"key\": \"es\",\n      \"label\": \"Aneka Es\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Es Campur\",\n        \"Es Buah\",\n        \"Es Cendol\",\n        \"Es Teler\"\n      ]\n    },\n    {\n      \"key\": \"pudding\",\n      \"label\": \"Aneka Pudding\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Puding Buah\",\n        \"Puding Coklat\",\n        \"Puding Yogurt\",\n        \"Rainbow Puding\"\n      ]\n    },\n    {\n      \"key\": \"mini_pastry\",\n      \"label\": \"Mini Pastry\",\n      \"choose\": 2,\n      \"note\": \"Pilih 2 macam\",\n      \"items\": [\n        \"Brownies\",\n        \"Pisang Goreng\",\n        \"Red Velvet\",\n        \"Rainbow Cake\"\n      ]\n    },\n    {\n      \"key\": \"menu_stall\",\n      \"label\": \"Menu Stall\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Soup Kimlo Mie Bakso\",\n        \"Soup Sosis Ayam\",\n        \"Mie Godog\",\n        \"Soup Tekwan\",\n        \"Soto Ayam\",\n        \"Soup Pengantin\",\n        \"Soup Sayuran\",\n        \"Mie Bakso\",\n        \"Nasi Brongkos\"\n      ]\n    },\n    {\n      \"key\": \"menu_ikan\",\n      \"label\": \"Menu Ikan\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Ikan Goreng Tepung\",\n        \"Ikan Pesmol\",\n        \"Ikan Asam Pedas\",\n        \"Ikan Saus Padang\",\n        \"Ikan Sambal Matah\",\n        \"Ikan Asam Manis\"\n      ]\n    }\n  ],\n  \"addons\": [\n    {\n      \"label\": \"Penambahan Stall\",\n      \"price\": 35000,\n      \"items\": [\n        \"Rawon\",\n        \"Daging Kacang Merah\",\n        \"Nasi Gudeg\",\n        \"Empal Gentong\",\n        \"Lontong Gulai Iga\",\n        \"Nasi Liwet\",\n        \"Soup Timlo\",\n        \"Soup Pengantin\",\n        \"Soto Betawi\",\n        \"Tengkleng\"\n      ]\n    },\n    {\n      \"label\": \"Penambahan Menu Daging\",\n      \"price\": 25000,\n      \"items\": [\n        \"Gulai Daging\",\n        \"Rendang\",\n        \"Kalio Daging\",\n        \"Terik Daging\",\n        \"Sapi Lada Hitam\",\n        \"Daging Cabe Ijo\",\n        \"Opor Daging\",\n        \"Tongseng Daging\"\n      ]\n    }\n  ]\n}",
			'PAKET_RASA_3' => "{\n  \"categories\": [\n    {\n      \"key\": \"nasi_mie\",\n      \"label\": \"Menu Nasi & Mie\",\n      \"choose\": 2,\n      \"note\": \"Bebas memilih 2 macam\",\n      \"items\": [\n        \"Nasi Putih\",\n        \"Nasi Goreng Sosis\",\n        \"Nasi Goreng Bawang\",\n        \"Nasi Goreng Tradisional\",\n        \"Mie Goreng Jawa\",\n        \"Mie Goreng\",\n        \"Bihun Goreng\",\n        \"Oseng Soun\"\n      ]\n    },\n    {\n      \"key\": \"ayam\",\n      \"label\": \"Menu Ayam\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Ayam Bakar Mbe\",\n        \"Ayam Bakar Rujak\",\n        \"Ayam Bakar Kacang\",\n        \"Ayam Bakar Balado\",\n        \"Ayam Gor Kalasan\",\n        \"Ayam Gor Renyah\",\n        \"Ayam Kremes\",\n        \"Semur Ayam\"\n      ]\n    },\n    {\n      \"key\": \"lauk_pendamping\",\n      \"label\": \"Lauk Pendamping\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Tempe Crispy\",\n        \"Tempe Bacem\",\n        \"Bakwan Jagung\",\n        \"Tahu Crispy\",\n        \"Tahu Bacem\",\n        \"Bakwan Sayur\"\n      ]\n    },\n    {\n      \"key\": \"sayur\",\n      \"label\": \"Aneka Sayur\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Gulai Daun Singkong\",\n        \"Sayur Kare\",\n        \"Sambal Goreng Jipan\",\n        \"Sayur Ndeso\",\n        \"Sayur Lodeh\",\n        \"Buncis Asem Pedes\",\n        \"Cah Sayuran\",\n        \"Cap Jay Jawa\",\n        \"Cah Toge Ikan Asin\",\n        \"Orak Arik Sayuran\",\n        \"Tumis Kcg Panjang\",\n        \"Jamur Kembang Kol\",\n        \"Tumis Sawi Sendok\",\n        \"Tumis Buncis Wortel\",\n        \"Tumis Toge Tahu\",\n        \"Daun Pepaya Teri\",\n        \"Tumis Jamur\",\n        \"Pencok Sayur\",\n        \"Tumis Pare\",\n        \"Kembang Pepaya\"\n      ]\n    },\n    {\n      \"key\": \"es\",\n      \"label\": \"Aneka Es\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Es Campur\",\n        \"Es Buah\",\n        \"Es Cendol\",\n        \"Es Teler\"\n      ]\n    },\n    {\n      \"key\": \"pudding\",\n      \"label\": \"Aneka Pudding\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Puding Buah\",\n        \"Puding Coklat\",\n        \"Puding Yogurt\",\n        \"Rainbow Puding\"\n      ]\n    },\n    {\n      \"key\": \"mini_pastry\",\n      \"label\": \"Mini Pastry\",\n      \"choose\": 2,\n      \"note\": \"Pilih 2 macam\",\n      \"items\": [\n        \"Brownies\",\n        \"Pisang Goreng\",\n        \"Red Velvet\",\n        \"Rainbow Cake\"\n      ]\n    },\n    {\n      \"key\": \"menu_stall\",\n      \"label\": \"Menu Stall\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Soup Kimlo Mie Bakso\",\n        \"Soup Sosis Ayam\",\n        \"Mie Godog\",\n        \"Soup Tekwan\",\n        \"Soto Ayam\",\n        \"Soup Pengantin\",\n        \"Soup Sayuran\",\n        \"Mie Bakso\",\n        \"Nasi Brongkos\",\n        \"Soto Betawi\"\n      ]\n    },\n    {\n      \"key\": \"menu_ikan\",\n      \"label\": \"Menu Ikan\",\n      \"choose\": 1,\n      \"note\": \"Pilih 1 macam\",\n      \"items\": [\n        \"Ikan Goreng Tepung\",\n        \"Ikan Pesmol\",\n        \"Ikan Asam Pedas\",\n        \"Ikan Saus Padang\",\n        \"Ikan Sambal Matah\",\n        \"Ikan Asam Manis\"\n      ]\n    }\n  ],\n  \"addons\": [\n    {\n      \"label\": \"Penambahan Stall\",\n      \"price\": 35000,\n      \"items\": [\n        \"Rawon\",\n        \"Daging Kacang Merah\",\n        \"Nasi Gudeg\",\n        \"Empal Gentong\",\n        \"Lontong Gulai Iga\",\n        \"Nasi Liwet\",\n        \"Soup Timlo\",\n        \"Soup Pengantin\",\n        \"Soto Betawi\",\n        \"Tengkleng\"\n      ]\n    },\n    {\n      \"label\": \"Penambahan Menu Daging\",\n      \"price\": 25000,\n      \"items\": [\n        \"Gulai Daging\",\n        \"Rendang\",\n        \"Kalio Daging\",\n        \"Terik Daging\",\n        \"Sapi Lada Hitam\",\n        \"Daging Cabe Ijo\",\n        \"Opor Daging\",\n        \"Tongseng Daging\"\n      ]\n    }\n  ]\n}",
			'HALF_DAY_MEETING' => "{\n  \"includes\": [\n    \"Meeting Room for 4 hours\",\n    \"Internet\",\n    \"LCD Projector\",\n    \"Sound System\",\n    \"Paper Notes\",\n    \"Pencil\",\n    \"Mineral water & candies on table\",\n    \"1x Lunch / Dinner\",\n    \"1x Coffee Break\"\n  ],\n  \"coffee_break\": {\n    \"categories\": [\n      {\n        \"key\": \"snack\",\n        \"label\": \"Snack (pilih 4 macam) + Kletikan\",\n        \"choose\": 4,\n        \"note\": \"Pilih 4 macam plus kletikan\",\n        \"items\": [\n          \"Batagor\",\n          \"Lumpia\",\n          \"Sosis Solo\",\n          \"Cireng\",\n          \"Siomay\",\n          \"Risol Mayo\",\n          \"Martabak Mini\",\n          \"Pisang Goreng\",\n          \"Rainbow Cake\",\n          \"Brownies Coklat\",\n          \"Kroket Cake Tape\"\n        ]\n      }\n    ]\n  }\n}",
			'FULL_DAY_MEETING' => "{\n  \"includes\": [\n    \"Meeting Room for 4 hours\",\n    \"Internet\",\n    \"LCD Projector\",\n    \"Sound System\",\n    \"Paper Notes\",\n    \"Pencil\",\n    \"Candies\",\n    \"2x Mineral water on table\",\n    \"1x Lunch / Dinner\",\n    \"2x Coffee Break\"\n  ],\n  \"coffee_break\": {\n    \"categories\": [\n      {\n        \"key\": \"snack\",\n        \"label\": \"Snack (pilih 4 macam) + Kletikan\",\n        \"choose\": 4,\n        \"note\": \"Pilih 4 macam plus kletikan\",\n        \"items\": [\n          \"Batagor\",\n          \"Lumpia\",\n          \"Sosis Solo\",\n          \"Cireng\",\n          \"Siomay\",\n          \"Risol Mayo\",\n          \"Martabak Mini\",\n          \"Pisang Goreng\",\n          \"Rainbow Cake\",\n          \"Brownies Coklat\",\n          \"Kroket Cake Tape\"\n        ]\n      }\n    ]\n  }\n}",
			'COFFEE_BREAK_SNACKS' => "{\n  \"categories\": [\n    {\n      \"key\": \"snack\",\n      \"label\": \"Snack (pilih 4 macam) + Kletikan\",\n      \"choose\": 4,\n      \"note\": \"Pilih 4 macam plus kletikan\",\n      \"items\": [\n        \"Batagor\",\n        \"Lumpia\",\n        \"Sosis Solo\",\n        \"Cireng\",\n        \"Siomay\",\n        \"Risol Mayo\",\n        \"Martabak Mini\",\n        \"Pisang Goreng\",\n        \"Rainbow Cake\",\n        \"Brownies Coklat\",\n        \"Kroket Cake Tape\"\n      ]\n    }\n  ]\n}",
		);

		$data['menu_json_templates'] = $templates;
		// default template untuk add baru (biar admin tinggal edit)
		$data['menu_json_template'] = $templates['PAKET_RASA_2'];

		// ===== Load data untuk sidebar badge =====
		$data['result'] = $this->gedung_model->get_pending_transaction();

		// ===== Mode edit (jika ada ID di URL atau hidden input) =====
		$id = (int)$id_catering;
		if ($id <= 0) {
			$id = (int)$this->input->post('id_catering');
		}
		if ($id > 0) {
			$data['catering'] = $this->catering_model->get_by_id($id);
		}

		$submit = $this->input->post('submit');
		if (!empty($submit)) {
			$min_pax = $this->input->post('min_pax');
			$min_pax = ($min_pax === '' || $min_pax === null) ? null : (int)$min_pax;

			$payload = array(
				'NAMA_PAKET' => $this->input->post('nama_paket', true),
				'MENU_JSON' => $this->input->post('menu_json'),
				'HARGA' => (int)$this->input->post('harga'),
				'JENIS' => $this->input->post('jenis', true),
				'MIN_PAX' => $min_pax
			);

			if ($id > 0) {
				$this->catering_model->update_catering($id, $payload);
			} else {
				$this->catering_model->add_catering($payload);
			}

			redirect('admin/catering');
		}

		$this->load->view('admin/tambah_catering', $data);
	}

	function delete_catering()
	{
		$this->load->helper('url');
		$this->load->model('catering/catering_model');
		$id = (int)$this->input->post('id_catering');
		if ($id > 0) {
			$this->catering_model->delete_catering($id);
		}
		redirect('admin/catering');
	}
	function delete_gedung($id_gedung)
	{
		$this->load->model('gedung/gedung_model');
		$this->gedung_model->delete_gedung($id_gedung);
		redirect('admin/gedung');
	}

	function edit_gedung($id_gedung)
	{
		$this->load->model('gedung/gedung_model');

		$simpan = $this->input->post('submit');

		// 1) PROSES POST DULU
		if (!empty($simpan)) {
			$data = array(
				'NAMA_GEDUNG'      => $this->input->post('nama_gedung'),
				'KAPASITAS'        => $this->input->post('kapasitas_gedung'),
				'ALAMAT'           => $this->input->post('alamat_gedung'),
				'DESKRIPSI_GEDUNG' => $this->input->post('deskripsi_gedung'),
				'fasilitas'        => $this->input->post('fasilitas_gedung'), // ✅ TAMBAH
				'HARGA_SEWA'       => $this->input->post('harga_sewa')
			);

			$this->gedung_model->update_gedung((int)$id_gedung, $data);
			redirect('admin/gedung');
			return;
		}

		// 2) BARU LOAD DATA UNTUK VIEW
		$details['res']    = $this->gedung_model->get_pending_transaction();
		$details['result'] = $this->gedung_model->gedung_details((int)$id_gedung);

		$this->load->view('admin/edit_gedung', $details);
	}


	function kegiatan_export_pdf($start_date, $end_date)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('warsito_pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->jadwal_gedung($start_date, $end_date);
		$object = $this->load->view('admin/pdf_report_kegiatan', $data, true);
		$filename = "Report Kegiatan.pdf";
		generate_pdf($object, $filename, true);
	}

	function dashboard()
	{
		$admin_logged_in = $this->session->userdata('admin_logged_in');
		$admin_username  = $this->session->userdata('admin_username');

		if (!$admin_logged_in || empty($admin_username)) {
			redirect(site_url('admin'));
			return;
		}


		$this->load->model('gedung/gedung_model');
		$this->load->model('user/user_model');

		// Badge sidebar (pending transaksi/pembayaran)
		$data['result']          = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();

		// ===== TOTAL USERS (list user) =====
		$data['list_user'] = $this->user_model->get_all_users(); // array of users

		// ===== TOTAL GEDUNG (list ruangan) =====
		$data['list_gedung'] = $this->gedung_model->get_gedung(); // array of gedung

		// ===== PENDING BOOKINGS (berdasarkan inbox) =====
		// Pakai fungsi yang kamu pakai di halaman "transaksi/inbox"
		$data['inbox'] = $this->gedung_model->get_all_pending_transaction(); // array pending

		// ===== TOTAL REVENUE (berdasarkan transaksi) =====
		// Ambil semua pembayaran lalu jumlahkan field numeriknya (aman tanpa asumsi nama kolom)
		$data['transaksi'] = $this->gedung_model->get_all_pembayaran(); // array pembayaran

		// ===== RECENT BOOKING (invoice terbaru) =====
		// Pakai semua pemesanan lalu sort by invoice numeric DESC (terbaru di atas)
		$all = $this->gedung_model->get_all_pemesanan(); // dipakai juga di pemesanan2()
		$data['front_data']      = $this->gedung_model->fixed_date();
		$data['result']          = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();


		// Pastikan view yang kamu load sesuai nama file (Home.php vs home.php)
		$this->load->view('admin/home', $data);
	}


	function list_user()
	{
		$this->load->model('user/user_model');
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['res'] = $this->user_model->get_all_users();
		$this->load->view('admin/list_user', $data);
	}

	function list_gedung()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['res'] = $this->gedung_model->get_gedung();
		$this->load->view('admin/list_gedung', $data);
	}

	function list_catering()
	{
		$this->load->model('catering/catering_model');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_pending_transaction();
		$data['result'] = $this->catering_model->get_all();
		$this->load->view('admin/list_catering', $data);
	}

	public function verify_pembayaran($id_pembayaran, $action)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->library('notification_service');

		$id_pembayaran = (int)$id_pembayaran;
		$action = strtolower(trim((string)$action));

		if ($id_pembayaran <= 0) {
			show_error('ID pembayaran tidak valid');
			return;
		}

		$p = $this->db->get_where('pembayaran', ['ID_PEMBAYARAN' => $id_pembayaran])->row_array();
		if (!$p) {
			show_error('Data pembayaran tidak ditemukan');
			return;
		}

		if (empty($p['ID_PEMESANAN_RAW'])) {
			show_error('ID_PEMESANAN_RAW kosong.');
			return;
		}

		$id_pemesanan = (int)$p['ID_PEMESANAN_RAW'];
		if ($id_pemesanan <= 0) {
			show_error('ID pemesanan tidak valid');
			return;
		}

		$catatan = trim((string)$this->input->post('catatan_admin', TRUE));

		$ps = $this->db->get_where('pemesanan', ['ID_PEMESANAN' => $id_pemesanan])->row_array();
		if (!$ps) {
			show_error('Data pemesanan tidak ditemukan.');
			return;
		}
		$username_user = $ps['USERNAME'];

		$this->db->trans_begin();

		if ($action === 'confirm') {

			$this->db->where('ID_PEMBAYARAN', $id_pembayaran)->update('pembayaran', [
				'STATUS_VERIF'  => 'CONFIRMED',
				'CATATAN_ADMIN' => $catatan,
				'CONFIRMED_AT'  => date('Y-m-d H:i:s')
			]);

			$this->db->where('ID_PEMESANAN', $id_pemesanan)->update('pemesanan', ['STATUS' => 3]);

			$this->gedung_model->mark_flag_unread('PMSN000' . $id_pemesanan);
		} elseif ($action === 'reject') {

			if ($catatan === '') {
				$this->db->trans_rollback();
				show_error('Catatan admin wajib diisi saat menolak pembayaran.');
				return;
			}

			$this->db->where('ID_PEMBAYARAN', $id_pembayaran)->update('pembayaran', [
				'STATUS_VERIF'  => 'REJECTED',
				'CATATAN_ADMIN' => $catatan,
				'CONFIRMED_AT'  => null
			]);

			$this->db->where('ID_PEMESANAN', $id_pemesanan)->update('pemesanan', ['STATUS' => 4]);

			$this->gedung_model->mark_flag_unread('PMSN000' . $id_pemesanan);

			$this->db->where('ID_PEMESANAN', $id_pemesanan)->update('pemesanan_fix_detail', ['FINAL_STATUS' => 0]);
		} else {
			$this->db->trans_rollback();
			show_error('Aksi tidak dikenali');
			return;
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$err = $this->db->error();
			show_error('Gagal menyimpan data: ' . $err['message']);
			return;
		}

		$this->db->trans_commit();

		// URL notif (opsional: arahkan ke detail pemesanan biar jelas)
		$detailUrl = 'home/pemesanan/details/PMSN000' . $id_pemesanan;

		// notif + email setelah commit
		if ($action === 'confirm') {
			$this->notification_service->notifyUser(
				$username_user,
				'USER_TRANSAKSI',
				'Pembayaran dikonfirmasi',
				'Pembayaran untuk pesanan PMSN000' . $id_pemesanan . ' telah dikonfirmasi.',
				$detailUrl,
				true
			);
		} else {
			$this->notification_service->notifyUser(
				$username_user,
				'USER_TRANSAKSI',
				'Pembayaran ditolak',
				'Pembayaran untuk pesanan PMSN000' . $id_pemesanan . ' ditolak. Catatan: ' . $catatan,
				$detailUrl,
				true
			);
		}

		redirect('admin/pembayaran');
	}

	// =====================================================================
	// ✅ ADDED: Endpoint polling untuk desktop notification (browser)
	// Tujuan: JS di halaman admin akan memanggil endpoint ini tiap beberapa detik.
	// Output: JSON { ok: true, count: <jumlah unread> }
	// =====================================================================
	// public function notif_unread_count()
	// {
	// 	if ($this->session->userdata('admin_logged_in') !== TRUE) {
	// 		$this->output
	// 			->set_status_header(401)
	// 			->set_content_type('application/json')
	// 			->set_output(json_encode([
	// 				'ok' => false,
	// 				'message' => 'unauthorized'
	// 			]));
	// 		return;
	// 	}

	// 	$this->load->model('gedung/gedung_model');
	// 	$unread = $this->gedung_model->get_unread_transaction();

	// 	if (is_array($unread)) $count = count($unread);
	// 	elseif (is_numeric($unread)) $count = (int)$unread;
	// 	else $count = 0;

	// 	$this->output
	// 		->set_content_type('application/json')
	// 		->set_output(json_encode([
	// 			'ok'    => true,
	// 			'count' => $count,
	// 			'ts'    => date('Y-m-d H:i:s')
	// 		]));
	// }
	public function notif_poll_v2()
	{
		if (!$this->input->is_ajax_request()) show_404();

		$admin = $this->session->userdata('admin_username');
		if (!$admin) {
			echo json_encode(['ok' => false, 'msg' => 'no-admin-session']);
			return;
		}

		$this->load->model('Notification_model', 'notif');

		$inbox    = $this->notif->get_unread('admin', ['ADMIN_INBOX'], 5);
		$transaksi = $this->notif->get_unread('admin', ['ADMIN_TRANSAKSI'], 5);

		echo json_encode([
			'ok' => true,
			'counts' => [
				'inbox'    => $this->notif->count_unread('admin', ['ADMIN_INBOX']),
				'transaksi' => $this->notif->count_unread('admin', ['ADMIN_TRANSAKSI']),
			],
			'items' => [
				'inbox'    => $inbox,
				'transaksi' => $transaksi
			]
		]);
	}

	public function notif_counter()
{
    // keamanan: pastikan admin login
    if ($this->session->userdata('admin_logged_in') !== TRUE) {
        return $this->output
            ->set_status_header(401)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok' => false,
                'message' => 'unauthorized'
            ]));
    }

    $this->load->model('gedung/gedung_model');

    // =============================
    // INBOX: pending pemesanan (proposal masuk)
    // kamu sudah pakai: get_pending_transaction()
    // =============================
    $inboxRaw = $this->gedung_model->get_pending_transaction();
    $inboxCount = is_array($inboxRaw) ? count($inboxRaw) : (is_numeric($inboxRaw) ? (int)$inboxRaw : 0);

    // =============================
    // TRANSAKSI: unread pembayaran
    // kamu sudah pakai: get_unread_transaction()
    // =============================
    $trxRaw = $this->gedung_model->get_unread_transaction();
    $trxCount = is_array($trxRaw) ? count($trxRaw) : (is_numeric($trxRaw) ? (int)$trxRaw : 0);

    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'ok' => true,
            'inbox' => $inboxCount,
            'transaksi' => $trxCount,
            'ts' => date('Y-m-d H:i:s')
        ]));
}

}