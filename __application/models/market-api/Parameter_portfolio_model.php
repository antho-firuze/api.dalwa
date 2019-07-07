<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_portfolio_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}
	
	function portfolio_account($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AccountID, AccountCode, AccountDescription');
	   	$this->db->from('parameter_portfolio_account');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  	}
	
	function asset_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('AssetTypeID, AssetTypeCode, AssetTypeDescription');
	   	$this->db->from('parameter_portfolio_assettype');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}
	
	function benchmark_calculation($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('CalculationID, CalculationCode, CalculationDescription');
	   	$this->db->from('parameter_portfolio_benchmarkcalculation');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}
	
	function benchmark_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('BenchmarkTypeID, BenchmarkTypeCode, BenchmarkTypeDescription');
	   	$this->db->from('parameter_portfolio_benchmarktype');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}
	
	function portfolio_cpf($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, TypeCode, TypeDescription');
	   	$this->db->from('parameter_portfolio_cpf');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}
	
	function portfolio_days($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('DaysID, DaysCode, DaysDescription');
	   	$this->db->from('parameter_portfolio_days');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}

	function override_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('OverrideTypeID, OverrideTypeCode, OverrideTypeDescription');
	   	$this->db->from('parameter_portfolio_overridetype');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}

	function portfolio_pricing($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('PricingID, PricingCode, PricingDescription');
	   	$this->db->from('parameter_portfolio_pricing');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}

	function portfolio_return($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('ReturnID, ReturnCode, ReturnDescription');
	   	$this->db->from('parameter_portfolio_return');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}

	function portfolio_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('StatusID, StatusCode, StatusDescription');
	   	$this->db->from('parameter_portfolio_status');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}

	function transaction_apply($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('ApplyID, ApplyCode, ApplyDescription');
	   	$this->db->from('parameter_portfolio_transactionapply');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}
  
	function portfolio_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, TypeCode, TypeDescription');
	   	$this->db->from('parameter_portfolio_type');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}

}
