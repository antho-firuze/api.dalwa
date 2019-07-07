<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class AccountIndividual_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
		$this->load->library('client');
	}
	
	function new($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->get_default_salesman($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->get_default_currency($request);
		if (!$success) return [FALSE, $return];
		
		$key_mandatory_tobe_validate = [
			'NameFirst' 	=> ['mandatory'],
			'NameLast' 		=> ['mandatory'],
			'CountryOfBirth' => ['mandatory','CountryCode'],
			'PlaceOfBirth' 	=> ['mandatory'],
			'DateOfBirth' 	=> ['mandatory','Date'],
			'Gender' 		=> ['mandatory','Gender'],
			'EducationalBackground'	=> ['mandatory','LevelID'],
			'Religion' 	=> ['mandatory','ReligionID'],
			'Occupation' 	=> ['mandatory','OccupationID'],
			'MaritalStatus' => ['mandatory','StatusID'],
			'RiskLevel' 	=> ['mandatory','RiskID'],
			'AssetOwner' 	=> ['mandatory','AssetOwner'],
			'MobilePhone' 	=> ['mandatory'],
			'Email' 		=> ['mandatory','CorrespondenceEmail'],
			'IncomeLevel'	=> ['mandatory','AnswerID',['kycID'=>3]],
			'InvestmentObjective'	=> ['mandatory','AnswerID',['kycID'=>1]],
			'SourceOfFund'	=> ['mandatory','AnswerID',['kycID'=>15]],
			'BankCode' 		=> ['mandatory','CompanyExternalCode',['SystemID'=>8]],
			'BankName' 		=> ['mandatory'],
			'BankCountry' 	=> ['mandatory','CountryCode'],
			'AccountCcy' 	=> ['mandatory','Ccy'],
			'AccountNo' 	=> ['mandatory'],
			'AccountName' 	=> ['mandatory'],
			'TaxRegistrationDate' 	=> ['optional','Date'],
			'IDCardNo' 		=> ['mandatory'],
			'IDCardAddress' => ['mandatory'],
			'IDCardExpired' 		=> ['idcard','IDCardExpired'],
			'CountryOfNationality' 	=> ['idcard','CountryCode'],
			'IDCardCityCode' 		=> ['idcard','CityCode'],
			'CorrespondenceAddress'	=> ['mandatory'],
			'CorrespondenceCountry'	=> ['correspondence','CountryCode'],
			'CorrespondenceCityCode'=> ['correspondence','CityCode'],
			'DomicileCountry'	=> ['domicile','CountryCode','DomicileAddress'],
			'DomicileCityCode'	=> ['domicile','CityCode','DomicileAddress'],
			'FATCA'			=> ['fatca','AnswerID',['kycID'=>46]],
			'TIN'			=> ['fatca','TIN','FATCA'],
			'TINCountry'	=> ['fatca','CountryCode','FATCA'],
		];
		list($success, $return) = $this->simpi->check_valid_params($request, $key_mandatory_tobe_validate);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->client->generate_id_client($request);
		if (!$success) return [FALSE, $return];
		
		$request->params->simpiID = $request->simpiID;
		$request->params->ClientName = ($request->params->NameFirst ? $request->params->NameFirst : '').
										(isset($request->params->NameMiddle) ? ' '.$request->params->NameMiddle : '').
										($request->params->NameLast ? ' '.$request->params->NameLast : '');
		$request->params->TypeID = 1;
		$request->params->XRateID = 1;
		$request->params->StatusID = 2;
		$request->params->LF = ($request->params->CountryID == $request->params->CorrespondenceCountryID) ? 'L' : 'F';
		$request->params->LastUpdate = date('Y-m-d');
		$request->params->CreatedAt = date('Y-m-d');
		$request->params->IsUpdate = 1;
		$tbl['master_client'] = [
			'simpiID','ClientID','SalesID','ClientCode'=>'CIF','ClientName','TypeID','CcyID','XRateID','StatusID',
			'CorrespondenceAddress','CorrespondenceProvince','CorrespondenceCity'=>'CorrespondenceCityCode','CorrespondenceCountryID'=>'CorrespondenceCountryID',
			'CorrespondencePhone'=>'MobilePhone','CorrespondenceEmail'=>'Email','CorrespondencePostalCode','RiskID'=>'RiskLevel',
			'LF','LastUpdate','CreatedAt','IsUpdate',
		];
		$request->params->IDCardTypeID = 1;
		$request->params->OfficeBusinessActivityID = 0;
		$tbl['master_client_individu'] = [
			'simpiID','ClientID','NameFirst','NameMiddle','NameLast','BirthDate'=>'DateOfBirth','BirthPlace'=>'PlaceOfBirth','IDCardNo',
			'IDCardIssuer','IDCardExpired','IDCardTypeID','TaxID','Gender','NationalityID'=>'CountryID','ReligionID','OccupationID','MaritalStatusID','OfficeName',
			'OfficeName','OfficeAddress','OfficePhone','OfficeBusinessActivityID','SpouseName','SpouseBirthDate','TitleFirst','TitleLast','MMN'=>'MotherMaidenName',
			'LevelID'=>'EducationalBackground',
		];
		$request->params->BankCodeType = 1;
		$tbl['master_client_bankaccount'] = [
			'simpiID','ClientID','BankName','AccountNo','AccountName','AccountNotes'=>'BankCode','AccountCcyID'=>'AccountCcy','BankBranch','BankCodeType',
			'BankCountryID'=>'BankCountry','CreatedAt',
		];
		$request->params->kycAnswer44 = ($request->params->AssetOwner==1) ? 'MySelf' : ($request->params->AssetOwner==2) ? 'Representing Other Party' : '';
		$request->params->kycAnswer45 = 'e-Statement';
		$tbl['master_client_kyc'] = [
			['simpiID','ClientID','kycID'=>1,'kycAnswer'=>'kycAnswerInvestmentObjective'],
			['simpiID','ClientID','kycID'=>3,'kycAnswer'=>'kycAnswerIncomeLevel'],
			['simpiID','ClientID','kycID'=>15,'kycAnswer'=>'kycAnswerSourceOfFund'],
			['simpiID','ClientID','kycID'=>44,'kycAnswer'=>'kycAnswer44'],
			['simpiID','ClientID','kycID'=>45,'kycAnswer'=>'kycAnswer45'],
			['simpiID','ClientID','kycID'=>46,'kycAnswer'=>'kycAnswerFATCA'],
			['simpiID','ClientID','kycID'=>49,'kycAnswer'=>'IDCardAddress'],
			['simpiID','ClientID','kycID'=>50,'kycAnswer'=>'IDCardCityCode'],
			['simpiID','ClientID','kycID'=>51,'kycAnswer'=>'IDCardPostalCode'],
			['simpiID','ClientID','kycID'=>52,'kycAnswer'=>'DomicileAddress'],
			['simpiID','ClientID','kycID'=>53,'kycAnswer'=>'DomicileCityName'],
			['simpiID','ClientID','kycID'=>54,'kycAnswer'=>'DomicilePostalCode'],
			['simpiID','ClientID','kycID'=>55,'kycAnswer'=>'DomicileCountry'],
			['simpiID','ClientID','kycID'=>58,'kycAnswer'=>'TIN'],
			['simpiID','ClientID','kycID'=>59,'kycAnswer'=>'TINCountry'],
			['simpiID','ClientID','kycID'=>75,'kycAnswer'=>'DomicileCityCode'],
		];
		$request->params->RiskValue = isset($request->params->RiskValue) ? $request->params->RiskValue : 1;
		$tbl['master_client_questioner'] = [
			'simpiID','ClientID','QuestionerDate'=>'CreatedAt','RiskValue','RiskID'=>'RiskLevel',
		];
		$new_password = $this->f->gen_pwd(6);
		$request->params->password_plain = $new_password;
		$request->params->password = md5($new_password);
		$request->params->is_need_activate = 1;
		$request->params->forgot_token = $this->f->gen_token();
		$tbl['mobc_login'] = [
			'simpiID','ClientID','email'=>'Email','password','is_need_activate','forgot_token',
		];
		$request->params->_subject = $this->f->lang('email_subject_new_accountindividual');
		$request->params->_body = $this->f->lang('email_body_new_accountindividual', [
			'name' 				=> $request->params->ClientName, 
			'email' 			=> $request->params->Email,
			'new_password' => $new_password,
			'appcode' 		=> $request->appcode,
			'token' 			=> $request->params->forgot_token,
			'domain_frontend' 	=> 'http://www.simpipro.com/',
		]);
		$tbl['mobc_mail_queue'] = [
			'_to'=>'Email','_subject','_body',
		];
		list($success, $return) = $this->simpi->commit_data($request, $tbl);
		if (!$success) return [FALSE, $return];

		// return [TRUE, ['message' => $request]];
		return [TRUE, ['result' => ['CIF' => $request->params->CIF]]];
	}
	
	function new2($request)
	{
		list($success, $return) = $this->f->is_valid_licensekey($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->get_default_salesman($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->get_default_currency($request);
		if (!$success) return [FALSE, $return];
		
		$key_mandatory_tobe_validate = [
			'NameFirst' 	=> ['mandatory'],
			'NameLast' 		=> ['mandatory'],
			'Password' 		=> ['mandatory'],
			'CountryOfBirth' => ['mandatory','CountryCode'],
			'PlaceOfBirth' 	=> ['mandatory'],
			'DateOfBirth' 	=> ['mandatory','Date'],
			'Gender' 		=> ['mandatory','Gender'],
			'EducationalBackground'	=> ['mandatory','LevelID'],
			'Religion' 	=> ['mandatory','ReligionID'],
			'Occupation' 	=> ['mandatory','OccupationID'],
			'MaritalStatus' => ['mandatory','StatusID'],
			'RiskLevel' 	=> ['mandatory','RiskID'],
			'AssetOwner' 	=> ['mandatory','AssetOwner'],
			'MobilePhone' 	=> ['mandatory'],
			'Email' 		=> ['mandatory','CorrespondenceEmail'],
			'IncomeLevel'	=> ['mandatory','AnswerID',['kycID'=>3]],
			'InvestmentObjective'	=> ['mandatory','AnswerID',['kycID'=>1]],
			'SourceOfFund'	=> ['mandatory','AnswerID',['kycID'=>15]],
			'BankCode' 		=> ['mandatory','CompanyExternalCode',['SystemID'=>8]],
			'BankName' 		=> ['mandatory'],
			'BankCountry' 	=> ['mandatory','CountryCode'],
			'AccountCcy' 	=> ['mandatory','Ccy'],
			'AccountNo' 	=> ['mandatory'],
			'AccountName' 	=> ['mandatory'],
			'TaxRegistrationDate' 	=> ['optional','Date'],
			'IDCardNo' 		=> ['mandatory'],
			'IDCardAddress' => ['mandatory'],
			'IDCardExpired' 		=> ['idcard','IDCardExpired'],
			'CountryOfNationality' 	=> ['idcard','CountryCode'],
			'IDCardCityCode' 		=> ['idcard','CityCode'],
			'CorrespondenceAddress'	=> ['mandatory'],
			'CorrespondenceCountry'	=> ['correspondence','CountryCode'],
			'CorrespondenceCityCode'=> ['correspondence','CityCode'],
			'DomicileCountry'	=> ['domicile','CountryCode','DomicileAddress'],
			'DomicileCityCode'	=> ['domicile','CityCode','DomicileAddress'],
			'FATCA'			=> ['fatca','AnswerID',['kycID'=>46]],
			'TIN'			=> ['fatca','TIN','FATCA'],
			'TINCountry'	=> ['fatca','CountryCode','FATCA'],
		];
		list($success, $return) = $this->simpi->check_valid_params($request, $key_mandatory_tobe_validate);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->simpi->generate_id_client($request);
		if (!$success) return [FALSE, $return];
		
		$request->params->simpiID = $request->simpiID;
		$request->params->ClientName = ($request->params->NameFirst ? $request->params->NameFirst : '').
										(isset($request->params->NameMiddle) ? ' '.$request->params->NameMiddle : '').
										($request->params->NameLast ? ' '.$request->params->NameLast : '');
		$request->params->TypeID = 1;
		$request->params->XRateID = 1;
		$request->params->StatusID = 2;
		$request->params->LF = ($request->params->CountryID == $request->params->CorrespondenceCountryID) ? 'L' : 'F';
		$request->params->LastUpdate = date('Y-m-d');
		$request->params->CreatedAt = date('Y-m-d');
		$request->params->IsUpdate = 1;
		$tbl['master_client'] = [
			'simpiID','ClientID','SalesID','ClientCode'=>'CIF','ClientName','TypeID','CcyID','XRateID','StatusID',
			'CorrespondenceAddress','CorrespondenceProvince','CorrespondenceCity'=>'CorrespondenceCityCode','CorrespondenceCountryID'=>'CorrespondenceCountryID',
			'CorrespondencePhone'=>'MobilePhone','CorrespondenceEmail'=>'Email','CorrespondencePostalCode','RiskID'=>'RiskLevel',
			'LF','LastUpdate','CreatedAt','IsUpdate',
		];
		$request->params->IDCardTypeID = 1;
		$request->params->OfficeBusinessActivityID = 0;
		$tbl['master_client_individu'] = [
			'simpiID','ClientID','NameFirst','NameMiddle','NameLast','BirthDate'=>'DateOfBirth','BirthPlace'=>'PlaceOfBirth','IDCardNo',
			'IDCardIssuer','IDCardExpired','IDCardTypeID','TaxID','Gender','NationalityID'=>'CountryID','ReligionID','OccupationID','MaritalStatusID','OfficeName',
			'OfficeName','OfficeAddress','OfficePhone','OfficeBusinessActivityID','SpouseName','SpouseBirthDate','TitleFirst','TitleLast','MMN'=>'MotherMaidenName',
			'LevelID'=>'EducationalBackground',
		];
		$request->params->BankCodeType = 1;
		$tbl['master_client_bankaccount'] = [
			'simpiID','ClientID','BankName','AccountNo','AccountName','AccountNotes'=>'BankCode','AccountCcyID'=>'AccountCcy','BankBranch','BankCodeType',
			'BankCountryID'=>'BankCountry','CreatedAt',
		];
		$request->params->kycAnswer44 = ($request->params->AssetOwner==1) ? 'MySelf' : ($request->params->AssetOwner==2) ? 'Representing Other Party' : '';
		$request->params->kycAnswer45 = 'e-Statement';
		$tbl['master_client_kyc'] = [
			['simpiID','ClientID','kycID'=>1,'kycAnswer'=>'kycAnswerInvestmentObjective'],
			['simpiID','ClientID','kycID'=>3,'kycAnswer'=>'kycAnswerIncomeLevel'],
			['simpiID','ClientID','kycID'=>15,'kycAnswer'=>'kycAnswerSourceOfFund'],
			['simpiID','ClientID','kycID'=>44,'kycAnswer'=>'kycAnswer44'],
			['simpiID','ClientID','kycID'=>45,'kycAnswer'=>'kycAnswer45'],
			['simpiID','ClientID','kycID'=>46,'kycAnswer'=>'kycAnswerFATCA'],
			['simpiID','ClientID','kycID'=>49,'kycAnswer'=>'IDCardAddress'],
			['simpiID','ClientID','kycID'=>50,'kycAnswer'=>'IDCardCityCode'],
			['simpiID','ClientID','kycID'=>51,'kycAnswer'=>'IDCardPostalCode'],
			['simpiID','ClientID','kycID'=>52,'kycAnswer'=>'DomicileAddress'],
			['simpiID','ClientID','kycID'=>53,'kycAnswer'=>'DomicileCityName'],
			['simpiID','ClientID','kycID'=>54,'kycAnswer'=>'DomicilePostalCode'],
			['simpiID','ClientID','kycID'=>55,'kycAnswer'=>'DomicileCountry'],
			['simpiID','ClientID','kycID'=>58,'kycAnswer'=>'TIN'],
			['simpiID','ClientID','kycID'=>59,'kycAnswer'=>'TINCountry'],
			['simpiID','ClientID','kycID'=>75,'kycAnswer'=>'DomicileCityCode'],
		];
		$request->params->RiskValue = isset($request->params->RiskValue) ? $request->params->RiskValue : 1;
		$tbl['master_client_questioner'] = [
			'simpiID','ClientID','QuestionerDate'=>'CreatedAt','RiskValue','RiskID'=>'RiskLevel',
		];
		// $new_password = $this->f->gen_pwd(6);
		// $request->params->password_plain = $new_password;
		// $request->params->password = md5($new_password);
		$request->params->is_need_activate = 0;
		// $request->params->forgot_token = $this->f->gen_token();
		$tbl['mobc_login'] = [
			'simpiID','ClientID','email'=>'Email','password'=>'Password','is_need_activate',
		];
		list($success, $return) = $this->simpi->commit_data($request, $tbl);
		if (!$success) return [FALSE, $return];

		// return [TRUE, ['message' => $request]];
		return [TRUE, ['result' => ['CIF' => $request->params->CIF]]];
	}
	

}
