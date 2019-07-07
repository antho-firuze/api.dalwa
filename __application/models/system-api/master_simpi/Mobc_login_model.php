<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mobc_login_model extends CI_Model
{
	private $table_user = 'mobc_login';						 
	private $table_session = 'mobc_session';					 
	public $login_token_expiration = 60*60*24; // second*minute*hour

	private $min_password_length = 5;
	private $max_login_attempts = 3;
	private $lockout_time = 600;
	
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
		$this->load->library('MOBC');
	}
	
	private function is_correct_password($password1, $password2)
	{
		return md5("mochamadjunaidi129400033B10193056".$password1) == $password2;
	}
	
	function login($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->email) || empty($request->params->email))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'email')]];
		
		if (!isset($request->params->password) || empty($request->params->password))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'password')]];
		
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpi_id, 
														'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->lang('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}

		if (! $this->is_correct_password($request->params->password, $row->password)) {

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, $update_field, 
							  ['simpiID' => $request->simpi_id, 'email' => $row->email]
			);
			
			return [FALSE, ['message' => $this->f->lang('err_login_failed')]];
		}
		
		$token =  $this->f->gen_token();
		$token_expired = date('Y-m-d\TH:i:s\Z', time() + $this->login_token_expiration);
		// Invalidate old session
		$this->db->delete($this->table_session,
			['simpiID' => $row->simpiID, 'emailID' => $row->emailID, 'AppsID' => $request->AppsID, 'agent' => $request->agent, 'token <>' => $token]
		);
		
		$this->db->insert($this->table_session, [
				'simpiID' => $row->simpiID, 'emailID' => $row->emailID, 'AppsID' => $request->AppsID, 
				'agent' => $request->agent, 'token' => $token, 'token_expired' => $token_expired, 'LogTime' => date('Y-m-d H:i:s')
			]
		);
		
		$this->db->update($this->table_user, 
			['login_last' => date('Y-m-d H:i:s'), 'login_try' => 0, 'is_need_activate' => 0], 
			['simpiID' => $request->simpi_id, 'email' => $row->email] 
		);
		
		$request->emailID = $row->emailID;
		$request->ClientID = $row->ClientID;

		//$this->f->save_billing($request);

		$result = (object)[];
		$result->token = $token;
		$result->token_exp = $token_expired;
		$result->token_exp_epoch = strtotime($token_expired);
		
		return [TRUE, ['result' => $result, 'message' => $this->f->lang('success_login')]];
	}
	
	function logout($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$return = $this->db->delete($this->table_session, ['token' => $request->token])) 
			return [FALSE, ['message' => $this->db->error()['message']]];
		else
			return [TRUE, NULL];
	}
		
	function password_change($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->password))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'password')]];

		if (!isset($request->params->new_password))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'new_password')]];

		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpi_id, 'emailID' => $request->emailID])->row();
		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->lang('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}
		
		if (! $this->is_correct_password($request->params->password, $row->password)) {

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, 
				$update_field, 
				['simpiID' => $request->simpi_id, 'emailID' => $row->emailID]
			);
			
			return [FALSE, ['message' => $this->f->lang('err_old_password')]];
		}
		
		list($success, $result) = $this->is_valid_password($request->params->new_password);
		if (!$success) return [FALSE, ['message' => $result]];
		
		$new_password_enc = $result;
		$this->db->update($this->table_user, 
			['login_try' => 0, 'password' => $new_password_enc], 
			['simpiID' => $request->simpi_id, 'emailID' => $row->emailID]
		);
		
		list($success, $message) = $this->send_confirm_mail_chg_password($request);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('success_chg_password')]];
	}
	
	function password_forgot($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->email))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'email')]];

		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpi_id, 'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		// generate random password
		$new_password = $this->f->gen_pwd($this->min_password_length);
		$new_password_enc = md5($new_password);
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['simpiID' => $request->simpi_id, 'emailID' => $row->emailID]
		);
		
		list($success, $message) = $this->send_confirm_mail_forgot_password($request);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('info_sent_email_password')]];
	}
	
	function password_reset($request)
	{
		list($success, $return) = $this->f->is_valid_license($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->emailID))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'emailID')]];

		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpi_id, 'emailID' => $request->params->emailID])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		// generate random password
		$new_password = $this->f->gen_pwd($this->min_password_length);
		$new_password_enc = md5($new_password);
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['simpiID' => $request->simpi_id, 'emailID' => $row->emailID]
		);
		
		list($success, $message) = $this->send_confirm_mail_reset_password($request);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('info_sent_email_rst_password')]];
	}

	private function send_confirm_mail_chg_password($request)
	{
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$email = [
			'_to' 		=> $request->user_info->email,
			'_subject' 	=> $this->f->lang('email_subject_chg_password'),
			'_body'		=> $this->f->lang('email_body_chg_password', [
				'name' 			=> $request->user_info->full_name, 
				'new_password' 	=> $request->params->new_password,
				'simpiName' 	=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 	=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
		]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, NULL];
	}
	
	private function send_confirm_mail_forgot_password($request)
	{
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$email = [
			'_to' 		=> $request->params->email,
			'_subject' 	=> $this->f->lang('email_subject_forgot_password_simple', ['AppsName' => $request->simpi_info->AppsName]),
			'_body'		=> $this->f->lang('email_body_forgot_password_simple', [
				'name' 			=> $request->user_info->full_name, 
				'new_password' 	=> $new_password,
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
		]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, NULL];
	}

	private function send_confirm_mail_reset_password($request)
	{
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$email = [
			'_to' 		=> $request->user_info->email,
			'_subject' 	=> $this->f->lang('email_subject_rst_password'),
			'_body'		=> $this->f->lang('email_body_rst_password', [
				'name' 			=> $request->user_info->full_name, 
				'new_password' 	=> $request->params->password,
				'simpiName' 	=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 	=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
			]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, NULL];
	}

	function token_inject($request)
	{
		list($success, $return) = $this->f->is_valid_license($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->emailID) || empty($request->params->emailID))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'emailID')]];
		else {
			$this->load->library('simpi');
			list($success, $return) = $this->simpi->check_is_mobc_login_valid($request);
			if (!$success) return [FALSE, $return];
		}

		if (!isset($request->params->token) || empty($request->params->token))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'token')]];

		if (!isset($request->params->token_expired) || empty($request->params->token_expired))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'token_expired')]];

		$this->db->delete('mobc_session', [
				'simpiID' => $request->simpi_id, 'emailID' => $request->params->emailID, 'AppsID' => $request->AppsID, 'agent' => $request->agent, 
			]
		);
		$this->db->insert('mobc_session', [
				'simpiID' => $request->simpi_id, 'emailID' => $request->params->emailID, 'AppsID' => $request->AppsID, 'agent' => $request->agent, 
				'token' => $request->params->token, 'token_expired' => $request->params->token_expired, 'LogTime' => date('Y-m-d H:i:s')
			]
		);

		$request->method = 'olap.token_inject';
		$response = Requests::post(API_OLAP, ['Accept' => 'application/json'], $request);
		$result = json_decode($response->body);
		if (! $result->status) return  [FALSE, ['message' => $result->message]];

		$request->method = 'market.token_inject';
		$response = Requests::post(API_MARKET, ['Accept' => 'application/json'], $request);
		$result = json_decode($response->body);
		if (! $result->status) return  [FALSE, ['message' => $result->message]];

		$request->method = 'master.token_inject';
		$response = Requests::post(API_MASTER, ['Accept' => 'application/json'], $request);
		$result = json_decode($response->body);
		if (! $result->status) return  [FALSE, ['message' => $result->message]];

		return [TRUE, ['message' => 'Success']];
	}

}
