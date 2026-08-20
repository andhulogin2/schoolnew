<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_setting_model extends CI_Model {

    protected $table = 'tbl_attendance_settings';
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

        if (!$settings) {
            // Default fallback
            return (object) array(
                'setting_id'                  => 1,
                'enable_present'              => 1,
                'enable_absent'               => 1,
                'enable_late'                 => 1,
                'enable_excused'              => 1,
                'enable_period_attendance'    => 1,
                'enable_absent_notification'  => 1,
                'enable_late_notification'    => 1,
                'enable_summary_notification' => 1,
                'absent_template'             => 'Dear Parent, your child {student_name} was marked absent on {date}.',
                'late_template'               => 'Dear Parent, your child {student_name} was marked late on {date}.',
                'excused_template'            => 'Dear Parent, your child {student_name} has been excused on {date}.',
                'summary_template'            => 'Attendance summary for {student_name}: Present {present_days}, Absent {absent_days}, Late {late_days}, Excused {excused_days}.',
                'notification_timing'         => 'On Marking',
            );
        }

        return $settings;
    }

    public function update_settings($data)
    {
        $existing = $this->get_settings();
        if ($existing && isset($existing->setting_id)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->db
                ->where($this->primaryKey, $existing->setting_id)
                ->update($this->table, $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert($this->table, $data);
        }
    }
}
