<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sales {
	
	public $ci;

    function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database(DATABASE_MASTER);
		$this->ci->load->library('System');
	}

	function check_sales($request)
	{
		$ci = $this->ci;
		$ci->db->select('SalesID, TreePrefix');  
		$ci->db->from('master_sales');
		$ci->db->where('simpiID', $request->simpi_id);
		if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
			$ci->db->where('SalesID', $request->params->SalesID);
		} elseif (isset($request->params->SalesCode) && !empty($request->params->SalesCode)) {
			$ci->db->where('SalesCode', $request->params->SalesCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$ci->db->where('TreePrefix', $request->params->TreePrefix);
		} elseif (isset($request->params->SInvestCode) && !empty($request->params->SInvestCode)) {
			$ci->db->where('SInvestCode', $request->params->SInvestCode);
		} else {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'parameter sales');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if ($request->log_access == 'license') {
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
		} elseif ($request->log_access == 'session') {
			$ci->db->like('TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} elseif ($request->log_access == 'apps') {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}			
		
		$row = $ci->db->get()->row();
		if ($row) {
			$request->params->SalesID = $row->SalesID;
			$request->params->TreePrefix = $row->TreePrefix;
			return [TRUE, NULL];
		} else {
			$return = $ci->system->error_data('00-2', $request->LanguageID, 'master sales');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
	}
    
}