<?php
class Db_Check extends CI_Controller
{
    public function index()
    {
        $fields = $this->db->list_fields('pemesanan');
        echo "Fields in pemesanan:\n";
        foreach ($fields as $field) {
            echo "- $field\n";
        }

        $fields_gedung = $this->db->list_fields('gedung');
        echo "\nFields in gedung:\n";
        foreach ($fields_gedung as $field) {
            echo "- $field\n";
        }
    }
}
