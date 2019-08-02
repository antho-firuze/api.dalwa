<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Bill_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
	}
  
  function dashboard($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select a.bill_id, a.partner_id, a.bill_status_id, bill_no, due_date, pay_date, amount, charge, total, 
      reg_no, first_name, last_name, region, class_diniyah, class_umum, room, sex, c.desc as bill_status 
			from bill as a 
      inner join c_partner as b on a.partner_id = b.partner_id 
      inner join bill_status as c on a.bill_status_id = c.bill_status_id 
      where a.bill_status_id = 1 and DATE_FORMAT(a.due_date,"%Y%m") <= DATE_FORMAT(now(),"%Y%m") and a.total > 0 and b.parent_id = ? 
		) g0';
		$table = $this->f->compile_qry($str, [$request->partner_id]);
		$this->db->from($table);
		return $this->f->get_result_($request);
  }

  function list($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select a.bill_id, a.partner_id, a.bill_status_id, bill_no, due_date, pay_date, amount, charge, total, 
      reg_no, first_name, last_name, region, class_diniyah, class_umum, room, sex, c.desc as bill_status 
			from bill as a 
      inner join c_partner as b on a.partner_id = b.partner_id 
      inner join bill_status as c on a.bill_status_id = c.bill_status_id 
      where DATE_FORMAT(a.due_date,"%Y%m") <= DATE_FORMAT(now(),"%Y%m") and a.total > 0 and a.client_id = ? and b.parent_id = ? 
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->partner_id]);
		$this->db->from($table);
		return $this->f->get_result_($request);
  }

}