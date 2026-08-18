<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academics extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Academic_year_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Subject_model');
        $this->load->model('Staff_model');
        $this->load->model('Class_teacher_model');
        $this->load->model('Subject_teacher_model');
        $this->load->model('Period_model');
        $this->load->model('Timetable_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        redirect('academics/years');
    }

    /* =========================================================================
       1. Academic Year Management
       ========================================================================= */
    public function years()
    {
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->form_validation->set_rules('year_name', 'Year Name', 'required|trim');
                $this->form_validation->set_rules('start_date', 'Start Date', 'required');
                $this->form_validation->set_rules('end_date', 'End Date', 'required');

                if ($this->form_validation->run() === TRUE) {
                    $isActive = ($this->input->post('is_active') == '1') ? 1 : 0;
                    $this->Academic_year_model->insert(array(
                        'year_name'  => $this->input->post('year_name'),
                        'start_date' => $this->input->post('start_date'),
                        'end_date'   => $this->input->post('end_date'),
                        'is_active'  => $isActive,
                        'status'     => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Academic Year added successfully!');
                }
            } elseif ($action === 'edit') {
                $id = $this->input->post('academic_year_id');
                $isActive = ($this->input->post('is_active') == '1') ? 1 : 0;
                $this->Academic_year_model->update($id, array(
                    'year_name'  => $this->input->post('year_name'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date'   => $this->input->post('end_date'),
                    'is_active'  => $isActive,
                    'updated_at' => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Academic Year updated successfully!');
            }
            redirect('academics/years');
        }

        $years = $this->Academic_year_model->get_all();

        $this->render('pages/academics/years', array(
            'title'      => 'Academic Years',
            'page_key'   => 'academic-years',
            'breadcrumb' => array('Academic Management', 'Academic Year'),
            'years'      => $years,
        ));
    }

    public function set_active_year($id = NULL)
    {
        if (!empty($id)) {
            $this->Academic_year_model->set_active($id);
            $this->session->set_flashdata('success', 'Active academic session updated!');
        }
        redirect('academics/years');
    }

    public function delete_year($id = NULL)
    {
        if (!empty($id)) {
            $this->Academic_year_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Academic Year deactivated.');
        }
        redirect('academics/years');
    }

    /* =========================================================================
       2. Classes Management
       ========================================================================= */
    public function classes()
    {
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->Class_model->insert(array(
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'class_name'       => $this->input->post('class_name'),
                    'class_code'       => $this->input->post('class_code') ?: strtoupper(substr($this->input->post('class_name'), 0, 4)),
                    'capacity'         => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                    'status'           => 1,
                    'created_at'       => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Class added successfully!');
            } elseif ($action === 'edit') {
                $id = $this->input->post('class_id');
                $this->Class_model->update($id, array(
                    'class_name' => $this->input->post('class_name'),
                    'class_code' => $this->input->post('class_code'),
                    'capacity'   => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                    'updated_at' => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Class updated successfully!');
            }
            redirect('academics/classes');
        }

        $year_id = $this->input->get('year_id');
        $classes = $this->Class_model->get_all($year_id);
        $years   = $this->Academic_year_model->get_all();

        $this->render('pages/academics/classes', array(
            'title'      => 'Classes',
            'page_key'   => 'classes',
            'breadcrumb' => array('Academic Management', 'Classes'),
            'classes'    => $classes,
            'years'      => $years,
        ));
    }

    public function delete_class($id = NULL)
    {
        if (!empty($id)) {
            $this->Class_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Class record deactivated.');
        }
        redirect('academics/classes');
    }

    /* =========================================================================
       3. Sections / Divisions Management
       ========================================================================= */
    public function sections()
    {
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->Section_model->insert(array(
                    'class_id'     => $this->input->post('class_id'),
                    'section_name' => $this->input->post('section_name'),
                    'room_no'      => $this->input->post('room_no'),
                    'capacity'     => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                    'status'       => 1,
                    'created_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Section created successfully!');
            } elseif ($action === 'edit') {
                $id = $this->input->post('section_id');
                $this->Section_model->update($id, array(
                    'class_id'     => $this->input->post('class_id'),
                    'section_name' => $this->input->post('section_name'),
                    'room_no'      => $this->input->post('room_no'),
                    'capacity'     => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                    'updated_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Section updated successfully!');
            }
            redirect('academics/sections');
        }

        $class_id = $this->input->get('class_id');
        $sections = $this->Section_model->get_all($class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/academics/sections', array(
            'title'      => 'Sections / Divisions',
            'page_key'   => 'sections',
            'breadcrumb' => array('Academic Management', 'Sections / Divisions'),
            'sections'   => $sections,
            'classes'    => $classes,
        ));
    }

    public function delete_section($id = NULL)
    {
        if (!empty($id)) {
            $this->Section_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Section deactivated.');
        }
        redirect('academics/sections');
    }

    /* =========================================================================
       4. Subjects Management
       ========================================================================= */
    public function subjects()
    {
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->Subject_model->insert(array(
                    'class_id'     => $this->input->post('class_id') ?: NULL,
                    'subject_name' => $this->input->post('subject_name'),
                    'subject_code' => $this->input->post('subject_code') ?: strtoupper(substr($this->input->post('subject_name'), 0, 4)),
                    'subject_type' => $this->input->post('subject_type') ?: 'Core',
                    'status'       => 1,
                    'created_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Subject created successfully!');
            } elseif ($action === 'edit') {
                $id = $this->input->post('subject_id');
                $this->Subject_model->update($id, array(
                    'class_id'     => $this->input->post('class_id') ?: NULL,
                    'subject_name' => $this->input->post('subject_name'),
                    'subject_code' => $this->input->post('subject_code'),
                    'subject_type' => $this->input->post('subject_type') ?: 'Core',
                    'updated_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Subject updated successfully!');
            }
            redirect('academics/subjects');
        }

        $class_id = $this->input->get('class_id');
        $subjects = $this->Subject_model->get_all($class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/academics/subjects', array(
            'title'      => 'Subjects',
            'page_key'   => 'subjects',
            'breadcrumb' => array('Academic Management', 'Subjects'),
            'subjects'   => $subjects,
            'classes'    => $classes,
        ));
    }

    public function delete_subject($id = NULL)
    {
        if (!empty($id)) {
            $this->Subject_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Subject deactivated.');
        }
        redirect('academics/subjects');
    }

    /* =========================================================================
       5. Class Teachers Assignment
       ========================================================================= */
    public function class_teachers()
    {
        if ($this->input->method() === 'post') {
            $year_id    = $this->input->post('academic_year_id') ?: 1;
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $staff_id   = $this->input->post('staff_id');

            $res = $this->Class_teacher_model->assign($year_id, $class_id, $section_id, $staff_id);
            if ($res) {
                $this->session->set_flashdata('success', 'Class Teacher assigned successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to assign class teacher. Only teaching faculty can be assigned.');
            }
            redirect('academics/class_teachers');
        }

        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'staff_id'         => $this->input->get('staff_id'),
        );

        $assignments = $this->Class_teacher_model->get_all($filters);
        $years       = $this->Academic_year_model->get_all();
        $classes     = $this->Class_model->get_all();
        $sections    = $this->Section_model->get_all();
        $teachers    = $this->Staff_model->get_teachers();

        $this->render('pages/academics/class_teachers', array(
            'title'       => 'Class Teachers',
            'page_key'    => 'class-teachers',
            'breadcrumb'  => array('Academic Management', 'Class Teachers'),
            'assignments' => $assignments,
            'years'       => $years,
            'classes'     => $classes,
            'sections'    => $sections,
            'teachers'    => $teachers,
        ));
    }

    public function delete_class_teacher($id = NULL)
    {
        if (!empty($id)) {
            $this->Class_teacher_model->delete($id);
            $this->session->set_flashdata('success', 'Class teacher assignment removed.');
        }
        redirect('academics/class_teachers');
    }

    /* =========================================================================
       6. Subject Teachers Assignment
       ========================================================================= */
    public function subject_teachers()
    {
        if ($this->input->method() === 'post') {
            $year_id    = $this->input->post('academic_year_id') ?: 1;
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $subject_id = $this->input->post('subject_id');
            $staff_id   = $this->input->post('staff_id');

            $res = $this->Subject_teacher_model->assign($year_id, $class_id, $section_id, $subject_id, $staff_id);
            if ($res) {
                $this->session->set_flashdata('success', 'Subject Teacher assigned successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to assign subject teacher. Only teaching faculty can be assigned.');
            }
            redirect('academics/subject_teachers');
        }

        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'subject_id'       => $this->input->get('subject_id'),
            'staff_id'         => $this->input->get('staff_id'),
        );

        $assignments = $this->Subject_teacher_model->get_all($filters);
        $years       = $this->Academic_year_model->get_all();
        $classes     = $this->Class_model->get_all();
        $sections    = $this->Section_model->get_all();
        $subjects    = $this->Subject_model->get_all();
        $teachers    = $this->Staff_model->get_teachers();

        $this->render('pages/academics/subject_teachers', array(
            'title'       => 'Subject Teachers',
            'page_key'    => 'subject-teachers',
            'breadcrumb'  => array('Academic Management', 'Subject Teachers'),
            'assignments' => $assignments,
            'years'       => $years,
            'classes'     => $classes,
            'sections'    => $sections,
            'subjects'    => $subjects,
            'teachers'    => $teachers,
        ));
    }

    public function delete_subject_teacher($id = NULL)
    {
        if (!empty($id)) {
            $this->Subject_teacher_model->delete($id);
            $this->session->set_flashdata('success', 'Subject teacher assignment removed.');
        }
        redirect('academics/subject_teachers');
    }

    /* =========================================================================
       7. Timetable & Periods Management
       ========================================================================= */
    public function timetable()
    {
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'save_entry') {
                $ttId = $this->input->post('timetable_id');
                $entryData = array(
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'class_id'         => $this->input->post('class_id'),
                    'section_id'       => $this->input->post('section_id'),
                    'day'              => $this->input->post('day'),
                    'period_id'        => $this->input->post('period_id'),
                    'subject_id'       => $this->input->post('subject_id'),
                    'teacher_id'       => $this->input->post('teacher_id')
                );

                $result = $this->Timetable_model->save_entry($entryData, $ttId ?: NULL);
                if ($result['success']) {
                    $this->session->set_flashdata('success', 'Timetable period scheduled successfully!');
                } else {
                    $this->session->set_flashdata('error', 'Timetable Conflict: ' . $result['message']);
                }
            } elseif ($action === 'add_period') {
                $this->Period_model->insert(array(
                    'period_name'  => $this->input->post('period_name'),
                    'start_time'   => $this->input->post('start_time'),
                    'end_time'     => $this->input->post('end_time'),
                    'period_order' => $this->input->post('period_order') ? intval($this->input->post('period_order')) : 1,
                    'status'       => 1,
                    'created_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Period slot created successfully!');
            }
            redirect('academics/timetable?class_id=' . $this->input->post('class_id') . '&section_id=' . $this->input->post('section_id'));
        }

        $years    = $this->Academic_year_model->get_all();
        $classes  = $this->Class_model->get_all();
        $sections = $this->Section_model->get_all();
        $periods  = $this->Period_model->get_all();
        $subjects = $this->Subject_model->get_all();
        $teachers = $this->Staff_model->get_teachers();

        $selected_year    = $this->input->get('academic_year_id') ?: (isset($years[0]) ? $years[0]->academic_year_id : 1);
        $selected_class   = $this->input->get('class_id') ?: (isset($classes[0]) ? $classes[0]->class_id : 8);
        $selected_section = $this->input->get('section_id') ?: (isset($sections[0]) ? $sections[0]->section_id : 1);

        $entries = $this->Timetable_model->get_entries(array(
            'academic_year_id' => $selected_year,
            'class_id'         => $selected_class,
            'section_id'       => $selected_section
        ));

        // Build Day x Period matrix
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $grid = array();
        foreach ($days as $d) {
            $grid[$d] = array();
            foreach ($periods as $p) {
                $grid[$d][$p->period_id] = NULL;
            }
        }
        foreach ($entries as $e) {
            if (isset($grid[$e->day]) && array_key_exists($e->period_id, $grid[$e->day])) {
                $grid[$e->day][$e->period_id] = $e;
            }
        }

        $this->render('pages/academics/timetable', array(
            'title'            => 'Timetable',
            'page_key'         => 'timetable',
            'breadcrumb'       => array('Academic Management', 'Timetable'),
            'years'            => $years,
            'classes'          => $classes,
            'sections'         => $sections,
            'periods'          => $periods,
            'subjects'         => $subjects,
            'teachers'         => $teachers,
            'days'             => $days,
            'grid'             => $grid,
            'selected_year'    => $selected_year,
            'selected_class'   => $selected_class,
            'selected_section' => $selected_section,
        ));
    }

    public function delete_timetable_entry($id = NULL)
    {
        $class_id = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        if (!empty($id)) {
            $this->Timetable_model->delete_entry($id);
            $this->session->set_flashdata('success', 'Timetable entry removed.');
        }
        redirect('academics/timetable?class_id=' . $class_id . '&section_id=' . $section_id);
    }
}
