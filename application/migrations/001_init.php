<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init extends CI_Migration {

    public function up() {
        //jobs
        $this->dbforge->drop_table('jobs', TRUE);
        $this->dbforge->add_field(array(
            'uuid' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
            ),
            'status' => array(
                'type' => 'TINYINT',
            )
        ));
        $this->dbforge->add_field('job_created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->dbforge->add_key('uuid', TRUE);
        $this->dbforge->create_table('jobs');

        //urls
        $this->dbforge->drop_table('urls', TRUE);
        $this->dbforge->add_field(array(
            'url_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'job_uuid' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
            ),
            'url_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'status' => array(
                'type' => 'TINYINT',
                'default' => 0
            ),
            'is_following' => array(
                'type' => 'TINYINT',
            )
        ));
        $this->dbforge->add_field('url_created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->dbforge->add_key('url_id', TRUE);
        $this->dbforge->create_table('urls');

        //emails
        $this->dbforge->drop_table('emails', TRUE);
        $this->dbforge->add_field(array(
            'email_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'url_id' => array(
                'type' => 'INT',
            ),
            'email_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
            )
        ));
        $this->dbforge->add_field('email_created_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->dbforge->add_key('email_id', TRUE);
        $this->dbforge->create_table('emails');
    }

}