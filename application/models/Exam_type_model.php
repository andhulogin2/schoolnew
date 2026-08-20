<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exam_type_model extends CI_Model {

    protected $table = 'tbl_exam_types';
    protected $primaryKey = 'exam_type_id';

    public function get_all($active_only = FALSE)
    {
        $this->db->from($this->table);
        if ($active_only) {
            $this->db->where('status', 1);
        }
        return $this->db->order_by('type_name', 'ASC')->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
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
        if (!$type) return FALSE;
        $new_status = ($type->status == 1) ? 0 : 1;
        return $this->update($id, array('status' => $new_status));
    }

    public function is_name_exists($name, $exclude_id = NULL)
    {
        $this->db->where('type_name', $name);
        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function is_safe_to_delete($id)
    {
        $exam_count = $this->db->where('exam_type_id', $id)->count_all_results('tbl_exams');
        return ($exam_count === 0);
    }

    public function delete($id)
    {
        if ($this->is_safe_to_delete($id)) {
            return $this->db->where($this->primaryKey, $id)->delete($this->table);
        }
        return $this->update($id, array('status' => 0));
    }
}
