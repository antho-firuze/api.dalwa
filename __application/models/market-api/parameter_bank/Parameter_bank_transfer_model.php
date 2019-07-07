<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_bank_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}

    function search($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TransferTypeID, TransferTypeCode, TransferTypeDescription')	;
    	$this->db->from('parameter_bank_transfertype');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
    }

    function load($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->TransferTypeID) || empty($request->params->TransferTypeID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter TransferTypeID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		} elseif (!isset($request->params->TransferTypeCode) || empty($request->params->TransferTypeCode)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter TransferTypeCode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter bank transfer');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];
        }
		$this->db->select('TransferTypeID, TransferTypeCode, TransferTypeDescription')	;
        $this->db->from('parameter_bank_transfertype');
		if (!isset($request->params->TransferTypeID) || empty($request->params->TransferTypeID)) {
            $this->db->where('TransferTypeID', $request->params->TransferTypeID);
		} elseif (!isset($request->params->TransferTypeCode) || empty($request->params->TransferTypeCode)) {
            $this->db->where('TransferTypeCode', $request->params->TransferTypeCode);
        }    
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
    }


}    