<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_structure_model extends CI_Model {

    public function get_all($filters = array())
    {
        $this->db->select('fs.*, fh.head_name as category_name, fh.category_code, ay.year_name, c.class_name')
                 ->from('tbl_fee_structures fs')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'inner')
                 ->join('tbl_academic_years ay', 'ay.academic_year_id = fs.academic_year_id', 'left')
                 ->join('tbl_classes c', 'c.class_id = fs.class_id', 'left');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('fs.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('fs.class_id', $filters['class_id']);
        }
        if (!empty($filters['fee_head_id'])) {
            $this->db->where('fs.fee_head_id', $filters['fee_head_id']);
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('fs.status', $filters['status']);
        }

        return $this->db->order_by('c.class_name', 'ASC')
                        ->order_by('fh.head_name', 'ASC')
                        ->get()
                        ->result();
    }

    public function get_by_id($id)
    {
        return $this->db->select('fs.*, fh.head_name as category_name, fh.category_code, ay.year_name, c.class_name')
                        ->from('tbl_fee_structures fs')
                        ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'inner')
                        ->join('tbl_academic_years ay', 'ay.academic_year_id = fs.academic_year_id', 'left')
                        ->join('tbl_classes c', 'c.class_id = fs.class_id', 'left')
                        ->where('fs.fee_structure_id', $id)
                        ->get()
                        ->row();
    }

    public function is_duplicate($fee_head_id, $academic_year_id, $class_id, $exclude_id = 0)
    {
        $this->db->where('fee_head_id', $fee_head_id)
                 ->where('academic_year_id', $academic_year_id)
                 ->where('class_id', $class_id);
        if ($exclude_id > 0) {
            $this->db->where('fee_structure_id !=', $exclude_id);
        }
        return ($this->db->count_all_results('tbl_fee_structures') > 0);
    }

    public function save($data, $id = 0)
    {
        if ($id > 0) {
            $this->db->where('fee_structure_id', $id)->update('tbl_fee_structures', $data);
            return $id;
        } else {
            $this->db->insert('tbl_fee_structures', $data);
            return $this->db->insert_id();
        }
    }

    public function delete($id)
    {
        // Prevent deletion if assigned to any student fee
        $assigned = $this->db->where('fee_structure_id', $id)->count_all_results('tbl_student_fees');
        if ($assigned > 0) {
            return false;
        }
        return $this->db->where('fee_structure_id', $id)->update('tbl_fee_structures', ['is_deleted' => 'y']);
    }
}
