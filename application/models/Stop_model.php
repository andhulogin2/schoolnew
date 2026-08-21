<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stop_model extends CI_Model {

    protected $table = 'tbl_route_stops';
    protected $primaryKey = 'stop_id';

    public function get_all_by_route($route_id = NULL)
    {
        $this->db
            ->select('s.*, r.route_name, r.route_code')
            ->from('tbl_route_stops s')
            ->join('tbl_transport_routes r', 'r.route_id = s.route_id', 'left')
            ->order_by('s.route_id', 'ASC')
            ->order_by('s.sequence_order', 'ASC');

        if ($route_id) {
            $this->db->where('s.route_id', $route_id);
        }

        $stops = $this->db->get()->result();

        foreach ($stops as &$st) {
            $st->students_count = (int)$this->db
                ->where('status', 'Active')
                ->group_start()
                    ->where('pickup_stop_id', $st->stop_id)
                    ->or_where('drop_stop_id', $st->stop_id)
                ->group_end()
                ->count_all_results('tbl_student_transport_assignments');
        }

        return $stops;
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('s.*, r.route_name, r.route_code')
            ->from('tbl_route_stops s')
            ->join('tbl_transport_routes r', 'r.route_id = s.route_id', 'left')
            ->where('s.' . $this->primaryKey, $id)
            ->get()
            ->row();
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
