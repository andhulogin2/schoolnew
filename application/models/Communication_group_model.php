<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communication_group_model extends CI_Model {

    protected $table = 'tbl_communication_groups';
    protected $primaryKey = 'group_id';

    public function get_all($active_only = FALSE)
    {
        if ($active_only) $this->db->where('status', 1);
        return $this->db->order_by('group_name', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, ['is_deleted' => 'y']);
    }
}
