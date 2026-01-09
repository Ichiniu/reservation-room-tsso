<?php

/**
 * @property CI_Loader           $load
 * @property CI_Input            $input
 * @property CI_DB_query_builder $db
 * @property CI_Output           $output
 * @property CI_Session          $session
 * @property CI_URI              $uri
 * @property CI_Upload           $upload
 *
 * @property Gedung_model        $gedung_model
 * @property Catering_Model      $catering_model
 * @property User_model          $user_model
 * @property Chec         $user_model
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
		$this->load->view('gedung/View_Catering', $data);
	}

	function check_date($date, $id_gedung, $jam_mulai = null, $jam_selesai = null)
	{
		$this->load->model('gedung/gedung_model');
		return $this->gedung_model->check_date($date, $id_gedung, $jam_mulai, $jam_selesai);
	}

	private function _extract_gedung_name($gedung_result)
	{
		if (is_array($gedung_result)) {
			if (isset($gedung_result[0])) $gedung_result = $gedung_result[0];
			return isset($gedung_result['NAMA_GEDUNG']) ? $gedung_result['NAMA_GEDUNG'] : '';
		}

		if (is_object($gedung_result)) {
			return isset($gedung_result->NAMA_GEDUNG) ? $gedung_result->NAMA_GEDUNG : '';
		}

		return '';
	}


	private function _min_booking_rule($id_gedung)
	{
		// pastikan model sudah ada (di order() dan order_gedung() kamu memang load)
		$g = $this->gedung_model->get_gedung_name($id_gedung);
		$nama = strtolower(trim($this->_extract_gedung_name($g)));

		// default
		$days = 10;

		// toleran typo "Amphiater"/"Amphitheater" -> pakai kata kunci "amphi"
		if (strpos($nama, 'amphi') !== false) {
			$days = 1;
		} elseif (strpos($nama, 'smart office meeting room') !== false || strpos($nama, 'meeting room') !== false) {
			$days = 1;
		} elseif (strpos($nama, 'smart office studio photo') !== false || strpos($nama, 'studio photo') !== false) {
			$days = 3;
		}

		return [
			'days' => $days,
			'text' => "Pemesanan minimal {$days} hari dari tanggal hari ini",
		];
	}


	public function order_gedung($id_gedung)
	{
		$username = $this->session->userdata('username');
		$this->load->model('catering/catering_model');
		$this->load->model('gedung/gedung_model');

		$gedung['hasil'] = $this->gedung_model->get_gedung_name($id_gedung);

		// ✅ rule min booking berdasarkan gedung
		$rule = $this->_min_booking_rule($id_gedung);
		$gedung['min_pesan'] = date('Y-m-d', strtotime('+' . $rule['days'] . ' day'));
		$gedung['min_text']  = $rule['text'];
		$gedung['min_days']  = $rule['days'];

		if (method_exists($this->catering_model, 'get_all')) {
			$data['res'] = $this->catering_model->get_all();
		} else {
			$data['res'] = $this->catering_model->get_catering_full();
		}

		$data['email'] = $this->gedung_model->get_email_address($username);
		$data['flag']  = $this->gedung_model->get_pemesanan_flag($username);
		// ✅ TAMBAHAN: cek INTERNAL / EKSTERNAL + aturan pilihan jam per ruangan
		$u = $this->db->select('perusahaan')
			->from('user')
			->where('USERNAME', $username)
			->get()
			->row();

		$perusahaan = ($u && isset($u->perusahaan)) ? $u->perusahaan : '';
		$data['is_internal'] = (strtoupper(trim((string)$perusahaan)) === 'INTERNAL');

		// Ambil nama ruangan untuk deteksi STUDIO
		$nama_gedung = '';
		if (!empty($gedung['hasil']) && is_array($gedung['hasil'])) {
			$first = reset($gedung['hasil']);
			if (is_array($first) && isset($first['NAMA_GEDUNG'])) {
				$nama_gedung = (string) $first['NAMA_GEDUNG'];
			}
		}
		$is_studio = (stripos($nama_gedung, 'studio') !== false);

		// INTERNAL: semua opsi | EKSTERNAL: studio = per jam saja, selain studio = half/full day saja
		if (!empty($data['is_internal'])) {
			$data['allowed_tipe_jam'] = array('CUSTOM', 'HALF_DAY_PAGI', 'HALF_DAY_SIANG', 'FULL_DAY');
		} else {
			$data['allowed_tipe_jam'] = $is_studio
				? array('CUSTOM')
				: array('HALF_DAY_PAGI', 'HALF_DAY_SIANG', 'FULL_DAY');
		}

		$data['default_tipe_jam'] = $data['allowed_tipe_jam'][0];


		$hasil = array_merge($gedung, $data);
		$this->load->view('gedung/order_gedung', $hasil);
	}





	public function pemesanan()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');

		// wajib di sini
		$this->gedung_model->clear_pemesanan_flag($username);

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

		// update flag (pastikan numeric)
		$this->gedung_model->update_user_flag((int)$temp_id);

		// data pemesanan (dari view V_PEMESANAN)
		$data['result'] = $this->gedung_model->get_detail_pesanan($id_pemesanan);
		$data['flag']   = $this->gedung_model->get_pemesanan_flag($username);

		// ambil data user untuk ditampilkan
		$u = $this->db->select('USERNAME, EMAIL, NAMA_LENGKAP, perusahaan')
			->from('user')
			->where('USERNAME', $username)
			->get()
			->row();

		$data['nama_lengkap_user'] = $u ? $u->NAMA_LENGKAP : $username;

		// ini buat ditampilkan di detail
		$data['user_username'] = $u && isset($u->USERNAME) ? $u->USERNAME : $username;
		$data['user_email']    = $u && isset($u->EMAIL)
			? $u->EMAIL
			: (isset($data['result']->EMAIL) ? $data['result']->EMAIL : null);

		// ambil proposal: keperluan acara + file upload
		$data['proposal_details'] = $this->gedung_model->get_proposal_by_id((int)$temp_id);

		$this->load->view('home/detail_pemesanan', $data);
	}

	public function upload_proposal($id_pemesanan = null)
	{
		$this->load->helper(['form']);
		$this->load->model('gedung/gedung_model');
		$this->load->model('pembayaran/pembayaran_model'); // untuk record admin (pembayaran)

		$username = $this->session->userdata('username');
		if (!$username) show_404();

		// ambil ID dari parameter atau POST
		$id = $id_pemesanan ?: (int)$this->input->post('id_pemesanan');
		if ($id <= 0) show_404();

		// pastikan order ini milik user yg login
		if (!$this->gedung_model->is_order_owner($id, $username)) show_404();

		// =========================
		// OPTIONAL UPLOAD (TIDAK WAJIB)
		// =========================
		$hasFile = (isset($_FILES['proposal']) && $_FILES['proposal']['error'] !== UPLOAD_ERR_NO_FILE && !empty($_FILES['proposal']['name']));

		$doc_name    = null;
		$public_path = null;

		if ($hasFile) {
			$upload_path = FCPATH . 'assets/user-proposal/';
			$public_path = rtrim(base_url('assets/user-proposal/'), '/') . '/';

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

			$this->load->library('upload');
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('proposal')) {
				// Upload OPTIONAL: kalau user pilih file tapi gagal, tetap kasih error
				$this->session->set_flashdata('upload_error', strip_tags($this->upload->display_errors()));
				redirect('home/home/validasi_upload/' . $id);
				return;
			}

			$upload_data = $this->upload->data();
			$doc_name    = $upload_data['file_name'];
		}

		// data untuk proposal (kalau tidak upload, PATH & FILE_NAME = NULL)
		$data = [
			'ID_PEMESANAN'    => $id,
			'PATH'            => $public_path,  // NULL jika tidak upload
			'FILE_NAME'       => $doc_name,     // NULL jika tidak upload
			'DESKRIPSI_ACARA' => $this->input->post('deskripsi-acara', TRUE),
		];

		// update kalau sudah ada, insert kalau belum
		if ($this->gedung_model->proposal_exists($id)) {

			// OPSI AMAN:
			// Kalau user tidak upload file, jangan timpa file lama jadi NULL.
			// (Kalau kamu memang mau timpa jadi NULL, hapus blok if ini dan langsung update $data)
			if (!$hasFile) {
				$this->gedung_model->update_proposal($id, [
					'ID_PEMESANAN'    => $id,
					'DESKRIPSI_ACARA' => $this->input->post('deskripsi-acara', TRUE),
				]);
			} else {
				$this->gedung_model->update_proposal($id, $data);
			}
		} else {
			// Insert baru: kalau tidak ada file, tetap insert dan akan ke-record NULL
			$this->gedung_model->upload_proposal($data);
		}

		// =========================
		// AUTO CONFIRMED UNTUK INTERNAL
		// =========================
		$u = $this->db->select('perusahaan')
			->from('user')
			->where('USERNAME', $username)
			->get()
			->row();

		$is_internal = ($u && strtoupper(trim((string)$u->perusahaan)) === 'INTERNAL');

		if ($is_internal) {
			$pesanan = $this->db->select("
                p.TANGGAL_PEMESANAN,
                g.NAMA_GEDUNG,
                c.NAMA_PAKET
            ")
				->from('pemesanan p')
				->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
				->join('catering c', 'c.ID_CATERING = p.ID_CATERING', 'left')
				->where('p.ID_PEMESANAN', $id)
				->get()
				->row();

			$exists = $this->db->select('ID_PEMESANAN_RAW')
				->from('pembayaran')
				->where('ID_PEMESANAN_RAW', $id)
				->limit(1)
				->get()
				->row();

			$this->db->trans_begin();

			if (!$exists) {
				$data_bayar = array(
					'ID_PEMESANAN_RAW'   => $id,
					'KODE_PEMESANAN'     => 'PMSN000',
					'TANGGAL_PEMESANAN'  => $pesanan ? $pesanan->TANGGAL_PEMESANAN : date('Y-m-d'),
					'NAMA_GEDUNG'        => $pesanan ? $pesanan->NAMA_GEDUNG : '-',
					'NAMA_PAKET'         => ($pesanan && !empty($pesanan->NAMA_PAKET)) ? $pesanan->NAMA_PAKET : '-',
					'TOTAL_TAGIHAN'      => 0,

					'BANK_TUJUAN'        => 'BCA',
					'NO_REKENING_TUJUAN' => '1234567890',
					'ATAS_NAMA_TUJUAN'   => 'Tiga Serangkai Smart Office',

					'ATAS_NAMA_PENGIRIM' => 'INTERNAL (AUTO)',
					'TANGGAL_TRANSFER'   => date('Y-m-d'),
					'BANK_PENGIRIM'      => '-',
					'NOMINAL_TRANSFER'   => 0,

					'BUKTI_PATH'         => '-',
					'BUKTI_NAME'         => '-',
					'BUKTI_MIME'         => '-',

					'STATUS_VERIF'       => 'CONFIRMED',
					'CATATAN_ADMIN'      => 'AUTO: INTERNAL - Langsung confirmed (gratis)',
					'CONFIRMED_AT'       => date('Y-m-d H:i:s'),
				);

				$this->pembayaran_model->insert_pembayaran($data_bayar);
			}

			$this->db->where('ID_PEMESANAN', $id);
			$this->db->update('pemesanan', array('STATUS' => 3));

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				show_error('Gagal auto-confirm untuk user INTERNAL.');
				return;
			}

			$this->db->trans_commit();

			$this->session->set_flashdata('upload_success', 'INTERNAL: Proposal tersimpan & pemesanan langsung CONFIRMED.');
			redirect('home/pemesanan');
			return;
		}

		// =========================
		// EKSTERNAL: tetap flow normal (menunggu admin)
		// =========================
		$this->session->set_flashdata('upload_success', 'Proposal berhasil disimpan (tanpa upload juga boleh).');
		redirect('home/home/proposal_success/' . $id);
		return;
	}


	public function proposal_success($id = null)
	{
		$this->load->model('gedung/gedung_model');

		$username = $this->session->userdata('username');
		if (!$username) redirect('login');

		$id = (int)$id;
		if ($id > 0 && !$this->gedung_model->is_order_owner($id, $username)) show_404();

		$this->load->view('home/success_page'); // view sukses kamu
	}

	public function order()
	{
		$this->load->model('gedung/gedung_model');
		$this->load->model('catering/Catering_Model', 'catering_model');

		$tanggal_pesan = $this->input->post('tgl_pesan', TRUE);
		$id_gedung     = (int) $this->uri->segment(4);
		$username      = $this->session->userdata('username');

		// ===== TIPE JAM (form) =====
		$tipe_jam_form = $this->input->post('tipe_jam', TRUE);
		if (empty($tipe_jam_form)) $tipe_jam_form = 'CUSTOM';

		// Untuk DB (karena enum kamu: CUSTOM, HALF_DAY, FULL_DAY)
		$tipe_jam_db = $tipe_jam_form;
		if ($tipe_jam_form === 'HALF_DAY_PAGI' || $tipe_jam_form === 'HALF_DAY_SIANG') {
			$tipe_jam_db = 'HALF_DAY';
		}

		$paket = array(
			'HALF_DAY_PAGI'  => array('08:00', '12:00'),
			'HALF_DAY_SIANG' => array('13:00', '16:00'),
			'FULL_DAY'       => array('08:00', '17:00'),
		);

		// Tentukan jam mulai & selesai
		if ($tipe_jam_form === 'CUSTOM') {
			$jam_mulai   = $this->input->post('jam_pesan', TRUE);
			$jam_selesai = $this->input->post('jam_selesai', TRUE);

			if (empty($jam_mulai) || empty($jam_selesai)) {
				$this->session->set_flashdata('error', 'Jam mulai dan jam selesai wajib diisi.');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}
		} elseif (isset($paket[$tipe_jam_form])) {
			$jam_mulai   = $paket[$tipe_jam_form][0];
			$jam_selesai = $paket[$tipe_jam_form][1];
		} else {
			show_error('Tipe jam tidak valid');
			return;
		}

		if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
			$this->session->set_flashdata('error', 'Jam mulai harus lebih kecil dari jam selesai.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ===== MIN BOOKING (DINAMIS PER GEDUNG) =====
		$rule = $this->_min_booking_rule($id_gedung);
		$min_pesan = date('Y-m-d', strtotime('+' . $rule['days'] . ' day'));

		if (strtotime($tanggal_pesan) < strtotime($min_pesan)) {
			$this->load->view('errors/pemesanan_alert', array(
				'tgl_pesan' => $tanggal_pesan,
				'min_pesan' => $min_pesan,
				'min_text'  => $rule['text'],
				'min_days'  => $rule['days'],
			));
			return;
		}


		// ===== CEK BENTROK (LOCKED) =====
		if ($this->gedung_model->has_locked_conflict($id_gedung, $tanggal_pesan, $jam_mulai, $jam_selesai)) {
			$this->session->set_flashdata(
				'error',
				'Maaf, jadwal tersebut sudah dipesan dan sudah ada pembayaran (menunggu verifikasi / sudah dikonfirmasi). Silakan cek pada halaman Jadwal Gedung.'
			);
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ===== CEK BENTROK NORMAL =====
		$exist = $this->gedung_model->check_date($tanggal_pesan, $id_gedung, $jam_mulai, $jam_selesai);
		if ($exist > 0) {
			$this->session->set_flashdata('error', 'Tanggal/gedung sudah terbooking.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ===== REQUEST ID =====
		$request_id = $this->input->post('request_id', TRUE);
		if (empty($request_id)) {
			$request_id = sha1(
				session_id() . '|' . $username . '|' . $id_gedung . '|' .
					$tanggal_pesan . '|' . $jam_mulai . '|' . $jam_selesai . '|' . $tipe_jam_form
			);
		}

		// ===== CATERING INPUT =====
		$catering_choice   = $this->input->post('radios', TRUE); // 'ya' / 'tidak'
		$id_catering_post  = $this->input->post('catering', TRUE);
		$jumlah_porsi_post = $this->input->post('jumlah-porsi', TRUE);

		// textarea input per kategori + addon
		$menu_input     = $this->input->post('menu_input', TRUE);     // array
		$addon_input    = $this->input->post('addon_input', TRUE);    // array
		$addon_enabled  = $this->input->post('addon_enabled', TRUE);  // array (key => "1")

		$id_catering_final  = null;
		$jumlah_porsi_final = null;

		// JSON columns
		$menu_pilihan_json = null; // untuk pilihan terstruktur (kalau nanti ada checkbox/select)
		$menu_input_json   = null; // dari textarea per kategori
		$addon_input_json  = null; // dari addon yang dicentang

		if ($catering_choice === 'ya') {

			// wajib pilih paket
			if (empty($id_catering_post)) {
				$this->session->set_flashdata('error', 'Jika memilih catering "Ya", paket catering wajib dipilih.');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}

			$id_catering_final = (int) $id_catering_post;

			$jumlah_porsi_final = (int) $jumlah_porsi_post;
			if ($jumlah_porsi_final < 1) {
				$this->session->set_flashdata('error', 'Jumlah porsi wajib diisi (minimal 1).');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}

			// validasi id catering + min pax
			$c_row = $this->catering_model->get_by_id($id_catering_final);
			if (empty($c_row)) {
				$this->session->set_flashdata('error', 'Paket catering tidak valid.');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}

			$min_pax = isset($c_row['MIN_PAX']) ? (int) $c_row['MIN_PAX'] : 1;
			if ($min_pax < 1) $min_pax = 1;

			if ($jumlah_porsi_final < $min_pax) {
				$this->session->set_flashdata('error', 'Jumlah porsi minimal untuk paket ini adalah ' . $min_pax . ' pax.');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}

			// ===== simpan MENU_INPUT_JSON (kategori) =====
			if (is_array($menu_input)) {
				$clean = array();
				foreach ($menu_input as $k => $v) {
					$v = trim((string) $v);
					if ($v !== '') $clean[$k] = $v;
				}
				if (!empty($clean)) {
					$menu_input_json = json_encode($clean, JSON_UNESCAPED_UNICODE);
				}
			}

			// ===== simpan ADDON_INPUT_JSON (yang enabled saja) =====
			if (is_array($addon_enabled)) {
				$addon_clean = array();
				foreach ($addon_enabled as $k => $flag) {
					if (!$flag) continue;

					$txt = '';
					if (is_array($addon_input) && isset($addon_input[$k])) {
						$txt = trim((string) $addon_input[$k]);
					}

					// simpan key addon walau kosong (biar kelihatan addon dicentang)
					$addon_clean[$k] = $txt;
				}

				if (!empty($addon_clean)) {
					$addon_input_json = json_encode($addon_clean, JSON_UNESCAPED_UNICODE);
				}
			}
		} else {
			// catering "tidak" -> pastikan NULL semua
			$id_catering_final  = null;
			$jumlah_porsi_final = null;
			$menu_pilihan_json  = null;
			$menu_input_json    = null;
			$addon_input_json   = null;
		}

		// ===== INSERT PEMESANAN =====
		$data = array(
			'USERNAME'          => $username,
			'TANGGAL_PEMESANAN' => $tanggal_pesan,
			'JAM_PEMESANAN'     => $jam_mulai,
			'JAM_SELESAI'       => $jam_selesai,
			'TIPE_JAM'          => $tipe_jam_db, // <-- penting: sesuai enum DB
			'EMAIL'             => $this->input->post('email', TRUE),

			'ID_GEDUNG'         => $id_gedung,
			'ID_CATERING'       => $id_catering_final,
			'JUMLAH_CATERING'   => $jumlah_porsi_final,
			'STATUS'            => 0,
			'REQUEST_ID'        => $request_id,
			'MENU_PILIHAN_JSON' => $menu_pilihan_json, // boleh NULL untuk sekarang
			'MENU_INPUT_JSON'   => $menu_input_json,
			'ADDON_INPUT_JSON'  => $addon_input_json,
		);

		$id_pemesanan = $this->gedung_model->insert_pemesanan($data);

		if ($id_pemesanan === false) {
			$row = $this->db->get_where('pemesanan', array('REQUEST_ID' => $request_id))->row_array();
			if (!empty($row)) {
				redirect('home/confirm-order/' . (int) $row['ID_PEMESANAN']);
				return;
			}
			show_error('Gagal membuat pemesanan. Silakan coba lagi.');
			return;
		}

		redirect('home/confirm-order/' . (int) $id_pemesanan);
	}
	public function edit_data($user = null)
	{
		$this->load->model('user/user_model');

		$session_user = (string) $this->session->userdata('username');
		if ($session_user === '') {
			redirect(site_url('login'));
			return;
		}

		// kalau URL mengirim param, izinkan beda kapital (Wahyu vs wahyu)
		if ($user !== null && $user !== '' && strcasecmp($session_user, $user) !== 0) {
			show_404();
			return;
		}

		// pakai username dari session sebagai sumber kebenaran
		$user = $session_user;

		$existing = $this->user_model->get_by_username($user);
		if (empty($existing)) {
			show_404();
			return;
		}

		if ($this->input->method(TRUE) === 'POST') {

			$nama_lengkap = trim($this->input->post('nama_lengkap', TRUE));
			$email        = trim($this->input->post('email', TRUE));
			$alamat       = trim($this->input->post('alamat', TRUE));
			$no_telepon   = trim($this->input->post('no_telepon', TRUE));
			$dob          = $this->input->post('dob', TRUE);

			$password = $this->input->post('password', TRUE);
			$confirm  = $this->input->post('confirm_pass', TRUE);

			$data = array();

			if ($nama_lengkap !== '') $data['NAMA_LENGKAP']  = $nama_lengkap;
			if ($email !== '')       $data['EMAIL']         = $email;
			if ($alamat !== '')      $data['ALAMAT']        = $alamat;
			if ($no_telepon !== '')  $data['NO_TELEPON']    = $no_telepon;
			if ($dob !== null && $dob !== '') $data['TANGGAL_LAHIR'] = $dob;

			if ($password !== null && $password !== '') {
				if ($confirm !== null && $confirm !== '' && $password !== $confirm) {
					$this->session->set_flashdata('error', 'Password dan confirm password tidak sama.');
					redirect(site_url('edit_data'));
					return;
				}
				$data['PASSWORD'] = $password;
			}

			// departemen & nama_perusahaan hanya tampil, tidak diupdate

			if (!empty($data)) {
				$this->user_model->update_data($user, $data);
				$this->session->set_flashdata('success_popup', 'Data anda berhasil diubah.');
			}

			// kembali ke HOME (index)
			redirect(site_url('home'), 'refresh');
			return;
		}

		$data = array('user' => $existing);
		$this->load->view('home/edit_data', $data);
	}
	public function edit_foto($user = null)
	{
		$this->load->helper(['form', 'url']);
		$this->load->model('user/user_model');
		$this->load->library('upload');

		$session_user = (string) $this->session->userdata('username');
		if ($session_user === '') {
			redirect(site_url('login'));
			return;
		}

		// kalau URL mengirim param username, pastikan sama dengan session
		if ($user !== null && $user !== '' && strcasecmp($session_user, $user) !== 0) {
			show_404();
			return;
		}

		$user = $session_user;

		$existing = $this->user_model->get_by_username($user);
		if (empty($existing)) {
			show_404();
			return;
		}

		// ===== POST: upload & simpan =====
		if ($this->input->method(TRUE) === 'POST') {

			$upload_dir = FCPATH . 'assets/images/profile/';
			if (!is_dir($upload_dir)) {
				@mkdir($upload_dir, 0755, true);
			}

			$existing_foto = !empty($existing['FOTO_PROFIL']) ? $existing['FOTO_PROFIL'] : null;

			// (A) Hasil crop base64 dari frontend (Cropper.js)
			$cropped = $this->input->post('cropped_image', false); // jangan XSS clean (base64 bisa kepotong)

			if (!empty($cropped)) {

				// Format: data:image/jpeg;base64,xxxx atau data:image/png;base64,xxxx
				if (preg_match('#^data:image/(png|jpeg);base64,#i', $cropped, $m) !== 1) {
					$this->session->set_flashdata('error', 'Data gambar tidak valid.');
					redirect(site_url('edit_foto/' . $user));
					return;
				}

				$ext = (strtolower($m[1]) === 'jpeg') ? 'jpg' : 'png';

				$base64 = preg_replace('#^data:image/(png|jpeg);base64,#i', '', $cropped);
				$binary = base64_decode($base64, true);

				if ($binary === false) {
					$this->session->set_flashdata('error', 'Gagal decode gambar.');
					redirect(site_url('edit_foto/' . $user));
					return;
				}

				// Validasi benar-benar image
				$imgInfo = @getimagesizefromstring($binary);
				if ($imgInfo === false || empty($imgInfo['mime']) || strpos($imgInfo['mime'], 'image/') !== 0) {
					$this->session->set_flashdata('error', 'File bukan gambar valid.');
					redirect(site_url('edit_foto/' . $user));
					return;
				}

				// Batasi ukuran (2MB)
				if (strlen($binary) > 2 * 1024 * 1024) {
					$this->session->set_flashdata('error', 'Ukuran hasil crop terlalu besar (maks 2MB).');
					redirect(site_url('edit_foto/' . $user));
					return;
				}

				$filename     = 'pp_' . $user . '_' . date('Ymd_His') . '.' . $ext;
				$new_rel_path = 'assets/images/profile/' . $filename;
				$new_abs_path = $upload_dir . $filename;

				if (file_put_contents($new_abs_path, $binary) === false) {
					$this->session->set_flashdata('error', 'Gagal menyimpan file gambar.');
					redirect(site_url('edit_foto/' . $user));
					return;
				}

				// Hapus foto lama
				if (!empty($existing_foto)) {
					$old_abs = FCPATH . $existing_foto;
					if (file_exists($old_abs)) {
						@unlink($old_abs);
					}
				}

				// Update DB + session
				$this->user_model->update_foto_profil($user, $new_rel_path);
				$this->session->set_userdata('foto_profil', $new_rel_path);

				$this->session->set_flashdata('success_popup', 'Foto profil berhasil diubah.');
				redirect(site_url('home'), 'refresh');
				return;
			}

			// (B) Kalau belum ada crop, jangan upload langsung
			$this->session->set_flashdata('error', 'Pilih foto lalu atur posisi (crop) dulu sebelum simpan.');
			redirect(site_url('edit_foto/' . $user));
			return;
		}

		// ===== GET: tampilkan halaman edit foto =====
		$data = array('user' => $existing);
		$this->load->view('home/edit_fotoprofil', $data);
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
	public function location()
	{
		$username = $this->session->userdata('username');

		$this->load->model('gedung/gedung_model');
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);

		$this->load->view('home/location', $data);
	}
	public function ulasan()
	{
		$this->load->model('Ulasan/Ulasan_Model', 'Ulasan_model');

		// ambil ulasan APPROVED
		$rows = $this->Ulasan_model->get_approved(30);

		// mapping biar cocok ke view (name/rating/date/title/comment)
		$reviews = array();
		foreach ($rows as $r) {
			$reviews[] = array(
				'name'    => isset($r['USERNAME']) ? $r['USERNAME'] : '',
				'rating'  => isset($r['RATING']) ? (int)$r['RATING'] : 0,
				'date'    => isset($r['CREATED_AT']) ? date('Y-m-d', strtotime($r['CREATED_AT'])) : '',
				'title'   => isset($r['TITLE']) ? $r['TITLE'] : '',      // ini nanti jadi NAMA GEDUNG
				'comment' => isset($r['COMMENT']) ? $r['COMMENT'] : ''
			);
		}

		// ✅ ambil list gedung untuk dropdown
		$gedungs = $this->db->select('ID_GEDUNG, NAMA_GEDUNG')
			->from('gedung')
			->order_by('NAMA_GEDUNG', 'ASC')
			->get()
			->result_array();

		$data['reviews'] = $reviews;
		$data['gedungs'] = $gedungs;

		$this->load->view('home/ulasan', $data);
	}



	public function submit_ulasan()
	{
		$rating  = (int)$this->input->post('rating');
		$name    = trim($this->input->post('name'));
		$gedung  = trim($this->input->post('gedung'));  // ✅ nama gedung dari dropdown
		$comment = trim($this->input->post('comment'));

		if ($rating < 1 || $rating > 5 || $comment === '' || $name === '' || $gedung === '') {
			$this->session->set_flashdata('error', 'Nama, gedung, rating, dan komentar wajib diisi.');
			redirect('home/ulasan');
			return;
		}

		$this->load->model('Ulasan/Ulasan_Model', 'Ulasan_model');
		if ($this->Ulasan_model->exists_by_username($name)) {
			$this->session->set_flashdata('error', 'Kamu sudah pernah mengirim ulasan. Tidak bisa mengisi ulang.');
			redirect('home/ulasan');
			return;
		}

		$ok = $this->Ulasan_model->insert_ulasan(array(
			'USERNAME'   => $name,
			'RATING'     => $rating,
			'TITLE'      => $gedung,          // ✅ simpan nama gedung ke kolom TITLE
			'COMMENT'    => $comment,
			'STATUS'     => 'APPROVED',        // Opsi A: langsung tampil
			'CREATED_AT' => date('Y-m-d H:i:s')
		));

		if ($ok) {
			$this->session->set_flashdata('success', 'Ulasan kamu berhasil dikirim dan sudah tampil.');
		} else {
			$this->session->set_flashdata('error', 'Gagal mengirim ulasan. Coba lagi.');
		}

		redirect('home/ulasan');
	}
	public function notif_status()
{
    $username = $this->session->userdata('username');
    if (empty($username)) {
        show_error('Unauthorized', 401);
        return;
    }

    $this->load->model('gedung/gedung_model');

    $rows = $this->gedung_model->get_status_by_user($username);

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($rows));
}
 public function notif_poll()
    {
        $username = $this->session->userdata('username');

        // keamanan: kalau belum login jangan kasih data
        if (!$username) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'ok' => false,
                    'flag' => 0,
                    'message' => 'unauthorized'
                ]));
            return;
        }

        $this->load->model('gedung/gedung_model');

        $flag = (int) $this->gedung_model->get_pemesanan_flag($username);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok' => true,
                'flag' => $flag
            ]));
    } 
	public function notif_status_by_user(){
		$username = $this->session->userdata('username');	
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_status_by_user($username);
		$this->load->view('home/notif_status_by_user', $data);
	}

}