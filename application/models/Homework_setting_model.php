<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homework_setting_model extends CI_Model {

    protected $table = 'tbl_homework_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if (!$settings) {
            return (object)[
                'setting_id'                       => 1,
                'default_submission_deadline_days' => 3,
                'allow_late_submissions_default'  => 1,
                'max_upload_size_mb'               => 10,
                'allowed_file_extensions'          => 'pdf,doc,docx,jpg,jpeg,png,zip,txt',
                'enable_grading'                   => 1,
                'enable_parent_notifications'      => 1
            ];
        }
        return $settings;
    }

    public function update_settings($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, 1)->update($this->table, $data);
    }

    public function get_audit_logs($limit = 50)
    {
        return $this->db
            ->select('log.*, s.full_name as user_name')
            ->from('tbl_homework_audit_logs log')
            ->join('tbl_staff s', 's.staff_id = log.user_id', 'left')
            ->order_by('log.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
