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

        $fields_notif = $this->db->list_fields('notifications');
        echo "\nFields in notifications:\n";
        foreach ($fields_notif as $field) {
            echo "- $field\n";
        }
    }
}
