<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Compliance_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MASTER);
		$this->load->library('System');
	}

	function kyc_search($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('T2.TypeID, T1.kycID, T1.kycCode, T1.kycDescription');  
		$this->db->from('parameter_client_kyc T1');
		$this->db->join('sales_kyc T2', 'T1.kycID = T2.kycID');  
		$this->db->where('T2.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function risk_client($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('T1.RiskID, T1.RiskCode, T1.RiskDescription, T2.MaximumValue');  
		$this->db->from('parameter_client_risklevel T1');
		$this->db->join('sales_risklevel T2', 'T1.RiskID = T2.RiskID');  
		$this->db->where('T2.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function risk_question($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('TypeID, QuestionNo, QuestionText');  
		$this->db->from('sales_risklevel_questioner');
		$this->db->where('simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function risk_answer($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('TypeID, QuestionNo, OptionNo, OptionText, OptionValue');  
		$this->db->from('sales_risklevel_answer');
		$this->db->where('simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function codeset_search($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('T1.FieldID, T1.FieldCode, T1.FieldDescription, T2.FieldData');  
		$this->db->from('codeset_portfolio_field T1');
		$this->db->join('codeset_portfolio_data T2', 'T1.FieldID = T2.FieldID');  
		$this->db->where('T2.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function codeset_get($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];
		//cek parameter: FieldID
		if (!isset($request->params->FieldID) || empty($request->params->FieldID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter FieldID'])]];
		}

		$this->db->select('FieldData');  
		$this->db->from('codeset_portfolio_data');
		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('FieldID', $request->params->FieldID);
		$row = $this->db->get()->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'codeset simpi'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['FieldData' => $row->FieldData]]];
	}

	function term_search($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('T1.TermID, T1.TermName, T2.Term1, T2.Term2');  
		$this->db->from('simpi_term T1');
		$this->db->join('simpi_term_data T2', 'T1.TermID = T2.TermID');  
		$this->db->where('T2.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function term_get1($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];
		//cek parameter: TermID
		if (!isset($request->params->TermID) || empty($request->params->TermID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TermID'])]];
		}

		$this->db->select('Term1');  
		$this->db->from('simpi_term_data');
		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('TermID', $request->params->TermID);
		$row = $this->db->get()->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'term simpi'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['Term1' => $row->Term1]]];
	}

	function term_get2($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];
		//cek parameter: TermID
		if (!isset($request->params->TermID) || empty($request->params->TermID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TermID'])]];
		}

		$this->db->select('Term2');  
		$this->db->from('simpi_term_data');
		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('TermID', $request->params->TermID);
		$row = $this->db->get()->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'term simpi'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['Term2' => $row->Term2]]];
	}

	function aml_risk($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('RiskID, RiskCode, RiskDescription, RiskValue, LimitIndividual, LimitInstitution');  
		$this->db->from('grc_aml_risklevel');
		$this->db->where('simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function aml_business_activity($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('T3.ActivityID, T3.ActivityCode, T2.RiskID, T2.RiskCode, T2.RiskValue');  
		$this->db->from('grc_aml_businessactivity T1');
		$this->db->join('grc_aml_risklevel T2', 'T1.RiskID = T2.RiskID');  
		$this->db->join('parameter_client_businessactivity T3', 'T1.ActivityID = T3.ActivityID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function aml_business_type($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('T3.TypeID, T3.TypeCode, T2.RiskID, T2.RiskCode, T2.RiskValue');  
		$this->db->from('grc_aml_businesstype T1');
		$this->db->join('grc_aml_risklevel T2', 'T1.RiskID = T2.RiskID');  
		$this->db->join('parameter_client_businesstype T3', 'T1.TypeID = T3.TypeID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function aml_occupation($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('T3.OccupationID, T3.OccupationCode, T2.RiskID, T2.RiskCode, T2.RiskValue');  
		$this->db->from('grc_aml_occupation T1');
		$this->db->join('grc_aml_risklevel T2', 'T1.RiskID = T2.RiskID');  
		$this->db->join('parameter_client_occupation T3', 'T1.OccupationID = T3.OccupationID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function aml_province($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

        $this->db->select('T3.ProvinceID, T3.Province, T2.RiskID, T2.RiskCode, T2.RiskValue');  
		$this->db->from('grc_aml_province T1');
		$this->db->join('grc_aml_risklevel T2', 'T1.RiskID = T2.RiskID');  
		$this->db->join('parameter_securities_country_province T3', 'T1.ProvinceID = T3.ProvinceID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}		
	
}
