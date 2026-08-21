<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->require_auth();
        $this->load->model(array(
            'Student_model',
            'Staff_model',
            'Attendance_model',
            'Fee_model',
            'Exam_model',
            'Transport_assignment_model'
        ));
    }

    public function index()
    {
        $today = date('Y-m-d');
        $total_students = $this->db->where('status >=', 0)->count_all_results('tbl_students');
        $total_staff = $this->db->where('status >=', 0)->count_all_results('tbl_staff');
        
        $att_stats = $this->Attendance_model->get_dashboard_stats($today);
        $attendance_pct = !empty($att_stats['percentage']) ? $att_stats['percentage'] : 0;
        
        $fee_metrics = $this->Fee_model->get_dashboard_metrics();
        $fee_collected = !empty($fee_metrics['total_collected']) ? $fee_metrics['total_collected'] : 0;
        $fee_pending = !empty($fee_metrics['total_pending']) ? $fee_metrics['total_pending'] : 0;
        
        $total_exams = $this->db->count_all_results('tbl_exams');
        $transport_users = $this->db->count_all_results('tbl_student_transport_assignments');

        $stats = array(
            'total_students'  => $total_students,
            'total_staff'     => $total_staff,
            'attendance_pct'  => $attendance_pct,
            'fee_collected'   => $fee_collected,
            'fee_pending'     => $fee_pending,
            'total_exams'     => $total_exams,
            'transport_users' => $transport_users,
        );

        $this->render('pages/reports/index', array(
            'title'    => 'Reports Dashboard',
            'page_key' => 'reports',
            'stats'    => $stats,
        ));
    }
}
