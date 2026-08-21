<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model {

    protected $table = 'tbl_departments';
    protected $primaryKey = 'department_id';

    public function get_all()
    {
        return $this->db
            ->select('d.*, s.full_name as head_name, (SELECT COUNT(staff_id) FROM tbl_staff WHERE department_id = d.department_id AND status = 1 AND is_deleted = \'n\') as staff_count')
            ->from('tbl_departments d')
            ->join('tbl_staff s', 's.staff_id = d.head_of_department_id', 'left')
            ->where('d.status', 1)
            ->where('d.is_deleted', 'n')
            ->order_by('d.department_id', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('d.*, s.full_name as head_name')
            ->from('tbl_departments d')
            ->join('tbl_staff s', 's.staff_id = d.head_of_department_id', 'left')
            ->where('d.department_id', $id)
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
            ->update($this->table, ['status' => 0, 'is_deleted' => 'y']);
    }
}
