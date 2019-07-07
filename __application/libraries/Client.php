<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Client {

	public $ci;

    function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database(DATABASE_MASTER);
		$this->ci->load->library('System');
	}

	function check_client($request)
	{
		$ci = $this->ci;
		$ci->db->select('T1.ClientID, T1.TypeID, T2.TreePrefix, T1.SID');  
		$ci->db->from('master_client T1');
		$ci->db->join('master_sales T2', 'T1.simpiID = T2.simpiID And T1.SalesID = T2.SalesID');  
		$ci->db->where('T1.simpiID', $request->simpi_id);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			$ci->db->where('T1.ClientID', $request->params->ClientID);
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			$ci->db->where('T1.ClientCode', $request->params->ClientCode);
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			$ci->db->where('T1.SID', $request->params->SID);
			$ci->db->where('(T1.TypeID = 1 or T1.TypeID = 2)');
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			$ci->db->where('T1.IFUA', $request->params->IFUA);
		} else {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'parameter client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if ($request->log_access == 'license') {
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
		} elseif ($request->log_access == 'session') {
			$ci->db->like('T2.TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$ci->db->where('T1.SID', $request->SID);
			return [TRUE, NULL];
		} elseif ($request->log_access == 'apps') {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}			
		$row = $ci->db->get()->row();
		if ($row) {
			$request->params->ClientID = $row->ClientID;
			$request->params->TypeID = $row->TypeID;
			return [TRUE, NULL];
		} else {
			$return = $ci->system->error_data('00-2', $request->LanguageID, 'master client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
	}

	function id_client($request)
	{
		$ci = $this->ci;

		$ci->db->select('CreditClient');  
		$ci->db->from('simpi_credit');
		$ci->db->where('simpiID', $request->simpi_id);
		if ($row = $ci->db->get()->row()) {
			$request->params->CreditClient = $row->CreditClient;
		} else {
			$return = $ci->system->error_data('03-1', $request->LanguageID, 'master client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$ci->db->from('master_client');
		$ci->db->where('simpiID', $request->simpi_id);
		$ci->db->where('(TypeID = 1 or TypeID = 2)');
		$request->params->TotalClient = $ci->db->count_all_results();

		if ($request->params->TotalClient >= $request->params->CreditClient) {
			$return = $ci->system->error_data('03-1', $request->LanguageID, 'master client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->params->ClientID = $ci->system->id_simpi($request->simpi_id, 'MASTER CLIENT', 2);
		$ci->db->select('FieldData');  
		$ci->db->from('codeset_simpi_data');
		$ci->db->where('simpiID', $request->simpi_id);
		$ci->db->where('FieldID', 5);
		$row = $ci->db->get()->row();
		if (!$row)
			$prefix = 'CIF';
		else 
			$prefix = $row->FieldData;	
		$request->params->CIF = $prefix.'-'.'1'.substr(date("Y", time()), -2).'-'.sprintf("%06d", $request->params->DataInt);		
		return [TRUE, NULL];
	}

}