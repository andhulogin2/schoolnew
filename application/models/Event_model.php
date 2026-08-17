<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_model extends CI_Model {

    protected $table = 'tbl_events';
    protected $primaryKey = 'event_id';

    public function get_all()
    {
        return $this->db
            ->where('status', 1)
            ->order_by('event_date', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_upcoming($limit = 3)
    {
        return $this->db
            ->where('status', 1)
            ->where('event_date >=', date('Y-m-d'))
            ->order_by('event_date', 'ASC')
            ->limit($limit)
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->get($this->table)
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
