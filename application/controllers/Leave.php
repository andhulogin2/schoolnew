<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave extends MY_Controller {

    public function index()
    {
        $this->render('pages/leave/index', array(
            'title'    => 'Leave Management',
            'page_key' => 'leave-management',
        ));
    }
}
