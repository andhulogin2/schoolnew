<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_audit_model extends CI_Model {

    public function log($action, $entity_type, $entity_id, $details = '', $previous_val = null, $new_val = null)
    {
        $user_id = $this->session->userdata('user_id');
        $this->db->insert('tbl_finance_audit_logs', array(
            'user_id'        => $user_id,
            'action'         => $action,
            'entity_type'    => $entity_type,
            'entity_id'      => $entity_id,
            'details'        => $details,
            'previous_value' => is_array($previous_val) ? json_encode($previous_val) : $previous_val,
            'new_value'      => is_array($new_val) ? json_encode($new_val) : $new_val,
            'created_at'     => date('Y-m-d H:i:s')
        ));
    }

    public function get_logs($limit = 50)
    {
        return $this->db->select('al.*, u.full_name as user_name')
                        ->from('tbl_finance_audit_logs al')
                        ->join('tbl_users u', 'u.user_id = al.user_id', 'left')
                        ->order_by('al.log_id', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result();
    }
}
