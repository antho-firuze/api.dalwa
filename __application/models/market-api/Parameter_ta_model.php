<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_ta_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}
	
	function charges($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('FeeID, FeeCode, FeeDescription')	;
    	$this->db->from('parameter_ata_charges');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function distribution_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('StatusID, StatusCode, StatusDescription')	;
    	$this->db->from('parameter_ata_distributionstatus');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function distribution_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TypeID, TypeCode, TypeDescription')	;
    	$this->db->from('parameter_ata_distributiontype');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function order_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('StatusID, StatusCode, StatusDescription')	;
    	$this->db->from('parameter_ata_status');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function order_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TypeID, TypeCode, TypeDescription')	;
    	$this->db->from('parameter_ata_order');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function trx_flag($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TrxFlagID, TrxFlagCode, TrxFlagDescription')	;
    	$this->db->from('parameter_ata_transaction_trxflag');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function trx_link($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TrxLinkID, TrxLinkCode, TrxLinkDescription')	;
    	$this->db->from('parameter_ata_transaction_trxlink');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function trx_type1($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TrxTypeID, TrxTypeCode, TrxTypeDescription')	;
    	$this->db->from('parameter_ata_transaction_trxtype1');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function trx_type2($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TrxTypeID, TrxType1, TrxTypeCode, TrxTypeDescription');
    	$this->db->from('parameter_ata_transaction_trxtype2');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
}
