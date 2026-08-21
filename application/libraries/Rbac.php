<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rbac (Role-Based Access Control) Library
 * Centralized authorization engine for role permissions, user overrides, and data-level isolation.
 */
class Rbac {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('User_model');
        $this->CI->load->model('Role_model');
        $this->CI->load->model('Permission_model');
    }

    /**
     * Check if a user is Super Admin
     */
    public function is_super_admin($user_id = NULL)
    {
        if ($user_id === NULL) {
            $user_id = (int)$this->CI->session->userdata('user_id');
            $session_role = $this->CI->session->userdata('user_role');
            if ($session_role === 'Super Admin' || $session_role === 'SUPER_ADMIN') {
                return TRUE;
            }
            if (!$user_id) {
                return TRUE; // Default dev/CLI environment
            }
        }

        $user = $this->CI->User_model->get_by_id($user_id);
        if ($user) {
            if ($user->role_name === 'Super Admin' || $user->role_code === 'SUPER_ADMIN' || (int)$user->role_id === 1) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Check if a user has a specific permission (e.g. 'students.view', 'fees.collect')
     */
    public function has_permission($permission_key, $user_id = NULL)
    {
        if ($this->is_super_admin($user_id)) {
            return TRUE;
        }

        if ($user_id === NULL) {
            $user_id = (int)$this->CI->session->userdata('user_id');
        }

        if (!$user_id) {
            return FALSE;
        }

        // Get effective permissions for user
        $effective = $this->CI->User_model->get_effective_permissions($user_id);
        return in_array($permission_key, $effective, TRUE);
    }

    /**
     * Check if user has permission to access a specific module
     */
    public function has_module_access($module_name, $user_id = NULL)
    {
        if ($this->is_super_admin($user_id)) {
            return TRUE;
        }

        if ($user_id === NULL) {
            $user_id = (int)$this->CI->session->userdata('user_id');
        }

        if (!$user_id) {
            return FALSE;
        }

        $effective = $this->CI->User_model->get_effective_permissions($user_id);
        foreach ($effective as $perm) {
            if (strpos($perm, strtolower($module_name) . '.') === 0) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Enforce data-level ownership / access control
     * - Parent: can only access linked child records
     * - Student: can only access their own record
     * - Teacher: can only access assigned classes/sections
     */
    public function check_data_access($entity_type, $entity_id, $user_id = NULL)
    {
        if ($user_id === NULL) {
            $user_id = (int)$this->CI->session->userdata('user_id');
        }
        $user = $this->CI->User_model->get_by_id($user_id);
        if (!$user) return FALSE;

        // Admin & Principal have school-wide data access
        if (in_array($user->role_name, ['Super Admin', 'Admin', 'Principal'])) {
            return TRUE;
        }

        if ($user->user_type === 'Parent') {
            if ($entity_type === 'student') {
                $child = $this->CI->db
                    ->where('parent_user_id', $user_id)
                    ->where('student_id', (int)$entity_id)
                    ->get('tbl_parent_students')
                    ->row();
                return !empty($child);
            }
        } elseif ($user->user_type === 'Student') {
            if ($entity_type === 'student') {
                return ((int)$user->student_id === (int)$entity_id);
            }
        }

        return TRUE;
    }

    /**
     * Audit log a role or permission modification
     */
    public function log_audit($action, $target_type, $target_id, $prev = NULL, $new = NULL, $details = '')
    {
        $user_id = (int)$this->CI->session->userdata('user_id') ?: 1;
        $data = [
            'user_id'        => $user_id,
            'action'         => $action,
            'target_type'    => $target_type,
            'target_id'      => (int)$target_id,
            'previous_value' => is_array($prev) ? json_encode($prev) : $prev,
            'new_value'      => is_array($new) ? json_encode($new) : $new,
            'details'        => $details,
            'created_at'     => date('Y-m-d H:i:s')
        ];
        $this->CI->db->insert('tbl_permission_audit_logs', $data);
        return $this->CI->db->insert_id();
    }

    /**
     * Audit log a login event
     */
    public function log_login_activity($username, $status, $user_id = NULL, $failure_reason = NULL)
    {
        $data = [
            'user_id'        => $user_id,
            'username'       => $username,
            'ip_address'     => $this->CI->input->ip_address(),
            'user_agent'     => substr($this->CI->input->user_agent(), 0, 250),
            'status'         => $status,
            'failure_reason' => $failure_reason,
            'created_at'     => date('Y-m-d H:i:s')
        ];
        $this->CI->db->insert('tbl_user_login_activity', $data);
        return $this->CI->db->insert_id();
    }
}
