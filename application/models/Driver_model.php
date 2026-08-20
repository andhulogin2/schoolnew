<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver_model extends CI_Model {

    protected $table = 'tbl_transport_drivers';
    protected $primaryKey = 'driver_id';

    public function get_all($active_only = FALSE)
    {
        $this->db
            ->select('d.*, v.vehicle_number, v.registration_number, v.vehicle_type, r.route_name, r.route_code, s.employee_code')
            ->from('tbl_transport_drivers d')
            ->join('tbl_vehicles v', 'v.vehicle_id = d.assigned_vehicle_id', 'left')
            ->join('tbl_transport_routes r', 'r.assigned_driver_id = d.driver_id', 'left')
            ->join('tbl_staff s', 's.staff_id = d.staff_id', 'left')
            ->order_by('d.driver_name', 'ASC');

        if ($active_only) {
            $this->db->where('d.status', 'Active');
        }

        $drivers = $this->db->get()->result();
        $today = new DateTime();

        foreach ($drivers as &$d) {
            $expiry = new DateTime($d->license_expiry_date);
            $diff = (int)$today->diff($expiry)->format('%r%a');
            $d->days_to_expiry = $diff;
            if ($diff < 0) {
                $d->license_status = 'Expired';
            } elseif ($diff <= 30) {
                $d->license_status = 'Expiring Soon';
            } else {
                $d->license_status = 'Valid';
            }
        }

        return $drivers;
    }

    public function get_by_id($id)
    {
        $d = $this->db
            ->select('d.*, v.vehicle_number, v.registration_number, v.vehicle_type, v.seating_capacity, r.route_name, r.route_code, s.employee_code, s.email')
            ->from('tbl_transport_drivers d')
            ->join('tbl_vehicles v', 'v.vehicle_id = d.assigned_vehicle_id', 'left')
            ->join('tbl_transport_routes r', 'r.assigned_driver_id = d.driver_id', 'left')
            ->join('tbl_staff s', 's.staff_id = d.staff_id', 'left')
            ->where('d.' . $this->primaryKey, $id)
            ->get()
            ->row();

        if ($d) {
            $today = new DateTime();
            $expiry = new DateTime($d->license_expiry_date);
            $diff = (int)$today->diff($expiry)->format('%r%a');
            $d->days_to_expiry = $diff;
            if ($diff < 0) {
                $d->license_status = 'Expired';
            } elseif ($diff <= 30) {
                $d->license_status = 'Expiring Soon';
            } else {
                $d->license_status = 'Valid';
            }
        }

        return $d;
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
        return $this->db->where($this->primaryKey, $id)->delete($this->table);
    }
}
