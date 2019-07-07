<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Client_kyc_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->library('System');
		$this->load->library('Client');
	}

	private function _sql($request)
	{
		if (isset($request->params->fields) && !empty($request->params->fields)) 
			$this->db->select($request->params->fields);
		else
			$this->db->select('T1.simpiID, T1.ClientID, T1.SalesID, T2.SalesCode, T2.TreePrefix, T2.SInvestCode, 
							T3.kycID, T4.kycCode, T4.kycDescription, T3.kycAnswer');  
		$this->db->from('master_client T1');
		$this->db->join('master_sales T2', 'T1.simpiID = T2.simpiID And T1.SalesID = T2.SalesID');  
		$this->db->join('master_client_kyc T3', 'T1.simpiID = T3.simpiID And T1.ClientID = T3.ClientID');  
		$this->db->join('parameter_client_kyc T4', 'T3.kycID = T4.kycID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->TypeID) && !empty($request->params->TypeID)) $this->db->where('T1.TypeID', $request->params->TypeID);
		if (isset($request->params->StatusID) && !empty($request->params->StatusID)) $this->db->where('T1.StatusID', $request->params->StatusID);
		if (isset($request->params->RiskID) && !empty($request->params->RiskID)) $this->db->where('T1.RiskID', $request->params->RiskID);
		if (isset($request->params->LF) && !empty($request->params->LF)) $this->db->where('T1.LF', $request->params->LF);
		if (isset($request->params->client_keyword) && !empty($request->params->client_keyword)) $this->db->like('T1.ClientName', $request->params->client_keyword);
	}
	
	private function _access($request)
	{
		if ($request->log_access == 'license') {
			return [TRUE, NULL];
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
			return [TRUE, NULL];
		} elseif ($request->log_access == 'session') {
			$this->db->like('T2.TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$this->db->where('T1.SID', $request->SID);
			return [TRUE, NULL];
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
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->kycID) || empty($request->params->kycID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter kycID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T3.kycID', $request->params->kycID);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			$this->db->where('T1.ClientID', $request->params->ClientID);
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			$this->db->where('T1.ClientCode', $request->params->ClientCode);
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			$this->db->where('T1.SID', $request->params->SID);
			$this->db->where('(T1.TypeID = 1 or T1.TypeID = 2)');
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			$this->db->where('T1.IFUA', $request->params->IFUA);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function kyc_get($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->kycID) || empty($request->params->kycID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter kycID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.kycID', $request->params->kycID);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			$this->db->where('T1.ClientID', $request->params->ClientID);
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			$this->db->where('T1.ClientCode', $request->params->ClientCode);
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			$this->db->where('T1.SID', $request->params->SID);
			$this->db->where('(T1.TypeID = 1 or T1.TypeID = 2)');
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			$this->db->where('T1.IFUA', $request->params->IFUA);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'client kyc');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['kycAnswer' => $row->kycAnswer]]];
	}

	function search($request)
	{	
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->kycID) && !empty($request->params->kycID)) 
			$this->db->where('T3.kycID', $request->params->kycID);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('T1.ClientID', $request->params->ClientID);
			else 
				$this->db->where('T1.ClientID', $request->params->ClientID);	
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			if (is_array($request->params->ClientCode)) 
				$this->db->where_in('T1.ClientCode', $request->params->ClientCode);
			else 
				$this->db->where('T1.ClientCode', $request->params->ClientCode);	
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			if (is_array($request->params->SID)) 
				$this->db->where_in('T1.SID', $request->params->SID);
			else 
				$this->db->where('T1.SID', $request->params->SID);	
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			if (is_array($request->params->IFUA)) 
				$this->db->where_in('T1.IFUA', $request->params->IFUA);
			else 
				$this->db->where('T1.IFUA', $request->params->IFUA);	
		} else {
			if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
				if (is_array($request->params->SalesID)) 
					$this->db->where_in('T1.SalesID', $request->params->SalesID);
		 		else 
					$this->db->where('T1.SalesID', $request->params->SalesID);
			}
			$this->_keyword($request);
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function team_direct($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->SalesID) || empty($request->params->SalesID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.SalesID', $request->params->SalesID);
		if (isset($request->params->kycID) && !empty($request->params->kycID)) 
			$this->db->where('T3.kycID', $request->params->kycID);
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function team_head($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->kycID) && !empty($request->params->kycID)) 
			$this->db->where('T3.kycID', $request->params->kycID);
		if (isset($request->params->TreeParentID) && !empty($request->params->TreeParentID))
			$this->db->where('T2.TreeParentID', $request->params->TreeParentID);
		else 
			$this->db->where('T2.TreeParentID = 0');		
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
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->kycID) && !empty($request->params->kycID)) 
			$this->db->where('T3.kycID', $request->params->kycID);
		if (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->like('T2.TreePrefix', $request->params->TreePrefix, 'after');
			if (strtolower($request->params->option_without) == 'y') 
				$this->db->where('T2.TreePrefix != ', $request->params->TreePrefix);			
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

}