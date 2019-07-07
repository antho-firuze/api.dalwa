<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Contact_model extends CI_Model
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

		if (!isset($request->params->ContactID) || empty($request->params->ContactID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter ContactID'])]];
		}

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('T1.CompanyID, T2.OfficeID, T4.PositionID, T4.PositionCode, T3.ContactID, T3.ContactName, 
								T3.ContactPhone, T3.ContactExt, T3.ContactEmail, T3.ContactHP');
		$this->db->from('market_company T1'); 
		$this->db->join('market_company_office T2', 'T1.CompanyID = T2.CompanyID');
		$this->db->join('market_company_contact T3', 'T2.OfficeID = T3.OfficeID');
		$this->db->join('parameter_securities_company_position T4', 'T3.PositionID = T4.PositionID');
  		$this->db->where('T3.ContactID', $request->params->ContactID, NULL, FALSE);	
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
			$this->db->select('T1.CompanyID, T2.OfficeID, T4.PositionID, T4.PositionCode, T3.ContactID, T3.ContactName, 
								T3.ContactPhone, T3.ContactExt, T3.ContactEmail, T3.ContactHP');
		$this->db->from('market_company T1'); 
		$this->db->join('market_company_office T2', 'T1.CompanyID = T2.CompanyID');
		$this->db->join('market_company_contact T3', 'T2.OfficeID = T3.OfficeID');
		$this->db->join('parameter_securities_company_position T4', 'T3.PositionID = T4.PositionID');
		if (isset($request->params->CompanyID)) {
			$this->db->where('T1.CompanyID', $request->params->CompanyID);
			if (isset($request->params->PositionID))
				$this->db->where('T4.PositionID', $request->params->PositionID);
			if (isset($request->params->contact_keyword))  
				$this->db->like('T3.ContactName', $request->params->contact_keyword);
		} elseif (isset($request->params->CompanyCode)) {
			$this->db->where('T1.CompanyCode', $request->params->CompanyCode);
			if (isset($request->params->PositionID))
				$this->db->where('T4.PositionID', $request->params->PositionID);
			if (isset($request->params->contact_keyword))  
				$this->db->like('T3.ContactName', $request->params->contact_keyword);
		} elseif (isset($request->params->ContactID)) {
			if (is_array($request->params->ContactID)) 
				$this->db->where_in('T3.ContactID', $request->params->ContactID);
			else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'contact parameter'])]];	
			}			
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'contact parameter'])]];
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}	

}