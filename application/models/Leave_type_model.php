<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_type_model extends CI_Model {

    protected $table = 'tbl_leave_types';
    protected $primaryKey = 'type_id';

    public function get_all($applicable_to = NULL, $active_only = FALSE)
    {
        if ($active_only) $this->db->where('status', 1);
        if ($applicable_to) {
            $this->db->group_start()
                ->where('applicable_to', $applicable_to)
                ->or_where('applicable_to', 'Both')
            ->group_end();
        }
        return $this->db->order_by('type_name', 'ASC')->get($this->table)->result();
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
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function delete_or_deactivate($id)
    {
        // Check if historical applications exist
        $hasHistory = $this->db->where('leave_type_id', $id)->count_all_results('tbl_leave_applications') > 0;
        if ($hasHistory) {
            return $this->update($id, ['status' => 0]);
        }
        return $this->db->where($this->primaryKey, $id)->delete($this->table);
    }
}
