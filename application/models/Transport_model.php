<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport_model extends CI_Model {

    public function get_dashboard_stats()
    {
        $total_vehicles = (int)$this->db->count_all_results('tbl_vehicles');
        $active_vehicles = (int)$this->db->where('status', 'Active')->count_all_results('tbl_vehicles');
        $inactive_vehicles = (int)$this->db->where('status !=', 'Active')->count_all_results('tbl_vehicles');
        $total_drivers = (int)$this->db->count_all_results('tbl_transport_drivers');
        $active_routes = (int)$this->db->where('status', 'Active')->count_all_results('tbl_transport_routes');
        $total_stops = (int)$this->db->count_all_results('tbl_route_stops');
        $students_using_transport = (int)$this->db->where('status', 'Active')->count_all_results('tbl_student_transport_assignments');
        $vehicles_maintenance = (int)$this->db->where('status', 'Maintenance')->count_all_results('tbl_vehicles');

        // Total capacity & occupancy
        $this->db->select_sum('seating_capacity');
        $this->db->where('status', 'Active');
        $cap_row = $this->db->get('tbl_vehicles')->row();
        $total_capacity = (int)($cap_row->seating_capacity ?? 0);

        // Pending fees sum for transport users
        $pending_fees = 0.0;
        $this->db->select_sum('due_amount');
        $this->db->where('payment_status !=', 'Paid');
        $due_row = $this->db->get('tbl_student_fees')->row();
        $pending_fees = (float)($due_row->due_amount ?? 0.0);

        return (object)[
            'total_vehicles'           => $total_vehicles,
            'active_vehicles'          => $active_vehicles,
            'inactive_vehicles'        => $inactive_vehicles,
            'total_drivers'            => $total_drivers,
            'active_routes'            => $active_routes,
            'total_stops'              => $total_stops,
            'students_using_transport' => $students_using_transport,
            'vehicles_maintenance'     => $vehicles_maintenance,
            'total_capacity'           => $total_capacity,
            'pending_fees'             => $pending_fees
        ];
    }
}
