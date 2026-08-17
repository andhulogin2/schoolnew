class Students extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Academic_year_model');
    }

    public function index()
    {
        $class_id   = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        $status     = $this->input->get('status');
        $search     = $this->input->get('search');

        $students = $this->Student_model->get_all(array(
            'class_id'   => $class_id,
            'section_id' => $section_id,
            'status'     => $status,
            'search'     => $search,
        ));
        $classes  = $this->Class_model->get_all();
        $sections = $this->Section_model->get_all();

        $this->render('pages/students/index', array(
            'title'    => 'All Students',
            'page_key' => 'students',
            'students' => $students,
            'classes'  => $classes,
            'sections' => $sections,
        ));
    }

    public function add()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
            $this->form_validation->set_rules('admission_number', 'Admission Number', 'required|trim');

            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'admission_number' => $this->input->post('admission_number', TRUE),
                    'first_name'       => $this->input->post('first_name', TRUE),
                    'last_name'        => $this->input->post('last_name', TRUE),
                    'gender'           => $this->input->post('gender', TRUE),
                    'date_of_birth'    => $this->input->post('date_of_birth', TRUE) ?: date('Y-m-d'),
                    'blood_group'      => $this->input->post('blood_group', TRUE),
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'class_id'         => $this->input->post('class_id') ?: 1,
                    'section_id'       => $this->input->post('section_id') ?: 1,
                    'roll_number'      => $this->input->post('roll_number', TRUE),
                    'guardian_name'    => $this->input->post('guardian_name', TRUE),
                    'guardian_relation'=> $this->input->post('guardian_relation', TRUE),
                    'guardian_phone'   => $this->input->post('guardian_phone', TRUE),
                    'guardian_email'   => $this->input->post('guardian_email', TRUE),
                    'address'          => $this->input->post('address', TRUE),
                    'status'           => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                );
                $new_id = $this->Student_model->insert($data);
                $this->session->set_flashdata('success', 'Student admitted successfully.');
                redirect('students/profile/' . $new_id);
                return;
            }
        }

        $classes  = $this->Class_model->get_all();
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/add', array(
            'title'    => 'Add Student',
            'page_key' => 'student-add',
            'classes'  => $classes,
            'sections' => $sections,
            'years'    => $years,
        ));
    }

    public function id_cards()
    {
        $class_id = $this->input->get('class_id');
        $students = $this->Student_model->get_all(array(
            'class_id' => $class_id,
            'status'   => 1
        ));
        $classes  = $this->Class_model->get_all();

        $this->render('pages/students/id_cards', array(
            'title'    => 'Student ID Cards',
            'page_key' => 'student-id-cards',
            'students' => $students,
            'classes'  => $classes,
        ));
    }

    public function profile($student_id = NULL)
    {
        if (!$student_id) {
            $first = $this->Student_model->get_all();
            $student_id = !empty($first) ? $first[0]->student_id : 1;
        }

        $student = $this->Student_model->get_profile($student_id);

        $this->render('pages/students/profile', array(
            'title'      => 'Student Profile',
            'page_key'   => 'student-profile',
            'breadcrumb' => array('Students', 'Student Profile'),
            'student'    => $student,
            'student_id' => $student_id,
        ));
    }
}
