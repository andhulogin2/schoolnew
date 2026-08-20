<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_queue_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_queue($filters = [])
    {
        $this->db->select('m.*, t.template_name')
            ->from('tbl_communication_messages m')
            ->join('tbl_communication_templates t', 't.template_id = m.template_id', 'left');

        if (!empty($filters['status'])) {
            $this->db->where('m.status', $filters['status']);
        } else {
            $this->db->where_in('m.status', ['Pending', 'Scheduled', 'Processing', 'Failed']);
        }

        if (!empty($filters['channel'])) {
            $this->db->where('m.channel', $filters['channel']);
        }
        if (!empty($filters['priority'])) {
            $this->db->where('m.priority', $filters['priority']);
        }
        if (!empty($filters['source_module'])) {
            $this->db->where('m.source_module', $filters['source_module']);
        }

        return $this->db->order_by('m.message_id', 'DESC')->get()->result();
    }

    public function get_failed($filters = [])
    {
        $this->db->select('m.*, t.template_name')
            ->from('tbl_communication_messages m')
            ->join('tbl_communication_templates t', 't.template_id = m.template_id', 'left')
            ->where('m.status', 'Failed');

        if (!empty($filters['channel'])) {
            $this->db->where('m.channel', $filters['channel']);
        }
        if (!empty($filters['source_module'])) {
            $this->db->where('m.source_module', $filters['source_module']);
        }

        return $this->db->order_by('m.message_id', 'DESC')->get()->result();
    }

    public function process_item($message_id)
    {
        return $this->db->where('message_id', $message_id)->update('tbl_communication_messages', [
            'status'       => 'Delivered',
            'sent_at'      => date('Y-m-d H:i:s'),
            'delivered_at' => date('Y-m-d H:i:s')
        ]);
    }
}
