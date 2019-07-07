<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Sales_codeset_model extends CI_Model
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
		$this->db->select('T1.simpiID, T1.SalesID, T1.SalesCode, T1.TreePrefix, T1.SInvestCode, 
							T2.FieldID, T3.FieldCode, T3.FieldDescription, T2.FieldData');  
		$this->db->from('master_sales T1');
		$this->db->join('codeset_sales_data T2', 'T1.simpiID = T2.simpiID And T1.SalesID = T2.SalesID');  
		$this->db->join('codeset_sales_field T3', 'T2.FieldID = T3.FieldID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->sales_keyword) && !empty($request->params->sales_keyword)) {
			$data = $this->security->xss_clean($request->params->sales_keyword);
			$strKeyword = "(T1.SalesCode LIKE '%".$data."%'"
						 ." or T1.TreePrefix LIKE '%".$data."%'"
						 ." or T1.SInvestCode LIKE '%".$data."%')";
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
			$this->db->like('T1.TreePrefix', $request->TreePrefix,'after');
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

		if (!isset($request->params->FieldID) || empty($request->params->FieldID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FieldID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T2.FieldID', $request->params->FieldID);
		if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
			$this->db->where('T1.SalesID', $request->params->SalesID);
		} elseif (isset($request->params->SalesCode) && !empty($request->params->SalesCode)) {
			$this->db->where('T1.SalesCode', $request->params->SalesCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->where('T1.TreePrefix', $request->params->TreePrefix);
		} elseif (isset($request->params->SInvestCode) && !empty($request->params->SInvestCode)) {
			$this->db->where('T1.SInvestCode', $request->params->SInvestCode);
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
		if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
			$this->db->where('T1.SalesID', $request->params->SalesID);
		} elseif (isset($request->params->SalesCode) && !empty($request->params->SalesCode)) {
			$this->db->where('T1.SalesCode', $request->params->SalesCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->where('T1.TreePrefix', $request->params->TreePrefix);
		} elseif (isset($request->params->SInvestCode) && !empty($request->params->SInvestCode)) {
			$this->db->where('T1.SInvestCode', $request->params->SInvestCode);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter sales');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'sales codeset');
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
		if (isset($request->params->SalesID)) {
			if (is_array($request->params->SalesID)) 
				$this->db->where_in('T1.SalesID', $request->params->SalesID);
			else 
				$this->db->where('T1.SalesID', $request->params->SalesID);
		} elseif (isset($request->params->SalesCode) && !empty($request->params->SalesCode)) {
			if (is_array($request->params->SalesCode)) 
				$this->db->where_in('T1.SalesCode', $request->params->SalesCode);
			else 
				$this->db->where('T1.SalesCode', $request->params->SalesCode);
		} elseif (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			if (is_array($request->params->TreePrefix)) {
				$this->db->where_in('T1.TreePrefix', $request->params->TreePrefix);
			} else {
				$this->db->where('T1.TreePrefix', $request->params->TreePrefix);
			}
		} elseif (isset($request->params->SInvestCode) && !empty($request->params->SInvestCode)) {
			if (is_array($request->params->SInvestCode)) 
				$this->db->where_in('T1.SInvestCode', $request->params->SInvestCode);
			else 
				$this->db->where('T1.SInvestCode', $request->params->SInvestCode);
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
			$this->db->where('T1.TreeParentID', $request->params->TreeParentID);
		else 
			$this->db->where('T1.TreeParentID = 0');		
		if (isset($request->params->FieldID) && !empty($request->params->FieldID)) 
			$this->db->where('T2.FieldID', $request->params->FieldID);
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
		if (isset($request->params->FieldID) && !empty($request->params->FieldID)) 
			$this->db->where('T2.FieldID', $request->params->FieldID);
		if (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->like('T1.TreePrefix', $request->params->TreePrefix, 'after');
			if (strtolower($request->params->option_without) == 'y') 
				$this->db->where('T1.TreePrefix != ', $request->params->TreePrefix);			
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