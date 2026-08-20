<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate_type_model extends CI_Model {

    protected $table = 'tbl_certificate_types';
    protected $primaryKey = 'type_id';

    public function get_all($status = null)
    {
        $this->db->from($this->table);
        if ($status !== null) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('type_id', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    public function get_by_code($code)
    {
        return $this->db->where('type_code', $code)->get($this->table)->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function toggle_status($id)
    {
        $type = $this->get_by_id($id);
        if (!$type) return false;
        $new_status = ($type->status === 'Active') ? 'Inactive' : 'Active';
        return $this->update($id, array('status' => $new_status));
    }
}
