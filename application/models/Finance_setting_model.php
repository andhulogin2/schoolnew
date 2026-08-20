<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_setting_model extends CI_Model {

    public function get_settings()
    {
        $settings = $this->db->get_where('tbl_finance_settings', array('setting_id' => 1))->row();
        if (!$settings) {
            $default = array(
                'setting_id'                 => 1,
                'currency_symbol'            => '₹',
                'currency_code'              => 'INR',
                'receipt_prefix'             => 'REC-2026-',
                'next_receipt_number'        => 1001,
                'receipt_footer'             => 'Thank you for your fee payment. This is a computer generated receipt.',
                'authorized_signature_title' => 'Accounts Officer',
                'allow_partial_payments'     => 1,
                'allow_overpayment'          => 0,
                'require_transaction_ref'    => 0,
                'grace_period_days'          => 7,
                'discount_approval_required' => 1,
                'reminder_template_upcoming' => 'Dear Parent, the fee amount of {amount} for {student_name} is due on {due_date}.',
                'reminder_template_overdue'  => 'Dear Parent, the fee amount of {amount} for {student_name} is overdue by {days_overdue} days.',
                'reminder_template_payment'  => 'Payment of {amount} has been received for {student_name}. Receipt No: {receipt_no}.',
            );
            $this->db->insert('tbl_finance_settings', $default);
            return (object)$default;
        }
        return $settings;
    }

    public function update_settings($data)
    {
        $this->db->where('setting_id', 1)->update('tbl_finance_settings', $data);
    }

    public function generate_next_receipt_number()
    {
        $settings = $this->get_settings();
        $prefix = $settings->receipt_prefix ?: 'REC-';
        $num = (int)$settings->next_receipt_number ?: 1001;

        $receipt_no = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);

        // Increment next receipt number
        $this->db->where('setting_id', 1)->update('tbl_finance_settings', array(
            'next_receipt_number' => $num + 1
        ));

        return $receipt_no;
    }
}
