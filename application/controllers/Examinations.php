<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Examinations extends MY_Controller {

    public function index()
    {
        $this->render('pages/examinations/exams', array(
            'title'    => 'Exams',
            'page_key' => 'exams',
        ));
    }

    public function results()
    {
        $this->render('pages/examinations/results', array(
            'title'    => 'Results',
            'page_key' => 'results',
        ));
    }
}
