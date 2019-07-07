<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Referral_codeset_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->library('System');
		$this->load->library('Referral');
	}

	private function _sql($request)
	{
		if (isset($request->params->fields) && !empty($request->params->fields)) 
		   	$this->db->select($request->params->fields);
		else
			$this->db->select('T1.simpiID, T1.ReferralID, T1.ReferralCode, T1.TreePrefix, T2.FieldID, T3.FieldCode, T3.FieldDescription, T2.FieldData');  
		$this->db->from('master_referral T1');
		$this->db->join('codeset_referral_data T2', 'T1.simpiID = T2.simpiID And T1.ReferralID = T2.ReferralID');  
		$this->db->join('codeset_referral_field T3', 'T2.FieldID = T3.FieldID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->referral_keyword) && !empty($request->params->referral_keyword)) {
			$data = $this->security->xss_clean($request->params->referral_keyword);
			$strKeyword = "(T1.ReferralCode LIKE '%".$data."%'"
						 ." or T1.TreePrefix LIKE '%".$data."%')";
			$this->db->where($strKeyword);
		}
	}

	function load($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->FieldID) || empty($request->params->FieldID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FieldID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T2.FieldID', $request->params->FieldID);
		if (isset($request->params->ReferralID) && !empty($request->params->ReferralID)) {
			$this->db->where('T1.ReferralID', $request->params->ReferralID);
		} elseif (isset($request->params->ReferralCode) && !empty($request->params->ReferralCode)) {
			$this->db->where('T1.ReferralCode', $request->params->ReferralCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->where('T1.TreePrefix', $request->params->TreePrefix);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter referral');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}	

	function codeset_get($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->FieldID) || empty($request->params->FieldID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FieldID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T2.FieldID', $request->params->FieldID);
		if (isset($request->params->ReferralID) && !empty($request->params->ReferralID)) {
			$this->db->where('T1.ReferralID', $request->params->ReferralID);
		} elseif (isset($request->params->ReferralCode) && !empty($request->params->ReferralCode)) {
			$this->db->where('T1.ReferralCode', $request->params->ReferralCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->where('T1.TreePrefix', $request->params->TreePrefix);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter referral');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'referral codeset');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['FieldData' => $row->FieldData]]];
	}

	function search($request)
	{	
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->FieldID) && !empty($request->params->FieldID)) 
			$this->db->where('T2.FieldID', $request->params->FieldID);
		if (isset($request->params->ReferralID)) {
			if (is_array($request->params->ReferralID)) 
				$this->db->where_in('T1.ReferralID', $request->params->ReferralID);
			else 
				$this->db->where('T1.ReferralID', $request->params->ReferralID);
		} elseif (isset($request->params->ReferralCode) && !empty($request->params->ReferralCode)) {
			if (is_array($request->params->ReferralCode)) 
				$this->db->where_in('T1.ReferralCode', $request->params->ReferralCode);
			else 
				$this->db->where('T1.ReferralCode', $request->params->ReferralCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			if (is_array($request->params->TreePrefix)) {
				$this->db->where_in('T1.TreePrefix', $request->params->TreePrefix);
			} else {
				$this->db->where('T1.TreePrefix', $request->params->TreePrefix);
			}
		} else {
			$this->_keyword($request);
		}			
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;			 
	}

	function team_head($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->TreeParentID) && !empty($request->params->TreeParentID))
			$this->db->where('T1.TreeParentID', $request->params->TreeParentID);
		else 
			$this->db->where('T1.TreeParentID = 0');		
		if (isset($request->params->FieldID) && !empty($request->params->FieldID)) 
			$this->db->where('T2.FieldID', $request->params->FieldID);
		$this->_keyword($request);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function team_member($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->like('T1.TreePrefix', $request->params->TreePrefix);
			if (strtolower($request->params->option_without) == 'y') 
				$this->db->where('T1.TreePrefix != ', $request->params->TreePrefix);			
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'TreePrefix');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}			 		
		if (isset($request->params->FieldID) && !empty($request->params->FieldID)) 
			$this->db->where('T2.FieldID', $request->params->FieldID);
		$this->_keyword($request);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

}    