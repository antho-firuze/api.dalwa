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

	function list_santri($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select a.login_id, b.partner_id, a.reg_no, TRIM(CONCAT(b.first_name, " ", IFNULL(b.last_name,""))) as full_name
			from a_login_santri a
			inner join c_partner b on a.reg_no = b.reg_no
      where a.client_id = ? and a.login_id = ?
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->login_id]);
		$this->db->from($table);
		return $this->f->get_result_($request);
	}

	function add_santri($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['nis']);
		if (!$success) return [FALSE, $return];

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else 
			$this->db->select('reg_no, TRIM(CONCAT(first_name, " ", IFNULL(last_name,""))) as full_name');

		if (!$result = $this->db->get_where('c_partner', ['client_id' => $request->client_id, 'reg_no' => $request->params->nis]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_nis_not_found')]];
		
		if (!$result = $this->db->get_where('a_login_santri', ['client_id' => $request->client_id, 'login_id' => $request->login_id, 'reg_no' => $request->params->nis]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if ($row2 = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_nis_duplicate')]];

		if (!$result = $this->db->insert('a_login_santri', ['client_id' => $request->client_id, 'login_id' => $request->login_id, 'reg_no' => $row->reg_no]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		return [TRUE, ['message' => $this->f->lang('sucess_adding_santri'), 'result' => (object)['nis' => $row->reg_no, 'full_name' => $row->full_name]]];
	}

}