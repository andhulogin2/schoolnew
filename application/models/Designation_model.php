<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designation_model extends CI_Model {

    protected $table = 'tbl_designations';
    protected $primaryKey = 'designation_id';

    public function get_all()
    {
        return $this->db
            ->select('dg.*, (SELECT COUNT(staff_id) FROM tbl_staff WHERE designation_id = dg.designation_id AND status = 1) as staff_count')
            ->from('tbl_designations dg')
            ->where('dg.status', 1)
            ->order_by('dg.designation_id', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->get($this->table)
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
