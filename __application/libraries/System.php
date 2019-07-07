<?php defined('BASEPATH') OR exit('No direct script access allowed');

class System {
	
	public $ci;

    function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database(DATABASE_SYSTEM);
		$this->ci->load->helper('mylanguage');
	}

	function database_server($request, $server_type)
	{
		$ci = $this->ci;
		$ci->db->select('database_server, database_port, database_name, database_user, database_password, database_type');  
		$ci->db->from('simpi_server2');
		$ci->db->where('simpiID', $request->simpi_id);
		$ci->db->where('server_type', $server_type);
		$ci->db->where('is_ssh', 'N');
		$row = $ci->db->get()->row();
		if (!$row) {
			$return = $this->error_data('00-2', $request->LanguageID, 'server setting');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$db = [
			'dsn'	=> '',
			'hostname' => $row->database_server,
			'username' => $row->database_user,
			'password' => $row->database_password,
			'database' => $row->database_name,
			'dbdriver' => 'mysqli',
			'port' 	   => $row->database_port,
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => IS_LOCAL,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE
		];
		
		return [TRUE, $db];	
	}

	function id_simpi($simpi_id, $field_code, $field_type = 1)
	{
		$ci = $this->ci;
		$ci->db->select('DataInt, DataBig');  
		$ci->db->from('simpi_id');
		$ci->db->where('simpiID', $simpi_id);
		$ci->db->where('FieldCode', $field_code);
		$row = $ci->db->get()->row();
		if (!$row) {
			if ($field_type == 1) {
				$ci->db->set('simpiID', $simpi_id);
				$ci->db->set('FieldCode', $field_code);
				$ci->db->set('DataInt', 0);
				$ci->db->set('DataBig', 1);
				$ci->db->set('FieldType', 1);
				$ci->db->insert('simpi_id');		
			} elseif ($field_type == 2) {
				$ci->db->set('simpiID', $simpi_id);
				$ci->db->set('FieldCode', $field_code);
				$ci->db->set('DataInt', 1);
				$ci->db->set('DataBig', 0);
				$ci->db->set('FieldType', 2);
				$ci->db->insert('simpi_id');		
			}
			return 1;
		}
		else {
			if ($field_type == 1) {
				$ci->db->set('DataBig', $row->DataBig+1);
				$ci->db->where('simpiID', $simpi_id);
				$ci->db->where('FieldCode', $field_code);
				$ci->db->update('simpi_id');
				return $row->DataBig+1;
			} elseif ($field_type == 2) {	
				$ci->db->set('DataInt', $row->DataInt+1);
				$ci->db->where('simpiID', $simpi_id);
				$ci->db->where('FieldCode', $field_code);
				$ci->db->update('simpi_id');
				return $row->DataInt+1;
			} else {
                return 1;
			}
		}
	}

	function id_system($field_code, $field_type = 1)
	{
		$ci = $this->ci;
		$ci->db->select('DataInt, DataBig');  
		$ci->db->from('system_id');
		$ci->db->where('FieldCode', $field_code);
		$row = $ci->db->get()->row();
		if (!$row) {
			if ($field_type == 1) {
				$ci->db->set('FieldCode', $field_code);
				$ci->db->set('DataInt', 0);
				$ci->db->set('DataBig', 1);
				$ci->db->set('FieldType', 1);
				$ci->db->insert('system_id');
			} elseif ($field_type == 2) {
				$ci->db->set('FieldCode', $field_code);
				$ci->db->set('DataInt', 1);
				$ci->db->set('DataBig', 0);
				$ci->db->set('FieldType', 2);
				$ci->db->insert('system_id');
			}		
			return 1;
		}
		else {
			if ($field_type == 1) {
				$ci->db->set('DataBig', $row->DataBig+1);
				$ci->db->where('FieldCode', $field_code);
				$ci->db->update('system_id');
				return $row->DataBig+1;
			} elseif ($field_type == 2) {	
				$ci->db->set('DataInt', $row->DataInt+1);
				$ci->db->where('FieldCode', $field_code);
				$ci->db->update('system_id');
				return $row->DataInt+1;
			} else {
				return 1;
			}		
		}
	}

	function commit_data($request, $sqls, $desc, $content, $server)
	{
		$log_id = $this->id_system_big('SYSTEM LOG');
		$log = [
			'LogID' => $log_id,                       
			'AppsDate' => date('Y-m-d H:i:s'),
			'UserID' => $request->user_id,
			'emailID' => $request->email_id,
			'AppsID' => $request->AppsID,
			'AppsTerminal' => $request->ip_address,
			'LogDescription' => $desc, 		
			'LogContent' => $content, 		
			'LogAccess' => $request->log_access,	//license, apps, session, token, dll
			'LogAgent' => $request->agent,			//web, android, ios, windows
			'LogDate' => date('Y-m-d H:i:s')
		];

		$commit = $this->load->database($server);
		$commit->db->trans_strict(TRUE);
		$commit->db->trans_start();

		$commit->db->insert('system_log', $log);
		foreach($sqls as $sql) {			
			$commit->db->query($sql);
			$commit->db->insert('system_log_sql', ['LogID' => $log_id, 'LogSQL' => $sql]);
		}
		$commit->db->trans_complete();

		if ($commit->db->trans_status() === FALSE) {
			$return = $this->error_data('00-0', $request->LanguageID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		return [TRUE, NULL];
	}

	function refill_message($source, $refill = NULL)
	{
		$ci = $this->ci;
		$ci->load->helper('mystring');
		if (!isset($refill) || empty($refill)) return $source;
		return sprintfx($source, $refill);
	}

	function error_data($errCode, $LanguageID = NULL, $replace = NULL)
	{
		$ci = $this->ci;
		if (!isset($LanguageID) || ($LanguageID == 0))  $LanguageID = 1;
		if (!isset($replace) || empty($replace)) $replace = '';
		$ci->db->reset_query();
		$row = $ci->db->get_where('system_error2', ['ErrorCode' => $errCode, 'LanguageID' => $LanguageID], 1)->row();
		print_r($ci->db->last_query());
		exit();
		if (!$row) {
			$message = $errCode;
			$res['id'] = 0;	
			$res['code'] = $errCode;	
			$res['title'] = '';	
			$res['data'] = $replace;
			$res['message'] = '';	
			return ['error' => $res, 'message' => $message];
		} else {
			if ($replace == '') {
				$message = $row->ErrorMessage;
			} else {
				$ci->load->helper('mystring');
				$message = sprintfx($row->ErrorMessage, ['data' => $replace]);	 			
			}
			$res['id'] = $row->ErrorID;	
			$res['code'] = $errCode;	
			$res['title'] = $row->ErrorTitle;	
			$res['data'] = $replace;
			$res['message'] = $row->ErrorMessage;
			return ['error' => $res, 'message' => $message];
		}
	}

	function error_message($errCode, $LanguageID = NULL)
	{
		$ci = $this->ci;
        if (!isset($LanguageID) || ($LanguageID == 0))  $LanguageID = 1;
		$row = $ci->db->get_where('system_error2', ['ErrorCode' => $errCode, 'LanguageID' => $LanguageID], 1)->row();
		if (!$row) return [FALSE, ['message' => $errCode]];	
		return [TRUE, ['id' => $row->ErrorID, 'title' => $row->ErrorTitle, 'message' => $row->ErrorMessage]];
	}

	function mail_message($msgCode, $LanguageID = NULL)
	{
		$ci = $this->ci;
        if (!isset($LanguageID) || ($LanguageID == 0))  $LanguageID = 1;
		$row = $ci->db->get_where('system_message', ['msgCode' => $msgCode, 'LanguageID' => $LanguageID], 1)->row();
		if (!$row) return [FALSE, ['message' => $msgCode]];	
		return [TRUE, ['id' => $row->msgID, 'title' => $row->msgTitle, 'message' => $row->msgMessage]];
	}
	
	function is_valid_access4($request)
	{
		if (isset($request->license_key) && !empty($request->license_key)) {
			list($success, $return) = $this->is_valid_license($request);
			if (!$success) return [FALSE, $return];			 
		} elseif (isset($request->session_id) && !empty($request->session_id)) {
			list($success, $return) = $this->is_valid_session($request);
			if (!$success) return [FALSE, $return];			
		} elseif (isset($request->token_id) && !empty($request->token_id)) {
			list($success, $return) = $this->is_valid_token($request);
			if (!$success) return [FALSE, $return];			
		} elseif (isset($request->appkey) && !empty($request->appkey)) {
			list($success, $return) = $this->is_valid_appkey($request);
			if (!$success) return [FALSE, $return];			
		} else {
			$return = $this->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		return [TRUE, NULL];
	}

	function is_valid_access3($request)
	{
		if (isset($request->license_key) && !empty($request->license_key)) {
			list($success, $return) = $this->is_valid_license($request);
			if (!$success) return [FALSE, $return];			 
		} elseif (isset($request->session_id) && !empty($request->session_id)) {
			list($success, $return) = $this->is_valid_session($request);
			if (!$success) return [FALSE, $return];			
		} elseif (isset($request->token_id) && !empty($request->token_id)) {
			list($success, $return) = $this->is_valid_token($request);
			if (!$success) return [FALSE, $return];			
		} else {
			$return = $this->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		return [TRUE, NULL];
	}

	function is_valid_access2($request)
	{
		if (isset($request->license_key) && !empty($request->license_key)) {
			list($success, $return) = $this->is_valid_license($request);
			if (!$success) return [FALSE, $return];			 
		} elseif (isset($request->session_id) && !empty($request->session_id)) {
			list($success, $return) = $this->is_valid_session($request);
			if (!$success) return [FALSE, $return];			
		} else {
			$return = $this->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		return [TRUE, NULL];
	}

	function is_valid_simpipro($request)
	{
		list($success, $return) = $this->is_valid_access2($request);
		if (!$success) return [FALSE, $return];			 
		if ($request->simpi_id != 1) {
			$return = $this->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		return [TRUE, NULL];
	}

	function is_valid_license($request)
	{
		$ci = $this->ci;
		$row = $ci->db->get_where('master_simpi', ['simpiLicenseKey' => $request->license_key], 1)->row();
		if (!$row) {
			$return = $this->error_data('00-2', $request->LanguageID, 'license key');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$request->simpi_id = $row->simpiID;
		$request->LanguageID = $row->LanguageID;
		$request->log_access = 'license';
		$request->user_id = 0;
		$request->TreePrefix = '';
		$request->email_id = 0;
		$request->SID = '';

		list($success, $return) = $this->is_valid_credit($request);
		if (!$success) return [FALSE, $return];		

		if (!isset($request->appcode) || empty($request->appcode)) {
			$return = $this->error_data('00-1', $request->LanguageID, 'appcode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$row = $ci->db->get_where('system_application', ['AppsCode' => $request->appcode], 1)->row();
		if (!$row) {
			$return = $this->error_data('00-2', $request->LanguageID, 'appcode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$request->AppsID = $row->AppsID;

		return [TRUE, NULL];
	}

	function is_valid_session($request)
	{
		$ci = $this->ci;
		$ci->db->select('a.AppsID, c.AppsCode, a.simpiID, b.LanguageID, a.UserID, d.TreePrefix');
		$ci->db->from('simpi_session a');
		$ci->db->join('master_simpi b', 'a.simpiID = b.simpiID');  
		$ci->db->join('system_application c', 'a.AppsID = c.AppsID');  
		$ci->db->join('simpi_user d', 'a.simpiID = a.simpiID And a.UserID = d.UserID');  
		$ci->db->where('a.session', $request->session_id);
		$ci->db->where('a.agent', $request->agent);
		$row = $ci->db->get()->row();
		if (!$row) {
			$return = $this->error_data('00-2', $request->LanguageID, 'session_id');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$request->simpi_id = $row->simpiID;
		$request->LanguageID = $row->LanguageID;
		$request->log_access = 'session';
		$request->user_id = $row->UserID;
		$request->TreePrefix = $row->TreePrefix;
		$request->email_id = 0;
		$request->SID = '';
		$request->AppsID = $row->AppsID;
		$request->appcode = $row->AppsCode;

		list($success, $return) = $this->is_valid_credit($request);
		if (!$success) return [FALSE, $return];		

		return [TRUE, NULL];
	}

	function is_valid_token($request)
	{
		$ci = $this->ci;
		$ci->db->select('a.application_id, a.token_expired, b.code as application_code, c.partner_id, c.username');
		$ci->db->from('a_session a');
		$ci->db->join('a_application b', 'a.application_id = b.application_id');  
		$ci->db->join('a_login c', 'a.login_id = c.login_id');  
		$ci->db->where('a.token', $request->token_id);
		$ci->db->where('a.agent', $request->agent);
		$row = $ci->db->get()->row();
		if (!$row) {
			return [FALSE, ['message' => err_msg('err_token_invalid')]];
		}
		$request->simpi_id = $row->simpiID;
		$request->LanguageID = $row->LanguageID;
		$request->log_access = 'token';
		$request->user_id = 0;
		$request->TreePrefix = '';
		$request->email_id = $row->emailID;
		$request->SID = $row->SID;
		$request->application_id = $row->application_id;
		$request->appcode = $row->AppsCode;

		list($success, $return) = $this->is_valid_credit($request);
		if (!$success) return [FALSE, $return];		

		return [TRUE, NULL];
	}

	function is_valid_appcode($request)
	{
		$ci = $this->ci;
		$ci->db->select('*');
		$ci->db->from('a_application');
		$ci->db->where('code', $request->appcode);
		$row = $ci->db->get()->row();
		if (!$row) {
			return [FALSE, ['message' => err_msg('err_appcode_invalid')]];
		}
		$request->application_id = $row->id;

		return [TRUE, NULL];
	}

	function is_valid_appkey($request)
	{
		$ci = $this->ci;
		$ci->db->select('a.AppsID, a.AppsCode, a.simpiID, b.LanguageID');
		$ci->db->from('system_application a');
		$ci->db->join('master_simpi b', 'a.simpiID = b.simpiID');  
		$ci->db->where('a.LicenseKey', $request->appkey);
		$row = $ci->db->get()->row();
		if (!$row) {
			$return = $this->error_data('00-2', $request->LanguageID, 'appkey');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		$request->simpi_id = $row->simpiID;
		$request->LanguageID = $row->LanguageID;
		$request->log_access = 'apps';
		$request->user_id = 0;
		$request->TreePrefix = '';
		$request->email_id = 0;
		$request->SID = '';
		$request->AppsID = $row->AppsID;
		$request->appcode = $row->AppsCode;

		list($success, $return) = $this->is_valid_credit($request);
		if (!$success) return [FALSE, $return];		
		return [TRUE, NULL];
	}

	function is_valid_credit($request)
	{
		$ci = $this->ci;
		$row = $ci->db->get_where('simpi_credit', ['simpiID' => $request->simpi_id], 1)->row();
		if (!$row) {
			$return = $this->error_data('00-2', $request->LanguageID, 'simpi credit');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}

		if ($row->GracePeriod < date('Y-m-d H:i:s')) {
			$return = $this->error_data('01-2', $request->LanguageID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		if ($row->CreditBalance < 1) {
			$return = $this->error_data('01-1', $request->LanguageID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		return [TRUE, NULL];
	}

	function save_billing($request)
	{
		$ci = $this->ci;
		$ci->load->library('simpi');
		$log_id = $this->id_simpi($request->simpi_id, 'BILLING LOG');
		$method_class = explode(".", $request->method);
		if (sizeof($method_class) == 2) {
			$lc = $method_class[0];
			$lm = $method_class[1];
		} elseif (sizeof($method_class) == 3) {
			$lc = $method_class[0].'.'.$method_class[1];
			$lm = $method_class[2];
		} else {
			$lc = $request->method;
			$lm = $request->method;
		}			 		
		$BM =& load_class('Benchmark', 'core');
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');

		$data = [
			'simpiID' => $request->simpi_id,
			'LogID' => $log_id,                      //     $return['dataID'],
			'LogType' => $request->log_type, 		//data, process, order 
			'LogAccess' => $request->log_access,	//license, apps, session, token, dll
			'LogAgent' => $request->agent,			//web, android, ios, windows
			'UserID' => $request->user_id,
			'emailID' => $request->email_id,
			'AppsID' => $request->AppsID,
			'LogDate' => date('Y-m-d H:i:s'),
			'LogClass' => $lc,
			'LogMethod' => $lm,
			'LogSize' => $request->log_size,
			'LogTime' => $elapsed 
		];
		$ci->db->insert('simpi_billing', $data);
	}

	function mail_queue($email = [])
	{
		$ci = $this->ci;
		$ci->config->load('email', FALSE);
		$email = is_array($email) ? (object)$email : $email; 

		$config = [
			'useragent'		=> 'CI Webservice',
			'newline'		=> "\r\n",
			'protocol'		=> 'smtp',
			'smtp_host'		=> $ci->config->item('smtp_host'),
			'smtp_port'		=> $ci->config->item('smtp_port'),
			'smtp_user'		=> $ci->config->item('smtp_user'),
			'smtp_pass'		=> $ci->config->item('smtp_pass'),
			'smtp_timeout'	=> '7',
			'charset'		=> 'iso-8859-1',
			'mailtype'		=> 'html',
			'priority'		=> '1',
		];
		// ['_from', '_to', '_cc', '_bcc', '_subject', '_body', '_attachment', '_config', 'is_test']
		// is_test : char(1) ['0'|'1']
		$email->is_test = IS_LOCAL ? '1' : '0';
		$email->_config = json_encode($config);
		$email->_from = isset($email->_from) ? $email->_from : $ci->config->item('email_from');
		if (!isset($email->_to)) {
			$return = $this->error_data('00-2', $request->LanguageID, 'email_to');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		}
		// _attachment & _config : JSON formatted string
		if (isset($email->_attachment)) {
			if (is_array($email->_attachment))
				$email->_attachment = json_encode($email->_attachment);
		}

		if (!$result = $ci->db->insert('mobc_mail_queue', $email))
			return [FALSE, ['message' => $ci->db->error()['message']]];
		
		return [TRUE, NULL];
	}

}