<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

    protected $table = 'tbl_school_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db
            ->where($this->primaryKey, 1)
            ->get($this->table)
            ->row();

        if (!$settings) {
            $settings = $this->db
                ->limit(1)
                ->get($this->table)
                ->row();
        }

        return $settings;
    }

    public function update_settings($data)
    {
        $existing = $this->get_settings();
        if ($existing) {
            return $this->db
                ->where($this->primaryKey, $existing->setting_id)
                ->update($this->table, $data);
        } else {
            return $this->db->insert($this->table, $data);
        }
    }
}
