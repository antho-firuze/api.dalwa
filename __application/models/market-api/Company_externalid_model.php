<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Company_externalid_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}

	function load($request)
	{
		//cek akses:  
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SystemID 
		if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
		}

		$this->db->select('T1.CompanyID, T2.SystemID, T3.SystemCode, T3.SystemName, T2.CompanyExternalCode');  
		$this->db->from('market_company T1');
		$this->db->join('market_company_id_external T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->join('parameter_securities_externalsystem T3', 'T2.SystemID = T3.SystemID');  
		$this->db->where('T2.SystemID', $request->params->SystemID);
		if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
		} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
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
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('T1.CompanyID, T2.SystemID, T3.SystemCode, T3.SystemName, T2.CompanyExternalCode');  
		$this->db->from('market_company T1');
		$this->db->join('market_company_id_external T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->join('parameter_securities_externalsystem T3', 'T2.SystemID = T3.SystemID');  
		if (isset($request->params->SystemID) && !empty($request->params->SystemID)) 
			$this->db->where('T2.SystemID', $request->params->SystemID);
		if (isset($request->params->CompanyID)) {
			if (is_array($request->params->CompanyID)) 
				$this->db->where_in('T1.CompanyID', $request->params->CompanyID);
			else 
				$this->db->where('T1.CompanyID', $request->params->CompanyID);
		} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			if (is_array($request->params->CompanyCode)) 
				$this->db->where_in('T1.CompanyCode', $request->params->CompanyCode);
			else 
				$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
		} else {
			if (isset($request->params->TypeID))
				$this->db->where('T1.TypeID', $request->params->TypeID);
			if (isset($request->params->CountryID))
				$this->db->where('T1.CountryID', $request->params->CountryID);
			if (isset($request->params->company_keyword)) {
				$data = $this->security->xss_clean($request->params->company_keyword);
				$strKeyword = "(T1.CompanyCode LIKE '%".$data."%'"
							 ." or T1.CompanyName LIKE '%".$data."%')";
				$this->db->where($strKeyword);
			}
		}			
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;			 
	}

	function external_get($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter:  SystemID --> sumber external identification 
		if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
		}

		$this->db->select('T1.CompanyExternalCode');
		$this->db->from('market_company_id_external T1');
		$this->db->join('market_company T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->where('T1.SystemID', $request->params->SystemID);
		if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('T2.CompanyID', $request->params->CompanyID);
		} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('T2.CompanyCode', $request->params->CompanyCode);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'company parameter'])]];
		}
		$row = $this->db->get()->row();
        if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market company'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['CompanyExternalCode' => $row->CompanyExternalCode]]];
	}

	function external_code($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CompanyExternalCode
		if (!isset($request->params->CompanyExternalCode) || empty($request->params->CompanyExternalCode)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyExternalCode'])]];
		}

		//cek parameter: SystemID --> sumber external identification 
		if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
		}

		$this->db->select('T2.CompanyCode');
		$this->db->from('market_company_id_external T1');
		$this->db->join('market_company T2', 'T1.CompanyID = T2.CompanyID');  
		$this->db->where('T1.SystemID', $request->params->SystemID);
		$this->db->where('T1.CompanyExternalCode', $request->params->CompanyExternalCode);
		$row = $this->db->get()->row();
        if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market company'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['CompanyCode' => $row->CompanyCode]]];
	}	

	function external_id($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CompanyExternalCode
		if (!isset($request->params->CompanyExternalCode) || empty($request->params->CompanyExternalCode)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyExternalCode'])]];
		}

		//cek parameter: SystemID --> sumber external identification 
		if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
		}

		$this->db->select('CompanyID');
		$this->db->from('market_company_id_external');
		$this->db->where('SystemID', $request->params->SystemID);
		$this->db->where('CompanyExternalCode', $request->params->CompanyExternalCode);
		$row = $this->db->get()->row();
        if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market company'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['CompanyID' => $row->CompanyID]]];
	}	

}