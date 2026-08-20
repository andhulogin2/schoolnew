<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Security_setting_model extends CI_Model {

    protected $table = 'tbl_user_security_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $row = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if (!$row) {
            return (object)[
                'setting_id'                 => 1,
                'max_failed_attempts'        => 5,
                'lockout_duration_minutes'   => 30,
                'session_timeout_minutes'    => 120,
                'password_min_length'        => 8,
                'require_special_chars'      => 1,
                'require_numbers'            => 1,
                'password_expiry_days'       => 90,
                'allow_concurrent_sessions'  => 1
            ];
        }
        return $row;
    }

    public function update_settings($data)
    {
        $exists = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if ($exists) {
            return $this->db->where($this->primaryKey, 1)->update($this->table, $data);
        } else {
            $data['setting_id'] = 1;
            return $this->db->insert($this->table, $data);
        }
    }
}
