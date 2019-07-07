<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Email_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('simpi_gateway');
	}

	/**
	 * return date of beginning year from input position date
	 */
	function queue($request)
	{
		list($success, $return) = $this->f->is_valid_licensekey($request);
		if (!$success) return [FALSE, $return];
		
		$this->load->library('simpi');
		list($success, $return) = $this->simpi->check_valid_sender_email($request);
		if (!$success) return [FALSE, $return];

		if (! isset($request->params->to) || empty($request->params->to))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'to')]];

		if (! isset($request->params->subject) || empty($request->params->subject))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'subject')]];

		if (! isset($request->params->body) || empty($request->params->body))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'body')]];

		if (isset($request->params->attachment) && !empty($request->params->attachment)) {
			if (! is_array($request->params->attachment)) 
				$request->params->attachment = json_encode((array) $request->params->attachment);
			else
				$request->params->attachment = json_encode($request->params->attachment);
		}

		$datas = [
			'mail_queue' => [
				'senderID' => $request->params->senderID, 
				'_to' => $request->params->to, 
				'_cc' => isset($request->params->cc) && !empty($request->params->cc) ? $request->params->cc : NULL, 
				'_bcc' => isset($request->params->bcc) && !empty($request->params->bcc) ? $request->params->bcc : NULL, 
				'_subject' => $request->params->subject, 
				'_body' => $request->params->body, 
				'_attachment' => isset($request->params->attachment) ? $request->params->attachment : NULL, 
				'CreatedAt' => date('Y-m-d H:i:s')
			],
		];
		list($success, $return) = $this->f->batch_insert($datas);
		if (!$success) return [FALSE, $return];

		return [TRUE, ['message' => $this->f->lang('info_email_sent')]];
	}

	/**
	 * Upload file for attachment email, can be upload multiple file at once
	 *
	 * @param [type] $request
	 * @return void
	 */
	function upload($request)
	{
		list($success, $return) = $this->f->is_valid_licensekey($request);
		if (!$success) return [FALSE, $return];
		
		// exit(var_dump($_FILES));

		$upload_url = BASE_URL.'__tmp'.SEPARATOR;
		$upload_path = FCPATH.'__tmp'.DIRECTORY_SEPARATOR;
		is_dir($upload_path) OR mkdir($upload_path, 0777, true);

		$this->load->helper('myencrypt');

		function upload_recursive($request, $file, &$result = []) {
			$ci = &get_instance();

			if (! $ci->upload->do_upload($file)) {
				$error = $ci->upload->display_errors();
				
				$result[] = ['file_name' => $_FILES[$file]['name'], 'uploaded' => false, 'message' => $error];
			} else {
				$data = (object) $ci->upload->data();
	
				$datas = [
					'mail_attachment' => [
						'attachment_id' => $_FILES[$file]['uuid'], 'simpiID' => $request->simpiID, 'file_name' => $_FILES[$file]["name"], 'file_size' => $data->file_size, 'file_type' => $data->file_type, 'file_ext' => $data->file_ext, 'upload_date' => date('Y-m-d H:i:s')
					],
				];
				list($success, $return) = $ci->f->batch_insert($datas);
				if (!$success) 
					$result[] = ['file_name' => $_FILES[$file]['name'], 'uploaded' => false, 'message' => $return];
				else
					$result[] = ['file_name' => $_FILES[$file]["name"], 'uploaded' => true, 'attachment_id' => $_FILES[$file]['uuid']];
			}
		}

		$file = 'userfile';
		$allowed_types = 'gif|jpg|png|pdf';
		$max_size = 5120;
		$config = ['upload_path' => $upload_path, 'allowed_types' => $allowed_types, 'overwrite' => 1, 'max_size' => $max_size];
		$this->load->library('upload', $config);
		if (is_array($_FILES[$file]['name'])) {
			foreach($_FILES[$file]['name'] as $key => $val) {
				$uuid = UUIDv4();
				$config['file_name'] = $uuid;
				$this->upload->initialize($config);
				$_FILES['file']['uuid'] 		= $uuid;
				$_FILES['file']['name'] 		= $_FILES[$file]['name'][$key];
				$_FILES['file']['type'] 		= $_FILES[$file]['type'][$key];
				$_FILES['file']['tmp_name'] = $_FILES[$file]['tmp_name'][$key];
				$_FILES['file']['error'] 		= $_FILES[$file]['error'][$key];
				$_FILES['file']['size'] 		= $_FILES[$file]['size'][$key];
				upload_recursive($request, 'file', $result);
			}
		} else {
			$uuid = UUIDv4();
			$config['file_name'] = $uuid;
			$this->upload->initialize($config);
			$_FILES[$file]['uuid'] = $uuid;
			upload_recursive($request, $file, $result);
		}

		return [TRUE, ['result' => $result]];
	}

	/**
	 * Delete attachment email
	 *
	 * @param [type] $request
	 * @return void
	 */
	function delete($request)
	{
		list($success, $return) = $this->f->is_valid_licensekey($request);
		if (!$success) return [FALSE, $return];
		
		if (! isset($request->params->attachment_id) || empty($request->params->attachment_id))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'attachment_id')]];

		function delete_recursive($attachment_id, &$result = []) {
			if (is_array($attachment_id)) {
				foreach($attachment_id as $v)
					delete_recursive($v, $result);

				return;
			} 

			$ci = &get_instance();
			$upload_path = FCPATH.'__tmp'.DIRECTORY_SEPARATOR;
			if ($row = $ci->db->get_where('mail_attachment', ['attachment_id' => $attachment_id], 1)->row()) {
				if ($row->is_sent) {
					$result[] = ['attachment_id' => $attachment_id, 'deleted' => false, 'message' => 'This attachment cannot be delete'];
					return;
				} else {
					@unlink($upload_path.$attachment_id.$row->file_ext);
				}
			} else {
				$result[] = ['attachment_id' => $attachment_id, 'deleted' => false, 'message' => $ci->f->lang('err_attachment_not_exist')];
				return;
			}

			$datas = ['mail_attachment' => ['attachment_id' => $attachment_id]];
			list($success, $return) = $ci->f->batch_delete($datas);
			if (!$success) 
				$result[] = ['attachment_id' => $attachment_id, 'deleted' => false, 'message' => $return];
			else
				$result[] = ['attachment_id' => $attachment_id, 'deleted' => true];

			return;
		}

		delete_recursive($request->params->attachment_id, $result);

		return [TRUE, ['result' => $result]];

		// $upload_path = FCPATH.'__tmp'.DIRECTORY_SEPARATOR;
		// if ($row = $this->db->get_where('mail_attachment', ['attachment_id' => $request->params->attachment_id], 1)->row()) {
		// 	if ($row->is_sent)
		// 		return [FALSE, ['message' => 'This attachment cannot be delete']];

		// 	@unlink($upload_path.$request->params->attachment_id.$row->file_ext);
		// } else {
		// 	return [FALSE, ['message' => $this->f->lang('err_attachment_not_exist')]];
		// }

		// $datas = [
		// 	'mail_attachment' => ['attachment_id' => $request->params->attachment_id],
		// ];
		// list($success, $return) = $this->f->batch_delete($datas);
		// if (!$success) return [FALSE, $return];

		// return [TRUE, ['message' => $this->f->lang('success_delete')]];
	}

}
