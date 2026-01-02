<?php

/**
* 
*/
class Admin_Controls extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$session_id = $this->session->userdata('username');
		if(empty($session_id)) {
			redirect(site_url('admin'));
		}
	}

	function index() {
		$this->load->view('admin/home');
	}

	function tambah_gedung() {
		$this->load->helper('form');
		$this->load->helper('url');
		$submit = $this->input->post('submit');
		$this->load->model('gedung/gedung_model');
		$path = "./assets/images/gedung/";
		$img_name = "listrik_".date('dmY_his');
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'jpg|png';
		$config['max_size'] = '500';
		$config['max_width'] = '500';
		$config['max_height'] = '500';
		$this->load->library('upload', $config);
		$img_name = $this->upload->data();

		if(!empty($submit)) {
			$data = array(
			'NAMA_GEDUNG' => $this->input->post('nama_gedung'), 
			'KAPASITAS' => $this->input->post('kapasitas_gedung'),
			'ALAMAT' => $this->input->post('alamat_gedung'),
			'DESKRIPSI_GEDUNG' => $this->input->post('deskripsi_gedung'),
			'HARGA_SEWA' => $this->input->post('harga_sewa')
			);
			//$success = array();
			//$upload_files = array(
			//	'img_gedung1' => $_FILES['img_gedung1'],
			//	'img_gedung2' => $_FILES['img_gedung2'],
			//	'img_gedung3' => $_FILES['img_gedung3']
			//	);

			foreach($_FILES as $key => $value) {

				if(!empty($value['name'])) {

					if(!$this->upload->do_upload($key)) {
						echo '<script type="text/javascript"> alert("Terjadi Kesalahan, Periksa Resolusi dan Ukuran File Upload Anda");
						</script>';
					} else {
						$files = $this->upload->data();
						$base_url = base_url();
					$img_path = $base_url. "assets/images/gedung/";
					$this->gedung_model->insert_gedung($data);
					$this->gedung_model->get_last_id_gedung();
					$id_gedung = $this->gedung_model->get_last_id_gedung();
					$img_data = array(
						'ID_GEDUNG' => $id_gedung->ID_GEDUNG,
						'NAMA_GEDUNG' => $this->input->post('nama_gedung'),
						'PATH' => $img_path,
						'IMG_NAME' => $files['file_name']
						);
					$this->gedung_model->insert_gedung_img($img_data);
					redirect('admin/gedung');
					}
					//else {
					//code..
					//}
				}
			}
			//var_dump($errors);
			//var_dump($success);
		}
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$this->load->view('admin/tambah_gedung', $data);
	}

	function rekap_aktivitas() {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$this->load->view('admin/rekap_aktivitas', $data);
	}

	function rekap_aktivitas_det($tanggal_awal, $tanggal_akhir) {
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

	function rekap_pembayaran() {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$this->load->view('admin/rekap_pembayaran', $data);
	}

	function rekap_pembayaran_det($tanggal_awal, $tanggal_akhir) {
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

	function rekap_transaksi() {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$this->load->view('admin/rekap_transaksi', $data);
	}

	function rekap_transaksi_det($tanggal_awal, $tanggal_akhir) {
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

	function transaksi_export_pdf($start_date, $end_date) {
		$this->load->model('gedung/gedung_model');
		$this->load->helper('warsito_pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->laporan_pembayaran_periodic($start_date, $end_date);
		$object = $this->load->view('admin/pdf_report_transaksi', $data, true);
		$filename = "Report Transaksi.pdf";
		generate_pdf($object, $filename, true);
	}
	function detail_pemesanan($id_pemesanan) {
		$this->load->model('gedung/gedung_model');
		$hasil['hasil'] = $this->gedung_model->get_detail_pesanan($id_pemesanan);
		$hasil['result'] = $this->gedung_model->get_pending_transaction();
		$this->load->view('admin/detail_pemesanan', $hasil);
	}

	function pembayaran() {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['pembayaran'] = $this->gedung_model->get_all_pembayaran();
		$this->load->view('admin/pembayaran', $data);
	}

	function delete_jadwal($id_gedung) {
		$this->load->model('gedung/gedung_model');
		$data = array (
			'FINAL_STATUS' => 2
		);
		$this->gedung_model->delete_jadwal($id_gedung, $data);
		redirect('admin/dashboard');
	}

	function pemesanan2() {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['pemesanan'] = $this->gedung_model->get_all_pemesanan();
		$this->load->view('admin/pemesanan_2', $data);
	}

	function read_transaction($id_pembayaran) {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['details'] = $this->gedung_model->get_detail_pembayaran($id_pembayaran);
		//$this->gedung_model->set_finish_transaction($id_pembayaran);
		$this->load->view('admin/detail_pembayaran', $data);
	}

	function transaksi() {
		$this->load->model('gedung/gedung_model');
		$data['pemesanan'] = $this->gedung_model->get_all_pending_transaction();
		$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$this->load->view('admin/pemesanan', $data);
	}

	function detail_transaksi($id_pemesanan) {
    $this->load->helper('date');
    $tanggal_approval = '%Y-%m-%d';
    $this->load->model('gedung/gedung_model');

    $temp_id = (int)substr($id_pemesanan, 7);

    // === PROSES POST DULU ===
    if ($this->input->method(TRUE) === 'POST') {
        $status  = (int)$this->input->post('status-proposal');
        $remarks = $this->input->post('remarks', TRUE);

        if ($status === 1) {
            // TERIMA PROPOSAL -> PROPOSAL APPROVE (1)
            $this->gedung_model->update_transaksi($temp_id, 1, '');
            redirect('admin/transaksi');
            return;
        }

        if ($status === 4) {
            // TOLAK PROPOSAL -> REJECTED (4)
            $this->gedung_model->update_transaksi($temp_id, 4, $remarks);
            redirect('admin/transaksi');
            return;
        }

        show_error('Status tidak valid.');
        return;
    }

    // === BARU LOAD DATA UNTUK VIEW ===
    $data['details'] = $this->gedung_model->get_proposal_by_id($temp_id);
    $data['hasil']   = $this->gedung_model->get_detail_pesanan($id_pemesanan);
    $data['result']  = $this->gedung_model->get_pending_transaction();

    $this->load->view('admin/detail_transaksi', $data);
}


	function send_mail($to_email, $pesan) {
		$from_email = "Admin Pembayaran";
		$this->load->library('email');
		$this->email->from($from_email, 'admin@reservasigedung.com');
		$this->email->to($to_email);
		$this->email->subject('Deadline Pembayaran Reservasi Gedung');
		$this->email->set_mailtype('html');
		$this->email->message($pesan);
		$this->email->send();
		if(!$this->email->send()) {
			$this->email->print_debugger();
		}

	}

	function download_proposal($id_pemesanan) {
		$this->load->helper('download');
		$this->load->model('gedung/gedung_model');
		$temp_id = substr($id_pemesanan, 7);
		$data = $this->gedung_model->get_proposal_by_id($temp_id);
		$path = file_get_contents($data->PATH.$data->FILE_NAME);
		$file_name = $data->FILE_NAME;
		force_download($file_name, $path);
	}

	function update_transaksi($id_pemesanan) {
		$this->load->model('gedung/gedung_model');
		$this->load->helper('form');
		$temp_id = substr($id_pemesanan, 7);
		
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

	function edit_gedung($id_gedung) {
		$this->load->model('gedung/gedung_model');
		$details['res'] = $this->gedung_model->get_pending_transaction();
		$details['result'] = $this->gedung_model->gedung_details($id_gedung);
		$this->load->view('admin/edit_gedung', $details);
		$simpan = $this->input->post('submit');
		if(!empty($simpan)) {
			$data = array(
				'NAMA_GEDUNG' => $this->input->post('nama_gedung'),
				'KAPASITAS' => $this->input->post('kapasitas_gedung'),
				'HARGA_SEWA' => $this->input->post('harga_sewa')
				);
			$this->gedung_model->update_gedung($id_gedung, $data);
			redirect('admin/gedung');
		}
	}

	function kegiatan_export_pdf($start_date, $end_date) {
		$this->load->model('gedung/gedung_model');
		$this->load->helper('warsito_pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->jadwal_gedung($start_date, $end_date);
		$object = $this->load->view('admin/pdf_report_kegiatan', $data, true);
		$filename = "Report Kegiatan.pdf";
		generate_pdf($object, $filename, true);
	}

	function perawatan_export_pdf($start_date, $end_date) {
		$this->load->model('gedung/gedung_model');
		$this->load->helper('warsito_pdf_helper');
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		$data['report'] = $this->gedung_model->laporan_perawatan_periodic($start_date, $end_date);
		$object = $this->load->view('admin/pdf_report_perawatan', $data, true);
		$filename = "Report Perawatan.pdf";
		generate_pdf($object, $filename, true);
	}

	function dashboard() {
		$session_id = $this->session->userdata('username');
		if(empty($session_id)) {
			redirect(site_url('admin'));
		} else {
			$this->load->model('gedung/gedung_model');
			$data['front_data'] = $this->gedung_model->fixed_date();
			$data['result'] = $this->gedung_model->get_pending_transaction();
			$data['get_transaction'] = $this->gedung_model->get_unread_transaction();
			$this->load->view('admin/home', $data);
		}
	}

	function list_user() {
		$this->load->model('user/user_model');
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['res'] = $this->user_model->get_all_users();
		$this->load->view('admin/list_user', $data);
	}

	function list_gedung() {
		$this->load->model('gedung/gedung_model');
		$data['result'] = $this->gedung_model->get_pending_transaction();
		$data['res'] = $this->gedung_model->get_gedung();
		$this->load->view('admin/list_gedung', $data);
	}

	function list_catering() {
		$this->load->model('catering/catering_model');
		$this->load->model('gedung/gedung_model');
		$data['res'] = $this->gedung_model->get_pending_transaction();
		$data['result'] = $this->catering_model->get_all();
		$this->load->view('admin/list_catering', $data);
	}

public function verify_pembayaran($id_pembayaran, $action)
{
    $id_pembayaran = (int)$id_pembayaran;
    $action = strtolower($action);

    if ($id_pembayaran <= 0) {
        show_error('ID pembayaran tidak valid');
        return;
    }

    // Ambil data pembayaran
    $p = $this->db->get_where('pembayaran', [
        'ID_PEMBAYARAN' => $id_pembayaran
    ])->row_array();

    if (!$p) {
        show_error('Data pembayaran tidak ditemukan');
        return;
    }

    $id_pemesanan = (int)$p['ID_PEMESANAN_RAW'];
    $catatan = trim($this->input->post('catatan_admin', TRUE));

    $this->db->trans_begin();

    if ($action === 'confirm') {

        // CONFIRM → CATATAN TETAP DISIMPAN
        $this->db->where('ID_PEMBAYARAN', $id_pembayaran);
        $this->db->update('pembayaran', [
            'STATUS_VERIF'  => 'CONFIRMED',
            'CATATAN_ADMIN' => $catatan,
            'CONFIRMED_AT'  => date('Y-m-d H:i:s')
        ]);

        $this->db->where('ID_PEMESANAN', $id_pemesanan);
        $this->db->update('pemesanan', ['STATUS' => 3]);

    } elseif ($action === 'reject') {

        if ($catatan === '') {
            $this->db->trans_rollback();
            show_error('Catatan admin wajib diisi saat menolak pembayaran.');
            return;
        }

        $this->db->where('ID_PEMBAYARAN', $id_pembayaran);
        $this->db->update('pembayaran', [
            'STATUS_VERIF'  => 'REJECTED',
            'CATATAN_ADMIN' => $catatan,
            'CONFIRMED_AT'  => null
        ]);

        $this->db->where('ID_PEMESANAN', $id_pemesanan);
        $this->db->update('pemesanan', ['STATUS' => 4]);

    } else {
        $this->db->trans_rollback();
        show_error('Aksi tidak dikenali');
        return;
    }

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        show_error('Gagal menyimpan data');
        return;
    }

    $this->db->trans_commit();
    redirect('admin/pembayaran');
}



}