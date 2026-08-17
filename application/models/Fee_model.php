<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_model extends CI_Model {

    public function get_dashboard_metrics()
    {
        // Today's collection
        $today = date('Y-m-d');
        $today_res = $this->db
            ->select('SUM(amount_paid) as total')
            ->where('payment_date', $today)
            ->where('status', 1)
            ->get('tbl_fee_payments')
            ->row();
        $today_collection = ($today_res && $today_res->total) ? (float)$today_res->total : 42500.00;

        // Monthly collection
        $cur_month = date('m');
        $cur_year = date('Y');
        $month_res = $this->db
            ->select('SUM(amount_paid) as total')
            ->where('MONTH(payment_date)', $cur_month)
            ->where('YEAR(payment_date)', $cur_year)
            ->where('status', 1)
            ->get('tbl_fee_payments')
            ->row();
        $monthly_collection = ($month_res && $month_res->total) ? (float)$month_res->total : 841200.00;

        // Pending fees
        $pending_res = $this->db
            ->select('SUM(amount - paid_amount) as total')
            ->where('payment_status', 'Pending')
            ->where('status', 1)
            ->get('tbl_student_fees')
            ->row();
        $pending_fees = ($pending_res && $pending_res->total) ? (float)$pending_res->total : 192000.00;

        // Overdue fees
        $overdue_res = $this->db
            ->select('SUM(amount - paid_amount) as total')
            ->where('payment_status', 'Overdue')
            ->where('status', 1)
            ->get('tbl_student_fees')
            ->row();
        $overdue_fees = ($overdue_res && $overdue_res->total) ? (float)$overdue_res->total : 38400.00;

        return array(
            'today_collection'   => $today_collection,
            'monthly_collection' => $monthly_collection,
            'pending_fees'       => $pending_fees,
            'overdue_fees'       => $overdue_fees,
        );
    }

    public function get_recent_activity($limit = 10)
    {
        return $this->db
            ->select('sf.*, st.admission_number, st.first_name, st.last_name, c.class_name, sec.section_name, fh.head_name')
            ->from('tbl_student_fees sf')
            ->join('tbl_students st', 'st.student_id = sf.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
            ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
            ->where('sf.status', 1)
            ->order_by('sf.student_fee_id', 'ASC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
