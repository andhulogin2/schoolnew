<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document_category_model extends CI_Model {

    protected $table = 'tbl_document_categories';
    protected $primaryKey = 'category_id';

    public function get_all($status = null)
    {
        $this->db->from($this->table);
        if ($status !== null) {
            $this->db->where('status', $status);
        }
        $this->db->order_by('category_id', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }
}
