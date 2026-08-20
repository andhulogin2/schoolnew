<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_rule_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all($filters = [])
    {
        $this->db->select('r.*, t.template_name, t.template_code, t.message_template')
            ->from('tbl_notification_rules r')
            ->join('tbl_communication_templates t', 't.template_id = r.template_id', 'left');

        if (!empty($filters['source_module'])) {
            $this->db->where('r.source_module', $filters['source_module']);
        }
        if (!empty($filters['channel'])) {
            $this->db->where('r.channel', $filters['channel']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('r.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('r.rule_name', $filters['search'])
                ->or_like('r.event_name', $filters['search'])
                ->or_like('t.template_name', $filters['search'])
                ->group_end();
        }

        return $this->db->order_by('r.rule_id', 'DESC')->get()->result();
    }

    public function get_by_id($rule_id)
    {
        return $this->db->select('r.*, t.template_name, t.template_code, t.message_template')
            ->from('tbl_notification_rules r')
            ->join('tbl_communication_templates t', 't.template_id = r.template_id', 'left')
            ->where('r.rule_id', $rule_id)
            ->get()->row();
    }

    public function insert($data)
    {
        $this->db->insert('tbl_notification_rules', $data);
        return $this->db->insert_id();
    }

    public function update($rule_id, $data)
    {
        return $this->db->where('rule_id', $rule_id)->update('tbl_notification_rules', $data);
    }

    public function toggle_status($rule_id)
    {
        $r = $this->get_by_id($rule_id);
        if (!$r) return FALSE;
        $new_status = ($r->status === 'Active') ? 'Inactive' : 'Active';
        return $this->update($rule_id, ['status' => $new_status]);
    }
}
