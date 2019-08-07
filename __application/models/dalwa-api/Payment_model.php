<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Payment_model extends CI_Model
{
	private $sender_id = 1;
	
	function __construct()
	{
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
	}
  
  function setting($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		if (!$result = $this->db->get_where('payment_setting', ['client_id' => $request->client_id]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_setting_not_found')]];

		return [TRUE, ['result' => $row]];
	}

  function method($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

			$str = '(
				select a.payment_method_id, a.bank_id, a.account_no, a.account_name, a.is_autocheck, is_banin, b.code as bank_code, b.name as bank_name
				from payment_method a
				inner join c_bank b on a.bank_id = b.bank_id
				where a.client_id = ? 
			) g0';
			$table = $this->f->compile_qry($str, [$request->client_id]);
			$this->db->from($table);
			return $this->f->get_result_($request);
		}

  function confirm($request)
  {
		$this->db->trans_strict(TRUE);
		$this->db->trans_start();

		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['nis','payment_method_id','bill_ids']);
		if (!$success) return [FALSE, $return];

		list($success, $return) = $this->_is_valid_santri($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) = $this->_is_valid_billing($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) = $this->_is_valid_payment_method($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) = $this->_get_payment_setting($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->f->gen_doc_no($request, 'payment');
		if (!$success) return [FALSE, $return];
		
		$request->params->grand_total = $request->params->sub_total + $request->params->admin_charge;

		$this->db->insert('payment', [
			'client_id' => $request->client_id,
			'partner_id' => $request->params->partner_id,
			'payment_method_id' => $request->params->payment_method_id,
			'account_no' => $request->params->account_no,
			'payment_status_id' => 1,
			'payment_no' => $request->params->payment_no,
			'sub_total' => $request->params->sub_total,
			'admin_charge' => $request->params->admin_charge,
			'grand_total' => $request->params->grand_total,
			'created_at' => date('Y-m-d H:i:s'),
		]);
		
		$request->params->payment_id = $this->db->insert_id();
		foreach ($request->params->bills as $key => $value) {
			$this->db->insert('payment_dt', [
				'client_id' => $request->client_id,
				'payment_id' => $request->params->payment_id,
				'bill_id' => $value->bill_id,
				'bill_type_id' => $value->bill_type_id,
				'desc' => $value->desc,
				'amount' => $value->amount,
			]);

			// Update billing status became PENDING
			$this->db->update('bill', ['bill_status_id' => 2], ['bill_id' => $value->bill_id]);
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			// return [FALSE, ['message' => $this->f->lang('err_commit_data')]];
			// return [FALSE, ['message' => $this->db->last_query()]];
			return [FALSE, ['message' => $this->db->error()['message']]];
		}

		$email = [
			'sender_id' => $this->sender_id,
			'_to' 			=> $request->email,
			'_subject' 	=> $this->f->lang('email_subject_confirmation', ['app_name' => $request->app_name]),
			'_body'			=> $this->f->lang('email_body_confirmation', [
				'name' 					=> $request->full_name, 
				'payment_no' 		=> $request->params->payment_no, 
				'grand_total' 	=> $request->params->grand_total,
				'account_no' 		=> $request->params->account_no,
				'app_name' 			=> $request->app_name,
				'powered_by' 		=> 'Powered by DALWA @2019',
				]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['result' => ['account_no' => $request->params->account_no]]];
	}

	function _is_valid_santri($request)
	{
		if (!$result = $this->db->get_where('c_partner', ['client_id' => $request->client_id, 'reg_no' => $request->params->nis]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_santri_invalid')]];

		$request->params->partner_id = $row->partner_id;
		$request->params->gender = $row->gender;
		return [TRUE, NULL];
	}

	function _is_valid_billing($request)
	{
		if (!is_array($request->params->bill_ids)) 
			return [FALSE, ['message' => $this->f->_err_msg('err_invalid_array', 'bill_ids')]];

		$this->db->select('*');
		$this->db->from('bill');
		$this->db->where('client_id', $request->client_id);
		$this->db->where('partner_id', $request->params->partner_id);
		$this->db->where_in('bill_id', $request->params->bill_ids);
		if (!$result = $this->db->get())
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$rows = $result->result()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_billing_invalid')]];

		$sub_total = 0;
		foreach ($rows as $key => $value) {
			$sub_total += $value->amount;
		}
		
		$request->params->sub_total = $sub_total;
		$request->params->bills = $rows;
		return [TRUE, NULL];
	}

	function _is_valid_payment_method($request)
	{
		if (!$result = $this->db->get_where('payment_method', ['client_id' => $request->client_id, 'payment_method_id' => $request->params->payment_method_id]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_payment_method_invalid')]];

		$request->params->account_no = $row->account_no;
		return [TRUE, NULL];
	}

	private function _get_payment_setting($request)
	{
		if (!$result = $this->db->get_where('payment_setting', ['client_id' => $request->client_id]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_setting_not_found')]];

		$request->params->admin_charge = $row->charge_amount;
		return [TRUE, NULL];
	}

  function history($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['partner_id']);
		if (!$success) return [FALSE, $return];

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select a.payment_id, a.partner_id, a.payment_method_id, a.payment_status_id, a.account_no, payment_no, sub_total, admin_charge, grand_total, created_at, payed_at, 
      d.code as bank_code, c.desc as payment_status_desc
			from payment as a 
      inner join payment_method as b on a.payment_method_id = b.payment_method_id 
      inner join payment_status as c on a.payment_status_id = c.payment_status_id 
      left outer join c_bank as d on b.bank_id = d.bank_id 
      where a.client_id = ? and a.partner_id = ? and a.grand_total > 0 
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->params->partner_id]);
		$this->db->order_by("created_at desc");
		$this->db->from($table);
		return $this->f->get_result_($request);
  }

  function history_detail($request)
  {
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
    
		list($success, $return) = $this->f->check_param_required($request, ['payment_id']);
		if (!$success) return [FALSE, $return];

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$str = '(
      select a.desc, amount
			from payment_dt as a 
			where client_id = ? and payment_id = ? 
			union all
			select (select charge_title from payment_setting where client_id = ?), admin_charge from payment where client_id = ? and payment_id = ?
		) g0';
		$table = $this->f->compile_qry($str, [$request->client_id, $request->params->payment_id, $request->client_id, $request->client_id, $request->params->payment_id]);
		$this->db->from($table);

		return $this->f->get_result_($request);
  }

}