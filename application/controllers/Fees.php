class Fees extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Fee_model');
    }

    public function index()
    {
        $metrics = $this->Fee_model->get_dashboard_metrics();
        $recent_activity = $this->Fee_model->get_recent_activity(10);

        $this->render('pages/fees/dashboard', array(
            'title'           => 'Fee Dashboard',
            'page_key'        => 'fee-dashboard',
            'metrics'         => $metrics,
            'recent_activity' => $recent_activity,
        ));
    }

    public function structure()
    {
        $this->render('pages/fees/structure', array(
            'title'    => 'Fee Structure',
            'page_key' => 'fee-structure',
        ));
    }

    public function student_fees()
    {
        $this->render('pages/fees/student_fees', array(
            'title'    => 'Student Fees',
            'page_key' => 'student-fees',
        ));
    }

    public function collection()
    {
        $this->render('pages/fees/collection', array(
            'title'    => 'Fee Collection',
            'page_key' => 'fee-collection',
        ));
    }
}
