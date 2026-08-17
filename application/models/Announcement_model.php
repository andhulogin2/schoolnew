<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcement_model extends CI_Model {

    protected $table = 'tbl_announcements';
    protected $primaryKey = 'announcement_id';

    public function get_all()
    {
        return $this->db
            ->select('a.*, a.announcement_date AS publish_date, a.announcement_date AS date')
            ->from('tbl_announcements a')
            ->where('a.status >=', 0)
            ->order_by('a.announcement_date', 'DESC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('a.*, a.announcement_date AS publish_date, a.announcement_date AS date')
            ->from('tbl_announcements a')
            ->where('a.' . $this->primaryKey, $id)
            ->get()
            ->row();
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

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, array('status' => 0));
    }
}
