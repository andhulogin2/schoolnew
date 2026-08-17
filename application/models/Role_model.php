<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    protected $table = 'tbl_roles';
    protected $primaryKey = 'role_id';

    public function get_all()
    {
        return $this->db
            ->where('status', 1)
            ->order_by('role_id', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->get($this->table)
            ->row();
    }
}
