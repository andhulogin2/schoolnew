<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_discount_model extends CI_Model {

    public function get_all($active_only = false)
    {
        $this->db->select('*')->from('tbl_fee_discounts');
        if ($active_only) {
            $this->db->where('status', 1);
        }
        return $this->db->order_by('name', 'ASC')->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('tbl_fee_discounts', array('discount_id' => $id))->row();
    }

    public function calculate_discount_amount($discount_id, $base_amount)
    {
        $d = $this->get_by_id($discount_id);
        if (!$d || $d->status != 1 || $base_amount <= 0) {
            return 0.00;
        }

        if ($d->discount_type === 'Percentage') {
            $amount = ($base_amount * (float)$d->discount_value) / 100.0;
        } else {
            $amount = (float)$d->discount_value;
        }

        if ($d->max_discount !== null && (float)$d->max_discount > 0 && $amount > (float)$d->max_discount) {
            $amount = (float)$d->max_discount;
        }

        return min($amount, $base_amount);
    }

    public function save($data, $id = 0)
    {
        if ($id > 0) {
            $this->db->where('discount_id', $id)->update('tbl_fee_discounts', $data);
            return $id;
        } else {
            $this->db->insert('tbl_fee_discounts', $data);
            return $this->db->insert_id();
        }
    }

    public function delete($id)
    {
        return $this->db->delete('tbl_fee_discounts', array('discount_id' => $id));
    }
}
