<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject_model extends CI_Model {

    protected $table = 'tbl_subjects';
    protected $primaryKey = 'subject_id';

    public function get_all($class_id = NULL)
    {
        $this->db
            ->select('sub.*, c.class_name, s.full_name as teacher_name')
            ->from('tbl_subjects sub')
            ->join('tbl_classes c', 'c.class_id = sub.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sub.teacher_id', 'left')
            ->where('sub.status', 1)
            ->order_by('sub.class_id', 'ASC')
            ->order_by('sub.subject_name', 'ASC');

        if ($class_id) {
            $this->db->where('sub.class_id', $class_id);
        }

        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('sub.*, c.class_name, s.full_name as teacher_name')
            ->from('tbl_subjects sub')
            ->join('tbl_classes c', 'c.class_id = sub.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sub.teacher_id', 'left')
            ->where('sub.subject_id', $id)
            ->get()
            ->row();
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
