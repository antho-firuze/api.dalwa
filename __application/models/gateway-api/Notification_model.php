<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Notification_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}

    /**
     * return date of beginning year from input position date
     */
    function queue($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		return $date;
	}

}
