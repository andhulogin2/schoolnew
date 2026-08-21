<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_category_model extends CI_Model {

    public function get_all($active_only = false)
    {
        $this->db->select('*')->from('tbl_fee_heads');
        if ($active_only) {
            $this->db->where('status', 1);
        }
        $this->db->where('is_deleted', 'n');
        return $this->db->order_by('head_name', 'ASC')->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('tbl_fee_heads', array('fee_head_id' => $id))->row();
    }

    public function is_name_unique($name, $exclude_id = 0)
    {
        $this->db->where('head_name', trim($name));
        if ($exclude_id > 0) {
            $this->db->where('fee_head_id !=', $exclude_id);
        }
        return ($this->db->count_all_results('tbl_fee_heads') === 0);
    }

    public function save($data, $id = 0)
    {
        if ($id > 0) {
            $this->db->where('fee_head_id', $id)->update('tbl_fee_heads', $data);
            return $id;
        } else {
            $this->db->insert('tbl_fee_heads', $data);
            return $this->db->insert_id();
        }
    }

    public function delete($id)
    {
        // Check if assigned in any fee structure
        $linked = $this->db->where('fee_head_id', $id)->count_all_results('tbl_fee_structures');
        if ($linked > 0) {
            return false;
        }
        return $this->db->where('fee_head_id', $id)->update('tbl_fee_heads', ['is_deleted' => 'y']);
    }
}
