<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_model extends CI_Model {

    protected $table = 'tbl_classes';
    protected $primaryKey = 'class_id';

    public function get_all($academic_year_id = NULL)
    {
        $this->db
            ->select('c.*, y.year_name, s.full_name as class_teacher_name, (SELECT COUNT(student_id) FROM tbl_students WHERE class_id = c.class_id AND status = 1) as student_count')
            ->from('tbl_classes c')
            ->join('tbl_academic_years y', 'y.academic_year_id = c.academic_year_id', 'left')
            ->join('tbl_staff s', 's.staff_id = c.class_teacher_id', 'left')
            ->where('c.status', 1)
            ->order_by('c.class_id', 'ASC');

        if ($academic_year_id) {
            $this->db->where('c.academic_year_id', $academic_year_id);
        }

        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('c.*, y.year_name, s.full_name as class_teacher_name')
            ->from('tbl_classes c')
            ->join('tbl_academic_years y', 'y.academic_year_id = c.academic_year_id', 'left')
            ->join('tbl_staff s', 's.staff_id = c.class_teacher_id', 'left')
            ->where('c.class_id', $id)
            ->get()
            ->row();
    }

    public function count_classes($academic_year_id = NULL)
    {
        if ($academic_year_id) {
            $this->db->where('academic_year_id', $academic_year_id);
        }
        return $this->db
            ->where('status', 1)
            ->count_all_results($this->table);
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
