<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Simpi_user_model extends CI_Model
{
	public $forgot_token_expiration = 60*60*1;				 
	public $login_token_expiration = 60*60*24;				 
	
	public $min_password_length = 5;
	public $max_login_attempts = 3;
	public $lockout_time = 600;
	
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
		$this->load->library('System');
	}

	private function is_correct_password($password1, $password2)
	{
		return md5("mochamadjunaidi129400033B10193056".$password1) == $password2;
	}
	
	function login_check($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'API access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		if (!isset($request->params->user_login) || empty($request->params->user_login)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter user_login');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		$this->db->select('T1.UserID');
		$this->db->from('simpi_user T1');
		$this->db->join('simpi_session T2', 'T1.UserID = T2.UserID');  
		$this->db->join('system_application T3', 'T2.AppsID = T3.AppsID');  
		$this->db->where('T3.LicenseKey', $request->appkey);
		$this->db->where('T2.agent', $request->agent);
		$user = $this->security->xss_clean($request->params->user_login);
		$user = "(T1.UserLogin = '".$user."' or T1.UserEmail = '".$user."')";
		$this->db->where($user);
		$row = $this->db->get()->row();
		if (!$row) {
			return [TRUE, ['result' => ['logon_status' => FALSE]]];
		} else {
			$return = $this->system->error_data('02-4', $request->LanguageID);
			return [TRUE, ['result' => ['logon_status' => TRUE, 'logon_message' => $return['message']]]];	
		}
	}

	function login($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'API access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		if (!isset($request->params->user_login) || empty($request->params->user_login)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter user_login');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		if (!isset($request->params->user_password) || empty($request->params->user_password)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'user_password');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		 
		$this->db->select('T1.UserID, T1.login_try, T1.account_locked_until, T1.UserPassword, 
							T1.UserName, T1.UserTitle, T2.simpiID, T2.simpiName, T2.LanguageID, T2.CountryID');
		$this->db->from('simpi_user T1');
		$this->db->join('master_simpi T2', 'T1.simpiID = T2.simpiID');  
		$user = $this->security->xss_clean($request->params->user_login);
		$user = "(T1.UserLogin = '".$user."' or T1.UserEmail = '".$user."')";
		$this->db->where($user);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'user_login');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$request->simpi_id = $row->simpiID;
		$request->LanguageID = $row->LanguageID;
		$request->log_access = 'apps';
		$request->user_id = $row->UserID;
		$request->email_id = 0;

		list($success, $return) = $this->system->is_valid_credit($request);
		if (!$success) return [FALSE, $return];				

		if ((integer)$row->login_try >= $this->max_login_attempts) {
			$currentTime = new DateTime();
			$lockTime = new DateTime($row->account_locked_until);
			if ($currentTime < $lockTime) {
				$this->load->helper('mydate');
				$return = $this->system->error_data('02-2', $request->LanguageID, nicetime_lang($lockTime, $request->idiom));
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
			} 
			else $row->login_try = 0;
		}
		
		if (! $this->is_correct_password($request->params->user_password, $row->UserPassword)) {
			$login_try = $row->login_try + 1;
			if ($login_try >= $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);		
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, $update_field, ['UserID' => $row->UserID]);			

			$return = $this->system->error_data('02-3', $request->LanguageID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		$token = $this->f->gen_token();
		$token_expired = date('Y-m-d\TH:i:s\Z', time() + $this->login_token_expiration);
		// Invalidate old session
		$this->db->delete('simpi_session', ['UserID' => $row->UserID, 'AppsID' => $request->AppsID, 'agent' => $request->agent]);
		$this->db->insert('simpi_session', ['simpiID' => $row->simpiID, 'UserID' => $row->UserID, 
											'AppsID' => $request->AppsID, 'agent' => $request->agent, 'session' => $token, 
											'session_expired' => $token_expired, 'LogTime' => date('Y-m-d H:i:s')]);	
		$this->db->update('simpi_user', ['login_last' => date('Y-m-d H:i:s'),'login_try' => 0], ['UserID' => $row->UserID]);

		$request->log_type	= 'process';	
		$request->log_size = 0;
		$this->system->save_billing($request);

		$result = (object)[];
		$result->simpi_id = $row->simpiID;
		$result->simpi_name = $row->simpiName;
		$result->LanguageID = $request->LanguageID;
		$result->CountryID = $row->CountryID;
		$result->UserID = $row->UserID;
		$result->UserName = $row->UserName;
		$result->UserTitle = $row->UserTitle;
		$result->session_id = $token;
		$result->session_exp = $token_expired;
		$result->session_exp_epoch = strtotime($token_expired);
		$result->AppsID = $request->AppsID;
		$result->appcode = $request->appcode;
		
		return [TRUE, ['result' => $result]];
	}

	function logout($request)
	{
		list($success, $return) = $this->system->is_valid_session($request);
		if (!$success) return [FALSE, $return];
		if (!$return = $this->db->delete('simpi_session', ['session' => $request->session_id])) 
			return [FALSE, ['message' => $this->db->error()['message']]];
		else
			return [TRUE, NULL];
	}
		
	function change_password($request)
	{
		list($success, $return) = $this->system->is_valid_session($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->password_new) || empty($request->password_new)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'new password'])]];
		}
		if (!isset($request->params->password_confirm) || empty($request->password_confirm)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'confirm'])]];
		}
		if ($request->params->password_new != $request->password_confirm) {
			list($success, $return) = $this->system->error_message('02-5', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '02-5'];
			return [FALSE, ['message' => $return['message']]];
		}
		
		list($success, $result) = $this->is_valid_password($request->params->new_password);
		if (!$success) return [FALSE, ['message' => $result]];
		
		$new_password_enc = $result;
		$this->db->update($this->table_user, 
			['login_try' => 0, 'UserPassword' => $new_password_enc], 
			['simpiID' => $request->simpi_id, 'UserID' => $request->user_id]
		);

		return [TRUE, ['message' => '']];
	}
	
	/**
	 * Method for forgotten password & email confirmation with generated random password
	 *
	 * @param json_object $request
	 * @return void
	 */
	function forgot_password($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->email))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'email')]];

		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpi_id, 'UserLogin' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		// generate random password
		$new_password = $this->f->gen_pwd($this->min_password_length);
		$new_password_enc = md5($new_password);
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['simpiID' => $request->simpi_id, 'UserID' => $row->UserID]
		);
		
		list($success, $message) = $this->send_confirm_mail_forgot_password($request);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('info_sent_email_password')]];
	}
	
	/**
	 * Method for reset password from admin, with checking of forgot token & email confirmation
	 *
	 * @param json_object $request
	 * @return void
	 */
	function reset_password($request)
	{
		list($success, $return) = $this->f->is_valid_license($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->UserID))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'UserID')]];

		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpi_id, 'UserID' => $request->params->UserID])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		// generate random password
		$new_password = $this->f->gen_pwd($this->min_password_length);
		$new_password_enc = md5($new_password);
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['simpiID' => $request->simpi_id, 'UserID' => $row->UserID]
		);
		
		list($success, $message) = $this->send_confirm_mail_reset_password($request);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('info_sent_email_rst_password')]];
	}
	
	function send_confirm_mail_chg_password($request)
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
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
				]),
			];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, NULL];
	}
	
	function send_confirm_mail_forgot_password($request)
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

	function send_confirm_mail_reset_password($request)
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
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
			]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, NULL];
	}

	function session_inject($request)
	{
		list($success, $return) = $this->f->is_valid_license($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->UserID) || empty($request->params->UserID))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'UserID')]];
		// else {
		// 	$this->load->library('simpi');
		// 	list($success, $return) = $this->simpi->check_is_simpi_user_valid($request);
		// 	if (!$success) return [FALSE, $return];
		// }
	
		if (!isset($request->params->session) || empty($request->params->session))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'session')]];

		if (!isset($request->params->session_expired) || empty($request->params->session_expired))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'session_expired')]];

		$this->db->delete('simpi_session', [
				'simpiID' => $request->simpi_id, 'UserID' => $request->params->UserID, 'AppsID' => $request->AppsID, 'agent' => $request->agent, 
			]
		);
		$this->db->insert('simpi_session', [
				'simpiID' => $request->simpi_id, 'UserID' => $request->params->UserID, 'AppsID' => $request->AppsID, 'agent' => $request->agent, 
				'session' => $request->params->session, 'session_expired' => $request->params->session_expired, 'LogTime' => date('Y-m-d H:i:s')
			]
		);

		$request->method = 'olap.session_inject';
		$response = Requests::post(API_OLAP, ['Accept' => 'application/json'], $request);
		$result = json_decode($response->body);
		if (! $result->status) return  [FALSE, ['message' => $result->message]];

		$request->method = 'market.session_inject';
		$response = Requests::post(API_MARKET, ['Accept' => 'application/json'], $request);
		$result = json_decode($response->body);
		if (! $result->status) return  [FALSE, ['message' => $result->message]];

		$request->method = 'master.session_inject';
		$response = Requests::post(API_MASTER, ['Accept' => 'application/json'], $request);
		$result = json_decode($response->body);
		if (! $result->status) return  [FALSE, ['message' => $result->message]];

		return [TRUE, ['message' => 'Success']];
	}

}
