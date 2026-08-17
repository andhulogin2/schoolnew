class Communication extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Notice_model');
        $this->load->model('Announcement_model');
    }

    public function index()
    {
        $notices = $this->Notice_model->get_all();

        $this->render('pages/communication/notices', array(
            'title'    => 'Notices',
            'page_key' => 'notices',
            'notices'  => $notices,
        ));
    }

    public function announcements()
    {
        $announcements = $this->Announcement_model->get_all();

        $this->render('pages/communication/announcements', array(
            'title'         => 'Announcements',
            'page_key'      => 'announcements',
            'breadcrumb'    => array('Communication', 'Announcements'),
            'announcements' => $announcements,
        ));
    }
}
