<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_setting_model extends CI_Model {

    protected $table = 'tbl_leave_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if (!$settings) {
            return (object)[
                'setting_id'               => 1,
                'enable_student_leave'     => 1,
                'enable_staff_leave'       => 1,
                'enable_half_day'          => 1,
                'working_days_only'        => 1,
                'student_approval_workflow'=> 'Class Teacher -> Principal',
                'staff_approval_workflow'  => 'Department Head -> Principal',
                'enable_balance_tracking'  => 1,
                'allow_carry_forward'      => 1,
                'max_carry_forward_days'   => 5,
                'require_document_default' => 0,
                'max_file_size_mb'         => 10
            ];
        }
        return $settings;
    }

    public function update_settings($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, 1)->update($this->table, $data);
    }

    public function get_audit_logs($limit = 30)
    {
        return $this->db
            ->select('log.*, s.full_name as user_name')
            ->from('tbl_leave_audit_logs log')
            ->join('tbl_staff s', 's.staff_id = log.user_id', 'left')
            ->order_by('log.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
