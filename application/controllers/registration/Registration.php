<?php

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_Email $email
 * @property User_model $user_model
 */
class Registration extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('/registration/daftar');
	}

	public function add_user()
	{
		$this->load->model('user/user_model');

		// ambil input dasar
		$username     = trim((string)$this->input->post('username', true));
		$nama_lengkap = trim((string)$this->input->post('nama_lengkap', true));
		$password     = $this->input->post('password', true);
		$email        = trim((string)$this->input->post('email', true));
		$alamat       = trim((string)$this->input->post('alamat', true));
		$no_telepon   = trim((string)$this->input->post('no_telepon', true));
		$dob          = $this->input->post('dob', true);

		// perusahaan logic
		$perusahaan      = $this->input->post('perusahaan', true); // INTERNAL / EKSTERNAL
		$nama_perusahaan = null;
		$departemen      = null;

		if ($perusahaan === 'INTERNAL') {
			$departemen = trim((string)$this->input->post('departemen', true));

			if ($departemen === '') {
				$this->session->set_flashdata('flash_msg', 'Departemen wajib dipilih untuk INTERNAL.');
				$this->session->set_flashdata('flash_type', 'error');
				redirect('/registration');
				return;
			}

			$nama_perusahaan = 'PT Tiga Serangkai Pustaka Mandiri';
		} elseif ($perusahaan === 'EKSTERNAL') {
			$nama_perusahaan = trim((string)$this->input->post('nama_perusahaan', true));

			if ($nama_perusahaan === '') {
				$this->session->set_flashdata('flash_msg', 'Nama perusahaan wajib diisi untuk EKSTERNAL.');
				$this->session->set_flashdata('flash_type', 'error');
				redirect('/registration');
				return;
			}

			$departemen = null;
		} else {
			$this->session->set_flashdata('flash_msg', 'Perusahaan wajib dipilih.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// data insert
		$data = [
			'USERNAME'        => $username,
			'NAMA_LENGKAP'    => $nama_lengkap,
			'perusahaan'      => $perusahaan,
			'nama_perusahaan' => $nama_perusahaan,
			'departemen'      => $departemen,
			'PASSWORD'        => password_hash((string)$password, PASSWORD_DEFAULT),
			'EMAIL'           => $email,
			'ALAMAT'          => $alamat,
			'NO_TELEPON'      => $no_telepon,
			'TANGGAL_LAHIR'   => $dob,
		];

		// ===== server-side validation =====
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->session->set_flashdata('flash_msg', 'Format email tidak valid.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}
		if (!preg_match('/@gmail(\.[a-z]{2,})?$/i', $email)) {
			$this->session->set_flashdata('flash_msg', 'Email harus menggunakan domain @gmail.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		if (!preg_match('/^\d{11,14}$/', $no_telepon)) {
			$this->session->set_flashdata('flash_msg', 'No telepon harus 11 sampai 13 digit angka (hanya angka diperbolehkan).');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}
		$data['NO_TELEPON'] = $no_telepon;

		if (!empty($dob)) {
			try {
				$birth = new DateTime($dob);
				$now   = new DateTime();
				$age   = $now->diff($birth)->y;
				if ($age < 18) {
					$this->session->set_flashdata('flash_msg', 'Usia minimal 18 tahun.');
					$this->session->set_flashdata('flash_type', 'error');
					redirect('/registration');
					return;
				}
			} catch (Exception $e) {
				$this->session->set_flashdata('flash_msg', 'Format tanggal lahir tidak valid.');
				$this->session->set_flashdata('flash_type', 'error');
				redirect('/registration');
				return;
			}
		} else {
			$this->session->set_flashdata('flash_msg', 'Tanggal lahir wajib diisi.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// cek username sudah ada (case-insensitive)
		$this->db->select('USERNAME');
		$this->db->where('LOWER(USERNAME) = ' . $this->db->escape(strtolower($data['USERNAME'])), null, false);
		$result = $this->db->get('user');

		if ($result->num_rows() > 0) {
			$this->session->set_flashdata('flash_msg', 'Username sudah digunakan. Silakan pilih username lain.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// cek nama_lengkap sudah ada (case-insensitive)
		$this->db->select('NAMA_LENGKAP');
		$this->db->where('LOWER(NAMA_LENGKAP) = ' . $this->db->escape(strtolower($data['NAMA_LENGKAP'])), null, false);
		$result_nama = $this->db->get('user');

		if ($result_nama->num_rows() > 0) {
			$this->session->set_flashdata('flash_msg', 'Nama lengkap sudah terdaftar. Silakan gunakan nama lain.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/registration');
			return;
		}

		// ===== EMAIL VERIFICATION: Generate token =====
		$token = bin2hex(random_bytes(32)); // 64-char hex token
		$expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

		$data['is_verified']        = 0;
		$data['verification_token'] = $token;
		$data['token_expires_at']   = $expires_at;

		// insert user
		$this->user_model->insert($data);

		// Kirim email verifikasi
		$this->_send_verification_email($email, $username, $nama_lengkap, $token);

		$this->session->set_flashdata('flash_msg', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
		$this->session->set_flashdata('flash_type', 'success');
		redirect('/login');
	}

	/**
	 * Endpoint verifikasi email - dipanggil saat user klik link di email
	 * URL: /registration/verify_email?token=xxxx
	 */
	public function verify_email()
	{
		$token = trim((string)$this->input->get('token', true));

		if (empty($token)) {
			$this->load->view('registration/verify_result', [
				'success' => false,
				'message' => 'Token verifikasi tidak valid.',
			]);
			return;
		}

		// Cari user dengan token ini
		$user = $this->db->get_where('user', [
			'verification_token' => $token,
		])->row();

		if (!$user) {
			$this->load->view('registration/verify_result', [
				'success' => false,
				'message' => 'Token verifikasi tidak ditemukan atau sudah digunakan.',
			]);
			return;
		}

		// Cek apakah sudah terverifikasi
		if ((int)$user->is_verified === 1) {
			$this->load->view('registration/verify_result', [
				'success' => true,
				'message' => 'Akun Anda sudah terverifikasi sebelumnya. Silakan login.',
			]);
			return;
		}

		// Cek expired
		if (!empty($user->token_expires_at) && strtotime($user->token_expires_at) < time()) {
			$this->load->view('registration/verify_result', [
				'success'      => false,
				'message'      => 'Token verifikasi sudah kedaluwarsa. Silakan kirim ulang email verifikasi.',
				'show_resend'  => true,
				'email'        => $user->EMAIL ?? '',
			]);
			return;
		}

		// VERIFIKASI berhasil!
		$this->db->where('USERNAME', $user->USERNAME);
		$this->db->update('user', [
			'is_verified'        => 1,
			'verification_token' => null,
			'token_expires_at'   => null,
		]);

		$this->load->view('registration/verify_result', [
			'success' => true,
			'message' => 'Email berhasil diverifikasi! Akun Anda sekarang aktif.',
		]);
	}

	/**
	 * Kirim ulang email verifikasi
	 * POST: /registration/resend_verification (email via POST)
	 */
	public function resend_verification()
	{
		$email = trim((string)$this->input->post('email', true));

		if (empty($email)) {
			$this->session->set_flashdata('flash_msg', 'Email wajib diisi.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/login');
			return;
		}

		$user = $this->db->get_where('user', ['EMAIL' => $email])->row();

		if (!$user) {
			$this->session->set_flashdata('flash_msg', 'Email tidak ditemukan.');
			$this->session->set_flashdata('flash_type', 'error');
			redirect('/login');
			return;
		}

		if ((int)$user->is_verified === 1) {
			$this->session->set_flashdata('flash_msg', 'Akun sudah terverifikasi. Silakan login.');
			$this->session->set_flashdata('flash_type', 'info');
			redirect('/login');
			return;
		}

		// Generate token baru
		$token = bin2hex(random_bytes(32));
		$expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

		$this->db->where('USERNAME', $user->USERNAME);
		$this->db->update('user', [
			'verification_token' => $token,
			'token_expires_at'   => $expires_at,
		]);

		$this->_send_verification_email($user->EMAIL, $user->USERNAME, $user->NAMA_LENGKAP, $token);

		$this->session->set_flashdata('flash_msg', 'Email verifikasi berhasil dikirim ulang! Silakan cek inbox Anda.');
		$this->session->set_flashdata('flash_type', 'success');
		redirect('/login');
	}

	/**
	 * Private: Kirim email verifikasi
	 */
	private function _send_verification_email($to_email, $username, $nama_lengkap, $token)
	{
		$this->load->config('notification', true);

		$smtp     = $this->config->item('smtp', 'notification');
		$from     = $this->config->item('mail_from', 'notification');
		$fromName = $this->config->item('mail_from_name', 'notification');

		$this->load->library('email');

		if (is_array($smtp) && !empty($smtp)) {
			if (!isset($smtp['mailtype'])) $smtp['mailtype'] = 'html';
			if (!isset($smtp['charset']))  $smtp['charset']  = 'utf-8';
			if (!isset($smtp['newline']))  $smtp['newline']  = "\r\n";
			if (!isset($smtp['crlf']))     $smtp['crlf']     = "\r\n";
			$this->email->initialize($smtp);
		}

		$verify_link = site_url('registration/verify_email?token=' . urlencode($token));

		$nama = htmlspecialchars((string)$nama_lengkap, ENT_QUOTES, 'UTF-8');
		$user = htmlspecialchars((string)$username, ENT_QUOTES, 'UTF-8');
		$link = htmlspecialchars($verify_link, ENT_QUOTES, 'UTF-8');

		$html = '<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
  <table width="100%" cellspacing="0" cellpadding="0" style="padding:24px 0;">
    <tr><td align="center">
      <table width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">
        <tr>
          <td style="padding:24px 22px;border-bottom:1px solid #e5e7eb;text-align:center;">
            <div style="font-size:12px;letter-spacing:3px;color:#6b7280;font-weight:bold;">BOOKING SMARTS</div>
            <div style="font-size:22px;font-weight:bold;margin-top:8px;color:#111827;">Verifikasi Email Anda</div>
          </td>
        </tr>
        <tr>
          <td style="padding:24px 22px;">
            <div style="font-size:14px;line-height:22px;">
              Halo <b>' . $nama . '</b> (<code>' . $user . '</code>),<br><br>
              Terima kasih telah mendaftar di <b>Booking Smarts - Smart Office</b>.<br>
              Silakan klik tombol di bawah ini untuk memverifikasi email Anda dan mengaktifkan akun:
            </div>
            <div style="margin:24px 0;text-align:center;">
              <a href="' . $link . '" style="display:inline-block;text-decoration:none;background:linear-gradient(135deg,#0A7F81,#2CC7C0);color:#fff;padding:14px 32px;border-radius:12px;font-size:15px;font-weight:bold;letter-spacing:0.5px;">
                Verifikasi Email Saya
              </a>
            </div>
            <div style="font-size:12px;color:#6b7280;line-height:18px;">
              Link ini berlaku selama <b>24 jam</b>. Jika link kadaluwarsa, Anda bisa meminta kirim ulang dari halaman login.<br><br>
              Jika Anda tidak merasa mendaftar, abaikan email ini.
            </div>
          </td>
        </tr>
        <tr>
          <td style="padding:0 22px 18px 22px;">
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;font-size:11px;color:#6b7280;word-break:break-all;">
              <b>Jika tombol tidak berfungsi, salin link berikut ke browser:</b><br>
              <a href="' . $link . '" style="color:#2563eb;">' . $link . '</a>
            </div>
          </td>
        </tr>
        <tr>
          <td style="padding:14px 22px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-align:center;">
            Email ini dikirim otomatis oleh sistem Booking Smarts.<br>
            &copy; ' . date('Y') . ' Smart Office Tiga Serangkai
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';

		$this->email->clear(true);
		$this->email->from($from, $fromName);
		$this->email->to($to_email);
		$this->email->subject('Verifikasi Email - Booking Smarts');
		$this->email->message($html);

		$ok = $this->email->send();
		if (!$ok) {
			log_message('error', 'VERIFY EMAIL FAIL: ' . $this->email->print_debugger(['headers', 'subject', 'body']));
		}

		return $ok;
	}

	/**
	 * AJAX endpoint - cek ketersediaan username / nama_lengkap
	 * GET/POST: /registration/check_availability?field=username&value=xxx
	 * Response: JSON { "available": true|false }
	 */
	public function check_availability()
	{
		$this->output->set_content_type('application/json');

		$field = $this->input->get_post('field', true);
		$value = trim((string)$this->input->get_post('value', true));

		$allowed = ['username' => 'USERNAME', 'nama_lengkap' => 'NAMA_LENGKAP'];

		if (!array_key_exists($field, $allowed) || $value === '') {
			echo json_encode(['available' => true]);
			return;
		}

		$col = $allowed[$field];
		$this->db->select($col);
		$this->db->where("LOWER({$col}) = " . $this->db->escape(strtolower($value)), null, false);
		$result = $this->db->get('user');

		echo json_encode(['available' => $result->num_rows() === 0]);
	}
}
