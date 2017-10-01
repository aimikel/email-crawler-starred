<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Url
 *
 * @author Em1K3l
 */
class Url {
    public $followingUrls = array();
    public $foundEmails = array();
    public $CI;
    public $domain;
    public $urlAddress;
    

    public function __construct($urlAddress = null, $domain = null, $followingUrls = array(), $emails = array()) {
        $this->urlAddress = $urlAddress;
        $this->domain = $domain;
        $this->followingUrls = $followingUrls;
        $this->foundEmails = $emails;
        $this->CI = &get_instance();
        $this->CI->load->model('url_model');
    }

    /**
     * Insert new URL in DB
     * 
     * @param type $urlData
     * @return boolean
     */
    public function addUrl($urlData) {
        if ($urlId = $this->CI->url_model->addUrl($urlData))
            return $urlId;
        return false;
    }

    /**
     * Prepare the array to insert in DB URL
     * 
     * @param type $jobUUID
     * @param type $urlAddress
     * @param type $status
     * @param type $isFollowing
     * @return array
     */
    public function prepareUrlData($jobUUID, $urlAddress, $status = 0, $isFollowing = 0) {
        $urlData['job_uuid'] = $jobUUID;
        $urlData['url_address'] = $urlAddress;
        $urlData['is_following'] = $status;
        $urlData['status'] = $isFollowing;
        return $urlData;
    }
}
