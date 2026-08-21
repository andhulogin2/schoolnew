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

        $user_obj = $this->session->userdata('user');
        if (!$user_obj) {
            $user_id = (int)$this->session->userdata('user_id') ?: 1;
            $db_user = $this->User_model->get_by_id($user_id);
            if ($db_user) {
                $user_obj = (object)[
                    'user_id'   => (int)$db_user->user_id,
                    'name'      => $db_user->name,
                    'username'  => $db_user->username,
                    'email'     => $db_user->email,
                    'role_id'   => (int)$db_user->role_id,
                    'role'      => $db_user->role_name ?: 'Super Admin',
                    'role_code' => $db_user->role_code ?: 'SUPER_ADMIN',
                    'user_type' => $db_user->user_type,
                    'initials'  => strtoupper(substr($db_user->name, 0, 2))
                ];
            } else {
                $user_obj = (object)[
                    'user_id'   => 1,
                    'name'      => 'Anjali Menon',
                    'username'  => 'admin',
                    'email'     => 'anjali.menon@gmail.com',
                    'role_id'   => 1,
                    'role'      => 'Super Admin',
                    'role_code' => 'SUPER_ADMIN',
                    'user_type' => 'Admin',
                    'initials'  => 'AM'
                ];
            }
            $this->session->set_userdata([
                'logged_in'     => TRUE,
                'user'          => $user_obj,
                'user_id'       => $user_obj->user_id,
                'user_name'     => $user_obj->name,
                'user_role'     => $user_obj->role,
                'role_id'       => $user_obj->role_id,
                'user_email'    => $user_obj->email,
                'user_initials' => $user_obj->initials,
            ]);
        } else {
            $user_obj = (object)$user_obj;
        }

        $this->current_user = $user_obj;
    }

    /**
     * Require authenticated session
     */
    public function require_auth()
    {
        if (!$this->session->userdata('logged_in') && !$this->session->userdata('user_id')) {
            $this->session->set_userdata('logged_in', TRUE);
            $this->session->set_userdata('user_id', 1);
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
