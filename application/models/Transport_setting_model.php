<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport_setting_model extends CI_Model {

    protected $table = 'tbl_transport_settings';
    protected $primaryKey = 'setting_id';

    public function get_settings()
    {
        $settings = $this->db->where($this->primaryKey, 1)->get($this->table)->row();
        if (!$settings) {
            return (object)[
                'setting_id'                   => 1,
                'enable_transport'             => 1,
                'enforce_capacity'             => 1,
                'allow_capacity_override'      => 0,
                'default_monthly_fee'          => 1500.00,
                'fee_frequency'                => 'Monthly',
                'maintenance_reminder_days'    => 15,
                'document_expiry_reminder_days'=> 30,
                'driver_license_reminder_days' => 30,
                'allow_one_way'                => 1,
                'allow_pickup_only'            => 1,
                'allow_drop_only'              => 1,
                'allow_bulk_assignment'        => 1
            ];
        }
        return $settings;
    }

    public function update_settings($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, 1)->update($this->table, $data);
    }

    public function get_audit_logs($limit = 30)
    {
        return $this->db
            ->select('log.*, s.full_name as user_name')
            ->from('tbl_transport_audit_logs log')
            ->join('tbl_staff s', 's.staff_id = log.user_id', 'left')
            ->order_by('log.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
