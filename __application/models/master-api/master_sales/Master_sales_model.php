<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Master_sales_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->library('System');
		$this->load->library('Sales');
	}

	private function _sql($request)
	{
		if (isset($request->params->fields) && !empty($request->params->fields)) 
		   $this->db->select($request->params->fields);
		else
		   $this->db->select('SalesID, SalesCode, CorrespondenceAddress, CorrespondencePhone, CorrespondenceEmail, TaxID, 
		   					LicenseNo, LicenseExpired, LicenseIssuer, TreeParentID, TreeLevel, TreeIsLeaf, TreePrefix, SInvestCode');
		$this->db->from('master_sales');
		$this->db->where('simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->sales_keyword) && !empty($request->params->sales_keyword)) {
			$data = $this->security->xss_clean($request->params->sales_keyword);
			$strKeyword = "(SalesCode LIKE '%".$data."%'"
						 ." or TreePrefix LIKE '%".$data."%'"
						 ." or SInvestCode LIKE '%".$data."%')";
			$this->db->where($strKeyword);
		}
	}
	
	private function _access($request)
	{
		if ($request->log_access == 'license') {
			return [TRUE, NULL];
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
			return [TRUE, NULL];
		} elseif ($request->log_access == 'session') {
			$this->db->like('TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} elseif ($request->log_access == 'apps') {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
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
		if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
			$this->db->where('SalesID', $request->params->SalesID);
		} elseif (isset($request->params->SalesCode) && !empty($request->params->SalesCode)) {
			$this->db->where('SalesCode', $request->params->SalesCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->where('TreePrefix', $request->params->TreePrefix);
		} elseif (isset($request->params->SInvestCode) && !empty($request->params->SInvestCode)) {
			$this->db->where('SInvestCode', $request->params->SInvestCode);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter sales');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
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
		if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
			 if (is_array($request->params->SalesID)) {
				$this->db->where_in('SalesID', $request->params->SalesID);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}
		} elseif (isset($request->params->SalesCode) && !empty($request->params->SalesCode)) {
			if (is_array($request->params->SalesCode)) {
				$this->db->where_in('SalesCode', $request->params->SalesCode);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesCode');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			if (is_array($request->params->TreePrefix)) {
				$this->db->where_in('TreePrefix', $request->params->TreePrefix);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter TreePrefix');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->SInvestCode) && !empty($request->params->SInvestCode)) {
			if (is_array($request->params->SInvestCode)) {
				$this->db->where_in('SInvestCode', $request->params->SInvestCode);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SInvestCode');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} else {
			$this->_keyword($request);
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
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
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
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
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function sales_id($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->SalesCode) || empty($request->params->SalesCode)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesCode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('SalesCode', $request->params->SalesCode);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->SalesCode);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SalesID' => $row->SalesID]]];
	}

	function sales_code($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SalesID
		if (!isset($request->params->SalesID) || empty($request->params->SalesID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('SalesID', $request->params->SalesID);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->SalesID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SalesCode' => $row->SalesCode]]];
	}

	function sales_prefix($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SalesID
		if (!isset($request->params->SalesID) || empty($request->params->SalesID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('SalesID', $request->params->SalesID);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->SalesID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['TreePrefix' => $row->TreePrefix]]];
	}

	function sales_sinvest($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SalesID
		if (!isset($request->params->SalesID) || empty($request->params->SalesID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('SalesID', $request->params->SalesID);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->SalesID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SInvestCode' => $row->SalesCode]]];
	}
	
}
