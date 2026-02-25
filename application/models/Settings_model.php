<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a setting value by key
     */
    public function get($key, $default = '')
    {
        $query = $this->db->get_where('app_settings', ['setting_key' => $key], 1);
        $row = $query->row_array();
        return $row['setting_value'] ?? $default;
    }

    /**
     * Set a setting value by key (insert or update)
     */
    public function set($key, $value)
    {
        $exists = $this->db->get_where('app_settings', ['setting_key' => $key], 1)->num_rows();
        if ($exists > 0) {
            $this->db->where('setting_key', $key);
            return $this->db->update('app_settings', ['setting_value' => $value]);
        } else {
            return $this->db->insert('app_settings', [
                'setting_key'   => $key,
                'setting_value' => $value
            ]);
        }
    }
}
