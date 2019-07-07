<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Prospect_model extends CI_Model
{
    function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MASTER);
		$this->load->library('simpi');
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
	 * require 	token,email,password
	 * params agent, token
	 * return @error 		array(status = FALSE, message = 'Token not found, or your account has already activate !')
	 * return @success 	array(status = TRUE, message = 'Thank you. Now your account has been activate !')
	 * @param json_object $request
	 * @return void
	*/ 
	/* function activation($request)
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
	} */
   

	function risk_value($request)
	{
		if (isset($request->appcode)) {
			list($success, $return) = $this->f->is_valid_appcode($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->license_key)) {
			list($success, $return) = $this->f->is_valid_license($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->session_id)) {
			list($success, $return) = $this->f->is_valid_session($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->token_id)) {
			list($success, $return) = $this->f->is_valid_token($request);
			if (!$success) return [FALSE, $return];
		}
		else {
      $qry = this->f->err_message('00-01', 1);
			return [FALSE, ['message' => $qry->message]]; 
    }
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('RiskID, RiskCode, MaximumValue');

		$str = '(select a.RiskID, b.RiskCode, a.MaximumValue 
					   from sales_risklevel a join parameter_client_risklevel b on a.RiskID = b.RiskID 
						 where a.simpiID = ?) g0';
		$table = $this->f->compile_qry($str, [$request->simpi_id]);
		$this->db->from($table);
		return $this->f->get_result($request);
	}
	
	/** 
	 * list parameter risk score level
	 */	  
	function risk_score_level($request)
	{
		if (isset($request->appcode)) {
			list($success, $return) = $this->f->is_valid_appcode($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->license_key)) {
			list($success, $return) = $this->f->is_valid_license($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->session_id)) {
			list($success, $return) = $this->f->is_valid_session($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->token_id)) {
			list($success, $return) = $this->f->is_valid_token($request);
			if (!$success) return [FALSE, $return];
		}
		else {
      $qry = this->f->err_message('00-01', 1);
			return [FALSE, ['message' => $qry->message]]; 
    }
		
		if (!isset($request->params->RiskScore) || empty($request->params->RiskScore))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'RiskScore')]];
		
		$str = 'select a.RiskID, b.RiskCode 
				    from sales_risklevel a join parameter_client_risklevel b on a.RiskID = b.RiskID 
				   	where a.simpiID = ? and a.MaximumValue <= ? order by a.MaximumValue desc';
		$qry = $this->db->query($str, [$request->simpiID, $request->params->RiskScore]);
		if ($qry->num_rows() < 1) {
			$qry->free_result();

			$str = 'select a.RiskID, b.RiskCode 
			        from sales_risklevel a join parameter_client_risklevel b on a.RiskID = b.RiskID 
			        where a.simpiID = ? order by a.MaximumValue';
			$qry = $this->db->query($str, [$request->simpiID]);
			if ($qry->num_rows() < 1)  return [FALSE, ['message' => 'Records not found']]; 
		}

		return [TRUE, ['result' => $qry->row()]];
	}
 
	function risk_questioner($request)
	{
		if (isset($request->appcode)) {
			list($success, $return) = $this->f->is_valid_appcode($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->license_key)) {
			list($success, $return) = $this->f->is_valid_license($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->session_id)) {
			list($success, $return) = $this->f->is_valid_session($request);
			if (!$success) return [FALSE, $return];
		}
		elseif (isset($request->token_id)) {
			list($success, $return) = $this->f->is_valid_token($request);
			if (!$success) return [FALSE, $return];
		}
		else {
      $qry = this->f->err_message('00-01', 1);
			return [FALSE, ['message' => $qry->message]]; 
    }
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		else
			$this->db->select('TypeID, TypeCode, QuestionNo, QuestionText');

		$str = '(select a.TypeID, a.TypeCode, b.QuestionNo, b.QuestionText  
				     from parameter_client_type AS a   
				     inner join sales_risklevel_questioner AS b on b.TypeID = a.TypeID  
				     where b.simpiID = ?) g0';
		$table = $this->f->compile_qry($str, [$request->simpi_id]);
		$this->db->from($table);
		$result = $this->db->get()->result();
		foreach($result as $key => $val) {
			$str2 = '';
			$result2 = $this->db
				->select('OptionNo, OptionText, OptionValue ')
				->where(['simpiID' => $request->simpi_id, 
					       'QuestionNo' => $val->QuestionNo,
					       'TypeID' => $val->TypeID])
				->get('sales_risklevel_answer')
				->result();
			$val->Options = $result2;
		}
		
		return [TRUE, ['result' => $result]];
	}


	function get_profile($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$row = $this->db->get_where('mobc_prospect', ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])->row())
			return [FALSE, ['message' => 'Records not found']];

		$row->email = $row->CorrespondenceEmail;
		$row->full_name = ($row->NameFirst ? $row->NameFirst : '').($row->NameMiddle ? ' '.$row->NameMiddle : '').($row->NameLast ? ' '.$row->NameLast : '');
		
		$mfields = [
			'NameFirst','NationalityID','BirthDate','BirthPlace','IDCardNo','Gender','LevelID','ReligionID','OccupationID','IncomeLevel','MaritalStatusID',
			'InvestmentObjective','SourceofFund','AssetOwner','KTPAddress','KTPCityCode','CorrespondenceAddress','CorrespondenceCityCode','CorrespondencePhone',
			'CorrespondenceEmail','AccountNotes','BankName','BankCountryID','AccountCcyID','AccountNo','AccountName',
		];
		$ofields = [
			'NameMiddle','NameLast','IDCardExpired','TaxID','CountryOfBirth','MothersMaidenName','SpouseName','RiskID','RiskScore','KTPPostalCode',
			'CorrespondenceCountryID','CorrespondenceProvince','CorrespondencePostalCode','DomicileAddress','DomicileCityCode','DomicilePostalCode',
			'DomicileCountry','FATCAStatus','TIN','TINCountry','BankBranch',
		];

		$t = array_intersect_key((array)$row, array_flip($mfields));
		$this->load->helper('myarray');
		$percent_populate = count(remove_empty_array($t)) / count($mfields)  * 100;

		return [TRUE, ['result' => $row, 'profile_status' => $percent_populate]];
	}

	function set_profile($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$fields = $this->db->list_fields('mobc_prospect');
		$data = array_intersect_key((array)$request->params, array_flip($fields)); // The Magic Script
		if ($data) {
			if (!$return = $this->db->update('mobc_prospect', $data, ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])) 
				return [FALSE, ['message' => $this->db->error()['message']]];
			
			$this->db->query("update mobc_prospect set IDCardExpired = '9998-12-31' where simpiID = ? and emailID = ? and IDCardExpired is null", [$request->simpiID, $request->emailID]);
			$this->db->query("update mobc_prospect set LF = (case when NationalityID = (select CountryID from master_simpi where simpiID = ?) then 'L' else 'F' end) where simpiID = ? and emailID = ? and LF is null", [$request->simpiID, $request->simpiID, $request->emailID]);
		}

		return [TRUE, ['message' => $this->f->lang('success_update')]];
	}

	function pdf_print($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$str = 'select t0.*, t1.ReligionCode, t2.OccupationCode   
		from mobc_prospect t0 
		left join parameter_client_religion t1 on t0.ReligionID = t1.ReligionID
		left join parameter_client_occupation t2 on t0.OccupationID = t2.OccupationID 
		where t0.simpiID = ? and t0.emailID = ?';
		if (!$row = $this->db->query($str, [$request->simpiID, $request->emailID])->row())
			return [FALSE, ['message' => 'Records not found']];

		$row->email = $row->CorrespondenceEmail;
		$row->full_name = ($row->NameFirst ? $row->NameFirst : '').($row->NameMiddle ? ' '.$row->NameMiddle : '').($row->NameLast ? ' '.$row->NameLast : '');

		list($success, $return) = $this->f->get_report($request, $row, ['name' => 'formulir_opening_account']);
		if (!$success) return [FALSE, $return];
		
		$result[] = $return;

		list($success, $return) = $this->f->get_report($request, $row, ['name' => 'formulir_risk_profile']);
		if (!$success) return [FALSE, $return];
	
		$result[] = $return;

		return [TRUE, ['result' => $result]];
	}

	function pdf_email($request) 
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		$str = 'select t0.*, t1.ReligionCode, t2.OccupationCode   
		from mobc_prospect t0 
		left join parameter_client_religion t1 on t0.ReligionID = t1.ReligionID
		left join parameter_client_occupation t2 on t0.OccupationID = t2.OccupationID 
		where t0.simpiID = ? and t0.emailID = ?';
		if (!$row = $this->db->query($str, [$request->simpiID, $request->emailID])->row())
			return [FALSE, ['message' => 'Records not found']];

		$row->email = $row->CorrespondenceEmail;
		$row->full_name = ($row->NameFirst ? $row->NameFirst : '').($row->NameMiddle ? ' '.$row->NameMiddle : '').($row->NameLast ? ' '.$row->NameLast : '');

		list($success, $return) = $this->f->get_report($request, $row, ['name' => 'formulir_opening_account']);
		if (!$success) return [FALSE, $return];
		
		$result[] = $return['path'];

		list($success, $return) = $this->f->get_report($request, $row, ['name' => 'formulir_risk_profile']);
		if (!$success) return [FALSE, $return];
	
		$result[] = $return['path'];

		$email = [
			'_to' 			=> $row->CorrespondenceEmail,
			'_subject' 	=> $this->f->lang('email_subject_opening_account'),
			'_body'			=> $this->f->lang('email_body_opening_account', [
				'name' 			=> $row->TitleFirst ? $row->TitleFirst.' '.$row->full_name : $row->full_name, 
			]),
			'_attachment'	=> $result,
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('success_email_report')]];
	}

}