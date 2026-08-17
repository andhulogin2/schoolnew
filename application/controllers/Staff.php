class Staff extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Staff_model');
        $this->load->model('Department_model');
        $this->load->model('Designation_model');
    }

    public function index()
    {
        $dept_id = $this->input->get('department_id');
        $desig_id = $this->input->get('designation_id');
        $search = $this->input->get('search');

        $staff = $this->Staff_model->get_all(array(
            'department_id'  => $dept_id,
            'designation_id' => $desig_id,
            'search'         => $search,
        ));
        $departments = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/index', array(
            'title'        => 'All Staff',
            'page_key'     => 'staff',
            'staff'        => $staff,
            'departments'  => $departments,
            'designations' => $designations,
        ));
    }

    public function teachers()
    {
        $subject = $this->input->get('subject');
        $teachers = $this->Staff_model->get_teachers(array(
            'subject_name' => $subject,
        ));

        $this->render('pages/staff/teachers', array(
            'title'      => 'Teachers',
            'page_key'   => 'teachers',
            'breadcrumb' => array('Staff', 'Teachers'),
            'teachers'   => $teachers,
        ));
    }

    public function departments()
    {
        $departments = $this->Department_model->get_all();

        $this->render('pages/staff/departments', array(
            'title'       => 'Departments',
            'page_key'    => 'departments',
            'breadcrumb'  => array('Staff', 'Departments'),
            'departments' => $departments,
        ));
    }

    public function designations()
    {
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/designations', array(
            'title'        => 'Designations',
            'page_key'     => 'designations',
            'breadcrumb'   => array('Staff', 'Designations'),
            'designations' => $designations,
        ));
    }
}
