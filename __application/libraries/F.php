<?php defined('BASEPATH') OR exit('No direct script access allowed');

class F {

	protected $_like_escape_chr = '!';

	function __construct()
	{
	}
	
	/**
	 * Shortcut for get language line key and with additional parameters
	 *
	 */
	function lang($key, $params = NULL)
	{
		$ci = get_instance()->load->helper('mylanguage');
		return mylang($key, $params);
	}
	
	/**
	 * Shortcut for get language line key and with additional parameters
	 *
	 */
	function _err_msg($key, $params = NULL)
	{
		return F::lang($key, $params);
	}
	
	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 * Sets boolean and null types
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function escape($str)
	{
		if (is_array($str))
		{
			$str = array_map(array(&$this, 'escape'), $str);
			return $str;
		}
		elseif (is_string($str) OR (is_object($str) && method_exists($str, '__toString')))
		{
			return "'".$this->escape_str($str)."'";
		}
		elseif (is_bool($str))
		{
			return ($str === FALSE) ? 0 : 1;
		}
		elseif ($str === NULL)
		{
			return 'NULL';
		}

		return $str;
	}

	/**
	 * Escape String
	 *
	 * @param	string|string[]	$str	Input string
	 * @param	bool	$like	Whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	public function escape_str($str, $like = FALSE)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->escape_str($val, $like);
			}
			return $str;
		}

		$str = $this->_escape_str($str);

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(
				array($this->_like_escape_chr, '%', '_'),
				array($this->_like_escape_chr.$this->_like_escape_chr, 
		   			  $this->_like_escape_chr.'%', $this->_like_escape_chr.'_'), $str);
		}

		return $str;
	}

	/**
	 * Platform-dependent string escape
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _escape_str($str)
	{
		return str_replace("'", "''", remove_invisible_characters($str, FALSE));
	}

	/**
	 * Compile Query
	 *
	 * @param	string	the sql statement
	 * @param	array	an array of bind data
	 * @return	string
	 */
	function compile_qry($sql, $binds=[])
	{
		$bind_marker = '?';
		if (empty($bind_marker) OR strpos($sql, $bind_marker) === FALSE)
		{
			return $sql;
		}
		elseif ( ! is_array($binds))
		{
			$binds = array($binds);
			$bind_count = 1;
		}
		else
		{
			// Make sure we're using numeric keys
			$binds = array_values($binds);
			$bind_count = count($binds);
		}

		$sql = preg_replace('/\s+/', ' ', trim($sql)); // Replaces all new line with space

		// We'll need the marker length later
		$ml = strlen($bind_marker);

		// Make sure not to replace a chunk inside a string that happens to match the bind marker
		if ($c = preg_match_all("/'[^']*'|\"[^\"]*\"/i", $sql, $matches))
		{
			$c = preg_match_all('/'.preg_quote($bind_marker, '/').'/i',
				str_replace($matches[0],
					str_replace($bind_marker, str_repeat(' ', $ml), $matches[0]),
					$sql, $c),
				$matches, PREG_OFFSET_CAPTURE);

			// Bind values' count must match the count of markers in the query
			if ($bind_count !== $c)
			{
				return $sql;
			}
		}
		elseif (($c = preg_match_all('/'.preg_quote($bind_marker, '/').'/i', $sql, $matches, PREG_OFFSET_CAPTURE)) !== $bind_count)
		{
			return $sql;
		}

		do
		{
			$c--;
			$escaped_value = $this->escape($binds[$c]);
			if (is_array($escaped_value))
			{
				$escaped_value = '('.implode(',', $escaped_value).')';
			}
			$sql = substr_replace($sql, $escaped_value, $matches[0][$c][1], $ml);
		}
		while ($c !== 0);

		return $this->remove_hidden_chars($sql, FALSE);
	}
	
	function remove_hidden_chars($str)
	{
		return preg_replace( '/[\x00-\x1F\x7F]/u', '', $str);
	}

	/*
	 * Generate random string with combination random salt & secret key
	 * 
	 */
	function gen_token()
	{
		$ci = get_instance()->load->helper('myencrypt');
		return create_token();
	}

	/*
	 * Simple random password generator with length limit
	 * 
	 */
	function gen_pwd($chars) 
	{
		$ci = get_instance()->load->helper('myencrypt');
		return random_str($chars);
	}

	function is_valid_appcode($request)
	{
		if (!isset($request->appcode) || empty($request->appcode)) 
			return [FALSE, ['message' => F::_err_msg('err_appcode_invalid')]];

		$ci =& get_instance();
		$ci->db->select('*');
		$ci->db->from('a_application');
		$ci->db->where('code', $request->appcode);
		if (! $result = $ci->db->get())
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];		

		if (! $row = $result->row()) 
			return [FALSE, ['message' => F::_err_msg('err_appcode_invalid')]];

		if ($row->agent != $request->agent) 
			return [FALSE, ['message' => F::_err_msg('err_appcode_agent_invalid')]];

		$request->client_id = $row->client_id;
		$request->app_id = $row->app_id;
		$request->app_name = $row->name;
		return [TRUE, NULL];
	}

	function is_valid_token($request)
	{
		$ci =& get_instance();
		$ci->db->select('a.app_id, a.agent, a.token_expired, b.client_id, b.login_id, b.username, b.password, c.code as app_code, c.name as app_name');
		$ci->db->from('a_session a');
		$ci->db->join('a_login b', 'b.login_id = a.login_id');  
		$ci->db->join('a_application c', 'c.app_id = a.app_id');  
		$ci->db->where('a.token', $request->token);
		$ci->db->where('a.agent', $request->agent);
		$ci->db->where('c.code', $request->appcode);
		if (! $result = $ci->db->get())
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];		

		if (! $row = $result->row()) 
			return [FALSE, ['message' => F::_err_msg('err_token_invalid'), 'need_login' => true]];
		
		if ($row->token_expired < date('Y-m-d H:i:s'))
			return [FALSE, ['message' => F::_err_msg('err_token_invalid'), 'need_login' => true]];
		
		$request->app_id = $row->app_id;
		$request->app_code = $row->app_code;
		$request->app_name = $row->app_name;
		$request->client_id = $row->client_id;
		$request->login_id = $row->login_id;
		return [TRUE, NULL];
	}

	/* 
	 * Method for saving request to log table
	 * 
	 * params array()
	 * 
	 * return @error 		array(status = FALSE, message = 'Invalid License Key !')
	 * return @success 	array(status = TRUE, data = $row)
	 * 
	 */
	function save_request($request)
	{
		$ci = &get_instance();
		// $ci->load->database('cloud_simpi');

		$data = [
			'request' => json_encode($request),
			'request_at' => date('Y-m-d H:i:s'),
			'request_method' => $request->method,
			'request_agent' => $request->agent,
			'http_protocol' => PROTOCOL,
			'http_host' => HTTP_HOST,
			'http_method' => HTTP_METHOD,
			'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL,
			'ip_address' => $request->ip_address,
			'is_local' => $request->is_local,
		];
		$ci->db->insert('simpi_log_api_tmp', $data);
	}
	
	function check_field_required($request, $fields = array())
	{
		foreach($fields as $k => $v)
		{
			if (!isset($request->{$v}) || empty($request->{$v}))
				return [FALSE, ['message' => F::_err_msg('err_field_required', $v)]];
		}

		return [TRUE, NULL];
	}

	function check_param_required($request, $fields = array())
	{
		foreach($fields as $k => $v)
		{
			if (!isset($request->params->{$v}) || empty($request->params->{$v}))
				return [FALSE, ['message' => F::_err_msg('err_param_required', $v)]];
		}

		return [TRUE, NULL];
	}

	/*
	 * Create avatar from word, like on google mail apps
	 * 
	 */
	function create_avatar_img($word = '', $img_path = '', $img_url = '', $font_path = '')
	{
		$defaults = array(
			'word'		=> '',
			'img_path'	=> '__tmp/',
			'img_url'	=> base_url('__tmp/'),
			'img_width'	=> '215',
			'img_height'	=> '215',
			'img_type'	=> 'png',
			'font_path'	=> BASEPATH.'fonts/texb.ttf',
			'word_length'	=> 1,
			'font_size'	=> 100,
			'img_id'	=> '',
		);
		
		if (is_array($word)) {
			foreach ($word as $k => $v)
			{
				$defaults[$k] = !empty($v) ? $v : $defaults[$k];
			}
		} else {
			$defaults['word'] 			= !empty($word) ? $word : $defaults['word'];
			$defaults['img_path'] 	= !empty($img_path) ? $img_path : $defaults['img_path'];
			$defaults['img_url'] 		= !empty($img_url) ? $img_url : $defaults['img_url'];
			$defaults['font_path'] 	= !empty($font_path) ? $font_path : $defaults['font_path'];
		}

		extract($defaults);

		if (! extension_loaded('gd'))
		{
			show_error('This '.__CLASS__.'->'.__FUNCTION__.' Function requires the php_gd extension.');
		}
		
		if ($img_path === '' OR $img_url === '' OR ! is_dir($img_path) OR ! is_really_writable($img_path))
		{
			return FALSE;
		}
		
		$im = function_exists('imagecreatetruecolor')
			? imagecreatetruecolor($img_width, $img_height)
			: imagecreate($img_width, $img_height);
		
		$i = strtoupper(substr($word, 0, 1));
		$r = rand(0, 255);
		$g = rand(0, 255);
		$b = rand(0, 255);
		$x = (imagesx($im) - $font_size * strlen($i)) / 2;
		$y = (imagesy($im) + ($font_size-($font_size*0.25))) / 2;
		$bg = imagecolorallocate($im, $r, $g, $b);
		$tc = imagecolorallocate($im, 255, 255, 255);
		
		// Create the rectangle
		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg);
		
		$use_font = ($font_path !== '' && file_exists($font_path) && function_exists('imagettftext'));
		if ($use_font === FALSE)
		{
			($font_size > 5) && $font_size = 5;
			imagestring($im, $font_size, $x, $y, $i, $tc);
		}
		else
		{
			// ($font_size > 30) && $font_size = 30;
			imagettftext($im, $font_size, 0, $x, $y, $tc, $font_path, $i);
		}

		// -----------------------------------
		//  Generate the image
		// -----------------------------------
		$now = microtime(TRUE);
		$img_url = rtrim($img_url, '/').'/';

		if ($img_type == 'jpeg')
		{
			$img_filename = $now.'.jpg';
			imagejpeg($im, $img_path.$img_filename);
		}
		elseif ($img_type == 'png')
		{
			$img_filename = $now.'.png';
			imagepng($im, $img_path.$img_filename);
		}
		else
		{
			return FALSE;
		}

		$img = '<img '.($img_id === '' ? '' : 'id="'.$img_id.'"').' src="'.$img_url.$img_filename.'" style="width: '.$img_width.'; height: '.$img_height .'; border: 0;" alt=" " />';
		ImageDestroy($im);

		return array(
			'image' 	=> $img, 
			'file_path' => $img_path.$img_filename, 
			'file_url'	=> $img_url.$img_filename,
			'filename' 	=> $img_filename
		);
	}

	/*
	 * Send Mail Method from CI
	 * 
	 */
	function mail_send($email = [])
	{
		$ci = &get_instance();
		$ci->config->load('email', FALSE);
		$ci->load->library('email');
		$ci->lang->load('email');
		$email = is_array($email) ? (object)$email : $email; 
		
		$config = [
			'useragent'		=> isset($email->config->useragent) ? $email->config->useragent : $ci->config->item('useragent'),
			'newline'		=> isset($email->config->newline) ? $email->config->newline : $ci->config->item('newline'),
			'crlf'			=> isset($email->config->crlf) ? $email->config->crlf : $ci->config->item('crlf'),
			'protocol'		=> isset($email->config->protocol) ? $email->config->protocol : $ci->config->item('protocol'),
			'smtp_host'		=> isset($email->config->smtp_host) ? $email->config->smtp_host : $ci->config->item('smtp_host'),
			'smtp_port'		=> isset($email->config->smtp_port) ? $email->config->smtp_port : $ci->config->item('smtp_port'),
			'smtp_user'		=> isset($email->config->smtp_user) ? $email->config->smtp_user : $ci->config->item('smtp_user'),
			'smtp_pass'		=> isset($email->config->smtp_pass) ? $email->config->smtp_pass : $ci->config->item('smtp_pass'),
			'smtp_crypto'	=> isset($email->config->smtp_crypto) ? $email->config->smtp_crypto : $ci->config->item('smtp_crypto'),
			'smtp_timeout'	=> isset($email->config->smtp_timeout) ? $email->config->smtp_timeout : $ci->config->item('smtp_timeout'),
			'charset'			=> isset($email->config->charset) ? $email->config->charset : $ci->config->item('charset'),
			'mailtype'		=> isset($email->config->mailtype) ? $email->config->mailtype : $ci->config->item('mailtype'),
			'priority'		=> isset($email->config->priority) ? $email->config->priority : $ci->config->item('priority'),
		];
		
		if (!$email->to)
			return [FALSE, ['message' => F::_err_msg('err_email_to')]];
		if (!$email->subject)
			return [FALSE, ['message' => F::_err_msg('err_email_subject')]];
		
		$ci->email->clear();
		$ci->email->initialize($config);
		if ((isset($email->from)) && isset($email->from_name)) {
			if (! empty($email->from_name))
				$ci->email->from($email->from, $email->from_name);
			else
				$ci->email->from($email->from);
		}
		$ci->email->to($email->to); 
		if (isset($email->cc)) $ci->email->cc($email->cc);
		if (isset($email->bcc)) $ci->email->bcc($email->bcc);
		$ci->email->subject($email->subject);
		$ci->email->message($email->body ? $email->body : '');	
		if (isset($email->attachment)) {
			if (is_array($email->attachment)) {
				foreach ($email->attachment as $file)
					$ci->email->attach($file);
			} else {
				$ci->email->attach($email->attachment);
			}
		}
		
		if (!$ci->email->send())
			return [FALSE, ['message' => $ci->email->print_debugger()]];
			// self::debug($ci->email->print_debugger());

		return [TRUE, NULL];
	}

	/*
	 * Send Email to Queue Table 
	 * 
	 * ['_from', '_to', '_cc', '_bcc', '_subject', '_body', '_attachment', '_config', 'is_test', 'CreatedAt']
	 * 
	 * _attachment & _config : JSON formatted string
	 * is_test : char(1) ['0'|'1']
	 */
	function mail_queue($email = [])
	{
		$ci = &get_instance();
		$email = is_array($email) ? (object)$email : $email; 

		$email->is_test = IS_LOCAL ? '1' : '0';
		$email->created_at = date('Y-m-d H:i:s');
		if (isset($email->_attachment)) {
			if (is_array($email->_attachment))
				$email->_attachment = json_encode($email->_attachment);
			elseif (is_object($email->_attachment))
				$email->_attachment = json_encode((array)$email->_attachment);
			else 
				$email->_attachment = json_encode([$email->_attachment]);
		}

		if (!$result = $ci->db->insert('mail_queue', $email))
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];
		
		return [TRUE, NULL];
	}

	/*
	 * Standard json output from me antho.firuze@gmail.com
	 * 
	 */
	function response($status = TRUE, $response = [], $statusHeader = FALSE, $exit = TRUE)
	{
		if ($statusHeader !== FALSE && !is_numeric($statusHeader)){
			header("HTTP/1.0 400");
			// === for Allow Cross Domain Webservice ===
			header('Access-Control-Allow-Origin: *');
			header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
			header("Access-Control-Allow-Headers: Origin");
			// === for Allow Cross Domain Webservice ===
			header('Content-Type: application/json');
			exit(json_encode(['status' => FALSE, 'message' => 'Status Header must be numeric']));
		}
		
		$BM =& load_class('Benchmark', 'core');
		$statusCode = $status  
										? 200 
										: $statusHeader ? $statusHeader : 200;
		
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');

		$output['status'] 		  = $status;
		$output['execution_time'] = $elapsed;
		// $output['environment'] = ENVIRONMENT;
		
		if (!$exit)
			return array_merge($output, $response);
			// return json_encode(array_merge($output, $response));
		
		header("HTTP/1.0 $statusCode");
		// === for Allow Cross Domain Webservice ===
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin");
		// === for Allow Cross Domain Webservice ===
		header('Content-Type: application/json');
		exit(json_encode(array_merge($output, $response)));
	}

	/*
	 * Bare json output from me antho.firuze@gmail.com
	 * 
	 */
	function bare_response($status = TRUE, $response = [], $statusHeader = FALSE, $exit = TRUE)
	{
		if ($statusHeader !== FALSE && !is_numeric($statusHeader)){
			header("HTTP/1.0 400");
			// === for Allow Cross Domain Webservice ===
			header('Access-Control-Allow-Origin: *');
			header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
			header("Access-Control-Allow-Headers: Origin");
			// === for Allow Cross Domain Webservice ===
			header('Content-Type: application/json');
			header('Accept-Ranges: bytes');
			exit(json_encode(['status' => FALSE, 'message' => 'Status Header must be numeric']));
		}
		
		$statusCode = $status ? 200 
								: $statusHeader ? $statusHeader 
								: 200;

		$output['status'] 				= $status;
		$output['environment'] 		= ENVIRONMENT;
		
		header("HTTP/1.0 $statusCode");
		// === for Allow Cross Domain Webservice ===
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin");
		// === for Allow Cross Domain Webservice ===
		header('Content-Type: application/json');
		header('Accept-Ranges: bytes');
		echo json_encode(array_merge($output, $response));
		if ($exit) 
			exit();
	}

	/*
	 * Standard debug output from me antho.firuze@gmail.com
	 * 
	 */
	function debug($data = '', $type = '')
	{
		header("HTTP/1.0 200");
		// === for Allow Cross Domain Webservice ===
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin");
		// === for Allow Cross Domain Webservice ===
		if ($type == '') {
			header('Content-Type: text/plain; charset=UTF-8');
			exit(print_r($data)); 
		} else if ($type == 'json') {
			header('Accept-Ranges: bytes');
			header('Content-Type: application/json');
			exit(json_encode($data)); 
		}
	}

	function print_query()
	{
		$ci = &get_instance();
		$qry = $ci->db->get();
		return [FALSE, ['message' => $ci->db->last_query()]];
	}
	
	function get_row()
	{
		$ci = &get_instance();
		if (!$qry = $ci->db->get())
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];

		if (!$row = $qry->row())
			return [FALSE, ['message' => 'Record not found']];
		
		return [TRUE, ['result' => $row]];
	}
	
	function get_result($request = NULL)
	{
		$ci = &get_instance();
		
		//exit($ci->db->get_compiled_select()); 
		if (!$qry = $ci->db->get())
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];
		
		if (IS_LOCAL) {
			$ci->load->helper('logger');
			logme('SELECT_QUERY', 'info', $ci->db->last_query());
		}

		if (!$result = $qry->result())
			return [FALSE, ['message' => 'Records not found']];
		 
		if (isset($request->memcache)) {
			if ($request->memcache)
				$ci->cache->save($request->method, $result, 60);
		}
			
		return [TRUE, ['cache' => false, 'result' => $result]];
	}
	
	function get_result_($request = NULL, $counter = FALSE)
	{
		$ci = &get_instance();

		if (isset($request->params->order) && !empty($request->params->order)) {
			$array = explode(",", $request->params->order);
			if (!empty($array)) {
				foreach ($array as $value) {
					$ci->db->order_by($value);
				}
			}
		}

		/* sample: &filter=field1=value1,field2=value2... */
		if (isset($request->params->filter) && !empty($request->params->filter)) {
			$array = explode(",", $request->params->filter);
			if (!empty($array)) {
				foreach ($array as $value) {
					// list($k, $v) = explode("=", $value);
					// $ci->db->where($k, empty($v)?0:$v);
					$ci->db->where($value, NULL, FALSE);
				}
			}
		}

		/* sample: &like=field1=value1,field2=value2... */
		if (isset($request->params->filter_like) && !empty($request->params->filter_like)) {
			$array = explode(",", $request->params->filter_like);
			if (!empty($array)) {
				foreach ($array as $value) {
					list($k, $v) = explode("=", $value);
					$ci->db->like($k, empty($v)?'':$v);
					// $ci->db->where($value, NULL, FALSE);
				}
			}
		}

		/* SQL Filter
		 * sample: &sfilter=is_import='1' and name like '%anonym%' */
		if (isset($request->params->sfilter) && !empty($request->params->sfilter)) {
			$ci->db->where($request->params->sfilter, NULL, FALSE);
		}

		/* For counting record number */
		if ($counter) {
			if (! $query = $ci->db->query($request))
				return FALSE;

			return ($query->num_rows() > 0) ? $query->num_rows() : 0;
		}

		$query_before_limit = $ci->db->get_compiled_select('', FALSE);
		// die($query_before_limit);

		// for avoid error: "Creating default object from empty value"
		if (! isset($request->params))
			$request->params = (object)[];					

		// LIMITATION FOR JQUERY DATATABLES COMPONENT & JQUERY JEASYUI COMPONENT
		/* sample: &limit=1&offset=0 */
		if (! isset($request->params->limit) && empty($request->params->limit))
			$request->params->limit = 10;

		if (! isset($request->params->length) && empty($request->params->length))
			$request->params->limit = 10;
		else
			$request->params->limit = $request->params->length;

		if (! isset($request->params->rows) && empty($request->params->rows))
			$request->params->limit = 10;
		else
			$request->params->limit = $request->params->rows;

		if (! isset($request->params->offset) && empty($request->params->offset))
			$request->params->offset = 0;
	
		if (! isset($request->params->start) && empty($request->params->start))
			$request->params->offset = 0;
		else
			$request->params->offset = $request->params->start;

		if (! isset($request->params->page) && empty($request->params->page))
			$request->params->offset = 0;
		else
			$request->params->offset = ($request->params->page - 1) * $request->params->limit;

		$ci->db->limit($request->params->limit, $request->params->offset);

		$query_after_limit = $ci->db->get_compiled_select('', FALSE);
		
		if (! $query = $ci->db->get() ) {
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];
		} 
		$result = $query->result();

		// die($ci->db->last_query());
		// echo F::get_result_($query_before_limit, TRUE);
		// die();
		
		$response['page'] = $request->params->offset + 1;
		$response['limit'] = $request->params->limit;
		$response['total'] = F::get_result_($query_before_limit, TRUE);
		$response['rows']  = $result;

		return [TRUE, ['cache' => false, 'result' => $response]];
	}
	
	function get_result_paging($request)
	{
		$ci = &get_instance();
		
		
		if (isset($request->params->limit) && !empty($request->params->limit)) {
			$page_no = 1;
			$limit 	= 10000;
			$limit = $request->params->limit;
			//if ($limit > 10000) $limit = 10000;
			if ($limit < 1) $limit = 10000;	
			if (isset($request->params->page_no) && !empty($request->params->page_no)) {
				$offset = 0;
				$page_no = $request->params->page_no;
				if ($page_no < 1) $page_no = 1;
				$offset = ($page_no - 1) * $limit;	
				$ci->db->limit($limit, $offset);
			} else {
				$ci->db->limit($limit);
			}	
		}
		
		if (!$qry = $ci->db->get())
			return [FALSE, ['message' => 'Database Error: '.$ci->db->error()['message']]];
		if (!$result = $qry->result())
			return [FALSE, ['message' => 'Records not found']];	 

		$request->log_size = mb_strlen(serialize($result), '8bit');

		$res['page_no'] = $page_no;
		$res['rows_size'] = $request->log_size;
		$res['rows_no'] = count($result);
		$res['rows'] = $result;

		return [TRUE, ['result' => $res]];
	}

	function get_result_yalinqo($request, $result)
	{
		$request->log_size = mb_strlen(serialize($result), '8bit');

		$res['page_no'] = 0;
		$res['rows_size'] = $request->log_size;
		$res['rows_no'] = count($result);
		$res['rows'] = $result;

		return [TRUE, ['result' => $res]];
	}

	/*
	 * For setting URL Address
	 * 
	 * backend: 	http://localhost:8080/backend?lang=id&state=auth&page=login&token=845j2h5lkj24352kjnb3545
	 * frontend: 	http://localhost:8080/frontend?lang=id&page=home
	 * 
	 */
	function setURL($path, $lang, $state = null, $page, $token = null)
	{
		if (!in_array($path, ['backend','frontend']))
			return '';
		
		if ($path == 'backend')
			
			return BASE_URL.$path
				.'?lang='.$lang
				.'&state='.$state
				.'&page='.$page
				.(isset($token) ? '&token='.urlencode($token) : '');
		else if ($path == 'frontend')
			
			return BASE_URL.$path
				.'?lang='.$lang
				.'&page='.$page;
	}
	
	/**
	 * getValue
	 *
	 * Function get value from table with object
	 *
	 * @param	string	$sel_field   DB field select
	 * @param	string	$table    DB table
	 * @param	string	$where_field   where field or where condition Ex. "field = '1' AND field2 = '2'"
	 * @param	string	$where_val   value of wherer (If $where_field has condition. Please null)
	 * @param	string	$orderby   Order by field or NULL 
	 * @param	string	$sort   asc or desc or NULL 
	 * @param	string	$groupby   Group by field or NULL 
	 * @return	Object or FALSE
	 */
	function getValue($sel_field = '*', $table, $where_field, $where_val, $limit = 0, $orderby = '', $sort = '', $groupby = '') {
		$ci = &get_instance();
		$ci->db->select($sel_field);
		if($where_field || $where_val){
			if (is_array($where_field) && is_array($where_val)) {
				for ($i = 0; $i < count($where_field); $i++) {
					$ci->db->where($where_field[$i], $where_val[$i]);
				}
			} else {
				$ci->db->where($where_field, $where_val);
			}
		}
		if ($groupby) {
			$ci->db->group_by($groupby);
		}
		if ($orderby && $sort) {
			$ci->db->order_by($orderby, $sort);
		}
		if ($limit) {
			$ci->db->limit($limit, 0);
		}
		$query = $ci->db->get($table);
		if (!empty($query)) {
			if ($query->num_rows() !== 0) {
				if ($query->num_rows() === 1) {
					$row = $query->row();
				} else {
					$row = $query->result();
				}
				return $row;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Is Memcached available? or service is running?
	 *
	 * @return bool
	 */
	function is_memcache_ok($request)
	{
		$ci = &get_instance();
		$ci->load->driver('cache', ['adapter' => 'memcached']);

		set_error_handler(function($errno, $errstr) { 
			if ($errno == 8)
				throw new ErrorException('Memcached Server not available');
			else
				throw new ErrorException($errstr);
		});

		try {
			if (!$ci->cache->memcached->is_supported())
				throw new ErrorException('Memcached not supported');

		}	catch (ErrorException $e) {
			$request->memcache = FALSE;
			return [FALSE, ['message' => $e->getMessage()]];
		} 

		try {
			$ci->cache->memcached->cache_info();
			restore_error_handler();

		}	catch (ErrorException $e) {
			$request->memcache = FALSE;
			return [FALSE, ['message' => $e->getMessage()]];
		} 

		$request->memcache = TRUE;
		return [TRUE, NULL];
	}

	/**
	 * Getting report file
	 *
	 * @param array $request	
	 * @param array $data
	 * @param array $config		array('output' => TRUE);
	 * @return void
	 */
	function get_report($request, $data, $config)
	{
		// if (!$data)
		// 	return [FALSE, ['message' => 'Records not found']];

		if (!isset($config['name']))
			return [FALSE, ['message' => F::_err_msg('err_param_required', 'name')]];
		
		$config['output'] = TRUE;
		$config['urlPath'] = REPOS_URL.$request->simpi_id.SEPARATOR.'reports'.SEPARATOR;
		$config['absPath'] = REPOS_DIR.$request->simpi_id.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR;

		$reportFilePath = $config['absPath'].$config['name'].'.php';
		if(!file_exists($reportFilePath))
			return [FALSE, ['message' => F::_err_msg('err_report_unknown', $config['name'])]];

		require $reportFilePath;

		$class = "\\{$config['name']}\F_Report";
		$report = new $class();
		list($success, $return) = $report->create($data, $config);
		if (!$success) return [FALSE, $return];

		return [TRUE, $return];
	}

	/**
	 * Batch Insert Records with transaction fail rollback
	 *
	 * @param array|object $datas			format: ['table_name' => ['field' => 'value', 'field' => 'value',,,,]]
	 * @return void
	 */
	function batch_insert($datas)
	{
		$ci = &get_instance();
		$ci->db->trans_strict(TRUE);
		$ci->db->trans_start();

		if (IS_LOCAL) $ci->load->helper('logger');

		foreach($datas as $table => $data) {
			$ci->db->insert($table, $data);

			if (IS_LOCAL)	logme('INSERT_QUERY', 'info', $ci->db->last_query());
		}

		$ci->db->trans_complete();
		if ($ci->db->trans_status() === FALSE)
		{
			if (IS_LOCAL)	logme('ERROR_QUERY', 'info', 'Database Error: '.$ci->db->error()['message']);
			return [FALSE, ['message' => F::_err_msg('err_commit_data')]];
		}

		return [TRUE, NULL];
	}

	/**
	 * Batch Update Records with transaction fail rollback
	 *
	 * @param array|object $datas			format: ['table_name' => [['field' => 'value', 'field' => 'value',,,,],['cond1' => 'cond1']]]
	 * @return void
	 */
	function batch_update($datas)
	{
		$ci = &get_instance();
		$ci->db->trans_strict(TRUE);
		$ci->db->trans_start();

		if (IS_LOCAL) $ci->load->helper('logger');

		foreach($datas as $table => $data) {
			$ci->db->update($table, $data[0], $data[1]);

			if (IS_LOCAL)	logme('UPDATE_QUERY', 'info', $ci->db->last_query());
		}

		$ci->db->trans_complete();
		if ($ci->db->trans_status() === FALSE)
		{
			if (IS_LOCAL)	logme('ERROR_QUERY', 'info', 'Database Error: '.$ci->db->error()['message']);
			return [FALSE, ['message' => F::_err_msg('err_commit_data')]];
		}

		return [TRUE, NULL];
	}

	/**
	 * Batch Delete Records with transaction fail rollback
	 *
	 * @param array|object $datas			format: ['table_name' => ['field' => 'value', 'field' => 'value',,,,]]
	 * @return void
	 */
	function batch_delete($datas)
	{
		$ci = &get_instance();
		$ci->db->trans_strict(TRUE);
		$ci->db->trans_start();

		if (IS_LOCAL) $ci->load->helper('logger');

		foreach($datas as $table => $condition) {
			$ci->db->delete($table, $condition);

			if (IS_LOCAL)	logme('DELETE_QUERY', 'info', $ci->db->last_query());
		}

		$ci->db->trans_complete();
		if ($ci->db->trans_status() === FALSE)
		{
			if (IS_LOCAL)	logme('ERROR_QUERY', 'info', 'Database Error: '.$ci->db->error()['message']);
			return [FALSE, ['message' => F::_err_msg('err_commit_data')]];
		}

		return [TRUE, NULL];
	}

}
// NOTE: 
// The Magic Script
// $a1 = ['satu' => 1, 'dua' => 2, 'lima' => 5];
// $a2 = ['satu', 'lima', 'tiga'];
// $a3 = array_intersect_key((array)$a1, array_flip($a2));
// result: 
// 		['satu' => 1, 'lima' => 5]

// Another Magic Script
// isset($z) OR $z = 'def';

/*
		list($success, $return) = $this->f->is_valid_license($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->f->is_valid_token_v2($request);
		if (!$success) return [FALSE, $return];

select * from religion where ReligionCode like '%investasi' and 1 = sleep(2);--
select * from religion where ReligionCode like '%m' union (select 1,2 from dual);--
select * from religion where ReligionCode like '%m' union (select TABLE_NAME, TABLE_SCHEMA from information_schema.tables);--
select * from religion where ReligionCode like '%m' union (select TABLE_NAME, TABLE_SCHEMA from information_schema.tables where LOWER(TABLE_NAME) like '%user%');--
select * from religion where ReligionCode like '%m' union (select COLUMN_NAME, 2 from information_schema.columns where LOWER(TABLE_NAME) like '%simpi_user%');--
select * from religion where ReligionCode like '%m' union (select UserLogin, UserPassword from simpi_user);--

$row = $this->db->select('*')->from('table_name')->where('field', 'value')->get()->row();
$result = $this->db->select('*')->from('table_name')->where('field', 'value')->get()->result();

$str = "select * from table_name where field = ?"
$qry = $this->db->query($str, ['value']);


die($this->db->get_compiled_select());

*/