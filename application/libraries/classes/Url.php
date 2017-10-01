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
    public $curlResponse;

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
    /**
     * Start crawling process 
     * 
     * 1. Get CURL response
     * 2. Find emails in URL
     * 3. Find following relative URLs
     * 
     */
    public function crawl() {
        $curlResponse = $this->getCurlResponse();
        $this->foundEmails = $this->findEmails($curlResponse);
        $this->followingUrls = $this->findUrls($curlResponse);
    }

    /**
     * Use CURL to get HTML response from URL
     * 
     * @return string
     */
    public function getCurlResponse() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlAddress);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * Find emails in HTML Response, using regular expression
     * 
     * @param type $curlResponse
     * @return array
     */
    public function findEmails($curlResponse) {
        $matches_emails = array(); //create array
        $pattern_emails = '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/i';
        preg_match_all($pattern_emails, $curlResponse, $matches_emails); //find matching pattern
        return $matches_emails[0];
    }

    /**
     * Find following URLS in HTML Response, using DOMDocument and DOMXpath
     * We assume that the following URLS should in href link elements
     * 
     * @param type $curlResponse
     * @return type
     */
    public function findUrls($curlResponse) {
        $doc = new DOMDocument();
        $doc->loadHTML($curlResponse);
        $xpath = new DOMXpath($doc);
        $nodes = $xpath->evaluate("/html/body//a"); //Get URLs in HTML a Elements
        $this->filterNodes($nodes); //Use filtering function to filter found URLs
        return array_values(array_unique($this->followingUrls));
    }

    /**
     * Filter the DOMXpath nodes to retrieve valid URLs
     * 
     * @param type $nodes
     */
    private function filterNodes($nodes) {
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href'); //Get href of the a element
            if ($url = $this->prepareUrl($href)) { //Prepare URL with following URLs. If found, add it to array
                array_push($this->followingUrls, $url);
            }
        }
    }
    
    /**
     * Filter and prepare the following URL found.
     * A URL can be absolute or relative for the same domain.
     * 
     * Only URLs that refer to the given domain are valid as per requirements.
     * 
     * @param type $url
     * @return string
     */
    private function prepareUrl($url) {
        if ($url == "#" || $url == "")
            return false;

        if (!filter_var($url, FILTER_VALIDATE_URL)) { //Check if URL is not relative and return the concatenated URL
            return $this->domain . "/" . $url;
        } else {
            return ($this->checkAbsoluteUrl($url) ? $url : false); //If domain is same, return it otherwise return false.
        }
    }

    /**
     * Use parse_url to check if the domain name of the following URL is same as the given domain name
     * 
     * @param type $url
     * @return type
     */
    private function checkAbsoluteUrl($url) {
        $domainHost = parse_url($this->domain); //Domain name of given URL
        $urlHost = parse_url($url); //Domain name of following URL
        return (strtolower($domainHost['host']) === strtolower($urlHost['host']));
    }

    /**
     * 
     * @param type $url
     * @return type
     */
    public function findDomain($url) {
        $sourceUrl = parse_url($url);
        return $sourceUrl['scheme'] . "://" . $sourceUrl['host'];
    }

    /**
     * Use filter_var in order to check if urlAddress is valid
     * 
     * @return boolean
     */
    public function validUrl() {
        if (filter_var($this->urlAddress, FILTER_VALIDATE_URL) === false)
            return false;
        return true;
    }
}
