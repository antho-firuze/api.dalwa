<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Auth_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database('cloud_simpi');
		$this->config->load('auth', FALSE);
		
		$this->table_user = $this->config->item('table_user');
		$this->table_session = $this->config->item('table_session');
		$this->table_user_config = $this->config->item('table_user_config');
		$this->forgot_token_expiration = $this->config->item('forgot_token_expiration');
		$this->login_token_expiration = $this->config->item('login_token_expiration');
		
		$this->android_token_expiration = $this->config->item('android_token_expiration');
		$this->ios_token_expiration = $this->config->item('ios_token_expiration');
		$this->web_token_expiration = $this->config->item('web_token_expiration');
		
		$this->min_password_length = $this->config->item('min_password_length');
		$this->max_password_length = $this->config->item('max_password_length');
		$this->remember_users = $this->config->item('remember_users');
		$this->max_login_attempts = $this->config->item('max_login_attempts');
		$this->lockout_time = $this->config->item('lockout_time');
		$this->domain_frontend = BASE_URL;
		
		$this->release_locked_account();
	}
	
	/**
	 * Method for auto release locked account
	 *
	 * @return void
	 */
	private function release_locked_account()
	{
		$this->db->update($this->table_user, 
			['login_try' => 0, 'account_locked_until' => null], 
			['login_try >' => 0, 'account_locked_until <' => date('Y-m-d H:i:s')]
		);
	}
	
	/**
	 * Method for checking password validation
	 *
	 * @param string $password
	 * @return bool
	 */
	private function is_valid_password($password)
	{
		if (!isset($password) || empty($password))
			return [FALSE, $this->f->lang('err_param_required', 'password')];
		
		if (strlen($password) < $this->min_password_length)
			return [FALSE, $this->f->lang('err_min_password_length', $this->min_password_length)];
		
		if (strlen($password) > $this->max_password_length)
			return [FALSE, $this->f->lang('err_max_password_length', $this->max_password_length)];
		
		$password = md5($password);
		return [TRUE, $password];
	}
	
	private function is_correct_password($password1, $password2)
	{
		return md5($password1) == $password2;
	}
	
	/**
	 * Method for force release locked account
	 *
	 * @param json_object $request
	 * @return void
	 */
	function force_release_locked_account($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->email) || empty($request->params->email))
			return [FALSE, $this->f->lang('err_param_required', 'email')];
		
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		$this->db->update($this->table_user, 
			['login_try' => 0, 'account_locked_until' => null], 
			['simpiID' => $request->simpiID, 'email' => $request->params->email]
		);
		
		return [TRUE, $this->f->lang('success_unlocked')];
	}
	
	/**
	 * Method for get detail data account, from master_client or mobc_prospect
	 *
	 * @param json_object $request
	 * @return void
	 */
	function _get_user_info($request)
	{
		// if ($request->ClientID) {
		// 	$row = $this->db->get_where('master_client', ['simpiID' => $request->simpiID, 'ClientID' => $request->ClientID])->row();
		// 	$row->email = $row->CorrespondenceEmail;
		// 	$row->full_name = $row->ClientName;
		// 	return $row;
		// } else {
		// 	list($success, $return) = $this->f->is_valid_token($request);
		// 	if (!$success) return [FALSE, $return];
	
		// 	$row = $this->db->get_where('mobc_prospect', ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])->row();
		// 	$row->email = $row->CorrespondenceEmail;
		// 	$row->full_name = ($row->NameFirst ? $row->NameFirst : '').
		// 		($row->NameMiddle ? ' '.$row->NameMiddle : '').
		// 		($row->NameLast ? ' '.$row->NameLast : '');
		// 	return $row;
		// }
		$row = $this->db->get_where('mobc_prospect', ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])->row();
		$row->email = $row->CorrespondenceEmail;
		$row->full_name = ($row->NameFirst ? $row->NameFirst : '').
			($row->NameMiddle ? ' '.$row->NameMiddle : '').
			($row->NameLast ? ' '.$row->NameLast : '');
		return $row;
	}
	
	/**
	 * Method for login, with checking of login attempt and generate session token
	 *
	 * @param json_object $request
	 * @return void
	 */
	function login($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->email) || empty($request->params->email))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'email')]];
		
		if (!isset($request->params->password) || empty($request->params->password))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'password')]];
		
		if (!isset($request->params->time_epoch) || empty($request->params->time_epoch))
			$currentTime = time();
		else
			$currentTime = $request->params->time_epoch; // strtotime($request->params->time);
		
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->lang('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}
		// die($this->is_correct_password($request->params->password, $row->password) ? 'true' : 'false');
		if (! $this->is_correct_password($request->params->password, $row->password)) {

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, 
				$update_field, 
				['simpiID' => $request->simpiID, 'email' => $row->email]
			);
			
			return [FALSE, ['message' => $this->f->lang('err_login_failed')]];
		}
		
		$token = $this->f->gen_token();
		$token_expired = date('Y-m-d\TH:i:s\Z', $currentTime + $this->login_token_expiration);
		if (!$result = $this->db->insert($this->table_session, 
			[
				'simpiID' => $row->simpiID, 'emailID' => $row->emailID, 'emailID' => $row->emailID, 'AppsID' => $request->AppsID, 
				'agent' => $request->agent, 'token' => $token, 'token_expired' => $token_expired
			]
		))
		// return [FALSE, ['message' => $this->db->error()['message']]];
		// return [FALSE, ['message' => $this->db->last_query()]];
		
		$this->db->update($this->table_user, 
			['login_last' => date('Y-m-d H:i:s'), 'login_try' => 0, 'is_need_activate' => 0], 
			['simpiID' => $request->simpiID, 'email' => $row->email] 
		);
		
		// Invalidate old session
		$this->db->update($this->table_session,
			['is_logout' => '1'],
			['emailID' => $row->emailID, 'AppsID' => $request->AppsID, 'agent' => $request->agent, 'token <>' => $token]
		);
		
		$request->emailID = $row->emailID;
		$request->ClientID = $row->ClientID;

		$result = (object)[];
		$result->user = $this->_get_user_info($request);
		$result->token = $token;
		$result->token_exp = $token_expired;
		$result->token_exp_epoch = strtotime($token_expired);
		
		return [TRUE, ['result' => $result, 'message' => $this->f->lang('success_login')]];
	}
	
	function logout($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$return = $this->db->update($this->table_session, ['is_logout' => '1'], ['token' => $request->token])) 
			return [FALSE, ['message' => $this->db->error()['message']]];
		else
			return [TRUE, NULL];
	}

	/**
	 * Method for unlock session, with checking of login attempt
	 *
	 * @param json_object $request
	 * @return void
	 */
	function unlock($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$row = $return['result'];
		
		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->lang('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}
		
		if (md5($request->params->password) != $row->password){

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, 
				$update_field, 
				['simpiID' => $request->simpiID, 'email' => $row->email]
			);
			
			return [FALSE, ['message' => $this->f->lang('err_unlocked_failed')]];
		}
		
		return [TRUE, ['message' => NULL]];
	}
	
	/**
	 * Method for simple forgotten password & email confirmation with generated random password
	 *
	 * @param json_object $request
	 * @return void
	 */
	function forgot_password_simple($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		$request->emailID = $row->emailID;
		$request->ClientID = $row->ClientID;
		$row->user = $this->_get_user_info($request);

		// generate random password
		$new_password = $this->f->gen_pwd($this->min_password_length);
		$new_password_enc = md5($new_password);
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['simpiID' => $request->simpiID, 'email' => $row->email]
		);
		
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$email = [
			'_to' 		=> $request->user_info->email,
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

		return [TRUE, ['message' => $this->f->lang('info_sent_email_password')]];
	}
	
	/**
	 * Method for forgotten password & email confirmation
	 *
	 * @param json_object $request
	 * @return void
	 */
	function forgot_password($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		$request->emailID = $row->emailID;
		$request->ClientID = $row->ClientID;
		$row->user = $this->_get_user_info($request);
	
		$token = $this->f->gen_token();
		$token_exp = date('Y-m-d H:i:s', time() + $this->forgot_token_expiration);
		$this->db->update($this->table_user, 
			['forgot_token' => $token, 'forgot_token_expired' => $token_exp], 
			['simpiID' => $request->simpiID, 'email' => $row->email]
		);
		
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$email = [
			'_to' 		=> $request->user_info->email,
			'_subject' 	=> $this->f->lang('email_subject_forgot_password'),
			'_body'		=> $this->f->lang('email_body_forgot_password', [
				'name' 				=> $request->user_info->full_name, 
				'token' 			=> $token,
				'domain_frontend' 	=> $this->domain_frontend,
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
			]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('info_sent_email_reset_password_link')]];
	}
	
	/**
	 * Method for reset password, with checking of forgot token & email confirmation
	 *
	 * @param json_object $request
	 * @return void
	 */
	function reset_password($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		$row = $this->db->get_where($this->table_user, ['forgot_token' => $request->params->token])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_token_invalid')]];
		
		$request->emailID = $row->emailID;
		$request->ClientID = $row->ClientID;
		$row->user = $this->_get_user_info($request);
	
		if ($row->forgot_token_expired < date('Y-m-d H:i:s'))
			return [FALSE, ['message' => $this->f->lang('err_token_expired')]];
		
		list($success, $message) = $this->is_valid_password($request->params->password);
		if (!$success)
			return [FALSE, ['message' => $message]];
		
		$new_password_enc = $message;
		$this->db->update($this->table_user, 
			['forgot_token' => null, 'forgot_token_expired' => null, 'password' => $new_password_enc], 
			['simpiID' => $request->simpiID, 'email' => $row->email]
		);
		
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$this->simpi->get_user_info($request);
		$email = [
			'_to' 		=> $request->user_info->email,
			'_subject' 	=> $this->f->lang('email_subject_reset_password'),
			'_body'		=> $this->f->lang('email_body_reset_password', [
				'name' 			=> $request->user_info->full_name, 
				'new_password' 	=> $request->params->password,
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
			]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('success_reset')]];
	}
	
	/**
	 * Method for reset password admin, with checking of forgot token & email confirmation
	 *
	 * @param json_object $request
	 * @return void
	 */
	function rst_password($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'email' => $request->params->email])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_email_not_found')]];
		
		$request->emailID = $row->emailID;
		$request->ClientID = $row->ClientID;
		$row->user = $this->_get_user_info($request);
	
		if (isset($request->params->auto) && $request->params->auto) {
			// generate random password
			$new_password = $this->f->gen_pwd($this->min_password_length);
			$new_password_enc = md5($new_password);
		} else {
			list($success, $message) = $this->is_valid_password($request->params->password);
			if (!$success)
				return [FALSE, ['message' => $message]];
			
			$new_password_enc = $message;
		}
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['simpiID' => $request->simpiID, 'email' => $request->params->email]
		);
		
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

		return [TRUE, ['message' => $this->f->lang('info_sent_email_rst_password')]];
	}
	
	/**
	 * Method for change password, with checking of token & old password, & with email confirmation
	 *
	 * @param json_object $request
	 * @return void
	 */
	function chg_password($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->new_password))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'new_password')]];

		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])->row();
		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->lang('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}
		
		// die(md5($request->params->password).'  '.$row->password);
		// die($this->is_correct_password($request->params->password, $row->password) ? 'true' : 'false');
		if (! $this->is_correct_password($request->params->password, $row->password)) {

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, 
				$update_field, 
				['simpiID' => $request->simpiID, 'email' => $row->email]
			);
			
			return [FALSE, ['message' => $this->f->lang('err_old_password')]];
		}
		
		// $new_password = isset($request->params->new_password) ? $request->params->new_password : null;
		list($success, $result) = $this->is_valid_password($request->params->new_password);
		if (!$success)
			return [FALSE, ['message' => $result]];
		
		$new_password_enc = $result;
		$this->db->update($this->table_user, 
			['login_try' => 0, 'password' => $new_password_enc], 
			['simpiID' => $request->simpiID, 'email' => $row->email]
		);
		
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

		return [TRUE, ['message' => $this->f->lang('success_chg_password')]];
	}
	
	/**
	 * Method for register new account or existing account to access mobile apps
	 *
	 * require 	email, phone, name_first, name_last, password, account = old/new
	 *
	 * @param json_object $request
	 * @return void
	 */
	function register($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!$request->params->email)
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'email')]];
		
		// #1: 
		// ==>
		// Check first on table_user 
		// if email exists, they may be real client or may be prospect client
		// <==
		$row = $this->db->get_where($this->table_user, ['simpiID' => $request->simpiID, 'email' => $request->params->email])->row();
		if ($row){
			if ($row->is_need_activate)
				return [FALSE, ['message' => $this->f->lang('err_email_has_register_not_active')]];
			
			return [FALSE, ['message' => $this->f->lang('err_email_has_register')]];
		}
		
		// #2: 
		// ==>
		// And then check on table master_client 
		// if email exists, they must be existing client. 
		// <==
		$row = $this->db->get_where('master_client', ['simpiID' => $request->simpiID, 'CorrespondenceEmail' => $request->params->email])->row();
		if ($row){
			$new_password = $this->f->gen_pwd($this->min_password_length);
			list($success, $message) = $this->is_valid_password($new_password);
			if (!$success)
				return [FALSE, ['message' => $message]];
			
			$new_password_enc = $message;
			$token = $this->f->gen_token();
			$this->db->insert($this->table_user, 
				[
					'simpiID' => $request->simpiID, 
					'email' => $row->CorrespondenceEmail, 
					'password' => $new_password_enc,
					'forgot_token' => $token,
					'is_need_activate' => 1,
				]
			);
			
			$this->db->insert('mobc_prospect', [
				'simpiID' => $request->simpiID, 
				'emailID' => $this->db->insert_id(), 
				'CorrespondenceEmail' => $request->params->email, 
				'CorrespondencePhone' => $request->params->phone, 
				'NameFirst' => $request->params->name_first,
				'NameLast' => $request->params->name_last,
				'AccountStatusID' => 9,		// 1:NOT COMPLETE 2:COMPLETE 3:PROCESSED 4:ACTIVE 5:REJECT 6:ALLOCATE 7:SUSPEND 8:PENDING 9:ACTIVATION => table mobc_status
			]);
		
			$this->load->library('simpi');
			$this->simpi->get_simpi_info($request);
			$email = [
				'_to' 		=> $request->params->email,
				'_subject' 	=> $this->f->lang('email_subject_register', ['AppsName' => $request->simpi_info->AppsName]),
				'_body'		=> $this->f->lang('email_body_register', [
					'name' 			=> $row->ClientName, 
					'email' 		=> $request->params->email, 
					'new_password' 	=> $new_password,
					'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
					'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
					]),
			];
			list($success, $message) = $this->f->mail_queue($email);
			if (!$success) return [FALSE, $message];
	
			return [TRUE, ['message' => $this->f->lang('success_register')]];
		} else {
			// they claim existing account
			// but not exists on table master_client
			// we should point them to ask to cs
			if ($request->params->account == 'old')
				return [FALSE, ['message' => $this->f->lang('err_old_client_lost_email')]];
		}
		
		if (!$request->params->phone)
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'phone')]];
		
		if (!$request->params->name_first)
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'name_first')]];
		
		if (!$request->params->name_last)
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'name_last')]];
		
		$new_password = $this->f->gen_pwd($this->min_password_length);
		list($success, $message) = $this->is_valid_password($new_password);
		if (!$success)
			return [FALSE, ['message' => $message]];
			
		$new_password_enc = $message;
		$token = $this->f->gen_token();

		// #3: 
		// ==>
		// If not exists on table mobc_login & master_client
		// then insert into table mobc_prospect & mobc_login without ClientID & is_need_activate is true
		// <==
		$this->db->insert($this->table_user, [
			'simpiID' => $request->simpiID, 
			'email' => $request->params->email, 
			'password' => $new_password_enc,
			'forgot_token' => $token,
			'is_need_activate' => 1,
		]);

		$this->db->insert('mobc_prospect', [
				'simpiID' => $request->simpiID, 
				'emailID' => $this->db->insert_id(), 
				'CorrespondenceEmail' => $request->params->email, 
				'CorrespondencePhone' => $request->params->phone, 
				'NameFirst' => $request->params->name_first,
				'NameLast' => $request->params->name_last,
				'AccountStatusID' => 1,		// 1:NOT COMPLETE 2:COMPLETE 3:PROCESSED 4:ACTIVE 5:REJECT 6:ALLOCATE 7:SUSPEND 8:PENDING 9:ACTIVATION => table mobc_status
		]);
		
		$this->load->library('simpi');
		$this->simpi->get_simpi_info($request);
		$email = [
			'_to' 		=> $request->params->email,
			'_subject' 	=> $this->f->lang('email_subject_register', ['AppsName' => $request->simpi_info->AppsName]),
			'_body'		=> $this->f->lang('email_body_register', [
				'name' 			=> 'New Client', 
				'email' 		=> $request->params->email, 
				'new_password' 	=> $new_password,
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
				]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];
	
		return [TRUE, ['message' => $this->f->lang('success_register')]];
	}
	
	/*
	 * Method for activation account just registered.
	 * 
	 * params agent, token
	 * 
	 * return @error 		array(status = FALSE, message = 'Token not found, or your account has already activate !')
	 * return @success 	array(status = TRUE, message = 'Thank you. Now your account has been activate !')
	 * 
		*/
	/**
	 * Method for activation account just registered
	 * 
	 * require 	token,email,password
	 *
	 * @param json_object $request
	 * @return void
	 */
	function activation($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->token) || empty($request->params->token))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'token')]];
		
		if (!isset($request->params->email) || empty($request->params->email))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'email')]];
		
		if (!isset($request->params->password) || empty($request->params->password))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'password')]];
		
		$row = $this->db->get_where($this->table_user, ['forgot_token' => $request->params->token])->row();
		if (!$row)
			return [FALSE, ['message' => $this->f->lang('err_activate_account')]];
		
		if ($request->params->email != $row->email)
			return [FALSE, ['message' => $this->f->lang('err_activate_account_email_password')]];

		if (md5($request->params->password) != $row->password)
			return [FALSE, ['message' => $this->f->lang('err_activate_account_email_password')]];

		$this->db->update($this->table_user, 
			['is_need_activate' => 0, 'forgot_token' => null],
			['simpiID' => $request->simpiID, 'email' => $row->email]
		);
		
		return [TRUE, ['message' => $this->f->lang('success_activation')]];
	}

	function checkToken($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		return [TRUE, NULL];
	}
}
