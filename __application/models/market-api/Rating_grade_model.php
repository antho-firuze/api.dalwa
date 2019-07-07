<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Rating_grade_model extends CI_Model
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

		$this->db->select('T1.CompanyID, T2.RatingID, T2.RatingCode, T2.RatingDescription'); 
		$this->db->from('market_company T1');
		$this->db->join('parameter_securities_rating T2', 'T1.CompanyID = T2.CompanyID');
		if (isset($request->params->RatingID) && !empty($request->params->RatingID)) {
			$this->db->where('T2.RatingID', $request->params->CompanyID);
		} elseif (isset($request->params->RatingCode) && !empty($request->params->RatingCode) && 
					isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
			$this->db->where('T2.RatingCode', $request->params->RatingCode);
		} elseif (isset($request->params->RatingCode) && !empty($request->params->RatingCode) && 
					isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
			$this->db->where('T2.RatingCode', $request->params->RatingCode);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'rating parameter'])]];
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

		$this->db->select('T1.CompanyID, T2.RatingID, T2.RatingCode, T2.RatingDescription'); 
		$this->db->from('market_company T1');
		$this->db->join('parameter_securities_rating T2', 'T1.CompanyID = T2.CompanyID');
		if (isset($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
			if (is_array($request->params->RatingCode)) 
				$this->db->where_in('T2.RatingCode', $request->params->RatingCode);
			else {
				if (isset($request->params->CountryID))
					$this->db->where('T3.CountryID', $request->params->CountryID);
				if (isset($request->params->office_keyword)) {
					$data = $this->security->xss_clean($request->params->office_keyword);
					$strKeyword = "(T2.RatingCode LIKE '%".$data."%'"
								 ." or T2.RatingDescription LIKE '%".$data."%')";
					$this->db->where($strKeyword);
				}
			}	
		} elseif (isset($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
			if (is_array($request->params->RatingCode)) 
				$this->db->where_in('T2.RatingCode', $request->params->RatingCode);
			else {
				if (isset($request->params->CountryID))
					$this->db->where('T3.CountryID', $request->params->CountryID);
				if (isset($request->params->office_keyword)) {
					$data = $this->security->xss_clean($request->params->office_keyword);
					$strKeyword = "(T2.RatingCode LIKE '%".$data."%'"
								 ." or T2.RatingDescription LIKE '%".$data."%')";
					$this->db->where($strKeyword);
				}
			}	
		} elseif (isset($request->params->RatingID)) {
			if (is_array($request->params->RatingID)) 
				$this->db->where_in('T2.RatingID', $request->params->RatingID);
			else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'rating parameter'])]];	
			}			
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'rating parameter'])]];
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}	

}