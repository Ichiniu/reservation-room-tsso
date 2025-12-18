<?php
/**
* 
*/
class Home extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$session_id = $this->session->userdata('username');
		if(empty($session_id)) {
			redirect(site_url().'/login');
		} 
	}
	
public function index() {
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

	public function cancel_order($id_pemesanan) {
		$this->load->model('gedung/gedung_model');
		$temp_id = "PMSN000".$id_pemesanan;
		$tanggal_pesan = $this->gedung_model->get_detail_pesanan($temp_id)->TANGGAL_PEMESANAN;
		$min_refund = date('Y-m-d', time());
		$perbedaan = date_diff(new DateTime($tanggal_pesan), new DateTime($min_refund));
		$c_perbedaan = $perbedaan->format('%d');
		if($c_perbedaan > 7) {
			$data = array ('STATUS' => 3);
			$this->gedung_model->cancel_order($id_pemesanan, $data);
		} else {
			$data = array('STATUS' => 4);
			$this->gedung_model->cancel_order($id_pemesanan, $data);
		}
		$jadwal = array('FINAL_STATUS' => 2);
		$this->gedung_model->delete_jadwal($id_pemesanan, $jadwal);
		redirect('home/pemesanan');
	}

	public function jadwal_gedung() {
		$username = $this->session->userdata('username');
		$this->load->helper('date');
		$akhir_bulan = strtotime('last day of this month', time());
		$second_date = date('Y-m-d', $akhir_bulan);
		$first_date = date('Y-m-d', time());
		$this->load->model('gedung/gedung_model');
		$data['jadwal'] = $this->gedung_model->jadwal_gedung($first_date, $second_date);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('gedung/jadwal_gedung', $data);
		$data['jadwal'] = $this->gedung_model->jadwal_gedung_upcoming();

	}

	public function jadwal_per_periode($start_date, $end_date) {
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

	public function view_catering() {
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_menu_catering();
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('gedung/view_catering', $data);
	}

	function check_date($date, $id_gedung) {
		$this->load->model('gedung/gedung_model');
		$data = $this->gedung_model->check_date($date, $id_gedung);
		return $data;
	}

	public function order_gedung($id_gedung) {
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

	public function pemesanan() {
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_pemesanan($username);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$data['no_data'] = "Data Kosong";
		$data['rows'] = $this->gedung_model->count_pemesanan($username);
		$this->load->view('home/pemesanan', $data);
	}

	public function pembayaran() {
		$this->load->model('gedung/gedung_model');
		$username = $this->session->userdata('username');
		$data['res'] = $this->gedung_model->user_detail_pembayaran($username);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/pembayaran', $data);
	}

	public function detail_pemesanan($id_pemesanan) {
		$username = $this->session->userdata('username');
		$temp_id = substr($id_pemesanan,7);
		$this->load->model('gedung/gedung_model');
		$this->gedung_model->update_user_flag($temp_id);
		$data['result'] = $this->gedung_model->get_detail_pesanan($id_pemesanan);
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/detail_pemesanan', $data);
	}

	public function upload_proposal() {
		$this->load->helper('form');
		$this->load->model('gedung/gedung_model');
		$username = $this->session->userdata('username');
		$file_name = $username."_".date('dmY_his');
		$upload_path = "./assets/user-proposal/";
		$image_path = base_url(). "assets/user-proposal/";
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'pdf|doc|docx';
		$config['max_size'] = '800';
		$config['file_name'] = $file_name;

		$this->load->library('upload');
		$this->upload->initialize($config);
		$row = $this->gedung_model->get_last_id_pesanan();
		
		if(!$this->upload->do_upload('proposal')) {
			echo $this->upload->display_errors();
		} else {
			$upload_data = $this->upload->data();
			$img_name = $upload_data['file_name'];
			$data = array(
				'ID_PEMESANAN' => $row->ID_PEMESANAN,
				'PATH' => $image_path,
				'FILE_NAME' => $img_name,
				'DESKRIPSI_ACARA' => $this->input->post('deskripsi-acara')
				);
			$result = array('upload_data', $this->upload->data());
			$this->gedung_model->upload_proposal($data);
			$this->load->view('home/success_page');
		}
	}

	public function order() {
    $this->load->model('gedung/gedung_model');

    $tanggal_pesan = $this->input->post('tgl_pesan', TRUE);

    // NEW: tipe jam dari form
    $tipe_jam = $this->input->post('tipe_jam', TRUE);
    if (empty($tipe_jam)) {
        $tipe_jam = 'CUSTOM';
    }

    // Aturan jam paket (UBAH sesuai kebutuhan)
    $paket = [
		'HALF_DAY_PAGI'  => ['08:00', '12:00'],
		'HALF_DAY_SIANG' => ['13:00', '16:00'],
		'FULL_DAY'       => ['08:00', '17:00'],
		];


    // Tentukan jam mulai & jam selesai final (server-side)
    if ($tipe_jam === 'CUSTOM') {
        $jam_mulai   = $this->input->post('jam_pesan', TRUE);      // jam mulai (nama lama)
        $jam_selesai = $this->input->post('jam_selesai', TRUE);    // NEW

        if (empty($jam_mulai) || empty($jam_selesai)) {
            $this->session->set_flashdata('error', 'Jam mulai dan jam selesai wajib diisi.');
            redirect('home/order_gedung/'.$this->uri->segment(4));
            return;
        }
    } elseif (isset($paket[$tipe_jam])) {
        $jam_mulai   = $paket[$tipe_jam][0];
        $jam_selesai = $paket[$tipe_jam][1];
    } else {
        show_error('Tipe jam tidak valid');
        return;
    }

    // Validasi jam mulai < jam selesai
    if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
        $this->session->set_flashdata('error', 'Jam mulai harus lebih kecil dari jam selesai.');
        redirect('home/order_gedung/'.$this->uri->segment(4));
        return;
    }

    $min_pesan = date('Y-m-d', strtotime("+10 day", time()));
    $id_gedung = $this->uri->segment(4);

    // WARNING: ini masih cek bentrok berdasarkan tanggal saja (lihat catatan bawah)
    $exist = $this->check_date($tanggal_pesan, $id_gedung);

    $username = $this->session->userdata('username');

    $data = array(
        'USERNAME' => $username,
        'TANGGAL_PEMESANAN' => $tanggal_pesan,

        // JAM_PEMESANAN sekarang dipakai sebagai JAM MULAI
        'JAM_PEMESANAN' => $jam_mulai,

        // NEW:
        'JAM_SELESAI' => $jam_selesai,
        'TIPE_JAM'    => $tipe_jam,

        'EMAIL' => $this->input->post('email', TRUE),
        'ID_CATERING' => $this->input->post('catering', TRUE),
        'ID_GEDUNG' => $id_gedung,
        'JUMLAH_CATERING' => $this->input->post('jumlah-porsi', TRUE),
        'STATUS' => 0
    );

    if ($tanggal_pesan < $min_pesan) {
        $data['tgl_pesan'] = $tanggal_pesan;
        $data['min_pesan'] = $min_pesan;
        $this->load->view('errors/pemesanan_alert', $data);
    } else {
        if ($exist > 0) {
            $this->load->view('gedung/gedung_exist.html');
        } else {
            $this->load->view('gedung/gedung_not_exist.html');

            // INSERT ke DB (sekarang akan menyimpan TIPE_JAM & JAM_SELESAI juga)
            $this->gedung_model->insert_pemesanan($data);

            $hasil['res'] = $this->gedung_model->get_last_order();
            $hasil['flag'] = $this->gedung_model->get_pemesanan_flag($username);
            $this->load->view('home/confirm_order', $hasil);
        }
    }
}

	public function edit_data($user) {
		$this->load->model('user/user_model');
		$this->load->view('/home/edit_data');
		$data = array(
			'password' => $password = $this->input->post('password'),
			'email' =>  $email = $this->input->post('email')
			);
		if(isset($_POST['password'])) {
			$this->user_model->update_data($user, $data);
			echo "<script> alert('Data Diperbarui'); </script>";
			redirect('/home/home/dashboard/'.$user.'/', 'refresh');
		}
	}

	public function sort_by_name() {
		$username = $this->session->userdata('username');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->sort_by_name();
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('/home/home_screen', $data);
	}

	public function sort_by_capacity() {
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->sort_by_capacity();
		$username = $this->session->userdata('username');
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('/home/home_screen', $data);
	}

	public function logout() {
		$this->session->sess_destroy();
		redirect(base_url());
	}

	public function gedung_details($id_gedung) {	
		$this->load->model('gedung/gedung_model');
		$id_gedung = $this->uri->segment(3);
		$gallery['gallery'] = $this->gedung_model->get_gedung_img($id_gedung);
		$details['result'] = $this->gedung_model->gedung_details($id_gedung);
		$username = $this->session->userdata('username');
		$flag['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$data = array_merge($gallery, $details, $flag);
		$this->load->view('gedung/gedung_details', $data);
	}

	public function search_gedung($nama_gedung) {
		$this->load->helper('form');
		$nama_gedung = $this->input->get('search_gedung');
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->search_gedung($nama_gedung);
		$username = $this->session->userdata('username');
		$data['flag'] = $this->gedung_model->get_pemesanan_flag($username);
		$this->load->view('home/search_gedung', $data);
	}
}