<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Communication_model extends CI_Model {

    protected $table = 'tbl_communication_messages';
    protected $primaryKey = 'message_id';

    public function get_dashboard_stats()
    {
        $total_notices = (int)$this->db->where('status', 'Published')->count_all_results('tbl_notices');
        $total_announcements = (int)$this->db->where('status', 'Published')->count_all_results('tbl_announcements');
        
        $total_sent = (int)$this->db->where_in('status', ['Sent', 'Delivered'])->count_all_results($this->table);
        $sms_sent = (int)$this->db->where('channel', 'SMS')->where_in('status', ['Sent', 'Delivered'])->count_all_results($this->table);
        $whatsapp_sent = (int)$this->db->where('channel', 'WhatsApp')->where_in('status', ['Sent', 'Delivered'])->count_all_results($this->table);
        $email_sent = (int)$this->db->where('channel', 'Email')->where_in('status', ['Sent', 'Delivered'])->count_all_results($this->table);
        $inapp_sent = (int)$this->db->where('channel', 'In-App')->where_in('status', ['Sent', 'Delivered'])->count_all_results($this->table);

        $pending = (int)$this->db->where_in('status', ['Scheduled', 'Processing', 'Draft'])->count_all_results($this->table);
        $failed = (int)$this->db->where('status', 'Failed')->count_all_results($this->table);

        $delivered = (int)$this->db->where('status', 'Delivered')->count_all_results($this->table);
        $delivery_pct = ($total_sent > 0) ? round(($delivered / $total_sent) * 100, 1) : 100;

        return (object)[
            'total_notices'       => $total_notices,
            'total_announcements' => $total_announcements,
            'total_sent'          => $total_sent,
            'sms_sent'            => $sms_sent,
            'whatsapp_sent'       => $whatsapp_sent,
            'email_sent'          => $email_sent,
            'inapp_sent'          => $inapp_sent,
            'pending'             => $pending,
            'failed'              => $failed,
            'delivered'           => $delivered,
            'delivery_pct'        => $delivery_pct
        ];
    }

    public function parse_template($template_str, $vars = array())
    {
        $defaults = [
            '{school_name}' => 'EduCore Model School',
            '{date}'        => date('d M Y'),
            '{time}'        => date('h:i A')
        ];
        $merged = array_merge($defaults, $vars);

        foreach ($merged as $key => $val) {
            $template_str = str_ireplace($key, (string)$val, $template_str);
        }
        return $template_str;
    }

    public function get_templates($filters = array())
    {
        $this->db->order_by('template_name', 'ASC');
        if (!empty($filters['channel'])) $this->db->where('channel', $filters['channel']);
        if (!empty($filters['type'])) $this->db->where('communication_type', $filters['type']);
        if (isset($filters['status'])) $this->db->where('status', $filters['status']);
        return $this->db->get('tbl_communication_templates')->result();
    }

    public function get_template_by_id($id)
    {
        return $this->db->where('template_id', $id)->get('tbl_communication_templates')->row();
    }

    public function insert_template($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_communication_templates', $data);
        return $this->db->insert_id();
    }

    public function update_template($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('template_id', $id)->update('tbl_communication_templates', $data);
    }

    public function delete_template($id)
    {
        return $this->db->where('template_id', $id)->delete('tbl_communication_templates');
    }

    public function dispatch_message($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        if (empty($data['sent_at']) && ($data['status'] ?? '') === 'Sent') {
            $data['sent_at'] = date('Y-m-d H:i:s');
            $data['delivered_at'] = date('Y-m-d H:i:s'); // In simulated local mode
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_messages($filters = array(), $limit = 50, $offset = 0)
    {
        $this->db
            ->select('m.*, t.template_name, s.full_name as sender_name')
            ->from('tbl_communication_messages m')
            ->join('tbl_communication_templates t', 't.template_id = m.template_id', 'left')
            ->join('tbl_staff s', 's.staff_id = m.sender_id', 'left')
            ->order_by('m.created_at', 'DESC');

        if (!empty($filters['channel'])) $this->db->where('m.channel', $filters['channel']);
        if (!empty($filters['status'])) $this->db->where('m.status', $filters['status']);
        if (!empty($filters['source_module'])) $this->db->where('m.source_module', $filters['source_module']);
        if (!empty($filters['date_from'])) $this->db->where('m.created_at >=', $filters['date_from'] . ' 00:00:00');
        if (!empty($filters['date_to'])) $this->db->where('m.created_at <=', $filters['date_to'] . ' 23:59:59');

        if (!empty($filters['search'])) {
            $q = $filters['search'];
            $this->db->group_start()
                ->like('m.recipient_name', $q)
                ->or_like('m.recipient_contact', $q)
                ->or_like('m.subject', $q)
                ->or_like('m.message', $q)
            ->group_end();
        }

        if ($limit) $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    public function get_scheduled_messages($limit = 50)
    {
        return $this->db
            ->select('m.*, t.template_name, s.full_name as sender_name')
            ->from('tbl_communication_messages m')
            ->join('tbl_communication_templates t', 't.template_id = m.template_id', 'left')
            ->join('tbl_staff s', 's.staff_id = m.sender_id', 'left')
            ->where_in('m.status', ['Scheduled', 'Processing'])
            ->order_by('m.scheduled_at', 'ASC')
            ->limit($limit)
            ->get()
            ->result();
    }

    public function cancel_scheduled($id)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, [
            'status'     => 'Cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
