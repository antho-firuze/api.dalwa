<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Bank_days_company_model extends CI_Model
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
			$this->db->select('T1.CompanyID, T2.DaysInYearID, T4.DaysInYearCode, T2.DaysInMonthID, T3.DaysInMonthCode');
		$this->db->from('market_company T1');
		$this->db->join('market_company_deposit_days T2', 'T1.CompanyID = T2.CompanyID');
		$this->db->join('parameter_securities_daysinmonth T3', 'T2.DaysInMonthID = T3.DaysInMonthID');  
		$this->db->join('parameter_securities_daysinyear T4', 'T2.DaysInYearID = T4.DaysInYearID');
		if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
		} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'bank parameter'])]];
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
			$this->db->select('T1.CompanyID, T2.DaysInYearID, T4.DaysInYearCode, T2.DaysInMonthID, T3.DaysInMonthCode');
		$this->db->from('market_company T1');
		$this->db->join('market_company_deposit_days T2', 'T1.CompanyID = T2.CompanyID');
		$this->db->join('parameter_securities_daysinmonth T3', 'T2.DaysInMonthID = T3.DaysInMonthID');  
		$this->db->join('parameter_securities_daysinyear T4', 'T2.DaysInYearID = T4.DaysInYearID');
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