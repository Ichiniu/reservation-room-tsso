<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    private function _get_google_client()
    {
        $client = new Google\Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URL'));
        $client->addScope("email");
        $client->addScope("profile");
        return $client;
    }

    public function google_login()
    {
        $client = $this->_get_google_client();
        $auth_url = $client->createAuthUrl();
        redirect($auth_url);
    }

    public function google_callback()
    {
        $client = $this->_get_google_client();

        if ($this->input->get('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($this->input->get('code'));
            
            if (isset($token['error'])) {
                $this->session->set_flashdata('flash_msg', 'Gagal login via Google.');
                $this->session->set_flashdata('flash_type', 'error');
                redirect('login');
                return;
            }

            $client->setAccessToken($token['access_token']);

            // Get profile info dari Google
            $google_oauth = new Google\Service\Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();

            $email = $google_account_info->email;
            $name  = $google_account_info->name;
            $google_id = $google_account_info->id;
            $picture = $google_account_info->picture;

            // Cek apakah user sudah ada di DB (cek via google_id atau email)
            $user = $this->db->group_start()
                             ->where('google_id', $google_id)
                             ->or_where('EMAIL', $email)
                             ->group_end()
                             ->get('user')
                             ->row();

            if ($user) {
                // Update google_id jika belum ada (mungkin user lama yang login via Google pertama kali)
                if (empty($user->google_id)) {
                    $this->db->where('USERNAME', $user->USERNAME);
                    $this->db->update('user', ['google_id' => $google_id]);
                }

                // Set Session
                $this->_set_user_session($user);

                // Cek apakah profil sudah lengkap
                if ((int)$user->is_profile_complete === 0) {
                    redirect('auth/complete_profile');
                } else {
                    $this->session->set_flashdata('flash_msg', 'Selamat datang kembali, ' . $user->NAMA_LENGKAP . '!');
                    $this->session->set_flashdata('flash_type', 'success');
                    redirect('home/' . $user->USERNAME . '/');
                }
            } else {
                // USER BARU: Buat baris di DB dulu dengan data seadanya dari Google
                // Karena USERNAME adalah Primary Key, kita buat dummy dulu (bisa diganti nanti)
                $temp_username = 'user_' . substr($google_id, -8);
                
                $data = [
                    'USERNAME'            => $temp_username,
                    'NAMA_LENGKAP'        => $name,
                    'EMAIL'               => $email,
                    'is_verified'         => 1, // Email dari Google otomatis trusted/verified
                    'google_id'           => $google_id,
                    'FOTO_PROFIL'         => $picture,
                    'is_profile_complete' => 0 // Tandai wajib lengkapi profil
                ];

                $this->db->insert('user', $data);

                // Set Session
                $new_user = $this->db->get_where('user', ['google_id' => $google_id])->row();
                $this->_set_user_session($new_user);

                // Redirect ke halaman lengkapi profil
                redirect('auth/complete_profile');
            }
        } else {
            redirect('login');
        }
    }

    private function _set_user_session($user)
    {
        $session_data = [
            'username'    => $user->USERNAME,
            'foto_profil' => $user->FOTO_PROFIL ?? '',
            'logged_in'   => TRUE,
            'session_id'  => session_id()
        ];
        $this->session->set_userdata($session_data);
    }

    public function complete_profile()
    {
        $username = $this->session->userdata('username');
        if (!$username) redirect('login');

        $user = $this->db->get_where('user', ['USERNAME' => $username])->row();
        if ($user->is_profile_complete == 1) redirect('home/' . $username . '/');

        $this->load->view('auth/complete_profile', ['user' => $user]);
    }

    public function save_profile()
    {
        $username_current = $this->session->userdata('username');
        if (!$username_current) redirect('login');

        // Ambil data dari form
        $new_username    = trim((string)$this->input->post('username', true));
        $no_telepon      = trim((string)$this->input->post('no_telepon', true));
        $alamat          = trim((string)$this->input->post('alamat', true));
        $dob             = $this->input->post('dob', true);
        $perusahaan      = $this->input->post('perusahaan', true);
        $nama_perusahaan = trim((string)$this->input->post('nama_perusahaan', true));
        $departemen      = trim((string)$this->input->post('departemen', true));

        // Validasi simpel
        if (empty($new_username) || empty($no_telepon) || empty($perusahaan)) {
            $this->session->set_flashdata('flash_msg', 'Mohon isi semua field yang wajib.');
            $this->session->set_flashdata('flash_type', 'error');
            redirect('auth/complete_profile');
            return;
        }

        // Cek apakah username baru sudah dipakai orang lain
        if (strtolower($new_username) !== strtolower($username_current)) {
            $exists = $this->db->get_where('user', ['USERNAME' => $new_username])->row();
            if ($exists) {
                $this->session->set_flashdata('flash_msg', 'Username sudah digunakan.');
                $this->session->set_flashdata('flash_type', 'error');
                redirect('auth/complete_profile');
                return;
            }
        }

        // Data Update
        $update_data = [
            'USERNAME'            => $new_username,
            'NO_TELEPON'          => $no_telepon,
            'ALAMAT'              => $alamat,
            'TANGGAL_LAHIR'       => $dob,
            'perusahaan'          => $perusahaan,
            'nama_perusahaan'     => ($perusahaan == 'INTERNAL') ? 'PT Tiga Serangkai Pustaka Mandiri' : $nama_perusahaan,
            'departemen'          => ($perusahaan == 'INTERNAL') ? $departemen : null,
            'is_profile_complete' => 1
        ];

        $this->db->where('USERNAME', $username_current);
        $this->db->update('user', $update_data);

        // Update session jika username berubah
        if ($new_username !== $username_current) {
            $this->session->set_userdata('username', $new_username);
        }

        $this->session->set_flashdata('flash_msg', 'Profil berhasil dilengkapi! Selamat datang.');
        $this->session->set_flashdata('flash_type', 'success');
        redirect('home/' . $new_username . '/');
    }
}
