<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Fund_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
    }
 
	function load($request) 
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('T1.SecuritiesID, T1.SecuritiesCode, T1.SecuritiesNameFull, T1.SecuritiesNameShort, T1.DateIssue, 
								T1.CompanyID, T1.IsSyariah, T1.SubTypeID, T3.SubTypeCode, T3.TypeID, T1.CcyID, 
								T1.CountryID, T1.StatusID, T1.DateCreated, T1.DateModified');
		$this->db->from('market_instrument T1');
		$this->db->join('market_company T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->join('parameter_securities_instrument_type_sub T3', 'T1.SubTypeID = T3.SubTypeID');  
		$this->db->join('market_instrument_fund T4', 'T1.SecuritiesID = T4.SecuritiesID');  
		$this->db->where('T1.IsPrivate', 'N');
		if (isset($request->params->SecuritiesID) && !empty($request->params->SecuritiesID)) {
			$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
		} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
			$this->db->where('T1.SecuritiesCode', $request->params->SecuritiesCode);
			if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
				$this->db->where('T1.CompanyID', $request->params->CompanyID);				
			} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
				$this->db->where('T2.CompanyCode', $request->params->CompanyCode);
			}	
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'instrument parameter'])]];
		}
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
	}

	function search($request)
	{	
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('T1.SecuritiesID, T1.SecuritiesCode, T1.SecuritiesNameFull, T1.SecuritiesNameShort, T1.DateIssue, 
								T1.CompanyID, T1.IsSyariah, T1.SubTypeID, T3.SubTypeCode, T3.TypeID, T1.CcyID, 
								T1.CountryID, T1.StatusID, T1.DateCreated, T1.DateModified');
		$this->db->from('market_instrument T1');
		$this->db->join('market_company T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->join('parameter_securities_instrument_type_sub T3', 'T1.SubTypeID = T3.SubTypeID');  
		$this->db->join('market_instrument_fund T4', 'T1.SecuritiesID = T4.SecuritiesID');  
		$this->db->where('T1.IsPrivate', 'N');
		if (isset($request->params->SecuritiesID)) {
			if (is_array($request->params->SecuritiesID)) {
				$this->db->where_in('T1.SecuritiesID', $request->params->SecuritiesID);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SecuritiesID'])]];	
			}
		} else {
			if (isset($request->params->SubTypeID)) 
				$this->db->where('T1.SubTypeID', $request->params->SubTypeID);
			if (isset($request->params->CountryID))
				$this->db->where('T1.CountryID', $request->params->CountryID);
			if (isset($request->params->CompanyID))
				$this->db->where('T1.CompanyID', $request->params->CompanyID);
			if (isset($request->params->securities_keyword)) {
				$data = $this->security->xss_clean($request->params->securities_keyword);
				$strKeyword = "(T1.SecuritiesCode LIKE '%".$data."%'"
				 			 ." or T1.SecuritiesNameFull LIKE '%".$data."%'"
				 			 ." or T1.SecuritiesNameShort LIKE '%".$data."%')";
				$this->db->where($strKeyword);
			}
		}
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
	} 

	function instrument_id($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('T1.SecuritiesID');
		$this->db->from('market_instrument T1');
		$this->db->join('market_company T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->join('parameter_securities_instrument_type_sub T3', 'T1.SubTypeID = T3.SubTypeID');  
		$this->db->join('market_instrument_fund T4', 'T1.SecuritiesID = T4.SecuritiesID');  
		$this->db->where('T1.IsPrivate', 'N');
		if (isset($request->params->SecuritiesID) && !empty($request->params->SecuritiesID)) {
			$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
		} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
			$this->db->where('T1.SecuritiesCode', $request->params->SecuritiesCode);
			if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
				$this->db->where('T1.CompanyID', $request->params->CompanyID);				
			} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
				$this->db->where('T2.CompanyCode', $request->params->CompanyCode);
			}	
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'instrument parameter'])]];
		}
		$row = $this->db->get()->row();
    	if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market instrument'])]];
    	}
	
		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SecuritiesID' => $row->SecuritiesID]]];
	}

	function instrument_code($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SecuritiesID
		if (!isset($request->params->SecuritiesID) || empty($request->params->SecuritiesID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SecuritiesID'])]];
		}

		$row = $this->db->get_where('market_instrument', ['SecuritiesID' => $request->params->SecuritiesID], 1)->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->SecuritiesID])]];
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SecuritiesCode' => $row->SecuritiesCode]]];
	}
					
} 