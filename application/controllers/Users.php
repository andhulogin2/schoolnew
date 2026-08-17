<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Role_model');
    }

    public function index()
    {
        $users = $this->User_model->get_all();
        $roles = $this->Role_model->get_all();

        $this->render('pages/users/index', array(
            'title'    => 'User Management',
            'page_key' => 'user-management',
            'users'    => $users,
            'roles'    => $roles,
        ));
    }
}
