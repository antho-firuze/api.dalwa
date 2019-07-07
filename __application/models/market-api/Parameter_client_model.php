<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_client_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}
	
	function business_activity($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('ActivityID, ActivityCode, ActivityDescription');
  	 	$this->db->from('parameter_client_businessactivity');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		   $this->system->save_billing($request);

		return $data;
  }
 
	function business_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TypeID, TypeCode, TypeDescription');
 	  	$this->db->from('parameter_client_businesstype');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		   $this->system->save_billing($request);

		return $data;
  }

	function client_risklevel($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('RiskID, RiskCode, RiskDescription');
 	  	$this->db->from('parameter_client_risklevel');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		   $this->system->save_billing($request);

		return $data;
  }

	function client_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('StatusID, StatusCode, StatusDescription');
 	  	$this->db->from('parameter_client_status');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		   $this->system->save_billing($request);

		return $data;
  }

	function client_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TypeID, TypeCode, TypeDescription');
  	 	$this->db->from('parameter_client_type');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		   $this->system->save_billing($request);

		return $data;
  }

	function education_level($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('LevelID, LevelCode, LevelDescription');
 	  	$this->db->from('parameter_client_educationlevel');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		   $this->system->save_billing($request);

		return $data;
  }

	function education_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TypeID, TypeCode, TypeDescription');
  	 	$this->db->from('parameter_client_educationtype');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
	   		$this->system->save_billing($request);

		return $data;
  }

	function idcard_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('TypeID, TypeCode, TypeDescription');
 	  	$this->db->from('parameter_client_idcardtype');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
   		$this->system->save_billing($request);

		return $data;
  	}

  	function document_type($request)
  	{
	  //cek akses: by 4 method
	  list($success, $return) = $this->system->is_valid_access4($request);
	  if (!$success) return [FALSE, $return];

	  $this->db->select('TypeID, TypeCode, TypeDescription');
		 $this->db->from('parameter_client_documenttype');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		 $this->system->save_billing($request);

	  return $data;
	}

	function client_kyc($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('kycID, kycCode, kycDescription');
  	 	$this->db->from('parameter_client_kyc');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}

	function client_kyc_answer($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('kycID, AnswerID, AnswerText');
  	 	$this->db->from('parameter_client_kyc_answer');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
		
	function marital_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('StatusID, StatusCode, StatusDescription');
   		$this->db->from('parameter_client_maritalstatus');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	  
		$this->system->save_billing($request);

		return $data;
  }
	
	function occupation($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('OccupationID, OccupationCode, OccupationDescription');
 	  	$this->db->from('parameter_client_occupation');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }
	
	function religion($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('ReligionID, ReligionCode, ReligionDescription');
 	  	$this->db->from('parameter_client_religion');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }
	
	function income_level($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AnswerID, AnswerText');
   		$this->db->from('parameter_client_kyc_answer');
  	 	$this->db->where('kycID = 3');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
	function investment_objective($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AnswerID, AnswerText');
  	 	$this->db->from('parameter_client_kyc_answer');
  	 	$this->db->where('kycID = 1');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function source_of_fund($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AnswerID, AnswerText');
  	 	$this->db->from('parameter_client_kyc_answer');
  	 	$this->db->where('kycID = 15');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function asset_owner($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AnswerID, AnswerText');
   		$this->db->from('parameter_client_kyc_answer');
   		$this->db->where('kycID = 44');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function fatca_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('AnswerID, AnswerText');
   		$this->db->from('parameter_client_kyc_answer');
   		$this->db->where('kycID = 46');
		   $data = $this->f->get_result_paging($request);

		   $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

}
