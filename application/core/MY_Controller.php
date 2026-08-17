<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller for every EduCore page that uses the main app shell
 * (sidebar + header, rendered client-side by assets/app.js).
 *
 * Usage from a child controller:
 *
 *   $this->render('pages/students/index', [
 *       'title'      => 'All Students',
 *       'page_key'   => 'students',
 *   ]);
 */
class MY_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $class = strtolower($this->router->fetch_class());
            if ($class !== 'auth') {
                redirect('auth/login');
            }
        }
    }

    /**
     * Renders templates/header -> $view -> templates/footer.
     *
     * @param string $view View file (relative to application/views), e.g. 'pages/dashboard'
     * @param array  $data Must include 'title' and 'page_key'. May include 'breadcrumb' (array).
     */
    public function render($view, array $data = array())
    {
        $data['title']    = isset($data['title']) ? $data['title'] : 'EduCore';
        $data['page_key'] = isset($data['page_key']) ? $data['page_key'] : '';
        $data['breadcrumb'] = isset($data['breadcrumb']) ? json_encode($data['breadcrumb']) : NULL;

        $current_user = $this->session->userdata('user');
        if (!$current_user) {
            $current_user = array(
                'name'     => $this->session->userdata('user_name') ?: 'Anjali Menon',
                'role'     => $this->session->userdata('user_role') ?: 'Super Admin',
                'email'    => $this->session->userdata('user_email') ?: 'anjali.menon@gmail.com',
                'initials' => $this->session->userdata('user_initials') ?: 'AM',
            );
        }
        $data['current_user'] = $current_user;

        $this->load->view('templates/header', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer', $data);
    }
}
