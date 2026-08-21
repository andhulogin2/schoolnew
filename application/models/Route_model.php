<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Route_model extends CI_Model {

    protected $table = 'tbl_transport_routes';
    protected $primaryKey = 'route_id';

    public function get_all($active_only = FALSE)
    {
        $this->db
            ->select('r.*, v.vehicle_number, v.registration_number, v.seating_capacity, d.driver_name, d.phone as driver_phone')
            ->from('tbl_transport_routes r')
            ->join('tbl_vehicles v', 'v.vehicle_id = r.assigned_vehicle_id', 'left')
            ->join('tbl_transport_drivers d', 'd.driver_id = r.assigned_driver_id', 'left')
            ->order_by('r.route_name', 'ASC');

        if ($active_only) {
            $this->db->where('r.status', 'Active');
        }

        $routes = $this->db->get()->result();

        foreach ($routes as &$r) {
            $r->stops_count = (int)$this->db
                ->where('route_id', $r->route_id)
                ->where('status', 1)
                ->count_all_results('tbl_route_stops');

            $r->students_count = (int)$this->db
                ->where('route_id', $r->route_id)
                ->where('status', 'Active')
                ->count_all_results('tbl_student_transport_assignments');
        }

        return $routes;
    }

    public function get_by_id($id)
    {
        $r = $this->db
            ->select('r.*, v.vehicle_number, v.registration_number, v.vehicle_type, v.seating_capacity, d.driver_name, d.phone as driver_phone, d.license_number')
            ->from('tbl_transport_routes r')
            ->join('tbl_vehicles v', 'v.vehicle_id = r.assigned_vehicle_id', 'left')
            ->join('tbl_transport_drivers d', 'd.driver_id = r.assigned_driver_id', 'left')
            ->where('r.' . $this->primaryKey, $id)
            ->get()
            ->row();

        if ($r) {
            $r->stops_count = (int)$this->db
                ->where('route_id', $r->route_id)
                ->where('status', 1)
                ->count_all_results('tbl_route_stops');

            $r->students_count = (int)$this->db
                ->where('route_id', $r->route_id)
                ->where('status', 'Active')
                ->count_all_results('tbl_student_transport_assignments');
        }

        return $r;
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
