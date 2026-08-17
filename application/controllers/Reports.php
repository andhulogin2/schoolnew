<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function index()
    {
        $this->render('pages/reports/index', array(
            'title'    => 'Reports',
            'page_key' => 'reports',
        ));
    }
}
