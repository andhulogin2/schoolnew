<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notice_model extends CI_Model {

    protected $table = 'tbl_notices';
    protected $primaryKey = 'notice_id';

    public function get_all()
    {
        return $this->db
            ->select('n.*, n.posted_by AS posted_by_name, n.notice_date AS publish_date, n.notice_date AS date')
            ->from('tbl_notices n')
            ->where('n.status', 1)
            ->order_by('n.notice_date', 'DESC')
            ->get()
            ->result();
    }

    public function get_recent($limit = 3)
    {
        return $this->db
            ->select('n.*, n.posted_by AS posted_by_name, n.notice_date AS publish_date, n.notice_date AS date')
            ->from('tbl_notices n')
            ->where('n.status', 1)
            ->order_by('n.notice_date', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('n.*, n.posted_by AS posted_by_name, n.notice_date AS publish_date, n.notice_date AS date')
            ->from('tbl_notices n')
            ->where('n.' . $this->primaryKey, $id)
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
