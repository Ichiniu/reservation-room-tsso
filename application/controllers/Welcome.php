<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Welcome Controller
 *
 * Default controller yang berfungsi sebagai entry point
 * ketika user mengakses root URL (http://localhost/bookingsmarts/).
 * Menampilkan Welcome_Screen (halaman publik, tanpa login).
 */
class Welcome extends CI_Controller
{
    public function index()
    {
        // Load model gedung untuk ambil daftar ruangan
        $this->load->model('gedung/gedung_model');

        $data['res'] = $this->gedung_model->get_all();

        // Tampilkan welcome screen (public, tanpa perlu login)
        $this->load->view('Welcome_Screen', $data);
    }
}
