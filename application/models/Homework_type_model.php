<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homework_type_model extends CI_Model {

    protected $table = 'tbl_assignment_types';
    protected $primaryKey = 'type_id';

    public function get_all($active_only = FALSE)
    {
        if ($active_only) {
            $this->db->where('status', 1);
        }
        return $this->db->order_by('type_name', 'ASC')->get($this->table)->result();
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
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        // Safe delete or toggle
        $used_cnt = $this->db->where('assignment_type_id', $id)->count_all_results('tbl_assignments');
        if ($used_cnt === 0) {
            return $this->db->where($this->primaryKey, $id)->update($this->table, ['is_deleted' => 'y']);
        }
        return $this->update($id, ['status' => 0]);
    }
}
