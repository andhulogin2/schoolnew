<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academic_calendar_model extends CI_Model {

    protected $table = 'tbl_academic_calendar';
    protected $primaryKey = 'calendar_id';

    public function get_all($filters = array())
    {
        $this->db
            ->select('cal.*, y.year_name')
            ->from('tbl_academic_calendar cal')
            ->join('tbl_academic_years y', 'y.academic_year_id = cal.academic_year_id', 'left')
            ->where('cal.status', 1)
            ->order_by('cal.start_date', 'ASC');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('cal.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['event_type'])) {
            $this->db->where('cal.event_type', $filters['event_type']);
        }
        if (!empty($filters['audience'])) {
            $this->db->where('cal.audience', $filters['audience']);
        }
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $month = str_pad($filters['month'], 2, '0', STR_PAD_LEFT);
            $year = $filters['year'];
            $this->db->where("DATE_FORMAT(cal.start_date, '%Y-%m') =", "$year-$month");
        }

        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('cal.*, y.year_name')
            ->from('tbl_academic_calendar cal')
            ->join('tbl_academic_years y', 'y.academic_year_id = cal.academic_year_id', 'left')
            ->where('cal.calendar_id', $id)
            ->get()
            ->row();
    }

    public function get_upcoming($limit = 5, $academic_year_id = NULL)
    {
        $this->db
            ->select('cal.*, y.year_name')
            ->from('tbl_academic_calendar cal')
            ->join('tbl_academic_years y', 'y.academic_year_id = cal.academic_year_id', 'left')
            ->where('cal.status', 1)
            ->where('cal.start_date >=', date('Y-m-d'))
            ->order_by('cal.start_date', 'ASC')
            ->limit($limit);

        if ($academic_year_id) {
            $this->db->where('cal.academic_year_id', $academic_year_id);
        }

        return $this->db->get()->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, ['status' => 0, 'is_deleted' => 'y']);
    }
}
