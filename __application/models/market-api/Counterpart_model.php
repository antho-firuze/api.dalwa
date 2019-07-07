<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Counterpart_model extends CI_Model
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

		//cek parameter: SystemID 
		if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
		}

		$this->db->select('T1.CompanyID, T2.BrokerCode, T2.EQGrossNet, T2.EQRounding, T2.BrokerCode, 
							T2.EQCommPercent, T2.EQLevyPercent, T2.EQVATPercent, T2.EQWHTPercent, T2.EQSalesPercent');  
		$this->db->from('market_company T1');
		$this->db->join('market_company_counterpart T2', 'T1.CompanyID = T2.CompanyID');  
		if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
		} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
		} elseif (isset($request->params->BrokerCode) && !empty($request->params->BrokerCode)) {
			$this->db->where('T2.BrokerCode', $request->params->BrokerCode);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'counterpart parameter'])]];
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

		$this->db->select('T1.CompanyID, T2.BrokerCode, T2.EQGrossNet, T2.EQRounding, T2.BrokerCode, 
							T2.EQCommPercent, T2.EQLevyPercent, T2.EQVATPercent, T2.EQWHTPercent, T2.EQSalesPercent');  
		$this->db->from('market_company T1');
		$this->db->join('market_company_counterpart T2', 'T1.CompanyID = T2.CompanyID');  
		if (isset($request->params->CompanyID)) {
			if (is_array($request->params->CompanyID)) {
				$this->db->where_in('T1.CompanyID', $request->params->CompanyID);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyID'])]];	
			}
		} elseif (isset($request->params->CompanyCode)) {
			if (is_array($request->params->CompanyCode)) {
				$this->db->where_in('T1.CompanyCode', $request->params->CompanyCode);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CompanyCode'])]];	
			}
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

}