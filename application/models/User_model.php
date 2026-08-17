<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'tbl_users';
    protected $primaryKey = 'user_id';

    public function get_all()
    {
        return $this->db
            ->select('u.*, r.role_name')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->where('u.status', 1)
            ->order_by('u.user_id', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('u.*, r.role_name')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->where('u.user_id', $id)
            ->get()
            ->row();
    }

    public function get_by_email($email)
    {
        return $this->db
            ->select('u.*, r.role_name')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->where('u.email', $email)
            ->where('u.status', 1)
            ->get()
            ->row();
    }

    public function verify_credentials($email_or_username, $password)
    {
        $user = $this->db
            ->select('u.*, r.role_name')
            ->from('tbl_users u')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->where('u.email', $email_or_username)
            ->where('u.status', 1)
            ->get()
            ->row();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return FALSE;
    }

    public function insert($data)
    {
        if (isset($data['password'])) {
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

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, array('status' => 0));
    }
}
