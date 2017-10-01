<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Job_model
 *
 * @author Em1K3l
 */
class Job_model {
   
    /**
     * Insert Job data
     * 
     * @param type $jobData
     * @return boolean
     */
    public function addJob($jobData) {
        $this->db->insert('jobs', $jobData);
        if ($this->db->affected_rows() == 1)
            return true;
        return false;
    }

    /**
     * Return all jobs data if not Job UUID provided
     * Otherwise return Job's data
     * 
     * @param type $jobUuid
     * @return boolean|array
     */
    public function getJobs($jobUuid = null) {
        $this->db->select('uuid, url_address, job_created_date,'
                        . "CASE jobs.status WHEN 0 THEN 'in_progress'
                     ELSE 'completed' END as job_status")
                ->from('jobs')
                ->join('urls', 'urls.job_uuid=jobs.uuid')
                ->where('urls.is_following', 0);

        if ($jobUuid != NULL) {
            $this->db->where('jobs.uuid', $jobUuid);
        }
        $qry = $this->db->get();
        if ($qry->num_rows() > 0)
            return $qry->result_array();
        return false;
    }

    /**
     * Get in progress Jobs
     * 
     * @return boolean|array
     */
    public function getInProgressJobs() {
        $qry = $this->db->select('*')
                ->from('jobs')
                ->join('urls', 'urls.job_uuid=jobs.uuid')
                ->where('urls.is_following', 0)
                ->where('jobs.status', 0)
                ->get();

        if ($qry->num_rows() > 0)
            return $qry->result_array();
        return false;
    }

    /**
     * Set a Job as completed
     * 
     * @param type $jobUUID
     * @return boolean
     */
    public function updateJobCompleted($jobUUID) {
        $this->db->where('uuid', $jobUUID)->update('jobs', array('status' => 1));
        if ($this->db->affected_rows() == 1)
            return true;
        return false;
    }
    
}
