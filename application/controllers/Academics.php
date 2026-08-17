class Academics extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Academic_year_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Subject_model');
        $this->load->model('Staff_model');
    }

    public function index()
    {
        redirect('academics/years');
    }

    public function years()
    {
        $years = $this->Academic_year_model->get_all();

        $this->render('pages/academics/years', array(
            'title'    => 'Academic Years',
            'page_key' => 'academic-years',
            'years'    => $years,
        ));
    }

    public function classes()
    {
        $year_id = $this->input->get('year_id');
        $classes = $this->Class_model->get_all($year_id);
        $years   = $this->Academic_year_model->get_all();

        $this->render('pages/academics/classes', array(
            'title'    => 'Classes',
            'page_key' => 'classes',
            'classes'  => $classes,
            'years'    => $years,
        ));
    }

    public function sections()
    {
        $class_id = $this->input->get('class_id');
        $sections = $this->Section_model->get_all($class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/academics/sections', array(
            'title'    => 'Sections',
            'page_key' => 'sections',
            'sections' => $sections,
            'classes'  => $classes,
        ));
    }

    public function subjects()
    {
        $class_id = $this->input->get('class_id');
        $subjects = $this->Subject_model->get_all($class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/academics/subjects', array(
            'title'    => 'Subjects',
            'page_key' => 'subjects',
            'subjects' => $subjects,
            'classes'  => $classes,
        ));
    }
}
