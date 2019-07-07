<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Office_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}

	function load($request)
	{
		//cek akses:  
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('T1.CompanyID, T2.OfficeID, T2.OfficeCode, T2.OfficeName, T2.OfficeAddress, T2.OfficePhone, 
								T2.OfficeFax, T2.OfficeCity, T3.CountryID, T3.CountryCode, T3.CountryName'); 
		$this->db->from('market_company T1');
		$this->db->join('market_company_office T2', 'T1.CompanyID = T2.CompanyID');
		$this->db->join('parameter_securities_country T3', 'T2.CountryID = T3.CountryID');  
		if (isset($request->params->OfficeID) && !empty($request->params->OfficeID)) {
			$this->db->where('T2.OfficeID', $request->params->CompanyID);
		} elseif (isset($request->params->OfficeCode) && !empty($request->params->OfficeCode) && 
					isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
			$this->db->where('T2.OfficeCode', $request->params->OfficeCode);
		} elseif (isset($request->params->OfficeCode) && !empty($request->params->OfficeCode) && 
					isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
			$this->db->where('T2.OfficeCode', $request->params->OfficeCode);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'office parameter'])]];
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
			$this->db->select('T1.CompanyID, T2.OfficeID, T2.OfficeCode, T2.OfficeName, T2.OfficeAddress, T2.OfficePhone, 
								T2.OfficeFax, T2.OfficeCity, T3.CountryID, T3.CountryCode, T3.CountryName'); 
		$this->db->from('market_company T1');
		$this->db->join('market_company_office T2', 'T1.CompanyID = T2.CompanyID');
		$this->db->join('parameter_securities_country T3', 'T2.CountryID = T3.CountryID');  
		if (isset($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
			if (is_array($request->params->OfficeCode)) 
				$this->db->where_in('T2.OfficeCode', $request->params->OfficeCode);
			else {
				if (isset($request->params->CountryID))
					$this->db->where('T3.CountryID', $request->params->CountryID);
				if (isset($request->params->office_keyword)) {
					$data = $this->security->xss_clean($request->params->office_keyword);
					$strKeyword = "(T2.OfficeCode LIKE '%".$data."%'"
								 ." or T2.OfficeName LIKE '%".$data."%')";
					$this->db->where($strKeyword);
				}
			}	
		} elseif (isset($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
			if (is_array($request->params->OfficeCode)) 
				$this->db->where_in('T2.OfficeCode', $request->params->OfficeCode);
			else {
				if (isset($request->params->CountryID))
					$this->db->where('T3.CountryID', $request->params->CountryID);
				if (isset($request->params->office_keyword)) {
					$data = $this->security->xss_clean($request->params->office_keyword);
					$strKeyword = "(T2.OfficeCode LIKE '%".$data."%'"
								 ." or T2.OfficeName LIKE '%".$data."%')";
					$this->db->where($strKeyword);
				}
			}	
		} elseif (isset($request->params->OfficeID)) {
			if (is_array($request->params->OfficeID)) 
				$this->db->where_in('T2.OfficeID', $request->params->OfficeID);
			else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'office parameter'])]];	
			}			
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'office parameter'])]];
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}	

	function office_id($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CompanyID, OfficeCode
		if (!isset($request->params->CompanyID) || empty($request->params->CompanyID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyID'])]];
		}
		if (!isset($request->params->OfficeCode) || empty($request->params->OfficeCode)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter OfficeCode'])]];
		}

		$row = $this->db->get_where('market_company_office', ['CompanyID' => $request->params->CompanyID,
																'OfficeCode' => $request->params->OfficeCode], 1)->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->OfficeCode])]];
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return [TRUE, ['result' => ['OfficeID' => $row->OfficeID]]];
	}

	function office_code($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: OfficeID
		if (!isset($request->params->OfficeID) || empty($request->params->OfficeID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter OfficeID'])]];
		}

		$row = $this->db->get_where('market_company_office', ['OfficeID' => $request->params->OfficeID], 1)->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->OfficeID])]];
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return [TRUE, ['result' => ['OfficeCode' => $row->OfficeCode]]];
	}

	function office_company($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: OfficeID
		if (!isset($request->params->OfficeID) || empty($request->params->OfficeID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter OfficeID'])]];
		}

		$row = $this->db->get_where('market_company_office', ['OfficeID' => $request->params->OfficeID], 1)->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->OfficeID])]];
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return [TRUE, ['result' => ['CompanyID' => $row->CompanyID]]];
	}

}