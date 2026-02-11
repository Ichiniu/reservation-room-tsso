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

	// 	public function index()
	// 	{
	// 		$username = $this->session->userdata('username');
	// 		$this->load->model('gedung/gedung_model');

	// 		// --- 1. Ambil data dasar seperti biasa ---
	// 		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
	// $data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

	// 		$data['res']  = $this->gedung_model->get_all();

	// 		// --- 2. Ambil filter tanggal & jam dari QUERY STRING (GET) ---
	// 		// contoh URL: /home?tanggal=2025-01-10&jam=09:00
	// 		$tanggal = $this->input->get('tanggal');
	// 		$jam     = $this->input->get('jam');

	// 		$data['tanggal_filter'] = $tanggal;
	// 		$data['jam_filter']     = $jam;

	// 		// --- 3. Hitung ketersediaan per gedung ---
	// 		// Pakai fungsi check_date() yang sudah ada (cek apakah tanggal tsb sudah dibooking)
	// 		$availability = [];

	// 		if (!empty($tanggal)) {
	// 			// kalau ada filter tanggal (jam opsional dulu), cek satu-satu
	// 			foreach ($data['res'] as $row) {
	// 				// get_all() kamu mengembalikan array, jadi akses pakai $row['ID_GEDUNG']
	// 				$id_gedung = $row['ID_GEDUNG'];

	// 				// check_date() akan mengembalikan jumlah booking di tanggal tsb
	// 				// kalau > 0 berarti SUDAH dibooking (tidak available)
	// 				$exist = $this->check_date($tanggal, $id_gedung);

	// 				// true  => tersedia
	// 				// false => sudah dibooking
	// 				$availability[$id_gedung] = ($exist == 0);
	// 			}
	// 		}

	// 		$data['availability'] = $availability;

	// 		// --- 4. Kirim ke view ---
	// 		$this->load->view('/home/home_screen', $data);
	// 	}

	public function index()
	{
		$username = (string)$this->session->userdata('username');

		// ===== LOAD MODEL =====
		$this->load->model('gedung/gedung_model');
		$this->load->model('Ulasan/Ulasan_Model', 'Ulasan_model');

		// ===== Helper tanggal Indo: 07 januari 2026 =====
		$tgl_indo = function ($tgl) {
			$tgl = trim((string)$tgl);
			if ($tgl === '') return '-';

			$bulan = array(
				1 => 'januari',
				'februari',
				'maret',
				'april',
				'mei',
				'juni',
				'juli',
				'agustus',
				'september',
				'oktober',
				'november',
				'desember'
			);

			$ts = strtotime($tgl);
			if (!$ts) return $tgl;

			$d = date('d', $ts);       // 01..31 (2 digit)
			$m = (int)date('n', $ts);  // 1..12
			$y = date('Y', $ts);

			return $d . ' ' . (isset($bulan[$m]) ? $bulan[$m] : '') . ' ' . $y;
		};

		// ===== Helper jam HH:MM =====
		$time_hm = function ($t) {
			$t = trim((string)$t);
			if ($t === '') return '';
			return (strlen($t) >= 5) ? substr($t, 0, 5) : $t;
		};

		// ===== Helper jam dot: 08:00 -> 08.00 =====
		$jam_dot = function ($hm) {
			$hm = trim((string)$hm);
			if ($hm === '') return '';
			$hm = (strlen($hm) >= 5) ? substr($hm, 0, 5) : $hm;
			return str_replace(':', '.', $hm);
		};

		// ===== Parse title "Ruangan - 2026-01-07 (08:00 - 12:00)" => 3 baris =====
		$title_3baris = function ($title) use ($tgl_indo, $jam_dot) {
			$title = trim((string)$title);
			if ($title === '') return '';

			$m = array();
			if (preg_match('/^\s*(.*?)\s*-\s*([0-9]{4}-[0-9]{2}-[0-9]{2})\s*\((.*?)\)\s*$/', $title, $m)) {
				$room = trim($m[1]);
				$tgl  = trim($m[2]);
				$rng  = trim($m[3]); // "08:00 - 12:00" atau "08:00"

				$rng = preg_replace('/\s+/', ' ', $rng);

				if (strpos($rng, '-') !== false) {
					$p = explode('-', $rng);
					$a = isset($p[0]) ? $jam_dot(trim($p[0])) : '';
					$b = isset($p[1]) ? $jam_dot(trim($p[1])) : '';
					$rng = ($a && $b) ? ($a . ' - ' . $b . ' wib') : (($a ?: $b) . ' wib');
				} else {
					$rng = $jam_dot($rng) . ' wib';
				}

				return $room . "\n" . $tgl_indo($tgl) . "\n" . $rng;
			}

			// fallback jika format berbeda
			return $title;
		};

		// ===== 1) DATA DASAR =====
		$data = array();
		$data['flag']     = (int)$this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = (int)$this->gedung_model->get_transaksi_flag($username);

		$res = $this->gedung_model->get_all();
		$data['res'] = (is_array($res)) ? $res : array();

		// ===== 2) FILTER (GET) =====
		$tanggal     = trim((string)$this->input->get('tanggal', TRUE));
		$jam_legacy  = trim((string)$this->input->get('jam', TRUE));

		$jam_mulai   = trim((string)$this->input->get('jam_mulai', TRUE));
		$jam_selesai = trim((string)$this->input->get('jam_selesai', TRUE));

		if ($jam_mulai === '' && $jam_legacy !== '') {
			$jam_mulai = $jam_legacy;
		}

		$data['tanggal_filter'] = $tanggal;
		$data['jam_filter']     = $jam_mulai;
		$data['jam_mulai']      = $jam_mulai;
		$data['jam_selesai']    = $jam_selesai;

		// ===== 3) HITUNG KETERSEDIAAN =====
		$availability = array();

		if ($tanggal !== '' && !empty($data['res'])) {

			$use_time_check = ($jam_mulai !== '' && $jam_selesai !== '');

			foreach ($data['res'] as $row) {
				$id_gedung = isset($row['ID_GEDUNG']) ? (int)$row['ID_GEDUNG'] : 0;
				if ($id_gedung <= 0) continue;

				if ($use_time_check) {
					$exist = $this->check_date($tanggal, $id_gedung, $jam_mulai, $jam_selesai);
				} else {
					$exist = $this->check_date($tanggal, $id_gedung);
				}

				$availability[$id_gedung] = ((int)$exist === 0);
			}
		}

		$data['availability'] = $availability;

		// ===== 4) ULASAN (SPILL DI HOME) =====
		$data['ulasan_summary'] = $this->Ulasan_model->get_summary_approved();

		$rows_ul = $this->Ulasan_model->get_approved(3);

		$ulasan_home = array();
		if (!empty($rows_ul) && is_array($rows_ul)) {
			foreach ($rows_ul as $r) {

				$name = !empty($r['USERNAME']) ? $r['USERNAME'] : 'Customer';

				$rating = isset($r['RATING']) ? (int)$r['RATING'] : 5;
				if ($rating < 1) $rating = 1;
				if ($rating > 5) $rating = 5;

				// ✅ tanggal ulasan (created_at) format indo
				$date_disp = '-';
				if (!empty($r['CREATED_AT'])) {
					$date_disp = $tgl_indo($r['CREATED_AT']);
				}

				// ✅ title jadi 3 baris kalau formatnya cocok
				$title_disp = isset($r['TITLE']) ? $title_3baris($r['TITLE']) : '';

				$ulasan_home[] = array(
					'name'    => $name,
					'rating'  => $rating,
					'date'    => $date_disp,
					'title'   => $title_disp,
					'comment' => isset($r['COMMENT']) ? $r['COMMENT'] : '',
				);
			}
		}

		$data['ulasan_home'] = $ulasan_home;

		// ===== 5) KIRIM KE VIEW =====
		$this->load->view('home/home_screen', $data);
	}



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
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

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
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

		$this->load->view('gedung/jadwal_gedung_per_periode', $data);
	}

	public function view_catering()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_menu_catering();
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

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
			$days = 2;
		} elseif (strpos($nama, 'smart office meeting room') !== false || strpos($nama, 'meeting room') !== false) {
			$days = 2;
		} elseif (strpos($nama, 'smart office studio photo') !== false || strpos($nama, 'studio photo') !== false) {
			$days = 3;
		} elseif (strpos($nama, 'studio podcast') !== false || strpos($nama, 'podcast') !== false) {
			// ✅ Studio Podcast: H-3
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

		// ✅ Check if user is internal first (before applying booking rules)
		$u = $this->db->select('perusahaan')
			->from('user')
			->where('USERNAME', $username)
			->get()
			->row();

		$perusahaan = ($u && isset($u->perusahaan)) ? $u->perusahaan : '';
		$data['is_internal'] = (strtoupper(trim((string)$perusahaan)) === 'INTERNAL');

		// ✅ Apply different minimum booking rules based on user type
		if (!empty($data['is_internal'])) {
			// INTERNAL: Can book on the same day (H+0)
			$gedung['min_pesan'] = date('Y-m-d');
			$gedung['min_text']  = 'Anda dapat melakukan pemesanan pada hari yang sama';
			$gedung['min_days']  = 0;
		} else {
			// EXTERNAL: Apply standard booking rules (H+2, H+3, H+10)
			$rule = $this->_min_booking_rule($id_gedung);
			$gedung['min_pesan'] = date('Y-m-d', strtotime('+' . $rule['days'] . ' day'));
			$gedung['min_text']  = $rule['text'];
			$gedung['min_days']  = $rule['days'];
		}

		if (method_exists($this->catering_model, 'get_all')) {
			$data['res'] = $this->catering_model->get_all();
		} else {
			$data['res'] = $this->catering_model->get_catering_full();
		}

		$data['email'] = $this->gedung_model->get_email_address($username);
		$data['flag']  = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

		// Ambil nama ruangan untuk deteksi STUDIO
		$nama_gedung = '';
		if (!empty($gedung['hasil']) && is_array($gedung['hasil'])) {
			$first = reset($gedung['hasil']);
			if (is_array($first) && isset($first['NAMA_GEDUNG'])) {
				$nama_gedung = (string) $first['NAMA_GEDUNG'];
			}
		}

		// ===== pricing mode (khusus EKSTERNAL) =====
		// Prioritas: kolom gedung.PRICING_MODE (kalau sudah ditambahkan)
		$this->load->helper('pricing');
		$pm_db = '';
		if (!empty($gedung['hasil']) && is_array($gedung['hasil'])) {
			$first = reset($gedung['hasil']);
			if (is_array($first) && isset($first['PRICING_MODE'])) {
				$pm_db = (string) $first['PRICING_MODE'];
			}
		}
		$pricing_mode = bs_detect_pricing_mode($nama_gedung, $pm_db);
		$data['pricing_mode'] = $pricing_mode;
		$is_studio = ($pricing_mode === 'PODCAST_PER_JAM');

		// INTERNAL: semua opsi | EKSTERNAL: podcast per jam = CUSTOM saja, selain itu = half/full day
		if (!empty($data['is_internal'])) {
			$data['allowed_tipe_jam'] = array('CUSTOM', 'HALF_DAY_PAGI', 'HALF_DAY_SIANG', 'FULL_DAY');
		} else {
			$data['allowed_tipe_jam'] = $is_studio
				? array('CUSTOM')
				: array('HALF_DAY_PAGI', 'HALF_DAY_SIANG', 'FULL_DAY');
		}

		$data['default_tipe_jam'] = $data['allowed_tipe_jam'][0];


		// ✅ Pass gedung data as 'res' for view compatibility (jadwal preview needs $res[0]['ID_GEDUNG'])
		$data['res'] = $gedung['hasil'];

		$hasil = array_merge($gedung, $data);
		$this->load->view('gedung/order_gedung', $hasil);
	}

	/**
	 * POST handler for order form submission
	 * Processes booking data and redirects to confirm page
	 */
	public function order($id_gedung)
	{
		$username = $this->session->userdata('username');
		if (empty($username)) {
			redirect('login');
			return;
		}

		$this->load->model('gedung/gedung_model');
		$this->load->model('catering/catering_model');
		$this->load->helper('pricing');

		$id_gedung = (int)$id_gedung;
		if ($id_gedung <= 0) {
			show_404();
			return;
		}

		// ===== GET POST DATA =====
		$tgl_pesan    = trim((string)$this->input->post('tgl_pesan', TRUE));
		$jam_pesan    = trim((string)$this->input->post('jam_pesan', TRUE));
		$jam_selesai  = trim((string)$this->input->post('jam_selesai', TRUE));
		$email        = trim((string)$this->input->post('email', TRUE));
		$catering_val = trim((string)$this->input->post('radios', TRUE));
		$tipe_jam     = trim((string)$this->input->post('tipe_jam', TRUE));
		$request_id   = trim((string)$this->input->post('request_id', TRUE));

		// ===== VALIDATION =====
		if (empty($tgl_pesan) || empty($jam_pesan) || empty($jam_selesai) || empty($email)) {
			$this->session->set_flashdata('error', 'Semua field wajib diisi.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ✅ Get user's latest pending booking to exclude from conflict check
		$exclude_id = $this->gedung_model->get_user_latest_pending_booking($username, $id_gedung);

		// Check for conflicts (excluding user's own pending booking)
		$conflict = $this->gedung_model->check_date($tgl_pesan, $id_gedung, $jam_pesan, $jam_selesai, $exclude_id ? $exclude_id : 0);

		if ($conflict > 0) {
			$this->session->set_flashdata('error', 'Ruangan sudah terbooking / Sudah ditahap payment. Silakan pilih tanggal atau jam lain.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ===== CATERING DATA =====
		$id_catering    = null;
		$jumlah_catering = null;
		$menu_inputs    = array();
		$addon_inputs   = array();

		if ($catering_val === 'ya') {
			$id_catering = (int)$this->input->post('catering');
			$jumlah_catering = (int)$this->input->post('jumlah-porsi');

			if ($id_catering <= 0 || $jumlah_catering <= 0) {
				$this->session->set_flashdata('error', 'Pilih paket catering dan jumlah porsi dengan benar.');
				redirect('home/order-gedung/' . $id_gedung);
				return;
			}

			$menu_raw = $this->input->post('menu_input');
			if (is_array($menu_raw)) {
				foreach ($menu_raw as $k => $v) {
					$menu_inputs[$k] = trim((string)$v);
				}
			}

			$addon_enabled = $this->input->post('addon_enabled');
			$addon_values  = $this->input->post('addon_input');

			if (is_array($addon_enabled)) {
				foreach ($addon_enabled as $k => $chk) {
					if ($chk == '1') {
						$addon_inputs[$k] = isset($addon_values[$k]) ? trim((string)$addon_values[$k]) : '';
					}
				}
			}
		}

		// ===== EKSTERNAL-SPECIFIC FIELDS =====
		$total_peserta = null;
		$podcast_type  = null;

		$u = $this->db->select('perusahaan')->from('user')->where('USERNAME', $username)->get()->row();
		$is_internal = ($u && strtoupper(trim((string)$u->perusahaan)) === 'INTERNAL');

		if (!$is_internal) {
			$gedung_data = $this->gedung_model->get_gedung_name($id_gedung);
			$nama_gedung = '';
			if (!empty($gedung_data) && is_array($gedung_data) && is_array($gedung_data[0])) {
				$nama_gedung = isset($gedung_data[0]['NAMA_GEDUNG']) ? (string)$gedung_data[0]['NAMA_GEDUNG'] : '';
			}

			$pm_db = '';
			if (!empty($gedung_data) && is_array($gedung_data) && is_array($gedung_data[0]) && isset($gedung_data[0]['PRICING_MODE'])) {
				$pm_db = (string)$gedung_data[0]['PRICING_MODE'];
			}

			$pricing_mode = bs_detect_pricing_mode($nama_gedung, $pm_db);

			if ($pricing_mode === 'PER_PESERTA') {
				$total_peserta = (int)$this->input->post('total_peserta');
				if ($total_peserta <= 0) {
					$this->session->set_flashdata('error', 'Total peserta wajib diisi.');
					redirect('home/order-gedung/' . $id_gedung);
					return;
				}
			} elseif ($pricing_mode === 'PODCAST_PER_JAM') {
				$podcast_type = trim((string)$this->input->post('podcast_type', TRUE));
				if (empty($podcast_type)) {
					$this->session->set_flashdata('error', 'Pilih jenis podcast.');
					redirect('home/order-gedung/' . $id_gedung);
					return;
				}
			}
		}

		// ===== BUILD PEMESANAN DATA =====
		$data_pemesanan = array(
			'USERNAME'          => $username,
			'ID_GEDUNG'         => $id_gedung,
			'TANGGAL_PEMESANAN' => $tgl_pesan,
			'JAM_PEMESANAN'     => $jam_pesan,
			'JAM_SELESAI'       => $jam_selesai,
			'TIPE_JAM'          => $tipe_jam,
			'EMAIL'             => $email,
			'ID_CATERING'       => $id_catering,
			'JUMLAH_CATERING'   => $jumlah_catering,
			'STATUS'            => 0,
			'FLAG'              => 1,
		);

		if (!empty($request_id)) {
			$data_pemesanan['REQUEST_ID'] = $request_id;
		}

		if ($total_peserta !== null) {
			if ($this->db->field_exists('TOTAL_PESERTA', 'pemesanan')) {
				$data_pemesanan['TOTAL_PESERTA'] = $total_peserta;
			}
		}

		if ($podcast_type !== null) {
			if ($this->db->field_exists('PODCAST_TYPE', 'pemesanan')) {
				$data_pemesanan['PODCAST_TYPE'] = $podcast_type;
			}
		}
		// ✅ FALLBACK: pastikan TOTAL_PESERTA tersimpan jika ada di POST
		if (!isset($data_pemesanan['TOTAL_PESERTA'])) {
			$peserta_post = $this->input->post('total_peserta');
			if ($peserta_post !== null && $peserta_post !== '') {
				$peserta_val = (int)$peserta_post;
				if ($peserta_val > 0) {
					$data_pemesanan['TOTAL_PESERTA'] = $peserta_val;
				}
			}
		}
		// ===== INSERT PEMESANAN =====
		$id_pemesanan = $this->gedung_model->insert_pemesanan($data_pemesanan);

		if (!$id_pemesanan) {
			$this->session->set_flashdata('error', 'Gagal menyimpan pemesanan. Silakan coba lagi.');
			redirect('home/order-gedung/' . $id_gedung);
			return;
		}

		// ===== SAVE CATERING DETAILS (menu + addon) =====
		if (!empty($menu_inputs) || !empty($addon_inputs)) {
			$combined = array_merge($menu_inputs, $addon_inputs);
			$json_str = json_encode($combined, JSON_UNESCAPED_UNICODE);

			if ($this->db->table_exists('pemesanan_catering_details')) {
				$this->db->replace('pemesanan_catering_details', array(
					'ID_PEMESANAN' => $id_pemesanan,
					'DETAIL_JSON'  => $json_str
				));
			}
		}

		// ===== REDIRECT TO CONFIRM PAGE =====
		redirect('home/confirm-order/' . $id_pemesanan);
	}





	public function pemesanan()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		// NOTE: do not clear pemesanan flag here — inbox badge should remain
		// based on STATUS='PROCESS' so users still see the badge after opening.

		$data['res'] = $this->gedung_model->get_pemesanan($username);
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

		$data['no_data'] = "Data Kosong";
		$data['rows'] = $this->gedung_model->count_pemesanan($username);
		$username = $this->session->userdata('username');

		$data['notifs_pemesanan'] = $this->db->order_by('id', 'DESC')
			->limit(10)
			->like('type', 'USER_PEMESANAN_', 'after')
			->get_where('notifications', [
				'username' => $username,
				'read_at'  => null
			])
			->result_array();

		$this->load->view('home/pemesanan', $data);
	}

	public function pembayaran()
	{
		$this->load->model('gedung/gedung_model');
		$username = $this->session->userdata('username');

		$this->gedung_model->clear_transaksi_flag($username);

		$data['res'] = $this->gedung_model->user_detail_pembayaran($username);
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

		$data['notifs_transaksi'] = $this->db->order_by('id', 'DESC')
			->limit(10)
			->like('type', 'USER_TRANSAKSI_', 'after')
			->get_where('notifications', [
				'username' => $username,
				'read_at'  => null
			])
			->result_array();
		$this->db->where('username', $username)
			->like('type', 'USER_TRANSAKSI_', 'after')
			->where('read_at IS NULL', null, false)
			->update('notifications', ['read_at' => date('Y-m-d H:i:s')]);

		$this->load->view('home/pembayaran', $data);
	}


	public function detail_pemesanan($id_pemesanan)
	{
		$username = $this->session->userdata('username');
		$temp_id = substr($id_pemesanan, 7);

		$this->load->model('gedung/gedung_model');

		// update flag (pastikan numeric)
		$this->gedung_model->update_user_flag((int)$temp_id);
		$temp_id = (int) preg_replace('/\D+/', '', (string)$id_pemesanan); // ganti substr biar aman

		$data['proposal_details'] = $this->gedung_model->get_proposal_by_id($temp_id);

		// data pemesanan (dari view V_PEMESANAN)
		$data['result'] = $this->gedung_model->get_detail_pesanan($id_pemesanan);
		$data['flag']   = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);


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

		// ===== hitung ulang harga sewa (khusus EKSTERNAL) supaya detail & pembayaran konsisten =====
		if (!empty($data['result'])) {
			$this->load->helper('pricing');

			$extra_select = array(
				'p.ID_PEMESANAN',
				'p.USERNAME',
				'p.ID_GEDUNG',
				'p.TIPE_JAM',
				'p.JAM_PEMESANAN',
				'p.JAM_SELESAI',
				'u.perusahaan',
				'g.NAMA_GEDUNG',
				'g.HARGA_SEWA'
			);

			if ($this->db->field_exists('TOTAL_PESERTA', 'pemesanan')) $extra_select[] = 'p.TOTAL_PESERTA';
			if ($this->db->field_exists('PODCAST_TYPE', 'pemesanan')) $extra_select[] = 'p.PODCAST_TYPE';
			if ($this->db->field_exists('PRICING_MODE', 'gedung')) $extra_select[] = 'g.PRICING_MODE';
			if ($this->db->field_exists('HARGA_HALF_DAY_PP', 'gedung')) $extra_select[] = 'g.HARGA_HALF_DAY_PP';
			if ($this->db->field_exists('HARGA_FULL_DAY_PP', 'gedung')) $extra_select[] = 'g.HARGA_FULL_DAY_PP';
			if ($this->db->field_exists('HARGA_AUDIO_PER_JAM', 'gedung')) $extra_select[] = 'g.HARGA_AUDIO_PER_JAM';
			if ($this->db->field_exists('HARGA_VIDEO_PER_JAM', 'gedung')) $extra_select[] = 'g.HARGA_VIDEO_PER_JAM';

			$extra = $this->db->select(implode(",\n\t\t\t\t", $extra_select), false)
				->from('pemesanan p')
				->join('user u', 'u.USERNAME = p.USERNAME', 'left')
				->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
				->where('p.ID_PEMESANAN', (int)$temp_id)
				->get()
				->row();

			if ($extra) {
				$perusahaan_val = (isset($extra->perusahaan) && $extra->perusahaan !== null) ? $extra->perusahaan : '';
				$is_internal_user = (strtoupper(trim((string)$perusahaan_val)) === 'INTERNAL');
				$harga_sewa_calc = (int) bs_calc_room_sewa($extra, $is_internal_user);
				$durasi_jam = (int) bs_duration_hours_ceil($extra->JAM_PEMESANAN, $extra->JAM_SELESAI);

				// override object hasil dari V_PEMESANAN
				$data['result']->HARGA_SEWA = $harga_sewa_calc;
				$data['result']->PRICING_MODE = bs_detect_pricing_mode($extra->NAMA_GEDUNG, isset($extra->PRICING_MODE) ? $extra->PRICING_MODE : '');
				$data['result']->DURASI_JAM = $durasi_jam;
				if (isset($extra->TOTAL_PESERTA)) $data['result']->TOTAL_PESERTA = (int)$extra->TOTAL_PESERTA;
				if (isset($extra->PODCAST_TYPE)) $data['result']->PODCAST_TYPE = (string)$extra->PODCAST_TYPE;

				$total_catering_val = isset($data['result']->TOTAL_HARGA) ? (int)$data['result']->TOTAL_HARGA : 0;
				$data['result']->TOTAL_KESELURUHAN = (int)$harga_sewa_calc + (int)$total_catering_val;
			}
		}

		// =========================
		// ✅ TAMBAHAN: Catatan Admin saat REJECT (dari tabel pembayaran)
		// =========================
		$cat = $this->db
			->select('CATATAN_ADMIN')
			->from('pembayaran')
			->where('ID_PEMESANAN_RAW', (int)$temp_id)
			->where('STATUS_VERIF', 'REJECTED')
			->order_by('ID_PEMBAYARAN', 'DESC')
			->limit(1)
			->get()
			->row();

		$data['catatan_admin_reject'] = $cat ? (string)$cat->CATATAN_ADMIN : '';
		// kalau status REJECTED, paksa REMARKS pakai catatan admin dari tabel pembayaran
		if (!empty($data['result']) && $data['catatan_admin_reject'] !== '') {
			$data['result']->REMARKS = $data['catatan_admin_reject'];
		}

		$this->load->view('home/detail_pemesanan', $data);
	}


	public function upload_proposal($id_pemesanan = null)
	{
		$this->load->helper(['form']);
		$this->load->model('gedung/gedung_model');
		$this->load->model('pembayaran/pembayaran_model');

		$username = $this->session->userdata('username');
		if (!$username) show_404();

		$id = $id_pemesanan ?: (int)$this->input->post('id_pemesanan');
		if ($id <= 0) show_404();

		// pastikan order ini milik user yg login
		if (!$this->gedung_model->is_order_owner($id, $username)) show_404();

		// tandai notif/flag unread
		$this->gedung_model->mark_flag_unread('PMSN000' . $id);

		// =========================
		// TANPA UPLOAD FILE (HANYA DESKRIPSI)
		// =========================
		$deskripsi = trim((string)$this->input->post('deskripsi-acara', TRUE));
		if ($deskripsi === '') {
			$this->session->set_flashdata('upload_error', 'Keperluan acara wajib diisi.');
			redirect('home/home/validasi_upload/' . $id);
			return;
		}

		// opsional: batasi max 200 karakter (sesuai maxlength di UI)
		if (strlen($deskripsi) > 200) {
			$deskripsi = substr($deskripsi, 0, 200);
		}

		$data = [
			'ID_PEMESANAN'    => $id,
			'PATH'            => '',   // aman kalau kolom NOT NULL
			'FILE_NAME'       => '',   // aman kalau kolom NOT NULL
			'DESKRIPSI_ACARA' => $deskripsi,
		];

		if ($this->gedung_model->proposal_exists($id)) {
			$this->gedung_model->update_proposal($id, [
				'ID_PEMESANAN'    => $id,
				'DESKRIPSI_ACARA' => $deskripsi,
				'PATH'            => '',
				'FILE_NAME'       => '',
			]);
		} else {
			$this->gedung_model->upload_proposal($data);
		}

		// =========================
		// Cek internal / eksternal
		// =========================
		$u = $this->db->select('perusahaan')
			->from('user')
			->where('USERNAME', $username)
			->get()
			->row();

		$is_internal = ($u && strtoupper(trim((string)$u->perusahaan)) === 'INTERNAL');

		// ✅ draft session dibuang setelah submit sukses (biar back/edit gak nyangkut)
		$this->session->unset_userdata('draft_pemesanan_id');

		// =========================
		// INTERNAL: auto confirmed
		// =========================
		if ($is_internal) {

			// ... (blok internal kamu tetap)

			$this->load->library('notification_service');
			$this->notification_service->notifyUser(
				$username,
				'USER_TRANSAKSI_CONFIRMED',
				'Pemesanan internal dikonfirmasi',
				'Pemesanan PMSN000' . $id . ' adalah INTERNAL dan otomatis CONFIRMED.',
				'home/pembayaran',
				true
			);

			$this->session->set_flashdata('upload_success', 'INTERNAL: Keperluan acara tersimpan & pemesanan langsung CONFIRMED.');
			redirect('home/pemesanan');
			return;
		}

		// =========================
		// EKSTERNAL: notif ke admin
		// =========================
		$this->load->library('notification_service');
		$this->notification_service->notifyAdmin(
			'ADMIN_INBOX',
			'Pesanan masuk (PROCESS)',
			'Ada pesanan baru PMSN000' . $id . ' dari user ' . $username . '.',
			'admin/pemesanan.php',
			true
		);

		$this->session->set_flashdata('upload_success', 'Keperluan acara berhasil disimpan.');
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

	public function sort_by_name()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->sort_by_name();
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

		$this->load->view('/home/home_screen', $data);
	}

	public function sort_by_capacity()
	{
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->sort_by_capacity();
		$username = $this->session->userdata('username');
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

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
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);

		$this->load->view('home/search_gedung', $data);
	}

	public function confirm_order($id_pemesanan)
	{
		$this->load->model('gedung/gedung_model');

		$username = $this->session->userdata('username');
		if (!$username) show_404();

		$hasil['res'] = $this->gedung_model->get_order_by_id_user($id_pemesanan, $username);
		if (empty($hasil['res'])) show_404();


		// ===== hitung ulang harga sewa (khusus EKSTERNAL) supaya tampilan & pembayaran konsisten =====
		$this->load->helper('pricing');
		$extra_select = array(
			'p.ID_PEMESANAN',
			'p.USERNAME',
			'p.ID_GEDUNG',
			'p.TIPE_JAM',
			'p.JAM_PEMESANAN',
			'p.JAM_SELESAI',
			'u.perusahaan',
			'g.NAMA_GEDUNG',
			'g.HARGA_SEWA'
		);

		if ($this->db->field_exists('TOTAL_PESERTA', 'pemesanan')) $extra_select[] = 'p.TOTAL_PESERTA';
		if ($this->db->field_exists('PODCAST_TYPE', 'pemesanan')) $extra_select[] = 'p.PODCAST_TYPE';
		if ($this->db->field_exists('PRICING_MODE', 'gedung')) $extra_select[] = 'g.PRICING_MODE';
		if ($this->db->field_exists('HARGA_HALF_DAY_PP', 'gedung')) $extra_select[] = 'g.HARGA_HALF_DAY_PP';
		if ($this->db->field_exists('HARGA_FULL_DAY_PP', 'gedung')) $extra_select[] = 'g.HARGA_FULL_DAY_PP';
		if ($this->db->field_exists('HARGA_AUDIO_PER_JAM', 'gedung')) $extra_select[] = 'g.HARGA_AUDIO_PER_JAM';
		if ($this->db->field_exists('HARGA_VIDEO_PER_JAM', 'gedung')) $extra_select[] = 'g.HARGA_VIDEO_PER_JAM';

		$extra = $this->db->select(implode(",
			", $extra_select), false)
			->from('pemesanan p')
			->join('user u', 'u.USERNAME = p.USERNAME', 'left')
			->join('gedung g', 'g.ID_GEDUNG = p.ID_GEDUNG', 'left')
			->where('p.ID_PEMESANAN', (int)$id_pemesanan)
			->get()
			->row();

		if ($extra) {
			$perusahaan_val = (isset($extra->perusahaan) && $extra->perusahaan !== null) ? $extra->perusahaan : '';
			$is_internal = (strtoupper(trim((string)$perusahaan_val)) === 'INTERNAL');
			$harga_sewa_calc = (int) bs_calc_room_sewa($extra, $is_internal);

			// override untuk view confirm_order
			if (isset($hasil['res'][0])) {
				$hasil['res'][0]['HARGA_SEWA'] = $harga_sewa_calc;
				$hasil['res'][0]['PRICING_MODE'] = bs_detect_pricing_mode($extra->NAMA_GEDUNG, isset($extra->PRICING_MODE) ? $extra->PRICING_MODE : '');
				if (isset($extra->TOTAL_PESERTA)) $hasil['res'][0]['TOTAL_PESERTA'] = (int)$extra->TOTAL_PESERTA;
				if (isset($extra->PODCAST_TYPE)) $hasil['res'][0]['PODCAST_TYPE'] = (string)$extra->PODCAST_TYPE;
				$durasi_jam = bs_duration_hours_ceil($extra->JAM_PEMESANAN, $extra->JAM_SELESAI);
				$hasil['res'][0]['DURASI_JAM'] = (int)$durasi_jam;

				$total_catering_val = isset($hasil['res'][0]['TOTAL_HARGA']) ? (float)$hasil['res'][0]['TOTAL_HARGA'] : 0;
				$hasil['res'][0]['TOTAL_KESELURUHAN'] = (float)$harga_sewa_calc + (float)$total_catering_val;

				// ✅ FALLBACK: pastikan TOTAL_PESERTA terambil dari database
				if (!isset($hasil['res'][0]['TOTAL_PESERTA']) || $hasil['res'][0]['TOTAL_PESERTA'] == 0) {
					if ($this->db->field_exists('TOTAL_PESERTA', 'pemesanan')) {
						$peserta_row = $this->db->select('TOTAL_PESERTA')
							->from('pemesanan')
							->where('ID_PEMESANAN', (int)$id_pemesanan)
							->get()
							->row();

						if ($peserta_row && isset($peserta_row->TOTAL_PESERTA) && $peserta_row->TOTAL_PESERTA > 0) {
							$hasil['res'][0]['TOTAL_PESERTA'] = (int)$peserta_row->TOTAL_PESERTA;
						}
					}
				}
			}
		}

		$hasil['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/confirm_order', $hasil);
	}
	public function location()
	{
		$username = $this->session->userdata('username');

		$this->load->model('gedung/gedung_model');
		$data['flag']     = $this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = $this->gedung_model->get_transaksi_flag($username);


		$this->load->view('home/location', $data);
	}
	// public function ulasan()
	// {
	// 	$this->load->model('Ulasan/Ulasan_Model', 'Ulasan_model');
	// 	$this->load->model('pemesanan/Pemesanan_Model', 'Pemesanan_model');

	// 	// ambil ulasan APPROVED
	// 	$rows = $this->Ulasan_model->get_approved(30);

	// 	// mapping biar cocok ke view (name/rating/date/title/comment)
	// 	$reviews = array();
	// 	foreach ($rows as $r) {
	// 		$reviews[] = array(
	// 			'name'    => isset($r['USERNAME']) ? $r['USERNAME'] : '',
	// 			'rating'  => isset($r['RATING']) ? (int)$r['RATING'] : 0,
	// 			'date'    => isset($r['CREATED_AT']) ? date('Y-m-d', strtotime($r['CREATED_AT'])) : '',
	// 			'title'   => isset($r['TITLE']) ? $r['TITLE'] : '',
	// 			'comment' => isset($r['COMMENT']) ? $r['COMMENT'] : ''
	// 		);
	// 	}

	// 	// summary akurat (berdasarkan semua APPROVED)
	// 	$data['summary'] = $this->Ulasan_model->get_summary_approved();

	// 	// helper format jam HH:MM
	// 	$time_hm = function ($t) {
	// 		$t = trim((string)$t);
	// 		if ($t === '') return '';
	// 		return (strlen($t) >= 5) ? substr($t, 0, 5) : $t;
	// 	};

	// 	// dropdown pemesanan: hanya STATUS=3 (submitted) dan belum pernah diulas
	// 	$username = $this->session->userdata('username');
	// 	$reservasi_list = array();

	// 	if (!empty($username)) {
	// 		$orders = $this->Pemesanan_model->get_submitted_by_username($username);

	// 		// filter agar yang sudah diulas tidak muncul
	// 		$has_id_pemesanan_col = $this->db->field_exists('ID_PEMESANAN', 'ulasan');
	// 		$reviewed_ids = $has_id_pemesanan_col ? $this->Ulasan_model->get_reviewed_id_pemesanan_by_username($username) : array();
	// 		$reviewed_titles = !$has_id_pemesanan_col ? $this->Ulasan_model->get_reviewed_titles_by_username($username) : array();

	// 		$reviewed_ids_map = array();
	// 		foreach ($reviewed_ids as $rid) $reviewed_ids_map[(int)$rid] = true;

	// 		$reviewed_titles_map = array();
	// 		foreach ($reviewed_titles as $t) $reviewed_titles_map[$t] = true;

	// 		foreach ($orders as $o) {
	// 			$id = (int)$o['ID_PEMESANAN'];

	// 			$nama_gedung = isset($o['NAMA_GEDUNG']) ? trim((string)$o['NAMA_GEDUNG']) : '';
	// 			$tanggal     = isset($o['TANGGAL_PEMESANAN']) ? trim((string)$o['TANGGAL_PEMESANAN']) : '';

	// 			$jam_mulai_disp   = $time_hm(isset($o['JAM_PEMESANAN']) ? $o['JAM_PEMESANAN'] : '');
	// 			$jam_selesai_disp = $time_hm(isset($o['JAM_SELESAI']) ? $o['JAM_SELESAI'] : '');

	// 			if ($jam_selesai_disp === '00:00') $jam_selesai_disp = '';

	// 			$range = $jam_selesai_disp ? ($jam_mulai_disp . ' - ' . $jam_selesai_disp) : $jam_mulai_disp;

	// 			// label untuk dropdown + kunci untuk cek sudah diulas
	// 			$title_key = $nama_gedung . ' - ' . $tanggal . ' (' . $range . ')';

	// 			if ($has_id_pemesanan_col) {
	// 				if (isset($reviewed_ids_map[$id])) continue;
	// 			} else {
	// 				if (isset($reviewed_titles_map[$title_key])) continue;
	// 			}

	// 			$reservasi_list[] = array(
	// 				'ID_PEMESANAN' => $id,
	// 				'label'        => $title_key,
	// 				'title_key'    => $title_key,
	// 			);
	// 		}
	// 	}

	// 	$data['reviews'] = $reviews;
	// 	$data['reservasi_list'] = $reservasi_list;

	// 	$this->load->view('home/ulasan', $data);
	// }

	public function ulasan()
	{
		$this->load->model('Ulasan/Ulasan_Model', 'Ulasan_model');
		$this->load->model('pemesanan/Pemesanan_Model', 'Pemesanan_model');

		// ===== helper format tanggal indo: 07 januari 2026 =====
		$tgl_indo = function ($tgl) {
			$tgl = trim((string)$tgl);
			if ($tgl === '') return '';

			$bulan = array(
				1 => 'januari',
				'februari',
				'maret',
				'april',
				'mei',
				'juni',
				'juli',
				'agustus',
				'september',
				'oktober',
				'november',
				'desember'
			);

			$ts = strtotime($tgl);
			if (!$ts) return $tgl;

			$d = date('d', $ts);
			$m = (int)date('n', $ts);
			$y = date('Y', $ts);

			return $d . ' ' . (isset($bulan[$m]) ? $bulan[$m] : '') . ' ' . $y;
		};

		// ===== helper jam HH:MM =====
		$time_hm = function ($t) {
			$t = trim((string)$t);
			if ($t === '') return '';
			return (strlen($t) >= 5) ? substr($t, 0, 5) : $t;
		};

		// ===== helper jam jadi 08.00 =====
		$jam_dot = function ($hm) {
			$hm = trim((string)$hm);
			if ($hm === '') return '';
			$hm = (strlen($hm) >= 5) ? substr($hm, 0, 5) : $hm;
			return str_replace(':', '.', $hm);
		};

		// ===== 1) Ambil ulasan APPROVED =====
		$rows = $this->Ulasan_model->get_approved(30);

		// mapping biar cocok ke view (name/rating/date/title/comment)
		$reviews = array();
		foreach ($rows as $r) {
			$created_at = isset($r['CREATED_AT']) ? (string)$r['CREATED_AT'] : '';
			$created_at_indo = $created_at ? $tgl_indo($created_at) : '';

			// Jika TITLE berisi "Nama - 2026-01-07 (08:00 - 12:00)" -> ubah jadi 3 baris
			// Kita ubah title jadi ada \n agar di view bisa ditampilkan per baris (atau kamu split)
			$title_raw = isset($r['TITLE']) ? (string)$r['TITLE'] : '';
			$title_pretty = $title_raw;

			// coba parse format title lama
			$m = array();
			if (preg_match('/^\s*(.*?)\s*-\s*([0-9]{4}-[0-9]{2}-[0-9]{2})\s*\((.*?)\)\s*$/', $title_raw, $m)) {
				$room = trim($m[1]);
				$tgl  = trim($m[2]);
				$rng  = trim($m[3]); // "08:00 - 12:00"

				// ubah jam
				$rng = preg_replace('/\s+/', ' ', $rng);
				if (strpos($rng, '-') !== false) {
					$p = explode('-', $rng);
					$a = isset($p[0]) ? $jam_dot(trim($p[0])) : '';
					$b = isset($p[1]) ? $jam_dot(trim($p[1])) : '';
					$rng = ($a && $b) ? ($a . ' - ' . $b . ' wib') : (($a ?: $b) . ' wib');
				} else {
					$rng = $jam_dot($rng) . ' wib';
				}

				// jadikan 3 baris (pakai \n)
				$title_pretty = $room . "\n" . $tgl_indo($tgl) . "\n" . $rng;
			}

			$reviews[] = array(
				'name'    => isset($r['USERNAME']) ? $r['USERNAME'] : '',
				'rating'  => isset($r['RATING']) ? (int)$r['RATING'] : 0,
				// ✅ tanggal ulasan sudah Indo
				'date'    => $created_at_indo,
				// ✅ title sudah 3 baris (atau tetap raw kalau formatnya beda)
				'title'   => $title_pretty,
				'comment' => isset($r['COMMENT']) ? $r['COMMENT'] : ''
			);
		}

		// ===== 2) summary akurat (berdasarkan semua APPROVED) =====
		$data['summary'] = $this->Ulasan_model->get_summary_approved();

		// ===== 3) dropdown pemesanan (STATUS=3) yang belum pernah diulas =====
		$username = $this->session->userdata('username');
		$reservasi_list = array();

		if (!empty($username)) {
			$orders = $this->Pemesanan_model->get_submitted_by_username($username);

			// filter agar yang sudah diulas tidak muncul
			$has_id_pemesanan_col = $this->db->field_exists('ID_PEMESANAN', 'ulasan');
			$reviewed_ids = $has_id_pemesanan_col ? $this->Ulasan_model->get_reviewed_id_pemesanan_by_username($username) : array();
			$reviewed_titles = !$has_id_pemesanan_col ? $this->Ulasan_model->get_reviewed_titles_by_username($username) : array();

			$reviewed_ids_map = array();
			foreach ($reviewed_ids as $rid) $reviewed_ids_map[(int)$rid] = true;

			$reviewed_titles_map = array();
			foreach ($reviewed_titles as $t) $reviewed_titles_map[$t] = true;

			foreach ($orders as $o) {
				$id = (int)$o['ID_PEMESANAN'];

				$nama_gedung = isset($o['NAMA_GEDUNG']) ? trim((string)$o['NAMA_GEDUNG']) : '';
				$tanggal_raw = isset($o['TANGGAL_PEMESANAN']) ? trim((string)$o['TANGGAL_PEMESANAN']) : '';
				$tanggal_indo = $tgl_indo($tanggal_raw);

				$jam_mulai_disp   = $time_hm(isset($o['JAM_PEMESANAN']) ? $o['JAM_PEMESANAN'] : '');
				$jam_selesai_disp = $time_hm(isset($o['JAM_SELESAI']) ? $o['JAM_SELESAI'] : '');

				if ($jam_selesai_disp === '00:00') $jam_selesai_disp = '';

				// ubah jadi dot + WIB
				$mulai = $jam_dot($jam_mulai_disp);
				$selesai = $jam_dot($jam_selesai_disp);

				$range = $selesai ? ($mulai . ' - ' . $selesai . ' wib') : ($mulai . ' wib');

				// ✅ label yang disimpan ke TITLE (sekalian jadi kunci cek sudah diulas)
				$title_key = $nama_gedung . ' - ' . $tanggal_raw . ' (' . $jam_mulai_disp . ($jam_selesai_disp ? ' - ' . $jam_selesai_disp : '') . ')';

				// ✅ label display 3 baris untuk dipakai di view (preview)
				$label_3baris = $nama_gedung . "\n" . $tanggal_indo . "\n" . $range;

				if ($has_id_pemesanan_col) {
					if (isset($reviewed_ids_map[$id])) continue;
				} else {
					if (isset($reviewed_titles_map[$title_key])) continue;
				}

				$reservasi_list[] = array(
					'ID_PEMESANAN' => $id,
					// simpan raw key untuk logika existing (tetap aman)
					'title_key'    => $title_key,
					// label yang dipakai view: sudah 3 baris + tanggal indo
					'label'        => $label_3baris,
					// kalau butuh juga versi raw:
					'label_raw'    => $title_key,
				);
			}
		}

		$data['reviews'] = $reviews;
		$data['reservasi_list'] = $reservasi_list;

		$this->load->view('home/ulasan', $data);
	}


	public function submit_ulasan()
	{
		$this->load->model('Ulasan/Ulasan_Model', 'Ulasan_model');
		$this->load->model('pemesanan/Pemesanan_Model', 'Pemesanan_model');

		$username = $this->session->userdata('username');
		if (empty($username)) {
			$this->session->set_flashdata('error', 'Silakan login dulu untuk mengirim ulasan.');
			redirect('home/ulasan');
			return;
		}

		$rating       = (int)$this->input->post('rating');
		$id_pemesanan = (int)$this->input->post('id_pemesanan');
		$comment      = trim($this->input->post('comment'));

		if ($rating < 1 || $rating > 5 || $comment === '' || $id_pemesanan <= 0) {
			$this->session->set_flashdata('error', 'Pemesanan, rating, dan komentar wajib diisi.');
			redirect('home/ulasan');
			return;
		}

		// validasi: pemesanan harus milik user + STATUS=3 (submitted)
		$pesanan = $this->Pemesanan_model->get_one_submitted_by_id_and_username($id_pemesanan, $username);
		if (empty($pesanan)) {
			$this->session->set_flashdata('error', 'Pemesanan tidak valid / bukan milik kamu / belum status submitted.');
			redirect('home/ulasan');
			return;
		}

		// helper format jam HH:MM
		$time_hm = function ($t) {
			$t = trim((string)$t);
			if ($t === '') return '';
			return (strlen($t) >= 5) ? substr($t, 0, 5) : $t;
		};

		$nama_gedung = isset($pesanan['NAMA_GEDUNG']) ? trim((string)$pesanan['NAMA_GEDUNG']) : '';
		$tanggal     = isset($pesanan['TANGGAL_PEMESANAN']) ? trim((string)$pesanan['TANGGAL_PEMESANAN']) : '';

		// kalau nama gedung / tanggal kosong, stop biar tidak tersimpan "- - (...)"
		if ($nama_gedung === '' || $tanggal === '') {
			$this->session->set_flashdata('error', 'Data gedung/tanggal pemesanan tidak ditemukan. Coba pilih pemesanan lain.');
			redirect('home/ulasan');
			return;
		}

		$jam_mulai_disp   = $time_hm(isset($pesanan['JAM_PEMESANAN']) ? $pesanan['JAM_PEMESANAN'] : '');
		$jam_selesai_disp = $time_hm(isset($pesanan['JAM_SELESAI']) ? $pesanan['JAM_SELESAI'] : '');

		if ($jam_selesai_disp === '00:00') $jam_selesai_disp = '';

		$range = $jam_selesai_disp ? ($jam_mulai_disp . ' - ' . $jam_selesai_disp) : $jam_mulai_disp;

		// ini yang disimpan ke TITLE
		$title_key = $nama_gedung . ' - ' . $tanggal . ' (' . $range . ')';

		// blokir dobel ulasan untuk pemesanan yang sama
		if ($this->Ulasan_model->exists_for_pemesanan($id_pemesanan, $username, $title_key)) {
			$this->session->set_flashdata('error', 'Pemesanan ini sudah pernah kamu ulas.');
			redirect('home/ulasan');
			return;
		}

		$insert = array(
			'USERNAME'   => $username,
			'RATING'     => $rating,
			'TITLE'      => $title_key,
			'COMMENT'    => $comment,
			'STATUS'     => 'APPROVED',
			'CREATED_AT' => date('Y-m-d H:i:s')
		);

		// optional: jika kolom ID_PEMESANAN sudah ada di tabel ulasan, ikut simpan
		if ($this->db->field_exists('ID_PEMESANAN', 'ulasan')) {
			$insert['ID_PEMESANAN'] = $id_pemesanan;
		}

		$ok = $this->Ulasan_model->insert_ulasan($insert);

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
	public function notif_poll_v2()
	{
		header('Content-Type: application/json; charset=utf-8');

		// pakai username (bukan id_user)
		$username = (string) $this->session->userdata('username');
		if ($username === '') {
			echo json_encode(array('ok' => false, 'message' => 'Unauthorized'));
			return;
		}

		$since_p = (int) $this->input->get('since_p');
		$since_t = (int) $this->input->get('since_t');

		$this->load->library('notification_service');

		// types sesuai DB kamu
		$typesP = array('USER_PEMESANAN');
		$typesT = array('USER_TRANSAKSI');

		try {
			// ambil list unread (lebih dari 5 biar ga miss)
			$rawP = $this->notification_service->get_unread($username, $typesP, 30);
			$rawT = $this->notification_service->get_unread($username, $typesT, 30);

			// filter berdasarkan since_id
			$itemsP = array();
			if (is_array($rawP)) {
				foreach ($rawP as $n) {
					$id = isset($n['id']) ? (int)$n['id'] : 0;
					if ($id > $since_p) $itemsP[] = $n;
				}
			}

			$itemsT = array();
			if (is_array($rawT)) {
				foreach ($rawT as $n) {
					$id = isset($n['id']) ? (int)$n['id'] : 0;
					if ($id > $since_t) $itemsT[] = $n;
				}
			}

			$countP = (int) $this->notification_service->count_unread($username, $typesP);
			$countT = (int) $this->notification_service->count_unread($username, $typesT);

			echo json_encode(array(
				'ok' => true,
				'counts' => array(
					'pemesanan' => $countP,
					'transaksi' => $countT
				),
				'items' => array(
					'pemesanan' => array_slice($itemsP, 0, 10),
					'transaksi' => array_slice($itemsT, 0, 10)
				)
			));
		} catch (Exception $e) {
			log_message('error', 'notif_poll_v2 USER error: ' . $e->getMessage());
			echo json_encode(array('ok' => false, 'message' => 'Server error'));
		}
	}




	/**
	 * OPTIONAL: notif_poll lama (flag) boleh tetap ada,
	 * tapi jangan dipakai lagi untuk device notification supaya tidak looping.
	 */
	public function notif_poll()
	{
		$username = $this->session->userdata('username');
		if (!$username) {
			$this->output
				->set_status_header(401)
				->set_content_type('application/json')
				->set_output(json_encode([
					'ok' => false,
					'flag' => 0,
					'trx_flag' => 0,
					'pemesanan_ids' => [],
					'trx_ids' => []
				]));
			return;
		}

		$this->load->model('gedung/gedung_model');

		$flag     = (int)$this->gedung_model->get_pemesanan_flag($username);
		$trx_flag = (int)$this->gedung_model->get_transaksi_flag($username);

		$p_ids  = $this->gedung_model->get_pemesanan_unread_ids($username, 5);
		$t_ids  = $this->gedung_model->get_transaksi_unread_ids($username, 5);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'ok' => true,
				'flag' => $flag,
				'trx_flag' => $trx_flag,
				'pemesanan_ids' => $p_ids,
				'trx_ids' => $t_ids
			]));
	}

	public function notif_status_by_user()
	{
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_status_by_user($username);
		$this->load->view('home/notif_status_by_user', $data);
	}

	public function edit_foto()
	{
		$this->load->model('user/user_model');
		$username = (string)$this->session->userdata('username');
		if ($username === '') {
			redirect('login');
			return;
		}

		// kalau POST berarti simpan hasil crop (base64)
		if ($this->input->method(TRUE) === 'POST') {
			$imgData = $this->input->post('cropped_image');
			if (empty($imgData)) {
				$this->session->set_flashdata('error', 'Gambar belum dipilih.');
				redirect('edit_foto');
				return;
			}

			// pastikan folder ada
			$dir = FCPATH . 'assets/user-profile/';
			if (!is_dir($dir)) @mkdir($dir, 0775, true);

			// ambil foto lama (buat delete)
			$old = $this->db->select('FOTO_PROFIL')->from('user')->where('USERNAME', $username)->get()->row();
			$oldPath = ($old && !empty($old->FOTO_PROFIL)) ? $old->FOTO_PROFIL : null;

			// decode base64
			if (strpos($imgData, 'base64,') !== false) {
				$imgData = substr($imgData, strpos($imgData, 'base64,') + 7);
			}
			$bin = base64_decode($imgData);
			if ($bin === false) {
				$this->session->set_flashdata('error', 'Format gambar tidak valid.');
				redirect('edit_foto');
				return;
			}

			// simpan file dengan nama UNIK (biar gak kena cache)
			$filename = 'avatar_' . $username . '_' . date('Ymd_His') . '_' . substr(md5(mt_rand()), 0, 6) . '.jpg';
			$full = $dir . $filename;
			if (file_put_contents($full, $bin) === false) {
				$this->session->set_flashdata('error', 'Gagal menyimpan file. Cek permission folder assets/user-profile.');
				redirect('edit_foto');
				return;
			}

			$relative = 'assets/user-profile/' . $filename;

			// update DB
			$this->db->where('USERNAME', $username)->update('user', ['FOTO_PROFIL' => $relative]);

			// update session biar navbar langsung berubah
			$this->session->set_userdata('foto_profil', $relative);

			// hapus file lama kalau ada dan memang file lokal
			if ($oldPath && strpos($oldPath, 'assets/user-profile/') === 0) {
				$oldFull = FCPATH . $oldPath;
				if (is_file($oldFull)) @unlink($oldFull);
			}

			$this->session->set_flashdata('success', 'Foto profil berhasil diperbarui.');
			redirect('edit_data'); // atau redirect('home');
			return;
		}

		// GET: tampilkan halaman edit foto
		$row = $this->db->select('FOTO_PROFIL')->from('user')->where('USERNAME', $username)->get()->row();
		$data['foto_profil'] = ($row && !empty($row->FOTO_PROFIL)) ? $row->FOTO_PROFIL : 'assets/default-avatar.png';
		$this->load->view('home/Edit_fotoprofil', $data);
	}

	public function trx_mark_read($id_pembayaran)
	{
		$username = $this->session->userdata('username');
		if (!$username) {
			$this->output->set_status_header(401)
				->set_content_type('application/json')
				->set_output(json_encode(['ok' => false, 'message' => 'unauthorized']));
			return;
		}

		$id_pembayaran = (int)$id_pembayaran;
		if ($id_pembayaran <= 0) {
			$this->output->set_status_header(400)
				->set_content_type('application/json')
				->set_output(json_encode(['ok' => false, 'message' => 'invalid id']));
			return;
		}

		$this->load->model('gedung/gedung_model');
		$this->gedung_model->mark_trx_read($id_pembayaran, $username);

		$trx_flag = (int)$this->gedung_model->get_transaksi_flag($username);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(['ok' => true, 'trx_flag' => $trx_flag]));
	}
	public function trx_mark_all_read()
	{
		$username = $this->session->userdata('username');
		if (!$username) {
			$this->output->set_status_header(401)
				->set_content_type('application/json')
				->set_output(json_encode(['ok' => false]));
			return;
		}

		$this->load->model('gedung/gedung_model');
		$this->gedung_model->clear_transaksi_flag($username);

		$this->output->set_content_type('application/json')
			->set_output(json_encode(['ok' => true, 'trx_flag' => 0]));
	}

	public function how_to_order()
	{
		$username = (string)$this->session->userdata('username');

		$data = array(
			'flag' => 0,
			'trx_flag' => 0
		);

		$this->load->model('gedung/gedung_model');
		$data['flag']     = (int)$this->gedung_model->get_pemesanan_flag($username);
		$data['trx_flag'] = (int)$this->gedung_model->get_transaksi_flag($username);

		$this->load->view('home/how_to_order', $data);
	}

	public function jadwal_by_date($id_gedung)
	{
		$this->output->set_content_type('application/json');
		$this->db->db_debug = false;

		$id_gedung = (int)$id_gedung;
		$date = $this->input->get('date', true); // YYYY-MM-DD

		// validasi
		if (!$id_gedung || empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
			return $this->output
				->set_status_header(400)
				->set_output(json_encode(array(
					'ok' => false,
					'message' => 'Parameter tidak valid'
				)));
		}

		$sql = "
        SELECT
            ps.ID_PEMESANAN,
            ps.TANGGAL_PEMESANAN AS TANGGAL_FINAL_PEMESANAN,
            DATE(p.CONFIRMED_AT) AS TANGGAL_APPROVAL,
            g.NAMA_GEDUNG,
            IFNULL(pd.DESKRIPSI_ACARA, '-') AS DESKRIPSI_ACARA,
            1 AS FINAL_STATUS,
            ps.USERNAME,
            u.NAMA_LENGKAP,
            TIME_FORMAT(ps.JAM_PEMESANAN, '%H:%i') AS JAM_MULAI,
            TIME_FORMAT(ps.JAM_SELESAI,  '%H:%i') AS JAM_SELESAI,
            ps.TIPE_JAM AS TIPE_JAM
        FROM PEMBAYARAN p
        JOIN PEMESANAN ps ON ps.ID_PEMESANAN = p.ID_PEMESANAN_RAW
        LEFT JOIN USER u ON u.USERNAME = ps.USERNAME
        LEFT JOIN GEDUNG g ON g.ID_GEDUNG = ps.ID_GEDUNG
        LEFT JOIN PEMESANAN_DETAILS pd ON pd.ID_PEMESANAN = ps.ID_PEMESANAN
        WHERE p.STATUS_VERIF = 'CONFIRMED'
          AND ps.ID_GEDUNG = ?
          AND ps.TANGGAL_PEMESANAN = ?
        ORDER BY ps.JAM_PEMESANAN ASC, ps.ID_PEMESANAN ASC
    ";

		$q = $this->db->query($sql, array($id_gedung, $date));

		if (!$q) {
			$dbError = $this->db->error();
			$msg  = isset($dbError['message']) ? $dbError['message'] : 'unknown';
			$code = isset($dbError['code']) ? $dbError['code'] : 0;

			return $this->output
				->set_status_header(500)
				->set_output(json_encode(array(
					'ok' => false,
					'message' => 'Database error: ' . $msg,
					'code' => $code
				)));
		}

		$rows = $q->result_array();

		return $this->output
			->set_status_header(200)
			->set_output(json_encode(array(
				'ok' => true,
				'date' => $date,
				'id_gedung' => $id_gedung,
				'data' => $rows
			)));
	}
}
