<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simpi Class Library
 *
 * This class contain various functions for Simpi Helper
 *
 */
class Simpi {
	
	public $ci;
	
	function __construct()
	{
		$this->ci =& get_instance();
	}

	function get_simpi_info($request)
	{
		$ci = $this->ci;
	    $this->ci->load->database(DATABASE_SYSTEM);
		$str = "select simpiID, simpiName, simpiNameshort, simpiAddress, simpiEmail, simpiPhone, simpiContact 
			    from master_simpi where simpiID = ?";
		$row = $ci->db->query($str, [$request->simpi_id])->row();
		if (!$row) return FALSE;
		$request->simpi_info = $row;

		return TRUE;
	}
	
	function get_default_currency($request)
	{
		$ci = $this->ci;
	    $this->ci->load->database(DATABASE_SYSTEM);
		$row = $ci->db->get_where('master_simpi', ['simpiID' => $request->simpi_id], 1)->row();
		if (!$row) return [FALSE, ['message' => $ci->f->lang('err_default_currency')]];	
		$request->params->CcyID = $row->CcyID;
		$request->params->CountryID = $row->CountryID;
		
		return [TRUE, NULL];
	}
	
	function check_valid_date($request, $keys = [])
	{
		$ci = $this->ci;
		$ci->load->helper('datetime');
		foreach($keys as $key)
			if (isset($request->params->{$key}) || !empty($request->params->{$key})) 
				if (!is_valid_date($request->params->{$key}, 'yyyy-mm-dd'))
					return [FALSE, ['message' => $ci->f->lang('err_invalid_date', [$key, 'yyyy-mm-dd'])]];

		return [TRUE, NULL];
	}
	
	function generate_id_simpi($simpi_id, $field_code, $field_type = 1)
	{
		$ci = &get_instance();
		$ci->load->database(DATABASE_SYSTEM);
		$row = $ci->db->get_where('simpi_id', ['FieldCode' => $field_code, 'simpiID' => $simpi_id], 1)->row();
		if (!$row) {
			$dataID = 1;
			if ($field_type == 1) 
				$data = ['simpiID' => $simpi_id, 
						 'FieldCode' => $field_code, 
						 'FieldType' => 1, 
						 'DataInt' => 0,
					 	 'DataBig' => 1];
			else 
				$data = ['simpiID' => $simpi_id, 
						 'FieldCode' => $field_code, 
						 'FieldType' => 2, 
						 'DataInt' => 1,
			 			 'DataBig' => 0];
			$ci->db->insert('simpi_id', $data);
		} else {
			if ($row->FieldType == 1) {
				$dataID = $row->DataBig + 1;
				$ci->db->update('simpi_id', ['DataBig' => $dataID], ['FieldCode' => $field_code, 'simpiID' => $simpi_id]);
			} else {
				$dataID = $row->DataInt + 1;
				$ci->db->update('simpi_id', ['DataInt' => $dataID], ['FieldCode' => $field_code, 'simpiID' => $simpi_id]);
			}
		}
		
		return [TRUE, ['dataID' => $dataID]];
	}
	
	function get_data_from_db($request, $table, $field, $where)
	{
		$ci = $this->ci;
		$request->params->{$field} = $ci->db->select($field)->from($table)->where($where)->get()->row()->{$field};
	}

	function commit_data($request, $tbl)
	{
		$ci = $this->ci;
		$ci->db->trans_strict(TRUE);
		$ci->db->trans_start();
		foreach($tbl as $tbl_name => $fields) {
			$data = [];

			if ($tbl_name == 'master_client_kyc') {
				foreach($tbl[$tbl_name] as $rows) {
					$data = [];
					foreach($rows as $k => $v) {
						if ($k === 'kycID') {
							$data[$k] = $v;
							continue;
						} 
		
						if (!isset($request->params->{$v}))
							continue 2;
		
						if (is_numeric($k)) 
							$data[$v] = !empty($request->params->{$v}) ? $request->params->{$v} : NULL;
					 	else 
							$data[$k] = !empty($request->params->{$v}) ? $request->params->{$v} : NULL;
						
					}		
					$ci->db->insert($tbl_name, $data);
				}			
				continue;
			}

			if ($tbl_name == 'mobc_mail_queue') {
				foreach($tbl[$tbl_name] as $k => $v) {
					if (is_numeric($k)) 
						$email[$v] = !empty($request->params->{$v}) ? $request->params->{$v} : NULL;
					else 
						$email[$k] = !empty($request->params->{$v}) ? $request->params->{$v} : NULL;
				}			
				Simpi::mail_queue($email);
				continue;
			}

			foreach($tbl[$tbl_name] as $k => $v) {
				if (!isset($request->params->{$v}))
					continue;

				if (is_numeric($k)) {
					$data[$v] = !empty($request->params->{$v}) ? $request->params->{$v} : 0;
				} else {
					$data[$k] = !empty($request->params->{$v}) ? $request->params->{$v} : NULL;
				}
					
			}

			$ci->db->insert($tbl_name, $data);
		}

		$ci->db->trans_complete();
		if ($ci->db->trans_status() === FALSE)
		{
			return [FALSE, ['message' => $ci->f->lang('err_commit_data')]];
			// return [FALSE, ['message' => $ci->db->last_query()]];
			// return [FALSE, ['message' => $ci->db->error()['message']]];
		}

		return [TRUE, NULL];
	}

	function check_valid_sender_email($request)
	{
		$ci = &get_instance();
		$ci->load->database(DATABASE_GATEWAY);
		$key = 'senderCode';

		if (!isset($request->params->{$key}) || empty($request->params->{$key}))
			return [FALSE, ['message' => $ci->f->lang('err_param_required', $key)]];

		$table = "(SELECT * FROM mail_setting_sender where simpiID = ? and senderCode = ?) g0 ";
		$table = $ci->f->compile_qry($table, [$request->simpi_id, $request->params->{$key}]);
		$ci->db->from($table);
		if (! $row = $ci->db->get()->row())
			return [FALSE, ['message' => $ci->f->lang('err_unidentified', $key.':'.$request->params->{$key})]];

		$request->params->senderID = $row->senderID;

		return [TRUE, NULL];
	}

	function check_is_simpi_user_valid($request)
	{
		$ci = $this->ci;
		$ci->load->database(DATABASE_GATEWAY);

		$table = "(select * from simpi_user where simpiID = ? and UserID = ?) g0 ";
		$table = $ci->f->compile_qry($table, [$request->simpi_id, $request->params->UserID]);
		$ci->db->from($table);
		if (! $row = $ci->db->get()->row())
			return [FALSE, ['message' => $ci->f->lang('err_simpi_user_invalid')]];

		return [TRUE, NULL];
	}
	
	
}