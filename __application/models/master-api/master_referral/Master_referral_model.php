<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Master_referral_model extends CI_Model
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
			$this->db->select('ReferralID, ReferralCode, CorrespondenceAddress, CorrespondencePhone, 
								CorrespondenceEmail, TaxID, TreeParentID, TreeLevel, TreeIsLeaf, TreePrefix');
		$this->db->from('master_referral');
		$this->db->where('simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->referral_keyword) && !empty($request->params->referral_keyword)) {
			$data = $this->security->xss_clean($request->params->referral_keyword);
			$strKeyword = "(ReferralCode LIKE '%".$data."%'"
						 ." or TreePrefix LIKE '%".$data."%')";
			$this->db->where($strKeyword);
		}
	}
	 
	function load($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->ReferralID) && !empty($request->params->ReferralID)) {
			$this->db->where('ReferralID', $request->params->ReferralID);
		} elseif (isset($request->params->ReferralCode) && !empty($request->params->ReferralCode)) {
			$this->db->where('ReferralCode', $request->params->ReferralCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->where('TreePrefix', $request->params->TreePrefix);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter referral');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function search($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->ReferralID) && !empty($request->params->ReferralID)) {
			if (is_array($request->params->ReferralID)) {
				$this->db->where_in('ReferralID', $request->params->ReferralID);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ReferralID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->ReferralCode) && !empty($request->params->ReferralCode)) {
			if (is_array($request->params->ReferralCode)) {
				$this->db->where_in('ReferralCode', $request->params->ReferralCode);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ReferralCode');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			if (is_array($request->params->TreePrefix)) {
				$this->db->where_in('TreePrefix', $request->params->TreePrefix);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter TreePrefix');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
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
			$this->db->where('TreeParentID', $request->params->TreeParentID);
		else 
			$this->db->where('TreeParentID = 0');		
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
			$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
			if (strtolower($request->params->option_without) == 'y') 
				$this->db->where('TreePrefix != ', $request->params->TreePrefix);			
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'TreePrefix');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}			 		
		$this->_keyword($request);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function referral_id($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ReferralCode) || empty($request->params->ReferralCode)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ReferralCode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('ReferralCode', $request->params->ReferralCode);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ReferralCode);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['ReferralID' => $row->ReferralID]]];
	}

	function referral_code($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ReferralID) || empty($request->params->ReferralID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ReferralID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('ReferralID', $request->params->ReferralID);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ReferralID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['ReferralCode' => $row->ReferralCode]]];
	}

	function referral_prefix($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ReferralID) || empty($request->params->ReferralID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ReferralID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('ReferralID', $request->params->ReferralID);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ReferralID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['TreePrefix' => $row->TreePrefix]]];
	}

}

