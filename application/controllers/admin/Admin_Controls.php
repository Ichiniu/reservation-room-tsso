<?php
defined('BASEPATH') or exit('No direct script access allowed');

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



	public function index()
	{
		$this->load->view('admin/home');
	}

	public function tambah_gedung()
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
		$parse_int = function ($v) {
			$v = preg_replace('/[^0-9]/', '', (string)$v);
			return ($v === '' ? 0 : (int)$v);
		};

		$pricing_mode = strtoupper(trim((string)$this->input->post('pricing_mode', true)));
		$allowed_pm = ['FLAT', 'PER_PESERTA', 'PODCAST_PER_JAM'];
		if (!in_array($pricing_mode, $allowed_pm, true)) $pricing_mode = '';

		$harga_halfday_pp = $parse_int($this->input->post('harga_halfday_pp', true));
		$harga_fullday_pp = $parse_int($this->input->post('harga_fullday_pp', true));
		$harga_audio_per_jam = $parse_int($this->input->post('harga_audio_per_jam', true));
		$harga_video_per_jam = $parse_int($this->input->post('harga_video_per_jam', true));

		$data_gedung = [
			'NAMA_GEDUNG'      => $this->input->post('nama_gedung'),
			'KAPASITAS'        => $this->input->post('kapasitas_gedung'),
			'ALAMAT'           => $this->input->post('alamat_gedung'),
			'DESKRIPSI_GEDUNG' => $this->input->post('deskripsi_gedung'),
			'fasilitas'        => $this->input->post('fasilitas_gedung'),
			'HARGA_SEWA'       => $this->input->post('harga_sewa')
		];

		// kolom harga eksternal (opsional - tidak error sebelum ALTER TABLE)
		if ($this->db->field_exists('PRICING_MODE', 'gedung') && $pricing_mode !== '') $data_gedung['PRICING_MODE'] = $pricing_mode;
		if ($this->db->field_exists('HARGA_HALF_DAY_PP', 'gedung')) $data_gedung['HARGA_HALF_DAY_PP'] = $harga_halfday_pp;
		if ($this->db->field_exists('HARGA_FULL_DAY_PP', 'gedung')) $data_gedung['HARGA_FULL_DAY_PP'] = $harga_fullday_pp;
		if ($this->db->field_exists('HARGA_AUDIO_PER_JAM', 'gedung')) $data_gedung['HARGA_AUDIO_PER_JAM'] = $harga_audio_per_jam;
		if ($this->db->field_exists('HARGA_VIDEO_PER_JAM', 'gedung')) $data_gedung['HARGA_VIDEO_PER_JAM'] = $harga_video_per_jam;

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

		// Simpan PATH relatif saja (tanpa domain) agar gambar tampil dari host manapun
		$img_path = "assets/images/gedung/";

		foreach ($_FILES as $key => $value) {
			if (empty($value['name'])) continue; // skip kalau kosong

			if (!$this->upload->do_upload($key)) {
				// kalau upload 1 gambar gagal, lanjut gambar lainnya
				// kamu bisa simpan error ke flashdata kalau mau
				continue;
			}

			$files = $this->upload->data();

			$img_data = [
				'ID_GEDUNG'   => $id_gedung,
				'NAMA_GEDUNG' => $this->input->post('nama_gedung'),
				'PATH'        => $img_path,
				'IMG_NAME'    => $files['file_name']
			];

			$this->gedung_model->insert_gedung_img($img_data);
		}

		redirect('admin/gedung');
	}


	public function rekap_aktivitas()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['gedung_list'] = $this->gedung_model->get_gedung();
		$this->load->view('admin/rekap_aktivitas', $data);
	}

	public function rekap_aktivitas_det($tanggal_awal = null, $tanggal_akhir = null)
	{
		$this->load->model('gedung/gedung_model');

		$start_date = $this->input->get('start_date');
		$end_date   = $this->input->get('end_date');
		$bulan      = $this->input->get('bulan');
		$tahun      = $this->input->get('tahun');
		$id_gedung  = $this->input->get('id_gedung');

		// Fallback ke segment jika GET kosong
		if (empty($start_date) && !empty($tanggal_awal)) $start_date = $tanggal_awal;
		if (empty($end_date) && !empty($tanggal_akhir)) $end_date = $tanggal_akhir;

		// Jika hanya bulan/tahun yang dipilih
		if (empty($start_date) && !empty($bulan) && !empty($tahun)) {
			$start_date = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
			$last_day   = date('t', strtotime($start_date));
			$end_date   = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . $last_day;
		}

		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['hasil'] = $this->gedung_model->jadwal_gedung($start_date, $end_date, $id_gedung);
		$data['first_period'] = $start_date;
		$data['last_period'] = $end_date;
		$data['id_gedung'] = $id_gedung;
		$data['gedung_list'] = $this->gedung_model->get_gedung();

		// Cari Nama Ruang yang difilter
		$data['nama_gedung_filter'] = '';
		if (!empty($id_gedung)) {
			foreach ($data['gedung_list'] as $g) {
				if ($g['ID_GEDUNG'] == $id_gedung) {
					$data['nama_gedung_filter'] = $g['NAMA_GEDUNG'];
					break;
				}
			}
		}

		$this->load->view('admin/rekap_aktivitas_det', $data);
	}

	public function rekap_transaksi()
	{
		$this->load->model('gedung/gedung_model');

		// badge sidebar
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();

		// halaman filter / form periode (buat view sendiri: admin/rekap_transaksi.php)
		$this->load->view('admin/rekap_transaksi', $data);
	}

	public function rekap_transaksi_det($tanggal_awal = null, $tanggal_akhir = null)
	{
		$this->load->model('gedung/gedung_model');

		// badge sidebar
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$start_date = $this->input->get('start_date');
		$end_date   = $this->input->get('end_date');

		// fallback kalau dipanggil via segment: /admin/rekap_transaksi_det/2026-02-01/2026-02-05
		if (empty($start_date) && !empty($tanggal_awal)) $start_date = $tanggal_awal;
		if (empty($end_date) && !empty($tanggal_akhir)) $end_date = $tanggal_akhir;

		// simpan periode untuk view
		$data['start_date']  = $start_date;
		$data['end_date']    = $end_date;

		// ambil data transaksi periodik (sudah kamu pakai di transaksi_export_pdf)
		$data['row'] = $this->gedung_model->laporan_pembayaran_periodic($start_date, $end_date);

		// arahkan ke view detail transaksi (pakai view yang kamu sudah punya: Rekap_Transaksi_Det.php)
		$this->load->view('admin/Rekap_Transaksi_Det', $data);
	}


	public function transaksi_export_pdf($start_date, $end_date)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('pdf_helper');
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



	public function pembayaran()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['pembayaran'] = $this->gedung_model->get_all_pembayaran();
		$data['notifs_admin_trx'] = $this->db->order_by('id', 'DESC')
			->limit(10)
			->like('type', 'ADMIN_TRANSAKSI', 'after')
			->get_where('notifications', [
				'username' => 'admin',
				'read_at'  => null
			])
			->result_array();

		$this->load->view('admin/pembayaran', $data);
	}

	public function delete_jadwal($id_gedung)
	{
		$this->load->model('gedung/gedung_model');
		$data = array(
			'FINAL_STATUS' => 2
		);
		$this->gedung_model->delete_jadwal($id_gedung, $data);
		redirect('admin/dashboard');
	}

	public function pemesanan2()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['pemesanan'] = $this->gedung_model->get_all_pemesanan();
		$this->load->view('admin/pemesanan_2', $data);
	}

	public function read_transaction($id_pembayaran)
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


	public function transaksi()
	{
		$this->load->model('gedung/gedung_model');
		$data['pemesanan'] = $this->gedung_model->get_all_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['notifs_admin_inbox'] = $this->db->order_by('id', 'DESC')
			->limit(10)
			->like('type', 'ADMIN_INBOX', 'after')
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
		$ps = $this->db->get_where('pemesanan', ['ID_PEMESANAN' => $temp_id])->row_array();
		$username_user = $ps['USERNAME'] ?? null;

		// === PROSES POST DULU ===
		if ($this->input->method(TRUE) === 'POST') {
			$status  = (int) $this->input->post('status-proposal');
			$remarks = trim((string) $this->input->post('remarks', TRUE));

			// URL langsung ke detail pemesanan
			$detailUrl = 'home/pemesanan/details/PMSN000' . $temp_id;

			// TERIMA PROPOSAL -> Check if internal or external user
			if ($status === 1) {
				//  Cek apakah user internal atau eksternal
				$u = $this->db->select('perusahaan')
					->from('user')
					->join('pemesanan p', 'p.USERNAME = user.USERNAME', 'left')
					->where('p.ID_PEMESANAN', $temp_id)
					->get()
					->row();

				$perusahaan = $u->perusahaan ?? '';
				$is_internal = (strtoupper(trim((string)$perusahaan)) === 'INTERNAL');

				if ($is_internal) {
					// USER INTERNAL: Langsung SUBMITTED (status 3) karena tidak ada pembayaran
					$this->gedung_model->update_transaksi($temp_id, 3, '');

					// Update pembayaran juga ke CONFIRMED
					$this->db->where('ID_PEMESANAN_RAW', $temp_id)
						->update('pembayaran', [
							'STATUS_VERIF' => 'CONFIRMED',
							'CATATAN_ADMIN' => 'INTERNAL - Disetujui admin (gratis)',
							'CONFIRMED_AT' => date('Y-m-d H:i:s')
						]);

					// badge lama user
					$this->gedung_model->mark_flag_unread('PMSN000' . $temp_id);

					// notif user bahwa pemesanan sudah CONFIRMED (langsung siap digunakan)
					if (!empty($username_user)) {
						$this->notification_service->notifyUser(
							$username_user,
							'USER_TRANSAKSI_CONFIRMED',
							'Pemesanan dikonfirmasi',
							'Pemesanan PMSN000' . $temp_id . ' telah disetujui dan siap digunakan.',
							'home/pembayaran',
							true
						);
					}
				} else {
					//  USER EKSTERNAL: Status APPROVE (1) untuk lanjut ke pembayaran
					$this->gedung_model->update_transaksi($temp_id, 1, '');

					// badge lama user
					$this->gedung_model->mark_flag_unread('PMSN000' . $temp_id);

					// notif + email user
					if (!empty($username_user)) {
						$this->notification_service->notifyProposalApproved($username_user, $temp_id, true);
					}
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
					$this->notification_service->notifyProposalRejected($username_user, $temp_id, $remarks, true);
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


	public function send_mail($to_email, $pesan)
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

	public function download_proposal($id_pemesanan)
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


	public function update_transaksi($id_pemesanan)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('form');
		$temp_id = (int) preg_replace('/\D+/', '', (string)$id_pemesanan); // ambil angka saja
		if ($temp_id <= 0) show_404();
	}

	public function tambah_catering($id_catering = null)
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

	public function delete_catering()
	{
		$this->load->helper('url');
		$this->load->model('catering/catering_model');
		$id = (int)$this->input->post('id_catering');
		if ($id > 0) {
			$this->catering_model->delete_catering($id);
		}
		redirect('admin/catering');
	}

	/**
	 * Toggle status aktif/nonaktif catering (POST)
	 */
	public function toggle_catering_status()
	{
		$this->load->helper('url');
		$this->load->model('catering/catering_model');
		$id = (int)$this->input->post('id_catering');
		if ($id > 0) {
			$this->catering_model->toggle_status($id);
		}
		redirect('admin/catering');
	}
	public function delete_gedung($id_gedung)
	{
		$this->load->model('gedung/gedung_model');
		$this->gedung_model->delete_gedung($id_gedung);
		redirect('admin/gedung');
	}

	public function edit_gedung($id_gedung)
	{
		$this->load->model('gedung/gedung_model');

		$simpan = $this->input->post('submit');

		// 1) PROSES POST DULU
		if (!empty($simpan)) {
			$parse_int = function ($v) {
				$v = preg_replace('/[^0-9]/', '', (string)$v);
				return ($v === '' ? 0 : (int)$v);
			};

			$pricing_mode = strtoupper(trim((string)$this->input->post('pricing_mode', true)));
			$allowed_pm = array('FLAT', 'PER_PESERTA', 'PODCAST_PER_JAM');
			if (!in_array($pricing_mode, $allowed_pm, true)) $pricing_mode = '';

			$harga_halfday_pp = $parse_int($this->input->post('harga_halfday_pp', true));
			$harga_fullday_pp = $parse_int($this->input->post('harga_fullday_pp', true));
			$harga_audio_per_jam = $parse_int($this->input->post('harga_audio_per_jam', true));
			$harga_video_per_jam = $parse_int($this->input->post('harga_video_per_jam', true));

			$data = array(
				'NAMA_GEDUNG'      => $this->input->post('nama_gedung'),
				'KAPASITAS'        => $this->input->post('kapasitas_gedung'),
				'ALAMAT'           => $this->input->post('alamat_gedung'),
				'DESKRIPSI_GEDUNG' => $this->input->post('deskripsi_gedung'),
				'fasilitas'        => $this->input->post('fasilitas_gedung'), //  TAMBAH
				'HARGA_SEWA'       => $this->input->post('harga_sewa')
			);

			// kolom harga eksternal (opsional - tidak error sebelum ALTER TABLE)
			if ($this->db->field_exists('PRICING_MODE', 'gedung') && $pricing_mode !== '') $data['PRICING_MODE'] = $pricing_mode;
			if ($this->db->field_exists('HARGA_HALF_DAY_PP', 'gedung')) $data['HARGA_HALF_DAY_PP'] = $harga_halfday_pp;
			if ($this->db->field_exists('HARGA_FULL_DAY_PP', 'gedung')) $data['HARGA_FULL_DAY_PP'] = $harga_fullday_pp;
			if ($this->db->field_exists('HARGA_AUDIO_PER_JAM', 'gedung')) $data['HARGA_AUDIO_PER_JAM'] = $harga_audio_per_jam;
			if ($this->db->field_exists('HARGA_VIDEO_PER_JAM', 'gedung')) $data['HARGA_VIDEO_PER_JAM'] = $harga_video_per_jam;

			$this->gedung_model->update_gedung((int)$id_gedung, $data);

			// PROSES UPLOAD GAMBAR (opsional)
			$path = "./assets/images/gedung/";
			$config['upload_path']   = $path;
			$config['allowed_types'] = 'jpg|jpeg|png';
			$config['max_size']      = 2048; // 2MB
			$config['max_width']     = 2000;
			$config['max_height']    = 2000;

			$this->load->library('upload');
			$this->upload->initialize($config);

			// Simpan PATH relatif saja (tanpa domain) agar gambar tampil dari host manapun
			$img_path = "assets/images/gedung/";

			foreach ($_FILES as $field => $fileinfo) {
				if (is_array($fileinfo['name'])) {
					// multiple files input (images[])
					$count = count($fileinfo['name']);
					for ($i = 0; $i < $count; $i++) {
						if (empty($fileinfo['name'][$i])) continue;
						$_FILES['tmpfile']['name'] = $fileinfo['name'][$i];
						$_FILES['tmpfile']['type'] = $fileinfo['type'][$i];
						$_FILES['tmpfile']['tmp_name'] = $fileinfo['tmp_name'][$i];
						$_FILES['tmpfile']['error'] = $fileinfo['error'][$i];
						$_FILES['tmpfile']['size'] = $fileinfo['size'][$i];

						if (!$this->upload->do_upload('tmpfile')) continue;
						$files = $this->upload->data();
						$img_data = array(
							'ID_GEDUNG'   => (int)$id_gedung,
							'NAMA_GEDUNG' => $this->input->post('nama_gedung'),
							'PATH'        => $img_path,
							'IMG_NAME'    => $files['file_name']
						);
						$this->gedung_model->insert_gedung_img($img_data);
					}
				} else {
					if (empty($fileinfo['name'])) continue;
					if (!$this->upload->do_upload($field)) continue;
					$files = $this->upload->data();
					$img_data = array(
						'ID_GEDUNG'   => (int)$id_gedung,
						'NAMA_GEDUNG' => $this->input->post('nama_gedung'),
						'PATH'        => $img_path,
						'IMG_NAME'    => $files['file_name']
					);
					$this->gedung_model->insert_gedung_img($img_data);
				}
			}

			redirect('admin/gedung');
			return;
		}

		// 2) BARU LOAD DATA UNTUK VIEW
		$details['res']    = $this->gedung_model->get_pending_transaction();
		$details['result'] = $this->gedung_model->gedung_details((int)$id_gedung);
		// ambil gambar gedung
		$details['images'] = $this->gedung_model->get_gedung_img((int)$id_gedung);

		// pastikan helper form ter-load agar form_open_multipart menyertakan CSRF jika aktif
		$this->load->helper('form');

		$this->load->view('admin/edit_gedung', $details);
	}


	public function kegiatan_export_pdf($start_date, $end_date, $id_gedung = null)
	{
		$this->load->model('gedung/gedung_model');
		$this->load->helper('pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->jadwal_gedung($start_date, $end_date, $id_gedung);

		// Cari Nama Ruang untuk header PDF
		$data['nama_gedung_filter'] = '';
		if (!empty($id_gedung)) {
			$gedung_list = $this->gedung_model->get_gedung();
			foreach ($gedung_list as $g) {
				if ($g['ID_GEDUNG'] == $id_gedung) {
					$data['nama_gedung_filter'] = $g['NAMA_GEDUNG'];
					break;
				}
			}
		}

		$object = $this->load->view('admin/pdf_report_kegiatan', $data, true);
		$filename = "Report Kegiatan.pdf";
		generate_pdf($object, $filename, true);
	}
	public function dashboard()
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

		// ===== TOTAL REVENUE (berdasarkan transaksi per bulan) =====
		$selected_month = $this->input->get('bulan') ? (int)$this->input->get('bulan') : (int)date('m');
		$selected_year  = $this->input->get('tahun') ? (int)$this->input->get('tahun') : (int)date('Y');

		$data['selected_month'] = $selected_month;
		$data['selected_year']  = $selected_year;
		$data['total_revenue']  = $this->gedung_model->get_total_revenue($selected_month, $selected_year);
		$data['transaksi']      = $this->gedung_model->get_all_pembayaran(); // tetap ada jika view butuh loop manual

		// ===== RECENT BOOKING (invoice terbaru) =====
		// Pakai semua pemesanan lalu sort by invoice numeric DESC (terbaru di atas)
		$all = $this->gedung_model->get_all_pemesanan(); // dipakai juga di pemesanan2()
		$data['front_data']      = $this->gedung_model->fixed_date();
		$data['result']          = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();

		// ===== TOP DEPARTMENTS (CHART) =====
		// Hanya pemesanan status CONFIRMED (1)
		$data['top_departments'] = $this->gedung_model->get_booking_stats_by_dept(100);

		// Pastikan view yang kamu load sesuai nama file (Home.php vs home.php)
		$this->load->view('admin/home', $data);
	}


	public function list_user()
	{
		$this->load->model('user/user_model');
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['res'] = $this->user_model->get_all_users();
		$this->load->view('admin/list_user', $data);
	}

	public function list_gedung()
	{
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['res'] = $this->gedung_model->get_gedung();
		$this->load->view('admin/list_gedung', $data);
	}

	public function list_catering()
	{
		$this->load->model('catering/catering_model');
		$this->load->model('gedung/gedung_model');
		$this->load->model('settings_model');
		$data['res'] = $this->gedung_model->get_pending_transaction();
		$data['result'] = $this->catering_model->get_all();
		$data['catering_phone'] = $this->settings_model->get('catering_phone', '089649261851');

		// Data rekening pembayaran
		$data['payment_bank_name']    = $this->settings_model->get('payment_bank_name', getenv('PAYMENT_BANK_NAME') ?: 'BCA');
		$data['payment_bank_account'] = $this->settings_model->get('payment_bank_account', getenv('PAYMENT_BANK_ACCOUNT') ?: '');
		$data['payment_bank_holder']  = $this->settings_model->get('payment_bank_holder', getenv('PAYMENT_BANK_HOLDER') ?: '');

		$this->load->view('admin/list_catering', $data);
	}

	/**
	 * Simpan nomor telepon catering (POST)
	 */
	public function save_catering_phone()
	{
		$this->load->helper('url');
		$this->load->model('settings_model');
		$phone = trim((string)$this->input->post('catering_phone', TRUE));
		if (!empty($phone)) {
			$this->settings_model->set('catering_phone', $phone);
		}
		redirect('admin/catering');
	}

	/**
	 * Simpan data rekening pembayaran (POST)
	 */
	public function save_payment_bank()
	{
		$this->load->helper('url');
		$this->load->model('settings_model');

		$bank_name    = trim((string)$this->input->post('payment_bank_name', TRUE));
		$bank_account = trim((string)$this->input->post('payment_bank_account', TRUE));
		$bank_holder  = trim((string)$this->input->post('payment_bank_holder', TRUE));

		if (!empty($bank_name)) {
			$this->settings_model->set('payment_bank_name', $bank_name);
		}
		if (!empty($bank_account)) {
			$this->settings_model->set('payment_bank_account', $bank_account);
		}
		if (!empty($bank_holder)) {
			$this->settings_model->set('payment_bank_holder', $bank_holder);
		}

		redirect('admin/catering');
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

		$catatan = trim((string)($this->input->post('catatan_admin', TRUE) ?? ''));

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
			$msg = $err['message'] ?? 'unknown';
			return;
		}

		$this->db->trans_commit();

		// URL notif (opsional: arahkan ke detail pemesanan biar jelas)
		$detailUrl = 'home/pemesanan/details/PMSN000' . $id_pemesanan;

		// notif + email setelah commit
		if ($action === 'confirm') {
			$this->notification_service->notifyPaymentConfirmed($username_user, $id_pemesanan, $catatan, true);
			// minta user untuk mengisi ulasan (email + in-app notif)
			$this->notification_service->notifyReviewRequest($username_user, $id_pemesanan);
		} else {
			$this->notification_service->notifyPaymentRejected($username_user, $id_pemesanan, $catatan, true);
		}

		redirect('admin/pembayaran');
	}
	public function notif_poll_v2()
	{
		header('Content-Type: application/json; charset=utf-8');

		// cek admin login
		$admin = (string) $this->session->userdata('admin_username');
		if ($admin === '') {
			echo json_encode(array('ok' => false, 'message' => 'Unauthorized'));
			return;
		}

		$since_i = (int) $this->input->get('since_i'); // optional
		$since_t = (int) $this->input->get('since_t');

		$this->load->library('notification_service');

		// types sesuai DB kamu
		$typesI = ['ADMIN_INBOX', 'ADMIN_INBOX_PROCESS'];
		$typesT = ['ADMIN_TRANSAKSI', 'ADMIN_TRANSAKSI_PENDING'];

		try {
			$adminKey = 'admin'; // sesuai DB kamu

			$rawI = $this->notification_service->get_unread($adminKey, $typesI, 30);
			$rawT = $this->notification_service->get_unread($adminKey, $typesT, 30);

			$itemsI = [];
			if (is_array($rawI)) {
				foreach ($rawI as $n) {
					$id = isset($n['id']) ? (int)$n['id'] : 0;
					if ($id > $since_i) $itemsI[] = $n;
				}
			}

			$itemsT = [];
			if (is_array($rawT)) {
				foreach ($rawT as $n) {
					$id = isset($n['id']) ? (int)$n['id'] : 0;
					if ($id > $since_t) $itemsT[] = $n;
				}
			}

			$countI = (int) $this->notification_service->count_unread($adminKey, $typesI);
			$countT = (int) $this->notification_service->count_unread($adminKey, $typesT);

			echo json_encode([
				'ok' => true,
				'counts' => ['inbox' => $countI, 'transaksi' => $countT],
				'items'  => [
					'inbox' => array_slice($itemsI, 0, 10),
					'transaksi' => array_slice($itemsT, 0, 10)
				]
			]);
		} catch (Exception $e) {
			log_message('error', 'notif_poll_v2 ADMIN error: ' . $e->getMessage());
			echo json_encode(['ok' => false, 'message' => 'Server error']);
		}
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
