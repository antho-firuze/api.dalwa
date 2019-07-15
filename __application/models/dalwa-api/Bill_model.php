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
      select a.id as bill_id, partner_id, bill_status_id, bill_no, bill_due_date, bill_pay_date, bill_amount, bill_charge, bill_total, 
      reg_no, first_name, last_name, region, class_diniyah, class_umum, room, sex  
			from bill as a 
      inner join c_partner as b on a.partner_id = b.id 
      inner join bill_status as c on a.bill_status_id = c.id 
      where bill_status_id = 1 and partner_id = ? 
		) g0';
		$table = $this->f->compile_qry($str, [$request->partner_id]);
		$this->db->from($table);
		return $this->f->get_result($request);
  }
}