<?php

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . 'libraries/classes/Job.php';

class Crawler extends REST_Controller {

    protected $job;

    public function __construct($config = 'rest') {
        parent::__construct($config);
    }

    /**
     * URL: crawler/getJobs
     * METHOD: GET
     * Get all saved Jobs
     */
    public function getJobs_get() {
        $url = new Url();
        $job = new Job($url);
        $jobs = $job->getJobsData();
        if ($jobs && !empty($jobs)) { //If images to return
            $this->response(['response' => 'success', 'jobs' => $jobs], REST_Controller::HTTP_OK);
        } else {
            $this->response(['response' => 'failed', 'error' => 'No jobs found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * URL: crawler/addJob
     * METHOD: POST
     * Create a new Job
     */
    public function addJob_post() {
        $urlAddress = $this->post('url');
        $url = new Url($urlAddress);
        $job = new Job($url);

        if ($jobUuid = $job->createJob()) {
            $this->response(['response' => 'success', 'uuid' => $jobUuid, 'status' => 'in_progress'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['response' => 'failed', 'error' => 'You need to provide a valid URL using HTTP:// or HTTPS://'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * URL: crawler/getJob
     * METHOD: GET
     * Get saved Job per UUID
     */
    public function getJob_get() {
        $jobUuid = $this->get('uuid');
        if (!$jobUuid || $jobUuid == "")
            $this->response(['response' => 'failed', 'error' => 'You need to provide a valid Job UUID'], REST_Controller::HTTP_NOT_FOUND);

        $url = new Url();
        $job = new Job($url);
        $jobData = $job->getJobsData($jobUuid);
        if ($jobData && !empty($jobData)) {
            $this->response(['response' => 'success', 'job' => $jobData], REST_Controller::HTTP_OK);
        } else {
            $this->response(['response' => 'failed', 'error' => 'No data found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

}


