<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'tbl_users';
    protected $primaryKey = 'user_id';

    public function get_all(array $filters = array())
    {
        $this->db
            ->select('u.*, r.role_name, r.role_code, r.user_type as role_user_type, s.full_name as staff_name, st.first_name as student_first_name, st.last_name as student_last_name, st.admission_number')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->join('tbl_staff s', 's.staff_id = u.staff_id', 'left')
            ->join('tbl_students st', 'st.student_id = u.student_id', 'left');

        if (!empty($filters['role_id'])) {
            $this->db->where('u.role_id', (int)$filters['role_id']);
        }
        if (!empty($filters['user_type'])) {
            $this->db->where('u.user_type', $filters['user_type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('u.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start()
                ->like('u.name', $search)
                ->or_like('u.username', $search)
                ->or_like('u.email', $search)
                ->or_like('u.phone', $search)
                ->or_like('st.admission_number', $search)
            ->group_end();
        }

        return $this->db->order_by('u.user_id', 'ASC')->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('u.*, r.role_name, r.role_code, s.full_name as staff_name, s.employee_code, st.first_name as student_first_name, st.last_name as student_last_name, st.admission_number')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->join('tbl_staff s', 's.staff_id = u.staff_id', 'left')
            ->join('tbl_students st', 'st.student_id = u.student_id', 'left')
            ->where('u.user_id', $id)
            ->get()
            ->row();
    }

    public function get_by_username_or_email($identifier)
    {
        return $this->db
            ->select('u.*, r.role_name, r.role_code')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->group_start()
                ->where('u.email', $identifier)
                ->or_where('u.username', $identifier)
            ->group_end()
            ->get()
            ->row();
    }

    /**
     * Compute Effective Permissions for a user
     * Role Permissions + (User Overrides: Grant) - (User Overrides: Revoke)
     */
    public function get_effective_permissions($user_id)
    {
        $user = $this->get_by_id($user_id);
        if (!$user) return [];

        // 1. Get Base Role Permissions
        $role_perms = $this->db
            ->select('p.permission_key')
            ->from('tbl_role_permissions rp')
            ->join('tbl_permissions p', 'p.permission_id = rp.permission_id')
            ->where('rp.role_id', $user->role_id)
            ->get()
            ->result();
        $permissions = array_map(function($r) { return $r->permission_key; }, $role_perms);

        // 2. Apply User Overrides
        $overrides = $this->db
            ->select('p.permission_key, up.override_type')
            ->from('tbl_user_permissions up')
            ->join('tbl_permissions p', 'p.permission_id = up.permission_id')
            ->where('up.user_id', $user_id)
            ->get()
            ->result();

        foreach ($overrides as $ov) {
            if ($ov->override_type === 'Grant') {
                if (!in_array($ov->permission_key, $permissions)) {
                    $permissions[] = $ov->permission_key;
                }
            } elseif ($ov->override_type === 'Revoke') {
                $permissions = array_diff($permissions, [$ov->permission_key]);
            }
        }

        return array_values(array_unique($permissions));
    }

    public function get_user_overrides($user_id)
    {
        return $this->db
            ->select('up.*, p.permission_key, p.permission_name, p.module')
            ->from('tbl_user_permissions up')
            ->join('tbl_permissions p', 'p.permission_id = up.permission_id')
            ->where('up.user_id', $user_id)
            ->get()
            ->result();
    }

    public function set_user_override($user_id, $permission_id, $override_type)
    {
        $exists = $this->db
            ->where('user_id', $user_id)
            ->where('permission_id', $permission_id)
            ->get('tbl_user_permissions')
            ->row();

        if ($exists) {
            return $this->db
                ->where('id', $exists->id)
                ->update('tbl_user_permissions', ['override_type' => $override_type]);
        } else {
            return $this->db->insert('tbl_user_permissions', [
                'user_id'       => (int)$user_id,
                'permission_id' => (int)$permission_id,
                'override_type' => $override_type
            ]);
        }
    }

    public function remove_user_override($user_id, $permission_id)
    {
        return $this->db
            ->where('user_id', (int)$user_id)
            ->where('permission_id', (int)$permission_id)
            ->delete('tbl_user_permissions');
    }

    public function get_parent_children($parent_user_id)
    {
        return $this->db
            ->select('ps.*, s.first_name, s.last_name, s.admission_number, c.class_name, sec.section_name')
            ->from('tbl_parent_students ps')
            ->join('tbl_students s', 's.student_id = ps.student_id')
            ->leftJoin('tbl_classes c', 'c.class_id = s.class_id')
            ->leftJoin('tbl_sections sec', 'sec.section_id = s.section_id')
            ->where('ps.parent_user_id', (int)$parent_user_id)
            ->get()
            ->result();
    }

    public function link_parent_student($parent_user_id, $student_id, $rel = 'Parent')
    {
        return $this->db->query("INSERT IGNORE INTO tbl_parent_students (parent_user_id, student_id, relationship) VALUES (?, ?, ?)",
            [(int)$parent_user_id, (int)$student_id, $rel]);
    }

    public function get_dashboard_stats()
    {
        $row = $this->db->query("
            SELECT 
                COUNT(*) as total_users,
                SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_users,
                SUM(CASE WHEN status = 'Inactive' THEN 1 ELSE 0 END) as inactive_users,
                SUM(CASE WHEN status = 'Locked' THEN 1 ELSE 0 END) as locked_users,
                SUM(CASE WHEN status = 'Suspended' THEN 1 ELSE 0 END) as suspended_users,
                SUM(CASE WHEN user_type = 'Admin' THEN 1 ELSE 0 END) as admin_users,
                SUM(CASE WHEN user_type = 'Teacher' THEN 1 ELSE 0 END) as teacher_users,
                SUM(CASE WHEN user_type = 'Staff' OR user_type IN ('Accountant','Transport Manager','Receptionist','Librarian') THEN 1 ELSE 0 END) as staff_users,
                SUM(CASE WHEN user_type = 'Parent' THEN 1 ELSE 0 END) as parent_users,
                SUM(CASE WHEN user_type = 'Student' THEN 1 ELSE 0 END) as student_users
            FROM tbl_users
        ")->row();

        return (object)[
            'total_users'     => (int)($row->total_users ?? 0),
            'active_users'    => (int)($row->active_users ?? 0),
            'inactive_users'  => (int)($row->inactive_users ?? 0),
            'locked_users'    => (int)($row->locked_users ?? 0),
            'suspended_users' => (int)($row->suspended_users ?? 0),
            'admin_users'     => (int)($row->admin_users ?? 0),
            'teacher_users'   => (int)($row->teacher_users ?? 0),
            'staff_users'     => (int)($row->staff_users ?? 0),
            'parent_users'    => (int)($row->parent_users ?? 0),
            'student_users'   => (int)($row->student_users ?? 0)
        ];
    }

    /**
     * Safety Check: Ensure at least one active Admin remains
     */
    public function count_active_admins()
    {
        return (int)$this->db
            ->where('user_type', 'Admin')
            ->where('status', 'Active')
            ->count_all_results($this->table);
    }

    public function insert($data)
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function toggle_status($id)
    {
        $user = $this->get_by_id($id);
        if (!$user) return FALSE;

        // Last active Admin safety guard
        if ($user->user_type === 'Admin' && $user->status === 'Active') {
            if ($this->count_active_admins() <= 1) {
                return 'LAST_ADMIN';
            }
        }

        $new_status = ($user->status === 'Active') ? 'Inactive' : 'Active';
        return $this->db->where($this->primaryKey, $id)->update($this->table, ['status' => $new_status]);
    }

    public function unlock_user($id)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, [
            'status'                => 'Active',
            'failed_login_attempts' => 0,
            'locked_until'          => NULL
        ]);
    }

    public function get_login_activity($limit = 100)
    {
        return $this->db
            ->order_by('activity_id', 'DESC')
            ->limit($limit)
            ->get('tbl_user_login_activity')
            ->result();
    }

    public function get_audit_logs($limit = 100)
    {
        return $this->db
            ->select('pal.*, u.name as user_name, u.username')
            ->from('tbl_permission_audit_logs pal')
            ->join('tbl_users u', 'u.user_id = pal.user_id', 'left')
            ->order_by('pal.log_id', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
