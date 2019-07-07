<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Market_research_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('S');
	}

}