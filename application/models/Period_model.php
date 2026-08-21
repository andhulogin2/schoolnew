<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Period_model extends CI_Model {

    protected $table = 'tbl_periods';
    protected $primaryKey = 'period_id';

    public function get_all($active_only = TRUE)
    {
        $this->db->from($this->table);
        if ($active_only) {
            $this->db->where('status', 1);
        }
        return $this->db
            ->order_by('period_number', 'ASC')
            ->order_by('period_order', 'ASC')
            ->order_by('start_time', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    public function insert($data)
    {
        if (!isset($data['period_order']) && isset($data['period_number'])) {
            $data['period_order'] = $data['period_number'];
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (isset($data['period_number']) && !isset($data['period_order'])) {
            $data['period_order'] = $data['period_number'];
        }
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function toggle_status($id)
    {
        $period = $this->get_by_id($id);
        if (!$period) return FALSE;
        $new_status = ($period->status == 1) ? 0 : 1;
        return $this->update($id, array('status' => $new_status));
    }

    public function check_number_exists($period_number, $exclude_id = NULL)
    {
        $this->db->where('period_number', $period_number);
        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function check_overlap($start_time, $end_time, $exclude_id = NULL)
    {
        // Check if [start_time, end_time] overlaps with any active period
        // Overlap occurs if (start < existing_end) AND (end > existing_start)
        $this->db->where('status', 1);
        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', $exclude_id);
        }
        $this->db->where("start_time <", $end_time);
        $this->db->where("end_time >", $start_time);
        return $this->db->get($this->table)->row();
    }

    public function is_safe_to_delete($id)
    {
        // Check if used in timetable or attendance
        $tt_count = $this->db->where('period_id', $id)->count_all_results('tbl_timetable');
        $att_count = $this->db->where('period_id', $id)->count_all_results('tbl_attendance');
        return ($tt_count === 0 && $att_count === 0);
    }

    public function delete($id)
    {
        if ($this->is_safe_to_delete($id)) {
            return $this->db->where($this->primaryKey, $id)->update($this->table, ['is_deleted' => 'y']);
        }
        // Soft delete if referenced
        return $this->update($id, array('status' => 0));
    }
}
