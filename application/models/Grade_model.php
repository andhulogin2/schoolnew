<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grade_model extends CI_Model {

    protected $table = 'tbl_grades';
    protected $primaryKey = 'grade_id';

    public function get_all($active_only = FALSE)
    {
        $this->db->from($this->table);
        if ($active_only) {
            $this->db->where('status', 1);
        }
        return $this->db->order_by('min_percentage', 'DESC')->get()->result();
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

    public function resolve_grade_for_percentage($percentage)
    {
        $percentage = (float)$percentage;
        $grade = $this->db
            ->where('status', 1)
            ->where('min_percentage <=', $percentage)
            ->where('max_percentage >=', $percentage)
            ->order_by('min_percentage', 'DESC')
            ->limit(1)
            ->get($this->table)
            ->row();

        if ($grade) {
            return $grade;
        }

        // Fallback lowest grade
        $lowest = $this->db->where('status', 1)->order_by('min_percentage', 'ASC')->limit(1)->get($this->table)->row();
        if ($lowest) return $lowest;

        return (object) [
            'grade_name'     => ($percentage >= 35) ? 'P' : 'F',
            'grade_point'    => ($percentage >= 35) ? 4.00 : 0.00,
            'description'    => ($percentage >= 35) ? 'Pass' : 'Fail',
            'min_percentage' => 0,
            'max_percentage' => 100
        ];
    }

    public function check_overlap($min_pct, $max_pct, $exclude_id = NULL)
    {
        $this->db->where('status', 1);
        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', $exclude_id);
        }
        $this->db->where('min_percentage <', $max_pct);
        $this->db->where('max_percentage >', $min_pct);
        return $this->db->get($this->table)->row();
    }
}
