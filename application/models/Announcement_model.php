<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcement_model extends CI_Model {

    protected $table = 'tbl_announcements';
    protected $primaryKey = 'announcement_id';

    public function get_all($filters = array(), $limit = NULL, $offset = 0)
    {
        $this->db
            ->select('a.*, a.announcement_date AS date')
            ->from('tbl_announcements a')
            ->order_by('a.announcement_date', 'DESC')
            ->order_by('a.announcement_id', 'DESC');

        if (!empty($filters['status'])) {
            $this->db->where('a.status', $filters['status']);
        } else {
            $this->db->where_in('a.status', ['Published', 'Scheduled']);
        }

        if (!empty($filters['priority'])) $this->db->where('a.priority', $filters['priority']);
        if (!empty($filters['audience'])) $this->db->where('a.audience', $filters['audience']);

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $this->db->group_start()
                ->like('a.title', $s)
                ->or_like('a.content', $s)
            ->group_end();
        }

        if ($limit) $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    public function get_recent($limit = 5)
    {
        return $this->db
            ->select('a.*, a.announcement_date AS date')
            ->from('tbl_announcements a')
            ->where('a.status', 'Published')
            ->order_by('a.announcement_date', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, ['is_deleted' => 'y']);
    }

    public function archive($id)
    {
        return $this->update($id, ['status' => 'Archived']);
    }
}
