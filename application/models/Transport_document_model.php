<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport_document_model extends CI_Model {

    protected $table = 'tbl_transport_documents';
    protected $primaryKey = 'document_id';

    public function get_all($entity_type = NULL, $entity_id = NULL)
    {
        $this->db
            ->select('td.*, v.vehicle_number, v.registration_number, d.driver_name')
            ->from('tbl_transport_documents td')
            ->join('tbl_vehicles v', "v.vehicle_id = td.entity_id AND td.entity_type = 'Vehicle'", 'left')
            ->join('tbl_transport_drivers d', "d.driver_id = td.entity_id AND td.entity_type = 'Driver'", 'left')
            ->order_by('td.expiry_date', 'ASC');

        if ($entity_type) $this->db->where('td.entity_type', $entity_type);
        if ($entity_id) $this->db->where('td.entity_id', $entity_id);

        $docs = $this->db->get()->result();
        $today = new DateTime();

        foreach ($docs as &$doc) {
            if ($doc->expiry_date) {
                $expiry = new DateTime($doc->expiry_date);
                $diff = (int)$today->diff($expiry)->format('%r%a');
                $doc->days_to_expiry = $diff;
                if ($diff < 0) {
                    $doc->status = 'Expired';
                } elseif ($diff <= 30) {
                    $doc->status = 'Expiring Soon';
                } else {
                    $doc->status = 'Active';
                }
            } else {
                $doc->status = 'Active';
                $doc->days_to_expiry = 999;
            }
        }

        return $docs;
    }

    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->where($this->primaryKey, $id)->update($this->table, ['is_deleted' => 'y']);
    }
}
