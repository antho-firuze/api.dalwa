<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sales {
	
	public $ci;

    function __construct()
	{
		$this->ci = &get_instance();
		$this->ci->load->database(DATABASE_MASTER);
	}

    
	function get_status_info($request, $StatusID)
	{
		$ci = $this->ci;
		$table = "(select * from mobc_status where StatusID = ?) g0";
		$table = $ci->f->compile_qry($table, [$StatusID]);
		$ci->db->from($table);
		if (! $row = $ci->db->get()->row())
			return FALSE;
			
		$request->{str_replace('get_', '', __FUNCTION__)} = $row;
		return TRUE;
	}

	function get_user_info($request)
	{
		$ci = $this->ci;
		$table = "(select * from mobc_prospect where simpiID = ? and emailID = ?) g0";
		$table = $ci->f->compile_qry($table, [$request->simpi_id, $request->emailID]);
		$ci->db->from($table);
		if (! $row = $ci->db->get()->row()) return FALSE;			
		$row->email = $row->CorrespondenceEmail;
		$row->full_name = ($row->NameFirst ? $row->NameFirst : '').
			(!empty(trim($row->NameMiddle)) ? ' '.$row->NameMiddle : '').
			($row->NameLast ? ' '.$row->NameLast : '');
		$request->{str_replace('get_', '', __FUNCTION__)} = $row;

		return TRUE;
	}

	function check_allow_delete_status($request, $arr_of_status)
	{
		$ci = $this->ci;
		$key = 'TrxID';

		if (!isset($request->params->{$key}) || empty($request->params->{$key}))
			return [FALSE, ['message' => $ci->f->lang('err_param_required', $key)]];

		$table = "(select TrxStatusID from mobc_order_subscription where TrxID = ?) g0 ";
		$table = $ci->f->compile_qry($table, [$request->params->TrxID]);
		$ci->db->from($table);
		if (! $row = $ci->db->get()->row())
			return [FALSE, ['message' => $ci->f->lang('err_unidentified', $key.':'.$request->params->{$key})]];

		if (! in_array($row->TrxStatusID, $arr_of_status))
			return [FALSE, ['message' => $ci->f->lang('err_transaction_delete_status')]];

		return [TRUE, NULL];
	}

	function check_is_mobc_login_valid($request)
	{
		$ci = $this->ci;
		$table = "(select * from mobc_login where simpiID = ? and emailID = ?) g0 ";
		$table = $ci->f->compile_qry($table, [$request->simpi_id, $request->params->emailID]);
		$ci->db->from($table);
		if (! $row = $ci->db->get()->row())
			return [FALSE, ['message' => $ci->f->lang('err_mobc_login_invalid')]];

		return [TRUE, NULL];
	}



}