<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_fa_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}
	
	function asset($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AssetID, AssetCode, AssetDescription');
	   	$this->db->from('parameter_afa_asset');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function charges($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('FeeID, FeeCode, FeeDescription');
	   	$this->db->from('parameter_afa_charges');
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

		$this->db->select('TrxLinkID, TrxLinkCode, TrxLinkDescription');
	   	$this->db->from('parameter_afa_transaction_trxlink');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function trx_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TrxTypeID, TrxTypeCode, TrxTypeDescription');
	   	$this->db->from('parameter_afa_transaction_trxtype');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
}
