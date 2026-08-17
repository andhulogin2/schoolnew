<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport extends MY_Controller {

    public function index()
    {
        $this->render('pages/transport/index', array(
            'title'    => 'Transport',
            'page_key' => 'transport',
        ));
    }
}
