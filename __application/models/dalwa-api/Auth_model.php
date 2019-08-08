<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Auth_model extends CI_Model
{
	private $table_user = 'a_login';						 
	private $table_session = 'a_session';					 
	public $login_token_expiration = 60*60*24; // second*minute*hour

	private $min_password_length = 5;
	private $max_password_length = 0;
	private $max_login_attempts = 3;
	private $lockout_time = 600;
	private $sender_id = 1;
	
	function __construct()
	{
		parent::__construct();
		$this->load->database(DATABASE_SYSTEM);
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
			return [FALSE, $this->f->_err_msg('err_param_required', 'password')];
		
		if (strlen($password) < $this->min_password_length)
			return [FALSE, $this->f->_err_msg('err_min_password_length', $this->min_password_length)];
		
		if ($this->max_password_length > 0) 
		{
			if (strlen($password) > $this->max_password_length)
				return [FALSE, $this->f->_err_msg('err_max_password_length', $this->max_password_length)];
		}
		
		$password = md5($password);
		return [TRUE, $password];
	}
	
	private function is_correct_password($password1, $password2)
	{
		return md5($password1) == $password2;
	}
	
	/**
	 * Method for register new account 
	 *
	 * require 	email, phone, full_name, password
	 *
	 * @param json_object $request
	 * @return void
	 */
	function register($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->f->check_param_required($request, ['full_name','phone','email','gender']);
		if (!$success) return [FALSE, $return];
	
		$row = $this->db->get_where($this->table_user, ['email' => $request->params->email])->row();
		if ($row)
			return [FALSE, ['message' => $this->f->_err_msg('err_email_has_register')]];
		
		$new_password = $this->f->gen_pwd($this->min_password_length);
		list($success, $result) = $this->is_valid_password($new_password);
		if (!$success)
			return [FALSE, ['message' => $result]];
			
		$new_password_enc = $result;
		$result = $this->db->insert($this->table_user, [
			'client_id' => $request->client_id, 
			'full_name' => $request->params->full_name, 
			'username' => $request->params->email, 
			'email' => $request->params->email, 
			'phone' => $request->params->phone, 
			'password' => $new_password_enc,
			'gender' => $request->params->gender,
			'created_at' => date('Y-m-d H:i:s', time()),
		]);
		if (! $result)
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];

		$email = [
			'sender_id' => $this->sender_id,
			'_to' 			=> $request->params->email,
			'_subject' 	=> $this->f->lang('email_subject_register', ['app_name' => $request->app_name]),
			'_body'			=> $this->f->lang('email_body_register', [
				'name' 					=> $request->params->full_name, 
				'email' 				=> $request->params->email, 
				'password' 			=> $new_password,
				'powered_by' 		=> 'Powered by DALWA @2019',
				'app_name' 			=> $request->app_name,
				]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];
	
		return [TRUE, ['message' => $this->f->lang('success_register')]];
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

		list($success, $return) = $this->f->check_param_required($request, ['username','password']);
		if (!$success) return [FALSE, $return];

		if (!$result = $this->db->get_where($this->table_user, ['username' => $request->params->username]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_username_or_email_not_found')]];
		
		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->_err_msg('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}

		if (! $this->is_correct_password($request->params->password, $row->password)) {

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, $update_field, ['username' => $request->params->username]
			);
			
			return [FALSE, ['message' => $this->f->_err_msg('err_login_failed')]];
		}
		
		$token =  $this->f->gen_token();
		$token_expired = date('Y-m-d\TH:i:s\Z', time() + $this->login_token_expiration);
		// Invalidate old session
		$this->db->delete($this->table_session, [
			'client_id' => $row->client_id, 'login_id' => $row->login_id, 'app_id' => $request->app_id, 'agent' => $request->agent, 'token <>' => $token
			]
		);
		
		$this->db->insert($this->table_session, [
				'client_id' => $row->client_id, 'login_id' => $row->login_id, 'app_id' => $request->app_id, 'agent' => $request->agent, 
				'token' => $token, 'token_expired' => $token_expired, 'created_at' => date('Y-m-d H:i:s')
			]
		);
		
		$this->db->update($this->table_user, 
			['login_last' => date('Y-m-d H:i:s'), 'login_try' => 0], 
			['client_id' => $row->client_id, 'username' => $request->params->username] 
		);
		
		$result = (object)[];
		$result->token = $token;
		
		return [TRUE, ['result' => $result, 'message' => $this->f->lang('success_login')]];
	}
	
	/**
	 * Method for login, with checking of login attempt and generate session token
	 *
	 * @param json_object $request
	 * @return void
	 */
	function logout($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$return = $this->db->delete($this->table_session, ['token' => $request->token])) 
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];
		else
			return [TRUE, NULL];
	}
	
	/**
	 * Method for change password
	 *
	 * @param json_object $request
	 * @return void
	 */
	function password_change($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->f->check_param_required($request, ['username', 'password', 'new_password']);
		if (!$success) return [FALSE, $return];
		
		if (!$result = $this->db->get_where($this->table_user, ['username' => $request->params->username]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_username_or_email_not_found')]];

		if ((integer)$row->login_try >= $this->max_login_attempts){
			$this->load->helper('mydate');
			return [FALSE, ['message' => $this->f->_err_msg('err_login_attempt_reached', nicetime_lang($row->account_locked_until, $request->idiom))]];
		}
		
		if (! $this->is_correct_password($request->params->password, $row->password)) {

			$login_try = $row->login_try + 1;
			if ($login_try == $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);
			
			$update_field['login_try'] = $login_try;
			$this->db->update($this->table_user, 
				$update_field, 
				['username' => $request->params->username]
			);
			
			return [FALSE, ['message' => $this->f->_err_msg('err_old_password')]];
		}
		
		list($success, $result) = $this->is_valid_password($request->params->new_password);
		if (!$success) return [FALSE, ['message' => $result]];
		
		$new_password = $request->params->new_password;
		$new_password_enc = $result;
		$this->db->update($this->table_user, 
			['login_try' => 0, 'password' => $new_password_enc], 
			['username' => $request->params->username]);
		
		$email = [
			'sender_id' => $this->sender_id,
			'_to' 			=> $request->email,
			'_subject' 	=> $this->f->lang('email_subject_chg_password', ['app_name' => $request->app_name]),
			'_body'			=> $this->f->lang('email_body_chg_password', [
				'name' 					=> $request->full_name, 
				'email' 				=> $request->email, 
				'password' 			=> $new_password,
				'powered_by' 		=> 'Powered by DALWA @2019',
				'app_name' 			=> $request->app_name,
				]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];
		
		return [TRUE, ['message' => $this->f->lang('success_chg_password')]];
	}
	
	/**
	 * Method for forgot password
	 *
	 * @param json_object $request
	 * @return void
	 */
	function password_forgot($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->f->check_param_required($request, ['email']);
		if (!$success) return [FALSE, $return];
		
		if (!$result = $this->db->get_where($this->table_user, ['email' => $request->params->email]))
			return [FALSE, ['message' => 'Database Error: '.$this->db->error()['message']]];		

		if (!$row = $result->row()) 
			return [FALSE, ['message' => $this->f->_err_msg('err_email_not_found')]];
		
		// generate random password
		$new_password = $this->f->gen_pwd($this->min_password_length);
		$new_password_enc = md5($new_password);
		$this->db->update($this->table_user, 
			['password' => $new_password_enc], 
			['email' => $row->email]
		);
		
		$email = [
			'sender_id' => $this->sender_id,
			'_to' 			=> $request->params->email,
			'_subject' 	=> $this->f->lang('email_subject_forgot_password', ['app_name' => $request->app_name]),
			'_body'			=> $this->f->lang('email_body_forgot_password', [
				'name' 					=> $row->full_name, 
				'email' 				=> $request->params->email, 
				'password' 			=> $new_password,
				'powered_by' 		=> 'Powered by DALWA @2019',
				'app_name' 			=> $request->app_name,
				]),
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('info_sent_email_password'), 'pwd' => $new_password]];
	}
	
}
