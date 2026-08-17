<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff_model extends CI_Model {

    protected $table = 'tbl_staff';
    protected $primaryKey = 'staff_id';

    public function get_all($filters = array())
    {
        $this->db
            ->select('s.*, d.department_name, dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status >=', 0)
            ->order_by('s.staff_id', 'ASC');

        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', $filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $this->db->where('s.designation_id', $filters['designation_id']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $this->db->group_start()
                ->like('s.full_name', $s)
                ->or_like('s.employee_code', $s)
                ->or_like('s.email', $s)
                ->group_end();
        }

        return $this->db->get()->result();
    }

    public function get_teachers($filters = array())
    {
        $this->db
            ->select('s.*, d.department_name, dg.designation_name,
                (SELECT GROUP_CONCAT(DISTINCT sub.subject_name SEPARATOR ", ") FROM tbl_subjects sub WHERE sub.teacher_id = s.staff_id AND sub.status = 1) as subjects_handled,
                (SELECT GROUP_CONCAT(DISTINCT c.class_name SEPARATOR ", ") FROM tbl_subjects sub JOIN tbl_classes c ON c.class_id = sub.class_id WHERE sub.teacher_id = s.staff_id AND sub.status = 1) as classes_handled,
                (SELECT GROUP_CONCAT(DISTINCT CONCAT(c.class_name, " ", sec.section_name) SEPARATOR ", ") FROM tbl_sections sec JOIN tbl_classes c ON c.class_id = sec.class_id WHERE sec.class_teacher_id = s.staff_id AND sec.status = 1) as sections_handled')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status', 1)
            ->group_start()
                ->where('s.category', 'Teacher')
                ->or_like('dg.designation_name', 'Teacher')
                ->or_like('dg.designation_name', 'Head of Department')
            ->group_end()
            ->order_by('s.staff_id', 'ASC');

        if (!empty($filters['subject_name'])) {
            $this->db->having('subjects_handled LIKE', '%' . $filters['subject_name'] . '%');
        }

        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('s.*, d.department_name, dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.staff_id', $id)
            ->get()
            ->row();
    }

    public function count_staff()
    {
        return $this->db
            ->where('status', 1)
            ->count_all_results($this->table);
    }

    public function count_teachers()
    {
        return $this->db
            ->from($this->table . ' s')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status', 1)
            ->group_start()
                ->where('s.category', 'Teacher')
                ->or_like('dg.designation_name', 'Teacher')
                ->or_like('dg.designation_name', 'Head of Department')
            ->group_end()
            ->count_all_results();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, array('status' => 0));
    }
}
