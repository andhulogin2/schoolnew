class Certificates extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Certificate_model');
    }

    public function index()
    {
        $certificates = $this->Certificate_model->get_all();

        $this->render('pages/certificates/index', array(
            'title'        => 'Certificates',
            'page_key'     => 'certificates',
            'certificates' => $certificates,
        ));
    }
}
