<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Url_model
 *
 * @author Em1K3l
 */
class Url_model {
    /**
     * Insert URL data
     * 
     * @param type $urlData
     * @return boolean|int
     */
    public function addUrl($urlData) {
        $this->db->insert('urls', $urlData);
        if ($this->db->affected_rows() == 1)
            return $this->db->insert_id();
        return false;
    }

    /**
     * Batch insert emails
     * 
     * @param type $emailData
     * @return boolean
     */
    public function addEmails($emailData) {
        $this->db->insert_batch('emails', $emailData);
        if ($this->db->affected_rows() >= 1)
            return true;
        return false;
    }

    /**
     * Set a URL as completed
     * 
     * @param type $urlId
     * @return boolean
     */
    public function updateUrlCompleted($urlId) {
        $this->db->where('url_id', $urlId)->update('urls', array('status' => 1));
        if ($this->db->affected_rows() == 1)
            return true;
        return false;
    }

    /**
     * Get Emails per URL
     * 
     * @param type $urlId
     * @return boolean|array
     */
    public function getEmailsPerUrl($urlId) {
        $qry = $this->db->select('emails.email_address')
                ->from('emails')
                ->where('emails.url_id', $urlId)
                ->get();
        if ($qry->num_rows() > 0)
            return $qry->result_array();
        return false;
    }

    /**
     * Get following URLS per Job UUID
     * 
     * @param type $jobUuid
     * @return boolean|array
     */
    public function getFollowingUrls($jobUuid) {
        $qry = $this->db->select('urls.url_id, urls.url_address')
                ->from('urls')
                ->where('urls.job_uuid', $jobUuid)
                ->get();
        if ($qry->num_rows() > 0)
            return $qry->result_array();
        return false;
    }
}
