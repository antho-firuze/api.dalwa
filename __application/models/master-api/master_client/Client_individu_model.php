<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Client_individu_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->library('System');
		$this->load->library('Client');
	}

	private function _sql($request)
	{
		if (isset($request->params->fields) && !empty($request->params->fields)) 
		   $this->db->select($request->params->fields);
		else
			$this->db->select('T1.simpiID, T1.ClientID, T1.ClientCode, T1.SID, T1.IFUA, T1.ReferralCode, T1.ClientName, 
					 T1.SalesID, T2.SalesCode, T2.TreePrefix, T2.SInvestCode, T1.TypeID, T1.CcyID, T1.StatusID, 
					 T1.CorrespondencePhone, T1.CorrespondenceEmail, T1.CorrespondenceAddress, T1.CorrespondenceCity, 
					 T1.CorrespondenceCityCode, T1.RiskID, T1.XRateID, T1.LF, T1.CorrespondenceProvince, 
					 T1.CorrespondenceCountryID, T1.CorrespondencePostalCode, T1.LastUpdate, T1.CreatedAt, T1.IsUpdate, 
					 T3.NameFirst, T3.NameMiddle, T3.NameLast, T3.TitleFirst, T3.TitleLast, T3.BirthPlace, T3.BirthDate, 
        			 T3.MMN, T3.TaxID, T3.Gender, T3.NationalityID, T3.LevelID, T3.ReligionID, T3.OccupationID, T3.OfficeName, 
        			 T3.OfficeAddress, T3.OfficePhone, T3.OfficeBusinessActivityID, T3.MaritalStatusID, T3.SpouseName, 
        			 T3.SpouseBirthDate, T3.IDCardTypeID, T3.IDCardNo, T3.IDCardIssuer, T3.IDCardIsExpired, T3.IDCardExpired');  
		$this->db->from('master_client T1');
		$this->db->join('master_sales T2', 'T1.simpiID = T2.simpiID And T1.SalesID = T2.SalesID');  
		$this->db->join('master_client_individu T3', 'T1.simpiID = T2.simpiID And T1.ClientID = T3.ClientID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->StatusID) && !empty($request->params->StatusID)) $this->db->where('T1.StatusID', $request->params->StatusID);
		if (isset($request->params->RiskID) && !empty($request->params->RiskID)) $this->db->where('T1.RiskID', $request->params->RiskID);
		if (isset($request->params->LF) && !empty($request->params->LF)) $this->db->where('T1.LF', $request->params->LF);
		if (isset($request->params->client_keyword) && !empty($request->params->client_keyword)) $this->db->like('T1.ClientName', $request->params->client_keyword);
	}
	
	private function _access($request)
	{
		if ($request->log_access == 'license') {
			return [TRUE, NULL];
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
			return [TRUE, NULL];
		} elseif ($request->log_access == 'session') {
			$this->db->like('T2.TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$this->db->where('T1.SID', $request->SID);
			return [TRUE, NULL];
		} elseif ($request->log_access == 'apps') {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
	}

	function load($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			$this->db->where('T1.ClientID', $request->params->ClientID);
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			$this->db->where('T1.ClientCode', $request->params->ClientCode);
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			$this->db->where('T1.SID', $request->params->SID);
			$this->db->where('T1.TypeID = 1');
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			$this->db->where('T1.IFUA', $request->params->IFUA);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function search($request)
	{
		//cek akses:  
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			if (is_array($request->params->ClientID)) {
				$this->db->where_in('T1.ClientID', $request->params->ClientID);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ClientID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			if (is_array($request->params->ClientCode)) {
				$this->db->where_in('T1.ClientCode', $request->params->ClientCode);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ClientCode');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			if (is_array($request->params->SID)) {
				$this->db->where_in('T1.SID', $request->params->SID);
				$this->db->where('T1.TypeID = 1');
			} else {
				$this->db->where('T1.SID', $request->params->SID);
				$this->db->where('(T1.TypeID = 3 or T1.TypeID = 5)');
			}
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			if (is_array($request->params->IFUA)) {
				$this->db->where_in('T1.IFUA', $request->params->IFUA);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter IFUA');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
		} else {
			if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
				if (is_array($request->params->SalesID)) 
					$this->db->where_in('T1.SalesID', $request->params->SalesID);
		 		else 
					$this->db->where('T1.SalesID', $request->params->SalesID);
			}
			$this->_keyword($request);
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	} 
 
	function team_direct($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->SalesID) || empty($request->params->SalesID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.SalesID', $request->params->SalesID);
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;				
	}

	function team_head($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->TreeParentID) && !empty($request->params->TreeParentID))
			$this->db->where('T2.TreeParentID', $request->params->TreeParentID);
		else 
			$this->db->where('T2.TreeParentID = 0');		
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function team_member($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->like('T2.TreePrefix', $request->params->TreePrefix, 'after');
			if (strtolower($request->params->option_without) == 'y') 
				$this->db->where('T2.TreePrefix != ', $request->params->TreePrefix);			
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'TreePrefix');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}			 		
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function client_id($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ClientCode) || empty($request->params->ClientCode)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ClientCode');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.ClientCode', $request->params->ClientCode);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ClientCode);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['ClientID' => $row->ClientID]]];
	}

	function client_code($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ClientID) || empty($request->params->ClientID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ClientID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.ClientID', $request->params->ClientID);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ClientID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['ClientCode' => $row->ClientCode]]];
	}

	function client_ifua($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ClientID) || empty($request->params->ClientID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ClientID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.ClientID', $request->params->ClientID);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ClientID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['IFUA' => $row->IFUA]]];
	}

	function client_sid($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->ClientID) || empty($request->params->ClientID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter ClientID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.ClientID', $request->params->ClientID);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->ClientID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SID' => $row->SID]]];
	}


	// function new($request)
	// {
	// 	list($success, $return) = $this->f->is_valid_appcode($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	list($success, $return) = $this->simpi->get_default_salesman($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	list($success, $return) = $this->simpi->get_default_currency($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	$key_mandatory_tobe_validate = [
	// 		'NameFirst' 	=> ['mandatory'],
	// 		'NameLast' 		=> ['mandatory'],
	// 		'CountryOfBirth' => ['mandatory','CountryCode'],
	// 		'PlaceOfBirth' 	=> ['mandatory'],
	// 		'DateOfBirth' 	=> ['mandatory','Date'],
	// 		'Gender' 		=> ['mandatory','Gender'],
	// 		'EducationalBackground'	=> ['mandatory','LevelID'],
	// 		'Religion' 	=> ['mandatory','ReligionID'],
	// 		'Occupation' 	=> ['mandatory','OccupationID'],
	// 		'MaritalStatus' => ['mandatory','StatusID'],
	// 		'RiskLevel' 	=> ['mandatory','RiskID'],
	// 		'AssetOwner' 	=> ['mandatory','AssetOwner'],
	// 		'MobilePhone' 	=> ['mandatory'],
	// 		'Email' 		=> ['mandatory','CorrespondenceEmail'],
	// 		'IncomeLevel'	=> ['mandatory','AnswerID',['kycID'=>3]],
	// 		'InvestmentObjective'	=> ['mandatory','AnswerID',['kycID'=>1]],
	// 		'SourceOfFund'	=> ['mandatory','AnswerID',['kycID'=>15]],
	// 		'BankCode' 		=> ['mandatory','CompanyExternalCode',['SystemID'=>8]],
	// 		'BankName' 		=> ['mandatory'],
	// 		'BankCountry' 	=> ['mandatory','CountryCode'],
	// 		'AccountCcy' 	=> ['mandatory','Ccy'],
	// 		'AccountNo' 	=> ['mandatory'],
	// 		'AccountName' 	=> ['mandatory'],
	// 		'TaxRegistrationDate' 	=> ['optional','Date'],
	// 		'IDCardNo' 		=> ['mandatory'],
	// 		'IDCardAddress' => ['mandatory'],
	// 		'IDCardExpired' 		=> ['idcard','IDCardExpired'],
	// 		'CountryOfNationality' 	=> ['idcard','CountryCode'],
	// 		'IDCardCityCode' 		=> ['idcard','CityCode'],
	// 		'CorrespondenceAddress'	=> ['mandatory'],
	// 		'CorrespondenceCountry'	=> ['correspondence','CountryCode'],
	// 		'CorrespondenceCityCode'=> ['correspondence','CityCode'],
	// 		'DomicileCountry'	=> ['domicile','CountryCode','DomicileAddress'],
	// 		'DomicileCityCode'	=> ['domicile','CityCode','DomicileAddress'],
	// 		'FATCA'			=> ['fatca','AnswerID',['kycID'=>46]],
	// 		'TIN'			=> ['fatca','TIN','FATCA'],
	// 		'TINCountry'	=> ['fatca','CountryCode','FATCA'],
	// 	];
	// 	list($success, $return) = $this->simpi->check_valid_params($request, $key_mandatory_tobe_validate);
	// 	if (!$success) return [FALSE, $return];
		
	// 	list($success, $return) = $this->simpi->generate_id_client($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	$request->params->simpiID = $request->simpiID;
	// 	$request->params->ClientName = ($request->params->NameFirst ? $request->params->NameFirst : '').
	// 									(isset($request->params->NameMiddle) ? ' '.$request->params->NameMiddle : '').
	// 									($request->params->NameLast ? ' '.$request->params->NameLast : '');
	// 	$request->params->TypeID = 1;
	// 	$request->params->XRateID = 1;
	// 	$request->params->StatusID = 2;
	// 	$request->params->LF = ($request->params->CountryID == $request->params->CorrespondenceCountryID) ? 'L' : 'F';
	// 	$request->params->LastUpdate = date('Y-m-d');
	// 	$request->params->CreatedAt = date('Y-m-d');
	// 	$request->params->IsUpdate = 1;
	// 	$tbl['master_client'] = [
	// 		'simpiID','ClientID','SalesID','ClientCode'=>'CIF','ClientName','TypeID','CcyID','XRateID','StatusID',
	// 		'CorrespondenceAddress','CorrespondenceProvince','CorrespondenceCity'=>'CorrespondenceCityCode','CorrespondenceCountryID'=>'CorrespondenceCountryID',
	// 		'CorrespondencePhone'=>'MobilePhone','CorrespondenceEmail'=>'Email','CorrespondencePostalCode','RiskID'=>'RiskLevel',
	// 		'LF','LastUpdate','CreatedAt','IsUpdate',
	// 	];
	// 	$request->params->IDCardTypeID = 1;
	// 	$request->params->OfficeBusinessActivityID = 0;
	// 	$tbl['master_client_individu'] = [
	// 		'simpiID','ClientID','NameFirst','NameMiddle','NameLast','BirthDate'=>'DateOfBirth','BirthPlace'=>'PlaceOfBirth','IDCardNo',
	// 		'IDCardIssuer','IDCardExpired','IDCardTypeID','TaxID','Gender','NationalityID'=>'CountryID','ReligionID','OccupationID','MaritalStatusID','OfficeName',
	// 		'OfficeName','OfficeAddress','OfficePhone','OfficeBusinessActivityID','SpouseName','SpouseBirthDate','TitleFirst','TitleLast','MMN'=>'MotherMaidenName',
	// 		'LevelID'=>'EducationalBackground',
	// 	];
	// 	$request->params->BankCodeType = 1;
	// 	$tbl['master_client_bankaccount'] = [
	// 		'simpiID','ClientID','BankName','AccountNo','AccountName','AccountNotes'=>'BankCode','AccountCcyID'=>'AccountCcy','BankBranch','BankCodeType',
	// 		'BankCountryID'=>'BankCountry','CreatedAt',
	// 	];
	// 	$request->params->kycAnswer44 = ($request->params->AssetOwner==1) ? 'MySelf' : ($request->params->AssetOwner==2) ? 'Representing Other Party' : '';
	// 	$request->params->kycAnswer45 = 'e-Statement';
	// 	$tbl['master_client_kyc'] = [
	// 		['simpiID','ClientID','kycID'=>1,'kycAnswer'=>'kycAnswerInvestmentObjective'],
	// 		['simpiID','ClientID','kycID'=>3,'kycAnswer'=>'kycAnswerIncomeLevel'],
	// 		['simpiID','ClientID','kycID'=>15,'kycAnswer'=>'kycAnswerSourceOfFund'],
	// 		['simpiID','ClientID','kycID'=>44,'kycAnswer'=>'kycAnswer44'],
	// 		['simpiID','ClientID','kycID'=>45,'kycAnswer'=>'kycAnswer45'],
	// 		['simpiID','ClientID','kycID'=>46,'kycAnswer'=>'kycAnswerFATCA'],
	// 		['simpiID','ClientID','kycID'=>49,'kycAnswer'=>'IDCardAddress'],
	// 		['simpiID','ClientID','kycID'=>50,'kycAnswer'=>'IDCardCityCode'],
	// 		['simpiID','ClientID','kycID'=>51,'kycAnswer'=>'IDCardPostalCode'],
	// 		['simpiID','ClientID','kycID'=>52,'kycAnswer'=>'DomicileAddress'],
	// 		['simpiID','ClientID','kycID'=>53,'kycAnswer'=>'DomicileCityName'],
	// 		['simpiID','ClientID','kycID'=>54,'kycAnswer'=>'DomicilePostalCode'],
	// 		['simpiID','ClientID','kycID'=>55,'kycAnswer'=>'DomicileCountry'],
	// 		['simpiID','ClientID','kycID'=>58,'kycAnswer'=>'TIN'],
	// 		['simpiID','ClientID','kycID'=>59,'kycAnswer'=>'TINCountry'],
	// 		['simpiID','ClientID','kycID'=>75,'kycAnswer'=>'DomicileCityCode'],
	// 	];
	// 	$request->params->RiskValue = isset($request->params->RiskValue) ? $request->params->RiskValue : 1;
	// 	$tbl['master_client_questioner'] = [
	// 		'simpiID','ClientID','QuestionerDate'=>'CreatedAt','RiskValue','RiskID'=>'RiskLevel',
	// 	];
	// 	$new_password = $this->f->gen_pwd(6);
	// 	$request->params->password_plain = $new_password;
	// 	$request->params->password = md5($new_password);
	// 	$request->params->is_need_activate = 1;
	// 	$request->params->forgot_token = $this->f->gen_token();
	// 	$tbl['mobc_login'] = [
	// 		'simpiID','ClientID','email'=>'Email','password','is_need_activate','forgot_token',
	// 	];
	// 	$request->params->_subject = $this->f->lang('email_subject_new_accountindividual');
	// 	$request->params->_body = $this->f->lang('email_body_new_accountindividual', [
	// 		'name' 				=> $request->params->ClientName, 
	// 		'email' 			=> $request->params->Email,
	// 		'new_password' => $new_password,
	// 		'appcode' 		=> $request->appcode,
	// 		'token' 			=> $request->params->forgot_token,
	// 		'domain_frontend' 	=> 'http://www.simpipro.com/',
	// 	]);
	// 	$tbl['mobc_mail_queue'] = [
	// 		'_to'=>'Email','_subject','_body',
	// 	];
	// 	list($success, $return) = $this->simpi->commit_data($request, $tbl);
	// 	if (!$success) return [FALSE, $return];

	// 	// return [TRUE, ['message' => $request]];
	// 	return [TRUE, ['result' => ['CIF' => $request->params->CIF]]];
	// }
	
	// function new2($request)
	// {
	// 	list($success, $return) = $this->f->is_valid_licensekey($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	list($success, $return) = $this->simpi->get_default_salesman($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	list($success, $return) = $this->simpi->get_default_currency($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	$key_mandatory_tobe_validate = [
	// 		'NameFirst' 	=> ['mandatory'],
	// 		'NameLast' 		=> ['mandatory'],
	// 		'Password' 		=> ['mandatory'],
	// 		'CountryOfBirth' => ['mandatory','CountryCode'],
	// 		'PlaceOfBirth' 	=> ['mandatory'],
	// 		'DateOfBirth' 	=> ['mandatory','Date'],
	// 		'Gender' 		=> ['mandatory','Gender'],
	// 		'EducationalBackground'	=> ['mandatory','LevelID'],
	// 		'Religion' 	=> ['mandatory','ReligionID'],
	// 		'Occupation' 	=> ['mandatory','OccupationID'],
	// 		'MaritalStatus' => ['mandatory','StatusID'],
	// 		'RiskLevel' 	=> ['mandatory','RiskID'],
	// 		'AssetOwner' 	=> ['mandatory','AssetOwner'],
	// 		'MobilePhone' 	=> ['mandatory'],
	// 		'Email' 		=> ['mandatory','CorrespondenceEmail'],
	// 		'IncomeLevel'	=> ['mandatory','AnswerID',['kycID'=>3]],
	// 		'InvestmentObjective'	=> ['mandatory','AnswerID',['kycID'=>1]],
	// 		'SourceOfFund'	=> ['mandatory','AnswerID',['kycID'=>15]],
	// 		'BankCode' 		=> ['mandatory','CompanyExternalCode',['SystemID'=>8]],
	// 		'BankName' 		=> ['mandatory'],
	// 		'BankCountry' 	=> ['mandatory','CountryCode'],
	// 		'AccountCcy' 	=> ['mandatory','Ccy'],
	// 		'AccountNo' 	=> ['mandatory'],
	// 		'AccountName' 	=> ['mandatory'],
	// 		'TaxRegistrationDate' 	=> ['optional','Date'],
	// 		'IDCardNo' 		=> ['mandatory'],
	// 		'IDCardAddress' => ['mandatory'],
	// 		'IDCardExpired' 		=> ['idcard','IDCardExpired'],
	// 		'CountryOfNationality' 	=> ['idcard','CountryCode'],
	// 		'IDCardCityCode' 		=> ['idcard','CityCode'],
	// 		'CorrespondenceAddress'	=> ['mandatory'],
	// 		'CorrespondenceCountry'	=> ['correspondence','CountryCode'],
	// 		'CorrespondenceCityCode'=> ['correspondence','CityCode'],
	// 		'DomicileCountry'	=> ['domicile','CountryCode','DomicileAddress'],
	// 		'DomicileCityCode'	=> ['domicile','CityCode','DomicileAddress'],
	// 		'FATCA'			=> ['fatca','AnswerID',['kycID'=>46]],
	// 		'TIN'			=> ['fatca','TIN','FATCA'],
	// 		'TINCountry'	=> ['fatca','CountryCode','FATCA'],
	// 	];
	// 	list($success, $return) = $this->simpi->check_valid_params($request, $key_mandatory_tobe_validate);
	// 	if (!$success) return [FALSE, $return];
		
	// 	list($success, $return) = $this->simpi->generate_id_client($request);
	// 	if (!$success) return [FALSE, $return];
		
	// 	$request->params->simpiID = $request->simpiID;
	// 	$request->params->ClientName = ($request->params->NameFirst ? $request->params->NameFirst : '').
	// 									(isset($request->params->NameMiddle) ? ' '.$request->params->NameMiddle : '').
	// 									($request->params->NameLast ? ' '.$request->params->NameLast : '');
	// 	$request->params->TypeID = 1;
	// 	$request->params->XRateID = 1;
	// 	$request->params->StatusID = 2;
	// 	$request->params->LF = ($request->params->CountryID == $request->params->CorrespondenceCountryID) ? 'L' : 'F';
	// 	$request->params->LastUpdate = date('Y-m-d');
	// 	$request->params->CreatedAt = date('Y-m-d');
	// 	$request->params->IsUpdate = 1;
	// 	$tbl['master_client'] = [
	// 		'simpiID','ClientID','SalesID','ClientCode'=>'CIF','ClientName','TypeID','CcyID','XRateID','StatusID',
	// 		'CorrespondenceAddress','CorrespondenceProvince','CorrespondenceCity'=>'CorrespondenceCityCode','CorrespondenceCountryID'=>'CorrespondenceCountryID',
	// 		'CorrespondencePhone'=>'MobilePhone','CorrespondenceEmail'=>'Email','CorrespondencePostalCode','RiskID'=>'RiskLevel',
	// 		'LF','LastUpdate','CreatedAt','IsUpdate',
	// 	];
	// 	$request->params->IDCardTypeID = 1;
	// 	$request->params->OfficeBusinessActivityID = 0;
	// 	$tbl['master_client_individu'] = [
	// 		'simpiID','ClientID','NameFirst','NameMiddle','NameLast','BirthDate'=>'DateOfBirth','BirthPlace'=>'PlaceOfBirth','IDCardNo',
	// 		'IDCardIssuer','IDCardExpired','IDCardTypeID','TaxID','Gender','NationalityID'=>'CountryID','ReligionID','OccupationID','MaritalStatusID','OfficeName',
	// 		'OfficeName','OfficeAddress','OfficePhone','OfficeBusinessActivityID','SpouseName','SpouseBirthDate','TitleFirst','TitleLast','MMN'=>'MotherMaidenName',
	// 		'LevelID'=>'EducationalBackground',
	// 	];
	// 	$request->params->BankCodeType = 1;
	// 	$tbl['master_client_bankaccount'] = [
	// 		'simpiID','ClientID','BankName','AccountNo','AccountName','AccountNotes'=>'BankCode','AccountCcyID'=>'AccountCcy','BankBranch','BankCodeType',
	// 		'BankCountryID'=>'BankCountry','CreatedAt',
	// 	];
	// 	$request->params->kycAnswer44 = ($request->params->AssetOwner==1) ? 'MySelf' : ($request->params->AssetOwner==2) ? 'Representing Other Party' : '';
	// 	$request->params->kycAnswer45 = 'e-Statement';
	// 	$tbl['master_client_kyc'] = [
	// 		['simpiID','ClientID','kycID'=>1,'kycAnswer'=>'kycAnswerInvestmentObjective'],
	// 		['simpiID','ClientID','kycID'=>3,'kycAnswer'=>'kycAnswerIncomeLevel'],
	// 		['simpiID','ClientID','kycID'=>15,'kycAnswer'=>'kycAnswerSourceOfFund'],
	// 		['simpiID','ClientID','kycID'=>44,'kycAnswer'=>'kycAnswer44'],
	// 		['simpiID','ClientID','kycID'=>45,'kycAnswer'=>'kycAnswer45'],
	// 		['simpiID','ClientID','kycID'=>46,'kycAnswer'=>'kycAnswerFATCA'],
	// 		['simpiID','ClientID','kycID'=>49,'kycAnswer'=>'IDCardAddress'],
	// 		['simpiID','ClientID','kycID'=>50,'kycAnswer'=>'IDCardCityCode'],
	// 		['simpiID','ClientID','kycID'=>51,'kycAnswer'=>'IDCardPostalCode'],
	// 		['simpiID','ClientID','kycID'=>52,'kycAnswer'=>'DomicileAddress'],
	// 		['simpiID','ClientID','kycID'=>53,'kycAnswer'=>'DomicileCityName'],
	// 		['simpiID','ClientID','kycID'=>54,'kycAnswer'=>'DomicilePostalCode'],
	// 		['simpiID','ClientID','kycID'=>55,'kycAnswer'=>'DomicileCountry'],
	// 		['simpiID','ClientID','kycID'=>58,'kycAnswer'=>'TIN'],
	// 		['simpiID','ClientID','kycID'=>59,'kycAnswer'=>'TINCountry'],
	// 		['simpiID','ClientID','kycID'=>75,'kycAnswer'=>'DomicileCityCode'],
	// 	];
	// 	$request->params->RiskValue = isset($request->params->RiskValue) ? $request->params->RiskValue : 1;
	// 	$tbl['master_client_questioner'] = [
	// 		'simpiID','ClientID','QuestionerDate'=>'CreatedAt','RiskValue','RiskID'=>'RiskLevel',
	// 	];
	// 	// $new_password = $this->f->gen_pwd(6);
	// 	// $request->params->password_plain = $new_password;
	// 	// $request->params->password = md5($new_password);
	// 	$request->params->is_need_activate = 0;
	// 	// $request->params->forgot_token = $this->f->gen_token();
	// 	$tbl['mobc_login'] = [
	// 		'simpiID','ClientID','email'=>'Email','password'=>'Password','is_need_activate',
	// 	];
	// 	list($success, $return) = $this->simpi->commit_data($request, $tbl);
	// 	if (!$success) return [FALSE, $return];

	// 	// return [TRUE, ['message' => $request]];
	// 	return [TRUE, ['result' => ['CIF' => $request->params->CIF]]];
	// }
	

}
