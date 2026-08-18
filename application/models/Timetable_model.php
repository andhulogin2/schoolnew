<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable_model extends CI_Model {

    protected $table = 'tbl_timetable';
    protected $primaryKey = 'timetable_id';

    public function get_entries($filters = array())
    {
        $this->db
            ->select('tt.*, y.year_name, c.class_name, sec.section_name, p.period_name, p.start_time, p.end_time, sub.subject_name, sub.subject_code, s.full_name as teacher_name, s.employee_code')
            ->from('tbl_timetable tt')
            ->join('tbl_academic_years y', 'y.academic_year_id = tt.academic_year_id', 'left')
            ->join('tbl_classes c', 'c.class_id = tt.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = tt.section_id', 'left')
            ->join('tbl_periods p', 'p.period_id = tt.period_id', 'left')
            ->join('tbl_subjects sub', 'sub.subject_id = tt.subject_id', 'left')
            ->join('tbl_staff s', 's.staff_id = tt.teacher_id', 'left')
            ->where('tt.status', 1)
            ->order_by('FIELD(tt.day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday")')
            ->order_by('p.period_order', 'ASC');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('tt.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('tt.class_id', $filters['class_id']);
        }
        if (!empty($filters['section_id'])) {
            $this->db->where('tt.section_id', $filters['section_id']);
        }
        if (!empty($filters['day'])) {
            $this->db->where('tt.day', $filters['day']);
        }
        if (!empty($filters['teacher_id'])) {
            $this->db->where('tt.teacher_id', $filters['teacher_id']);
        }

        return $this->db->get()->result();
    }

    public function check_conflicts($data, $exclude_id = NULL)
    {
        $errors = array();

        // 1. Conflict Check: Class + Section + Day + Period
        $this->db
            ->select('tt.*, sub.subject_name, s.full_name as teacher_name')
            ->from('tbl_timetable tt')
            ->join('tbl_subjects sub', 'sub.subject_id = tt.subject_id', 'left')
            ->join('tbl_staff s', 's.staff_id = tt.teacher_id', 'left')
            ->where('tt.academic_year_id', $data['academic_year_id'])
            ->where('tt.class_id', $data['class_id'])
            ->where('tt.section_id', $data['section_id'])
            ->where('tt.day', $data['day'])
            ->where('tt.period_id', $data['period_id'])
            ->where('tt.status', 1);

        if ($exclude_id) {
            $this->db->where('tt.timetable_id !=', $exclude_id);
        }
        $classClash = $this->db->get()->row();
        if ($classClash) {
            $errors[] = "Class already has '{$classClash->subject_name}' ({$classClash->teacher_name}) assigned on {$data['day']} during this period.";
        }

        // 2. Conflict Check: Teacher collision (Same teacher booked in another class at the same period)
        $this->db
            ->select('tt.*, c.class_name, sec.section_name, sub.subject_name, s.full_name as teacher_name')
            ->from('tbl_timetable tt')
            ->join('tbl_classes c', 'c.class_id = tt.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = tt.section_id', 'left')
            ->join('tbl_subjects sub', 'sub.subject_id = tt.subject_id', 'left')
            ->join('tbl_staff s', 's.staff_id = tt.teacher_id', 'left')
            ->where('tt.academic_year_id', $data['academic_year_id'])
            ->where('tt.teacher_id', $data['teacher_id'])
            ->where('tt.day', $data['day'])
            ->where('tt.period_id', $data['period_id'])
            ->where('tt.status', 1);

        if ($exclude_id) {
            $this->db->where('tt.timetable_id !=', $exclude_id);
        }
        $teacherClash = $this->db->get()->row();
        if ($teacherClash) {
            $errors[] = "Teacher '{$teacherClash->teacher_name}' is already teaching {$teacherClash->subject_name} for {$teacherClash->class_name} {$teacherClash->section_name} on {$data['day']} during this period.";
        }

        return $errors;
    }

    public function save_entry($data, $id = NULL)
    {
        // Enforce teacher only
        $staff = $this->db->where('staff_id', $data['teacher_id'])->where('staff_type', 'teacher')->get('tbl_staff')->row();
        if (!$staff) {
            return array('success' => FALSE, 'message' => 'Only teaching faculty can be assigned to timetable periods.');
        }

        $conflicts = $this->check_conflicts($data, $id);
        if (!empty($conflicts)) {
            return array('success' => FALSE, 'message' => implode(' ', $conflicts));
        }

        if ($id) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where($this->primaryKey, $id)->update($this->table, $data);
            return array('success' => TRUE, 'id' => $id);
        } else {
            $data['status'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $data);
            return array('success' => TRUE, 'id' => $this->db->insert_id());
        }
    }

    public function delete_entry($id)
    {
        return $this->db->where($this->primaryKey, $id)->delete($this->table);
    }
}
