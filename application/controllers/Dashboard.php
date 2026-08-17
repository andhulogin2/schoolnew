class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');
        $this->load->model('Staff_model');
        $this->load->model('Class_model');
        $this->load->model('Attendance_model');
        $this->load->model('Fee_model');
        $this->load->model('Event_model');
        $this->load->model('Notice_model');
    }

    public function index()
    {
        $today = date('Y-m-d');
        $total_students = $this->Student_model->get_total_count();
        $total_teachers = $this->Staff_model->get_total_teachers();
        $total_staff    = $this->Staff_model->get_total_staff();
        $total_classes  = $this->Class_model->get_total_classes();
        $attendance_sum = $this->Attendance_model->get_today_summary($today);
        $fee_metrics    = $this->Fee_model->get_dashboard_metrics();
        $gender_dist    = $this->Student_model->get_gender_distribution();
        $class_dist     = $this->Student_model->get_class_distribution();
        $events         = $this->Event_model->get_upcoming(3);
        $notices        = $this->Notice_model->get_recent(3);

        $this->render('pages/dashboard', array(
            'title'          => 'Dashboard',
            'page_key'       => 'dashboard',
            'total_students' => $total_students,
            'total_teachers' => $total_teachers,
            'total_staff'    => $total_staff,
            'total_classes'  => $total_classes,
            'attendance'     => $attendance_sum,
            'fees'           => $fee_metrics,
            'gender'         => $gender_dist,
            'class_dist'     => $class_dist,
            'events'         => $events,
            'notices'        => $notices,
        ));
    }
}
