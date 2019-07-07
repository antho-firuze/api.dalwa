<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

// require_once(APPPATH.'models/Base_model.php');

class Master_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_NAME);
	}
	
	function test($request)
	{
		// list($success, $return) = $this->f->is_valid_apimethod($request);
		// if (!$success) return [FALSE, $return];
		
		return [TRUE, ['message' => $this->f->lang('success_login')]];
	}
	
	function businessactivity($request)
	{
		// list($success, $return) = $this->f->is_valid_token($request);
		// if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_businessactivity');
		return $this->f->get_result($request);
	}
	
	function businesstype($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_businesstype');
		return $this->f->get_result($request);
	}
	
	function documenttype($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_documenttype');
		return $this->f->get_result($request);
	}
	
	function educationlevel($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_educationlevel');
		return $this->f->get_result($request);
	}
	
	function educationtype($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_educationtype');
		return $this->f->get_result($request);
	}
	
	function idcardtype($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_idcardtype');
		return $this->f->get_result($request);
	}
	
	function maritalstatus($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_maritalstatus');
		return $this->f->get_result($request);
	}
	
	function occupation($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_occupation');
		return $this->f->get_result($request);
	}
	
	function religion($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_religion');
		return $this->f->get_result($request);
	}
	
	function risk($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_risklevel');
		return $this->f->get_result($request);
	}
	
	function ccy($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_securities_ccy');
		return $this->f->get_result($request);
	}
	
	function kyc_IncomeLevel($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_kyc_answer')->where(['kycID'=>3]);
		return $this->f->get_result($request);
	}
	
	function kyc_InvestmentObjective($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_kyc_answer')->where(['kycID'=>1]);
		return $this->f->get_result($request);
	}
	
	function kyc_SourceOfFund($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_kyc_answer')->where(['kycID'=>15]);
		return $this->f->get_result($request);
	}
	
	function kyc_fatca($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_kyc_answer')->where(['kycID'=>46]);
		return $this->f->get_result($request);
	}
	
	function BankCode($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
			select a.CompanyID, a.CompanyCode, a.CompanyName, b.CompanyExternalCode as BankCode 
			from market_company as a
			inner join market_company_id_external as b on a.CompanyID = b.CompanyID
			where b.SystemID = 8
		) g0';
		$table = $this->f->compile_qry($str, []);
		$this->db->from($table);
		return $this->f->get_result($request);
	}
	
	function country($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_securities_country');
		return $this->f->get_result($request);
	}
	
	function province($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_securities_country_province');
		return $this->f->get_result($request);
	}
	
	function city($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_securities_country_city');
		return $this->f->get_result($request);
	}
	
	function country_cache($request)
	{
		list($success, $return) = $this->f->is_memcache_ok($request);
		if ($success) {
			if ($cache = $this->cache->get($request->method))
				return [TRUE, ['cache' => $request->memcache, 'result' =>  $cache]];
		}

		$this->db->from('parameter_securities_country');
		return $this->f->get_result($request);
	}
}
