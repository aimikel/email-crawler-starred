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
     * Get all in_progress Jobs and start Crawling 
     */
    public function executeJobs() {
        $jobsToExecute = $this->CI->job_model->getInProgressJobs();

        if ($jobsToExecute && !empty($jobsToExecute)) {
            foreach ($jobsToExecute as $jobToExec) {
                $this->executeJob($jobToExec);
            }
        }
    }

    /**
     * Start Crawl a single Job
     * 
     * @param type $job
     * @return boolean
     */
    private function executeJob($job) {
        $initUrl = $job['url_address'];
        $this->jobUUID = $job['job_uuid'];

        $this->parseUrl($initUrl, false, $job['url_id']);

        while (!empty($this->toParseUrls)) { //While $toParseUrls in not empty, continue
            $urlToParse = array_pop($this->toParseUrls);
            $this->parseUrl($urlToParse, true); //Parse the URL
        }
        if ($this->CI->job_model->updateJobCompleted($this->jobUUID))
            return true;
        return false;
    }

    /**
     * Parse the URL, find the emails and add them in DB.
     * If following URLs found, add them in array $toParseUrls and continue until all domain following URLs are parsed
     * 
     * @param Url $url
     * @param type $addUrlDataFlag
     */
    private function parseUrl($urlAddress, $addUrlDataFlag = false, $urlId = false) {
        $url = new Url($urlAddress);
        $url->domain = $url->findDomain($url->urlAddress); //Find the domain name of the URL in order to concatenate it to following relative URLS
        $url->crawl();
        array_push($this->parsedUrls, $urlAddress); //Add URL in parsedUrls

        if ($addUrlDataFlag) { //Add new URL in DB
            $urlData['job_uuid'] = $this->jobUUID;
            $urlData['url_address'] = $urlAddress;
            $urlData['status'] = 1;
            $urlData['is_following'] = 1;
            $urlId = $url->addUrl($urlData);
        }

        $foundEmails = $url->foundEmails;
        if ($emailsBatchInsert = $this->url->prepareEmails($urlId, $foundEmails)) { //Prepare the emails array to be inserted in DB
            $url->addEmails($emailsBatchInsert);
            $url->updateUrlCompleted($urlId);
        }
        $this->filterFollowingUrs($url->followingUrls); //Filter found following URLs
    }

    /**
     * Filter followingUrls in order to NOT parse again the already parsed one.
     * 
     * Use array_diff to get the difference between the URLs to be parsed and the parsed URLs
     * 
     * @param type $foundFollowingUrls
     */
    private function filterFollowingUrs($foundFollowingUrls) {
        $this->toParseUrls = array_merge($this->toParseUrls, $foundFollowingUrls);
        $this->toParseUrls = array_unique(array_diff($this->toParseUrls, $this->parsedUrls));
    }
    
        /**
     * Retrieve from DB all saved Jobs and manipulate the array
     * 
     * @return array
     */
    public function getJobsData($jobUuid = null) {
        $jobs = $this->CI->job_model->getJobs($jobUuid); //Get all saved Jobs from DB

        if ($jobs && !empty($jobs)) {
            foreach ($jobs as $job_key => $job_val) { //for each Job get following Urls
                $followingUrls = $this->url->getFollowingUrls($job_val['uuid']);
                if ($followingUrls && !empty($followingUrls)) {
                    foreach ($followingUrls as $followingUrl_key => $followingUrl_val) { // for each URL get found Emails
                        $emailsFound = $this->url->getEmailsPerUrl($followingUrl_val['url_id']);
                        unset($followingUrls[$followingUrl_key]['url_id']);
                        if ($emailsFound && !empty($emailsFound)) {
                            $followingUrls[$followingUrl_key]['emails'] = $emailsFound;
                        }
                    }
                    $jobs[$job_key]['urls'] = $followingUrls;
                }
            }
        }

        return $jobs;
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
