<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Wali_model extends CI_Model
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
    
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select partner_id, first_name, last_name, phone, fax, email, sex  
			from c_partner
      where client_id = ? and partner_id = ? 
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->partner_id]);
		$this->db->from($table);
		return $this->f->get_result_($request);
  }

	function list($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['partner_id']);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select a.partner_id, a.reg_no, TRIM(CONCAT(b.first_name, " ", IFNULL(b.last_name,""))) as full_name
			from c_partner_santri a
			inner join c_partner b on a.reg_no = b.reg_no
      where a.client_id = ? and a.partner_id = ?
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->partner_id]);
		$this->db->from($table);
		return $this->f->get_result_($request);
	}

}