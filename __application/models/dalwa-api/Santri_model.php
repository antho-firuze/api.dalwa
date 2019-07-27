<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Santri_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
	}
  
  function profile($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['partner_id']);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select partner_id, first_name, last_name, region, class_diniyah, class_umum, room, sex, reg_no   
			from c_partner 
      where client_id = ? and partner_id = ? 
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->partner_id]);
		$this->db->from($table);
		return $this->f->get_result_($request);
  }

  function bill($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['nis']);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select bill_id, a.partner_id, a.bill_status_id, a.desc, due_date, amount, b.desc as bill_status
			from bill a
			inner join bill_status b on a.bill_status_id = b.bill_status_id
			inner join c_partner c on a.partner_id = c.partner_id
      where a.client_id = ? and c.reg_no = ? 
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->params->nis]);
		$this->db->from($table);
		return $this->f->get_result_($request);
  }

}