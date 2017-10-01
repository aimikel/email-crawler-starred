<?php

require APPPATH . 'libraries/classes/Job.php';

class Testing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
    }

    public function index() {
        $url = new Url('http://www.example.com/1.html', 'http://www.example.com');

        $this->unit->run($url->findEmails('123 test@test.com adssadsadsad'), array(0 => 'test@test.com'), 'PASS: Find emails in String');
        $this->unit->run($url->findUrls('<html><body><a href="test.php"> adssadsadsad</body></html>'), array(0 => 'http://www.example.com/test.php'), 'PASS: Find urls in String');
        $this->unit->run($url->validUrl(), TRUE, 'PASS: Check if URL is valid');
        
        $url = new Url('http.com/1.html');
        $this->unit->run($url->findEmails('123 test@test.com adssadsadsad'), array(0 => '123test31232@te2st.com'), 'FAIL: Find emails in String');
        $this->unit->run($url->findUrls('<html><body><a href="test.html"> adssadsadsad</body></html>'), array(0 => 'http://www.example.com/test213123.php'), 'FAIL: Find urls in String');
        $this->unit->run($url->validUrl(), TRUE, 'FAIL: Check if URL is valid! URL is http.com/1.html');
        echo $this->unit->report();
    }

}
