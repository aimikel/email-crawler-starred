<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/classes/Job.php';

class Dashboard extends CI_Controller {
 

    public function __construct() {
        parent::__construct();
    }
    
     /**
     * Display all saved Jobs 
     */
    public function index() {
        $url = new Url();
        $job = new Job($url);
        $this->view_data['jobs'] = $job->getJobsData();
        $this->load->view('dashboard_view', $this->view_data);
    }

    /**
     * Create a new Job after URL validation
     */
    public function addJob() {
        $this->form_validation->set_rules('url_address', 'URL Address', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('dashboard_view', $this->view_data);
        } else {
            $urlAddress = $this->input->post('url_address'); //Get URL address through HTML FORM
            $url = new Url($urlAddress);
            $job = new Job($url);

            if ($job->createJob()) { //Create a new Job
                $this->session->set_flashdata('message', 'Job added succesfuly!');
            } else {
                $this->session->set_flashdata('error', 'You need to provide a valid URL using HTTP:// or HTTPS://');
            }
            redirect(base_url('dashboard'));
        }
    }
    
    /**
     * Start Executing saved in_progress Jobs 
     */
    public function execute() {
        $url = new Url();
        $job = new Job($url);
        $job->executeJobs();
    }
   
}

