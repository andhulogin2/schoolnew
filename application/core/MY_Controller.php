<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller for every EduCore page that uses the main app shell
 * (sidebar + header, rendered client-side by assets/app.js).
 */
class MY_Controller extends CI_Controller {

    public $current_user;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Rbac');
        $this->load->model('User_model');

        // Enforce authentication — redirect to login if no valid session exists
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user')) {
            redirect('auth/login');
            exit;
        }

        $user_obj = (object)$this->session->userdata('user');

        $this->current_user = $user_obj;
    }

    /**
     * Require authenticated session
     */
    public function require_auth()
    {
        if (!$this->session->userdata('logged_in') || !$this->session->userdata('user')) {
            redirect('auth/login');
            exit;
        }
    }

    /**
     * Enforce backend authorization for protected controller actions
     */
    public function require_permission($permission_key)
    {
        if ($this->rbac->is_super_admin()) {
            return;
        }

        if (!$this->rbac->has_permission($permission_key)) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => "Access denied. Required permission: {$permission_key}"]))
                    ->_display();
                exit;
            } else {
                $this->session->set_flashdata('error', "Access Denied: You do not have the required permission ({$permission_key}).");
                redirect('unauthorized');
            }
        }
    }

    /**
     * Renders templates/header -> $view -> templates/footer.
     */
    public function render($view, array $data = array())
    {
        $data['title']        = isset($data['title']) ? $data['title'] : 'EduCore';
        $data['page_key']     = isset($data['page_key']) ? $data['page_key'] : '';
        $data['breadcrumb']   = isset($data['breadcrumb']) ? json_encode($data['breadcrumb']) : NULL;
        $data['current_user'] = $this->current_user;
        $data['is_super_admin'] = $this->rbac->is_super_admin($this->current_user->user_id ?? NULL);

        // Pass effective permissions to view
        $uid = (int)($this->current_user->user_id ?? 1);
        $data['effective_permissions'] = $this->User_model->get_effective_permissions($uid);

        $this->load->view('templates/header', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer', $data);
    }
}
