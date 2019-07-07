<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Systemaccess_model extends CI_Model
{
	public $forgot_token_expiration = 60*60*1;				 
	public $login_token_expiration = 60*60*24;				 
	
	public $min_password_length = 8;
	public $max_login_attempts = 5;
	public $lockout_time = 600;

	function __construct() {
		parent :: __construct();
		$this->load->database(DATABASE_SYSTEM);
		$this->load->library('System');
	}

	function is_valid_access4($request)
	{
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
		return [TRUE, NULL];
	}   

	function is_valid_access3($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];
		return [TRUE, NULL];
	}   

	function is_valid_access2($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];
		return [TRUE, NULL];
	}   

	function is_valid_access2a($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if ($request->simpi_id != 1) {
			$return = $this->system->error_data('00-0', $request->LanguageID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		return [TRUE, NULL];
	}   

	function is_valid_app($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
		return [TRUE, NULL];
	}   

	function id_simpi($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->field_code) || empty($request->params->field_code)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter field_code');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		if (!isset($request->params->field_type) || empty($request->params->field_type)) $request->params->field_type = 1;

		$no = $this->system->id_simpi($request->simpi_id, $request->params->field_code, $request->params->field_type);
		return [TRUE,  ['no' => $no]];
	}   

	function id_system($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->field_code) || empty($request->params->field_code)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter field_code');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		if (!isset($request->params->field_type) || empty($request->params->field_type)) $request->params->field_type = 1;

		$no = $this->system->id_system($request->params->field_code, $request->params->field_type);
		return [TRUE,  ['no' => $no]];
	}   

	function error_message($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		$this->db->select('T1.ErrorID, T1.ErrorCode, T1.ErrorTitle, T1.ErrorMessage');
		$this->db->from('system_error2 T1');
		if (isset($request->params->LanguageID) && !empty($request->params->LanguageID)) {
			$this->db->where('T1.LanguageID', $request->params->LanguageID);
		} else {
			$this->db->join('system_application T2', 'T1.LanguageID = T2.LanguageID');
			$this->db->where('T2.LicenseKey', $request->appkey);
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
		
	function simpi_term($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		$this->db->select('TermID, TermName');
		$this->db->from('simpi_term');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}

	function user_login_check($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'appkey');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		if (!isset($request->params->login) || empty($request->params->login)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter login');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		$this->db->select('T1.UserID');
		$this->db->from('simpi_user T1');
		$this->db->join('simpi_session T2', 'T1.UserID = T2.UserID');  
		$this->db->join('system_application T3', 'T2.AppsID = T3.AppsID');  
		$this->db->where('T3.LicenseKey', $request->appkey);
		$this->db->where('T2.agent', $request->agent);
		$user = $this->security->xss_clean($request->params->login);
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

	function mobc_login_check($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'appkey');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		if (!isset($request->params->client_email) || empty($request->params->client_email)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client_email');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		$this->db->select('T1.emailID');
		$this->db->from('mobc_login T1');
		$this->db->join('mobc_session T2', 'T1.emailID = T2.emailID');  
		$this->db->join('system_application T3', 'T2.AppsID = T3.AppsID');  
		$this->db->where('T3.LicenseKey', $request->appkey);
		$this->db->where('T2.agent', $request->agent);
		$this->db->where('T1.client_email', $request->params->client_email);
		$row = $this->db->get()->row();
		if (!$row) {
			return [TRUE, ['result' => ['logon_status' => FALSE]]];
		} else {
			$return = $this->system->error_data('02-4', $request->LanguageID);
			return [TRUE, ['result' => ['logon_status' => TRUE, 'logon_message' => $return['message']]]];	
		}
	}

	function user_login($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'appkey');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		if (!isset($request->params->login) || empty($request->params->login)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter login');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		if (!isset($request->params->password) || empty($request->params->password)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter password');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		 
		$this->db->select('T1.UserID, T1.login_try, T1.account_locked_until, T1.UserPassword, T1.UserName, 
							T1.UserTitle, T1.TreePrefix, T2.simpiID, T2.simpiName, T2.LanguageID, T2.CountryID');
		$this->db->from('simpi_user T1');
		$this->db->join('master_simpi T2', 'T1.simpiID = T2.simpiID');  
		$user = $this->security->xss_clean($request->params->login);
		$user = "(T1.UserLogin = '".$user."' or T1.UserEmail = '".$user."')";
		$this->db->where($user);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'login');
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
			if (new DateTime() < new DateTime($row->account_locked_until)) {
				$this->load->helper('mydate');
				$return = $this->system->error_data('02-2', $request->LanguageID, nicetime_lang($row->account_locked_until, $request->idiom));
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
			} 
			else $row->login_try = 0;
		}
		
		if (md5("mochamadjunaidi129400033B10193056".$request->params->password) != $row->UserPassword) {
			$login_try = $row->login_try + 1;
			if ($login_try >= $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);		
			$update_field['login_try'] = $login_try;
			$this->db->update('simpi_user', $update_field, ['UserID' => $row->UserID]);			

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
		$result->user_id = $row->UserID;
		$result->UserName = $row->UserName;
		$result->UserTitle = $row->UserTitle;
		$request->TreePrefix = $row->TreePrefix;
		$result->session_id = $token;
		$result->session_exp = $token_expired;
		$result->session_exp_epoch = strtotime($token_expired);
		$result->AppsID = $request->AppsID;
		$result->appcode = $request->appcode;

		return [TRUE, ['result' => $result]];
	}

	function mobc_login($request)
	{
		if (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->system->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'appkey');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		if (!isset($request->params->client_email) || empty($request->params->client_email)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client_email');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		if (!isset($request->params->client_password) || empty($request->params->client_password)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client_password');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		 
		$this->db->select('T1.emailID, T1.login_try, T1.account_locked_until, T1.client_password, 
							T1.client_name, T1.SID, T2.simpiID, T2.simpiName, T2.LanguageID, T2.CountryID');
		$this->db->from('mobc_login T1');
		$this->db->join('master_simpi T2', 'T1.simpiID = T2.simpiID');  
		$this->db->where('T1.client_email', $request->params->client_email);
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'client_email');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$request->simpi_id = $row->simpiID;
		$request->LanguageID = $row->LanguageID;
		$request->log_access = 'apps';
		$request->user_id = 0;
		$request->email_id = $row->emailID;

		list($success, $return) = $this->system->is_valid_credit($request);
		if (!$success) return [FALSE, $return];				

		if ((integer)$row->login_try >= $this->max_login_attempts) {
			if (new DateTime() < new DateTime($row->account_locked_until)) {
				$this->load->helper('mydate');
				$return = $this->system->error_data('02-2', $request->LanguageID, nicetime_lang($row->account_locked_until, $request->idiom));
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
			} 
			else $row->login_try = 0;
		}
		
		if (md5("mochamadjunaidi129400033B10193056".$request->params->client_password) != $row->client_password) {
			$login_try = $row->login_try + 1;
			if ($login_try >= $this->max_login_attempts)
				$update_field['account_locked_until'] = date('Y-m-d H:i:s', time() + $this->lockout_time);		
			$update_field['login_try'] = $login_try;
			$this->db->update('mobc_login', $update_field, ['emailID' => $row->emailID]);			

			$return = $this->system->error_data('02-3', $request->LanguageID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		$token = $this->f->gen_token();
		$token_expired = date('Y-m-d\TH:i:s\Z', time() + $this->login_token_expiration);
		// Invalidate old session
		$this->db->delete('mobc_session', ['emailID' => $row->emailID, 'AppsID' => $request->AppsID, 'agent' => $request->agent]);
		$this->db->insert('mobc_session', ['simpiID' => $row->simpiID, 'emailID' => $row->emailID, 
											'AppsID' => $request->AppsID, 'agent' => $request->agent, 'token' => $token, 
											'token_expired' => $token_expired, 'LogTime' => date('Y-m-d H:i:s')]);	
		$this->db->update('mobc_login', ['login_last' => date('Y-m-d H:i:s'),'login_try' => 0], ['emailID' => $row->emailID]);

		$request->log_type	= 'process';	
		$request->log_size = 0;
		$this->system->save_billing($request);

		$result = (object)[];
		$result->simpi_id = $row->simpiID;
		$result->simpi_name = $row->simpiName;
		$result->LanguageID = $request->LanguageID;
		$result->CountryID = $row->CountryID;
		$result->emailID = $row->emailID;
		$result->client_name = $row->client_name;
		$result->SID = $row->SID;
		$result->token_id = $token;
		$result->token_exp = $token_expired;
		$result->token_exp_epoch = strtotime($token_expired);
		$result->AppsID = $request->AppsID;
		$result->appcode = $request->appcode;
		
		return [TRUE, ['result' => $result]];
	}

	function user_logout($request)
	{
		list($success, $return) = $this->system->is_valid_session($request);
		if (!$success) return [FALSE, $return];
		if (!$return = $this->db->delete('simpi_session', ['session' => $request->session_id])) 
			return [FALSE, ['message' => $this->db->error()['message'], 'error' => NULL]];
		else
			return [TRUE, NULL];
	}

	function mobc_logout($request)
	{
		list($success, $return) = $this->system->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		if (!$return = $this->db->delete('mobc_session', ['token' => $request->token_id])) 
			return [FALSE, ['message' => $this->db->error()['message'], 'error' => NULL]];
		else
			return [TRUE, NULL];
	}
		

}    