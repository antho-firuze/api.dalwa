<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Master_portfolio_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->library('System');
		$this->load->library('Portfolio');
	}

	private function _sql($request)
	{
		$subQuery =  'Select PortfolioID From system_access_portfolio 
					Where UserID = '.$request->user_id.' And simpiID = '.$request->simpi_id;

		if (isset($request->params->fields) && !empty($request->params->fields)) 
			$this->db->select($request->params->fields);
		else
		 	$this->db->select('simpiID, PortfolioID, PortfolioCode, PortfolioNameFull, PortfolioNameShort, pmID,  
					 InceptionDate, InceptionPrice, InceptionAUM, InceptionUnit, AssetTypeID, TaxID, BenchmarkTypeID, 
					 BenchmarkCalculationID, BenchmarkPremium, OverrideTypeID, BenchmarkID, SectorClassID, 
					 CcyID, TypeID, AccountID, ReturnID, DaysID, StatusID, InventoryID, CostID, ApplyID, IsSyariah');
		$this->db->from('master_portfolio');
		$this->db->where('simpiID', $request->simpi_id);
		
		if ($request->log_access == 'license') {
			return [TRUE, NULL]; //full akses
		} elseif ($request->log_access == 'session') {
		 	$this->db->where("'PortfolioID' IN ($subQuery)", NULL, FALSE); //user assignment
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} elseif ($request->log_access == 'apps') {
			//can not akses: must via apps
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
		list($success, $return) = $this->_sql($request);
		if (!$success) return [FALSE, $return];
		if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
			$this->db->where('PortfolioID', $request->params->PortfolioID);
		} elseif (isset($request->params->PortfolioCode) && !empty($request->params->PortfolioCode)) {
			$this->db->where('PortfolioCode', $request->params->PortfolioCode);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter portfolio');
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
		list($success, $return) = $this->_sql($request);
		if (!$success) return [FALSE, $return];
		if (isset($request->params->CcyID) && !empty($request->params->CcyID)) 
			$this->db->where('CcyID', $request->params->CcyID);
		if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
			 if (is_array($request->params->PortfolioID)) {
				$this->db->where_in('PortfolioID', $request->params->PortfolioID);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->PortfolioCode) && !empty($request->params->PortfolioCode)) {
			if (is_array($request->params->PortfolioCode)) { 
				$this->db->where_in('PortfolioCode', $request->params->PortfolioCode);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioCode');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} else {
			if (isset($request->params->portfolio_keyword) && !empty($request->params->portfolio_keyword)) {
				$data = $this->security->xss_clean($request->params->portfolio_keyword);
				$strKeyword = "(PortfolioCode LIKE '%".$data."%'"
							 ." or PortfolioNameShort LIKE '%".$data."%'"
							 ." or PortfolioNameFull LIKE '%".$data."%')";
				$this->db->where($strKeyword);
			if (isset($request->params->TypeID) && !empty($request->params->TypeID)) $this->db->where('TypeID', $request->params->TypeID);
			if (isset($request->params->AccountID) && !empty($request->params->AccountID)) $this->db->where('AccountID', $request->params->AccountID);
			if (isset($request->params->AssetTypeID) && !empty($request->params->AssetTypeID)) $this->db->where('AssetTypeID', $request->params->AssetTypeID);
			if (isset($request->params->StatusID) && !empty($request->params->StatusID)) $this->db->where('StatusID', $request->params->StatusID);
			if (isset($request->params->CcyID) && !empty($request->params->CcyID)) $this->db->where('CcyID', $request->params->CcyID);
			}
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	} 
	
	function portfolio_id($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioCode) || empty($request->params->PortfolioCode)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioCode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
		if (!$success) return [FALSE, $return];
		$this->db->where('PortfolioCode', $request->params->PortfolioCode);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->PortfolioCode);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['PortfolioID' => $row->PortfolioID]]];
	}

	function portfolio_code($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
		if (!$success) return [FALSE, $return];
		$this->db->where('PortfolioID', $request->params->PortfolioID);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->PortfolioID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['PortfolioCode' => $row->PortfolioCode]]];
	}

}
