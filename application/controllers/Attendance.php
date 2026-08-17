<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Academic_year_model');
    }

    public function index()
    {
        $date       = $this->input->get('date') ?: date('Y-m-d');
        $class_id   = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');

        if ($this->input->method() === 'post') {
            $post_attendance = $this->input->post('attendance'); // student_id => status
            $post_date       = $this->input->post('date') ?: date('Y-m-d');
            $active_year     = $this->Academic_year_model->get_active();
            $year_id         = $active_year ? $active_year->academic_year_id : 1;

            if (is_array($post_attendance)) {
                foreach ($post_attendance as $st_id => $st_status) {
                    $this->Attendance_model->mark_student_attendance(
                        $st_id,
                        $year_id,
                        $class_id ?: 1,
                        $section_id ?: 1,
                        $post_date,
                        $st_status
                    );
                }
                $this->session->set_flashdata('success', 'Attendance marked successfully for ' . date('d M Y', strtotime($post_date)) . '.');
            }
            redirect('attendance?date=' . $post_date . ($class_id ? '&class_id=' . $class_id : '') . ($section_id ? '&section_id=' . $section_id : ''));
            return;
        }

        $students = $this->Attendance_model->get_daily_sheet($date, $class_id, $section_id);
        $classes  = $this->Class_model->get_all();
        $sections = $this->Section_model->get_all();
        $summary  = $this->Attendance_model->get_today_summary($date);

        $this->render('pages/attendance/daily', array(
            'title'      => 'Daily Attendance',
            'page_key'   => 'attendance-daily',
            'students'   => $students,
            'classes'    => $classes,
            'sections'   => $sections,
            'summary'    => $summary,
            'date'       => $date,
            'class_id'   => $class_id,
            'section_id' => $section_id,
        ));
    }

    public function reports()
    {
        $class_id = $this->input->get('class_id');
        $reports  = $this->Attendance_model->get_reports_summary(NULL, $class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/attendance/reports', array(
            'title'    => 'Attendance Reports',
            'page_key' => 'attendance-reports',
            'reports'  => $reports,
            'classes'  => $classes,
        ));
    }
}
