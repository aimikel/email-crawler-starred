<?php
require APPPATH . 'libraries/classes/Url.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Job
 *
 * @author Em1K3l
 */
class Job {
   
    protected $CI;
    protected $toParseUrls = array(); //array to store URLS to parse
    protected $parsedUrls = array(); //array to store parsed URLS 
    protected $jobUUID;
    protected $url;

    public function __construct($url = null) {
        $this->CI = &get_instance();
        $this->CI->load->model('job_model');
        $this->url = $url;
    }

    /**
     * Create a new Job.
     * Add 1 record in db table Jobs and 1 row in db table Urls 
     * 
     * @param type $urlAddress
     * @return UUID
     */
    public function createJob() {
        if (!$this->url->validUrl()) //Check if URL is valid
            return false;

        if (!$jobUUID = $this->addJob()) //Add JOB in DB
            return false;

        echo $jobUUID;

        $urlData = $this->url->prepareUrlData($jobUUID, $this->url->urlAddress); //Prepare the URL array to be inserted in DB

        if (!$this->url->addUrl($urlData)) //Add URL in DB
            return false;

        return $jobUUID;
    }

    /**
     * Insert new Job with UUID
     * 
     * @return boolean|string
     */
    public function addJob() {
        $jobData['uuid'] = $this->getUuid();
        $jobData['status'] = 0;
        if ($this->CI->job_model->addJob($jobData)) //Add Job in DB
            return $jobData['uuid'];
        return false;
    }
    
       /**
     * Generate UUID
     * 
     * @return string
     */
    public function getUuid() {
        return str_replace('.', '', uniqid(rand(), TRUE));
    }
}
