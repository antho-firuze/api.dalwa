<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Referral {
	
	public $ci;

    function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database(DATABASE_MASTER);
		$this->ci->load->library('System');
	}

	function check_referral($request)
	{
		$ci = $this->ci;
		$ci->db->select('ReferralID, TreePrefix');  
		$ci->db->from('master_referral');
		$ci->db->where('simpiID', $request->simpi_id);
		if (isset($request->params->ReferralID) && !empty($request->params->ReferralID)) {
			$ci->db->where('ReferralID', $request->params->ReferralID);
		} elseif (isset($request->params->ReferralCode) && !empty($request->params->ReferralCode)) {
			$ci->db->where('ReferralCode', $request->params->ReferralCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$ci->db->where('TreePrefix', $request->params->TreePrefix);
		} else {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'referral parameter');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		
		$row = $ci->db->get()->row();
		if ($row) {
			$request->params->ReferralID = $row->ReferralID;
			$request->params->TreePrefix = $row->TreePrefix;
			return [TRUE, NULL];
		} else {
			$return = $ci->system->error_data('00-2', $request->LanguageID, 'master referral');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
	}
    
}