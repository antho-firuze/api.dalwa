<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_bank_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}

    function interest_accrued($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('InterestAccruedID, InterestAccruedCode, InterestAccruedDescription')	;
    $this->db->from('parameter_bank_interestaccrued');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
    }

    function interest_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('InterestTypeID, InterestTypeCode, InterestTypeDescription')	;
    	$this->db->from('parameter_bank_interesttype');
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);

		return $data;
    }

	function td_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TDStatusID, TDStatusCode, TDStatusDescription')	;
    	$this->db->from('parameter_bank_tdstatus');
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);

		return $data;
    }

	function td_term($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TDTermID, TDTermCode, TDTermDescription')	;
    	$this->db->from('parameter_bank_tdterm');
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);

		return $data;
    }

	function td_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TDTypeID, TDTypeCode, TDTypeDescription');
    	$this->db->from('parameter_bank_tdtype');
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);

		return $data;
    }

	function bank_transfer($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TransferTypeID, TransferTypeCode, TransferTypeDescription')	;
    	$this->db->from('parameter_bank_transfertype');
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);

		return $data;
    }

	function bank_type($request)
	{
		//cek akses: by 4 method 
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('BankTypeID, BankTypeCode, BankTypeDescription')	;
    	$this->db->from('parameter_bank_banktype');
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);

		return $data;
    }

}