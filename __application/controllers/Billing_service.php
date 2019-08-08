<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Billing Service Class
 *
 * This class contains functions for Billing Service Agent
 *
 */
class Billing_service extends CI_Controller 
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
        $this->load->library('f');
        $this->load->helper('logger');
	}
    
    function logger($message='NONAME')
    {
        if (PHP_OS === 'WINNT')
            logme('scheduler', 'info', "Method [$message]");
    }

    function generate_bill()
    {
        if (!$result = $this->db->get_where('c_partner', ['client_id' => 1, 'is_active' => 'Y', 'is_student' => 'Y']))
            die('Database Error: '.$this->db->error()['message']);

        if (!$rows = $result->result()) {
            $this->logger(__FUNCTION__);
            die('No Billing Generated');
        }

        
    }

    function test()
    {
        die($this->f->get_ip_address());
    }
}