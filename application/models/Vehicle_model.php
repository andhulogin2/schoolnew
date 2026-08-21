<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_model extends CI_Model {

    protected $table = 'tbl_vehicles';
    protected $primaryKey = 'vehicle_id';

    public function get_all($active_only = FALSE)
    {
        $this->db
            ->select('v.*, d.driver_name, d.phone as driver_phone, r.route_name, r.route_code')
            ->from('tbl_vehicles v')
            ->join('tbl_transport_drivers d', 'd.driver_id = v.assigned_driver_id', 'left')
            ->join('tbl_transport_routes r', 'r.route_id = v.assigned_route_id', 'left')
            ->order_by('v.vehicle_number', 'ASC');

        if ($active_only) {
            $this->db->where('v.status', 'Active');
        }

        $vehicles = $this->db->get()->result();

        // Calculate occupancy for each vehicle
        foreach ($vehicles as &$v) {
            $v->occupied_seats = (int)$this->db
                ->where('vehicle_id', $v->vehicle_id)
                ->where('status', 'Active')
                ->count_all_results('tbl_student_transport_assignments');
            $v->available_seats = max(0, (int)$v->seating_capacity - $v->occupied_seats);
            $v->occupancy_rate = ($v->seating_capacity > 0) ? round(($v->occupied_seats / $v->seating_capacity) * 100, 1) : 0;
        }

        return $vehicles;
    }

    public function get_by_id($id)
    {
        $v = $this->db
            ->select('v.*, d.driver_name, d.phone as driver_phone, d.license_number, d.license_expiry_date, r.route_name, r.route_code, r.start_point, r.end_point, r.estimated_distance_km')
            ->from('tbl_vehicles v')
            ->join('tbl_transport_drivers d', 'd.driver_id = v.assigned_driver_id', 'left')
            ->join('tbl_transport_routes r', 'r.route_id = v.assigned_route_id', 'left')
            ->where('v.' . $this->primaryKey, $id)
            ->get()
            ->row();

        if ($v) {
            $v->occupied_seats = (int)$this->db
                ->where('vehicle_id', $v->vehicle_id)
                ->where('status', 'Active')
                ->count_all_results('tbl_student_transport_assignments');
            $v->available_seats = max(0, (int)$v->seating_capacity - $v->occupied_seats);
            $v->occupancy_rate = ($v->seating_capacity > 0) ? round(($v->occupied_seats / $v->seating_capacity) * 100, 1) : 0;
        }

        return $v;
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
