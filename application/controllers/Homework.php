<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homework extends MY_Controller {

    public function index()
    {
        $this->render('pages/homework/assignments', array(
            'title'    => 'Assignments',
            'page_key' => 'homework-assignments',
        ));
    }

    public function submissions()
    {
        $this->render('pages/homework/submissions', array(
            'title'    => 'Submissions',
            'page_key' => 'homework-submissions',
        ));
    }
}
