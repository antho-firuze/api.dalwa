<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Download_model extends CI_Model
{
	function __construct(){
		parent::__construct();
	}
	

	function documents($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->formulir) || empty($request->params->formulir))
      return [FALSE, ['message' => $this->f->lang('err_param_required', 'formulir')]];

		if (!isset($request->params->kind) || empty($request->params->kind))
      $request->params->kind = 'print';

    list($success, $return) = $this->f->get_report($request, NULL, ['name' => $request->params->formulir]);
		if (!$success) return [FALSE, $return];
  
    if ($request->params->kind == 'print')
      return [TRUE, ['result' => $return]];
    
    $this->load->library('simpi');
    $this->simpi->get_client_info($request);
    $this->simpi->get_simpi_info($request);
      
    $email = [
      '_to' 			=> $request->client_info->CorrespondenceEmail,
      '_subject' 	=> $this->f->lang('email_subject_download_formulir', $return['title']),
      '_body'			=> $this->f->lang('email_body_download_formulir', [
        'name' 			    => $request->client_info->TitleFirst ? $request->client_info->TitleFirst.' '.$request->client_info->full_name : $request->client_info->full_name, 
				'simpiName' 		=> $request->simpi_info ? $request->simpi_info->simpiName : '@MI',
				'powered_by' 		=> 'Powered by PT. SIMPIPRO INDONESIA @2018',
      ]),
      '_attachment'	=> [$return['path']],
    ];
    list($success, $message) = $this->f->mail_queue($email);
    if (!$success) return [FALSE, $message];

    return [TRUE, ['message' => $this->f->lang('success_email_formulir')]];
  }

}