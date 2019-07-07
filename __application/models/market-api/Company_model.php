<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Company_model extends CI_Model
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
			$this->db->select('CompanyID, CompanyCode, CompanyName, TypeID, CompanyAddress, 
								CompanyPhone, CompanyFax, CompanyWeb, CompanyEmail, IsPrivate, CountryID');
		$this->db->from('market_company');
		$this->db->where('IsPrivate', 'N');
		if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('CompanyID', $request->params->CompanyID);
		} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('CompanyCode', $request->params->CompanyCode);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'company parameter'])]];
		}
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
	}

	function search($request)
	{
		//cek akses:  
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('CompanyID, CompanyCode, CompanyName, TypeID, CompanyAddress, 
								CompanyPhone, CompanyFax, CompanyWeb, CompanyEmail, IsPrivate, CountryID');
		$this->db->from('market_company');
		$this->db->where('IsPrivate', 'N');
		if (isset($request->params->CompanyID)) {
			if (is_array($request->params->CompanyID)) {
				$this->db->where_in('CompanyID', $request->params->CompanyID);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyID'])]];	
			}
		} elseif (isset($request->params->CompanyCode)) {
			if (is_array($request->params->CompanyCode)) {
				$this->db->where_in('CompanyCode', $request->params->CompanyCode);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyCode'])]];	
			}
		} else {
			if (isset($request->params->TypeID))
				$this->db->where('TypeID', $request->params->TypeID);
			if (isset($request->params->CountryID))
				$this->db->where('CountryID', $request->params->CountryID);
			if (isset($request->params->company_keyword)) {
				$data = $this->security->xss_clean($request->params->company_keyword);
				$strKeyword = "(CompanyCode LIKE '%".$data."%'"
							 ." or CompanyName LIKE '%".$data."%')";
				$this->db->where($strKeyword);
			}
		}
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
	} 

	function company_id($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CompanyCode
		if (!isset($request->params->CompanyCode) || empty($request->params->CompanyCode)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyCode'])]];
		}

		$row = $this->db->get_where('market_company', ['CompanyCode' => $request->params->CompanyCode], 1)->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->CompanyCode])]];
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return [TRUE, ['result' => ['CompanyID' => $row->CompanyID]]];
	}

	function company_code($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CompanyID
		if (!isset($request->params->CompanyID) || empty($request->params->CompanyID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyID'])]];
		}

		$row = $this->db->get_where('market_company', ['CompanyID' => $request->params->CompanyID], 1)->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->CompanyID])]];
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return [TRUE, ['result' => ['CompanyCode' => $row->CompanyCode]]];
	}

}
