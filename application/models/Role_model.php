<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    protected $table = 'tbl_roles';
    protected $primaryKey = 'role_id';

    public function get_all($include_inactive = FALSE)
    {
        if (!$include_inactive) {
            $this->db->where('status', 'Active');
        }
        return $this->db
            ->order_by('role_id', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_roles_with_counts()
    {
        return $this->db->query("
            SELECT r.*, 
                   COUNT(u.user_id) as total_users,
                   SUM(CASE WHEN u.status = 'Active' THEN 1 ELSE 0 END) as active_users,
                   SUM(CASE WHEN u.status != 'Active' THEN 1 ELSE 0 END) as inactive_users,
                   (SELECT COUNT(*) FROM tbl_role_permissions rp WHERE rp.role_id = r.role_id) as permission_count
            FROM tbl_roles r
            LEFT JOIN tbl_users u ON u.role_id = r.role_id
            GROUP BY r.role_id
            ORDER BY r.role_id ASC
        ")->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->get($this->table)
            ->row();
    }

    public function get_by_code($code)
    {
        return $this->db
            ->where('role_code', $code)
            ->get($this->table)
            ->row();
    }

    public function get_role_permission_ids($role_id)
    {
        $rows = $this->db
            ->select('permission_id')
            ->where('role_id', $role_id)
            ->get('tbl_role_permissions')
            ->result();
        return array_map(function($r) { return (int)$r->permission_id; }, $rows);
    }

    public function get_role_permission_keys($role_id)
    {
        $rows = $this->db
            ->select('p.permission_key')
            ->from('tbl_role_permissions rp')
            ->join('tbl_permissions p', 'p.permission_id = rp.permission_id')
            ->where('rp.role_id', $role_id)
            ->get()
            ->result();
        return array_map(function($r) { return $r->permission_key; }, $rows);
    }

    public function set_role_permissions($role_id, array $permission_ids)
    {
        $this->db->trans_start();
        $this->db->where('role_id', $role_id)->delete('tbl_role_permissions');

        foreach ($permission_ids as $pid) {
            $this->db->insert('tbl_role_permissions', [
                'role_id'       => (int)$role_id,
                'permission_id' => (int)$pid
            ]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function toggle_status($id)
    {
        $role = $this->get_by_id($id);
        if (!$role || $role->is_system) return FALSE; // Cannot deactivate protected system role
        $new_status = ($role->status === 'Active') ? 'Inactive' : 'Active';
        return $this->db->where($this->primaryKey, $id)->update($this->table, ['status' => $new_status]);
    }
}
