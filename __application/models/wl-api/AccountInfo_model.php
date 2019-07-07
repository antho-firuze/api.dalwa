<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class AccountInfo_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}
	
	//A1
	function education($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('LevelID, LevelCode');
		
		$this->db->from('parameter_client_educationlevel');
		return $this->f->get_result($request);
	}
	
	//A2
	function religion($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('ReligionID, ReligionCode');
		
		$this->db->from('parameter_client_religion');
		return $this->f->get_result($request);
	}
	
	//A3
	function occupation($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('OccupationID, OccupationCode');
		
		$this->db->from('parameter_client_occupation');
		return $this->f->get_result($request);
	}
	
	//A4
	function incomelevel($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('AnswerID, AnswerText');
		
		$this->db->from('parameter_client_kyc_answer')->where('kycID=3');
		return $this->f->get_result($request);
	}
		
	//A5
	function maritalstatus($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('StatusID, StatusCode');
		
		$this->db->from('parameter_client_maritalstatus');
		return $this->f->get_result($request);
	}
	
	//A6
	function risklevel($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('RiskID, RiskCode');
		
		$this->db->from('parameter_client_risklevel');
		return $this->f->get_result($request);
	}
	
	//A7
	function investmentobjective($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('AnswerID, AnswerText');
		
		$this->db->from('parameter_client_kyc_answer')->where('kycID=1');
		return $this->f->get_result($request);
	}
	
	//A8
	function sourceoffund($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('AnswerID, AnswerText');
		
		$this->db->from('parameter_client_kyc_answer')->where('kycID=15');
		return $this->f->get_result($request);
	}
	
	//A9
	function assetowner($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('AnswerID, AnswerText');
		
		$this->db->from('parameter_client_kyc_answer')->where('kycID=44');
		return $this->f->get_result($request);
	}
	
	//A10
	function country($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_securities_country');
		return $this->f->get_result($request);
	}
	
	//A11
	function city($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('CityCode, CityName');

		$this->db->from('parameter_securities_country_city');
		return $this->f->get_result($request);
	}
	
	//A12
	function fatcastatus($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('AnswerID, AnswerText');

		$this->db->from('parameter_client_kyc_answer')->where('kycID=46');
		return $this->f->get_result($request);
	}
	
	//A13
	function bankcode($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('CompanyID, CompanyCode, CompanyName, BankCode');
		
		$table = '(
			select a.CompanyID, a.CompanyCode, a.CompanyName, b.CompanyExternalCode as BankCode 
			from market_company as a
			inner join market_company_id_external as b on a.CompanyID = b.CompanyID
			where b.SystemID = 8
		) g0';
		$this->db->from($table);
		return $this->f->get_result($request);
	}
	
	//A14
	function riskvalue($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('RiskID, RiskCode, MaximumValue');

		$str = '(
			select a.RiskID, b.RiskCode, a.MaximumValue 
			from sales_risklevel a join parameter_client_risklevel b on a.RiskID = b.RiskID 
			where a.simpiID = ?
		) g0';
		$table = $this->f->compile_qry($str, [$request->simpiID]);
		$this->db->from($table);
		return $this->f->get_result($request);
	}
	
	//A15
	function riskscorelevel($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->RiskScore) || empty($request->params->RiskScore))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'RiskScore')]];
		
		$str = 'select a.RiskID, b.RiskCode 
			from sales_risklevel a join parameter_client_risklevel b on a.RiskID = b.RiskID 
			where a.simpiID = ? and a.MaximumValue <= ? order by a.MaximumValue desc';
		$qry = $this->db->query($str, [$request->simpiID, $request->params->RiskScore]);
		if ($qry->num_rows() < 1) {
			$qry->free_result();

			$str = 'select a.RiskID, b.RiskCode 
			from sales_risklevel a join parameter_client_risklevel b on a.RiskID = b.RiskID 
			where a.simpiID = ? order by a.MaximumValue';
			$qry = $this->db->query($str, [$request->simpiID]);
			if ($qry->num_rows() < 1) {
				return [FALSE, ['message' => 'Records not found']]; 
			}
		}

		return [TRUE, ['result' => $qry->row()]];
	}
	
	//A16
	function ccy($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('CcyID, Ccy');
		
		$this->db->from('parameter_securities_ccy');
		return $this->f->get_result($request);
	}

	//A17
	function riskquestioner($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('TypeID, TypeCode, QuestionNo, QuestionText');

		$str = '(
			select a.TypeID, a.TypeCode, b.QuestionNo, b.QuestionText  
			from parameter_client_type AS a   
			inner join sales_risklevel_questioner AS b on b.TypeID = a.TypeID  
			where b.simpiID = ?
		) g0';
		$table = $this->f->compile_qry($str, [$request->simpiID]);
		$this->db->from($table);
		$result = $this->db->get()->result();
		foreach($result as $key => $val) {
			$str2 = '';
			$result2 = $this->db
				->select('OptionNo, OptionText, OptionValue ')
				->where([
					'simpiID' => $request->simpiID, 
					'QuestionNo' => $val->QuestionNo,
					'TypeID' => $val->TypeID
				])
				->get('sales_risklevel_answer')
				->result();
			$val->Options = $result2;
		}
		
		return [TRUE, ['result' => $result]];
	}
	
}
