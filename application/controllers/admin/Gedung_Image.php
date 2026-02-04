<?php

class Gedung_Image extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        // optional: check admin login
        if ($this->session->userdata('admin_logged_in') !== TRUE) {
            redirect('admin');
            return;
        }
    }

    public function delete()
    {
        $this->load->model('gedung/gedung_model');
        $this->load->helper('url');

        $id_gedung = (int)$this->input->post('id_gedung');
        $img_name = $this->input->post('img_name');

        if ($id_gedung <= 0 || empty($img_name)) {
            $this->output->set_status_header(400);
            echo "Parameter tidak lengkap";
            return;
        }

        // try delete from gedung_img
        $deleted = $this->gedung_model->delete_gedung_img($id_gedung, $img_name);

        // fallback: mungkin baris ada di HOME_DATA atau tabel lain (coba hapus juga)
        if (!$deleted) {
            // coba hapus di HOME_DATA
            $this->db->where('IMG_NAME', $img_name)->delete('HOME_DATA');
            $deleted = $this->db->affected_rows() > 0;
        }

        // delete file fisik jika ada
        $file_path = FCPATH . 'assets/images/gedung/' . $img_name;
        if (is_file($file_path)) {
            @unlink($file_path);
        }

        // set flash message
        if ($deleted) {
            $this->session->set_flashdata('msg_success', 'Foto berhasil dihapus.');
        } else {
            $this->session->set_flashdata('msg_error', 'Gagal menghapus foto (mungkin sudah terhapus atau nama file berbeda).');
        }

        redirect('admin/edit/' . $id_gedung);
    }
}
