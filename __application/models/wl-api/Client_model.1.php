<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Client_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}
	
	function account_info($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('master_client')->where(['ClientID' => $request->ClientID]);
		return $this->f->get_row();
	}
	
	function balance($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->position_date) || empty($request->params->position_date)) {
			$str = '
				select t0.simpiID, t0.PositionDate, t0.ClientID, t3.AssetTypeCode, t0.PortfolioID, t0.UnitBalance, t0.UnitPrice, t0.CostPrice, t0.UnitValue, t0.CostTotal, 
				t1.PortfolioCode, t1.PortfolioNameFull, t1.PortfolioNameShort, t1.CcyID, t2.Ccy, t2.CcyDescription 
				from ata_balance t0 
				inner join master_portfolio t1 on t0.PortfolioID = t1.PortfolioID 
				inner join parameter_securities_ccy t2 on t1.CcyID = t2.CcyID 
				inner join parameter_portfolio_assettype t3 on t1.AssetTypeID = t3.AssetTypeID 
				where t0.simpiID = ? and t0.ClientID = ? and t0.PositionDate = (select PositionDate from mobc_last_position_date where simpiID = ?)';
			$str = $this->f->compile_qry($str, [$request->simpiID, $request->ClientID, $request->simpiID]);
		} else {
			$str = '
				select t0.simpiID, t0.PositionDate, t0.ClientID, t3.AssetTypeCode, t0.PortfolioID, t0.UnitBalance, t0.UnitPrice, t0.CostPrice, t0.UnitValue, t0.CostTotal, 
				t1.PortfolioCode, t1.PortfolioNameFull, t1.PortfolioNameShort, t1.CcyID, t2.Ccy, t2.CcyDescription 
				from ata_balance t0 
				inner join master_portfolio t1 on t0.PortfolioID = t1.PortfolioID 
				inner join parameter_securities_ccy t2 on t1.CcyID = t2.CcyID 
				inner join parameter_portfolio_assettype t3 on t1.AssetTypeID = t3.AssetTypeID 
				where t0.simpiID = ? and t0.ClientID = ? and t0.PositionDate = ?';
			$str = $this->f->compile_qry($str, [$request->simpiID, $request->ClientID, $request->params->position_date]);
		}
		
		$qry = $this->db->query($str);
		if ($qry->num_rows() < 1) 
			return [FALSE, ['message' => $this->f->lang('err_record_empty')]];

		return [TRUE, ['result' => $qry->result(), 'method' => $request->method]];
	}

	function balance_ccy($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->position_date) || empty($request->params->position_date)) {
			$str = 'select distinct t1.CcyID, t2.Ccy, t2.CcyDescription 
				from ata_balance t0 
				inner join master_portfolio t1 on t0.PortfolioID = t1.PortfolioID 
				inner join parameter_securities_ccy t2 on t1.CcyID = t2.CcyID 
				where t0.simpiID = ? and t0.ClientID = ? and t0.PositionDate = (select PositionDate from mobc_last_position_date where simpiID = ?)';
			$str = $this->f->compile_qry($str, [$request->simpiID, $request->ClientID, $request->simpiID]);
		} else {
			$str = 'select distinct t1.CcyID, t2.Ccy, t2.CcyDescription 
				from ata_balance t0 
				inner join master_portfolio t1 on t0.PortfolioID = t1.PortfolioID 
				inner join parameter_securities_ccy t2 on t1.CcyID = t2.CcyID 
				where t0.simpiID = ? and t0.ClientID = ? and t0.PositionDate = ?';
			$str = $this->f->compile_qry($str, [$request->simpiID, $request->ClientID, $request->params->position_date]);
		}
		
		$qry = $this->db->query($str);
		if ($qry->num_rows() < 1) 
			return [FALSE, ['message' => $this->f->lang('err_record_empty')]];

		return [TRUE, ['result' => $qry->result(), 'method' => $request->method]];
	}

	function transaction($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			select t0.simpiID, TrxID, t0.PortfolioID, t0.ClientID, t0.SalesID, SalesCode, CorrespondenceEmail, 
			TrxDate, NAVDate, TrxDescription, TrxType1, TrxAmount, TrxUnit, TrxPrice, TrxCost, AverageCost, SellingFeePercentage, 
			RedemptionFeePercentage, PortfolioCode, PortfolioNameFull, PortfolioNameShort, CcyID, Ccy, CcyDescription 
			from ata_transaction t0 
			inner join master_portfolio t1 on t0.PortfolioID = t1.PortfolioID 
			inner join parameter_securities_country t2 on t1.CcyID = t2.CountryID 
			inner join master_sales t3 on t0.SalesID = t3.SalesID
			) g0 ";
		$this->db->from($table)
			->where([
				'simpiID' => $request->simpiID, 
				'ClientID' => $request->ClientID
			], NULL, FALSE)
			->order_by('TrxDate', 'desc');
		return $this->f->get_result();
	}

	/**
	 * Get data from "mobc_prospect" if ClientID is NULL, else from "master_client & master_client_individu" 
	 *
	 * @param json_object $request
	 * @return void
	 */
	function _get_profile($request)
	{
		if (!empty($request->ClientID)) {
			$str = "
				select t0.*, ClientName, CorrespondenceAddress, CorrespondenceCity, CorrespondenceProvince, CorrespondenceCountryID,
				CorrespondencePhone, CorrespondenceEmail, CorrespondencePostalCode, 
				BankName, AccountNo, AccountName, AccountNotes, AccountCcyID, BankBranch, BankCodeType, BankCountryID 
				from master_client_individu t0 
				inner join master_client t1 on t0.simpiID = t1.simpiID and t0.ClientID = t1.ClientID 
				inner join master_client_bankaccount t2 on t0.simpiID = t2.simpiID and t0.ClientID = t2.ClientID 
				where t0.simpiID = ? and t0.ClientID = ?";
			$str = $this->f->compile_qry($str, [$request->simpiID, $request->ClientID]);
			if ($qry = $this->db->query($str)) {
				if ($qry->num_rows() > 0) {
					$row = $qry->row();
					$row->email = $row->CorrespondenceEmail;
					$row->full_name = $row->ClientName;
					return $row;
				}
			}
		} else {
			$row = $this->db->get_where('mobc_prospect', ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])->row();
			$row->email = $row->CorrespondenceEmail;
			$row->full_name = ($row->NameFirst ? $row->NameFirst : '').
				($row->NameMiddle ? ' '.$row->NameMiddle : '').
				($row->NameLast ? ' '.$row->NameLast : '');
			return $row;
		}

		return FALSE;
	}

	function get_profile($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$row = $this->_get_profile($request))
			return [FALSE, ['message' => 'Records not found']];
		
		$mfields = [
			'NameLast','NationalityID','BirthDate','BirthPlace','IDCardNo','Gender','LevelID','ReligionID','OccupationID','IncomeLevel','MaritalStatusID',
			'InvestmentObjective','SourceofFund','AssetOwner','KTPAddress','KTPCityCode','CorrespondenceAddress','CorrespondenceCityCode','CorrespondencePhone',
			'CorrespondenceEmail','AccountNotes','BankName','BankCountryID','AccountCcyID','AccountNo','AccountName',
		];
		$ofields = [
			'NameFirst','NameMiddle','IDCardExpired','TaxID','CountryOfBirth','MothersMaidenName','SpouseName','RiskID','RiskScore','KTPPostalCode',
			'CorrespondenceCountryID','CorrespondenceProvince','CorrespondencePostalCode','DomicileAddress','DomicileCityCode','DomicilePostalCode',
			'DomicileCountry','FATCAStatus','TIN','TINCountry','BankBranch',
		];

		$t = array_intersect_key((array)$row, array_flip($mfields));
		$this->load->helper('myarray');
		$percent_populate = count(remove_empty_array($t)) / count($mfields)  * 100;

		return [TRUE, ['result' => $row, 'profile_status' => $percent_populate]];
	}

	function _set_profile($request)
	{
		if ($request->ClientID && $request->ClientID !== 0) {
			$tbl['master_client'] = [
				'SalesID','ClientCode'=>'CIF','ClientName','TypeID','CcyID','XRateID','StatusID',
				'CorrespondenceAddress','CorrespondenceProvince','CorrespondenceCity'=>'CorrespondenceCityCode','CorrespondenceCountryID'=>'CorrespondenceCountryID',
				'CorrespondencePhone'=>'MobilePhone','CorrespondenceEmail'=>'Email','CorrespondencePostalCode','RiskID'=>'RiskLevel',
				'LF','LastUpdate','CreatedAt','IsUpdate',
			];
			$tbl['master_client_individu'] = [
				'NameFirst','NameMiddle','NameLast','BirthDate'=>'DateOfBirth','BirthPlace'=>'PlaceOfBirth','IDCardNo',
				'IDCardIssuer','IDCardExpired','IDCardTypeID','TaxID','Gender','NationalityID'=>'CountryID','ReligionID','OccupationID','MaritalStatusID','OfficeName',
				'OfficeName','OfficeAddress','OfficePhone','OfficeBusinessActivityID','SpouseName','SpouseBirthDate','TitleFirst','TitleLast','MMN'=>'MotherMaidenName',
				'LevelID'=>'EducationalBackground',
			];
			$tbl['master_client_bankaccount'] = [
				'BankName','AccountNo','AccountName','AccountNotes'=>'BankCode','AccountCcyID'=>'AccountCcy','BankBranch','BankCodeType',
				'BankCountryID'=>'BankCountry','CreatedAt',
			];
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
			$tbl['master_client_questioner'] = [
				'simpiID','ClientID','QuestionerDate'=>'CreatedAt','RiskValue','RiskID'=>'RiskLevel',
			];

			$where = [
				'simpiID'	=> $request->simpiID,
				'emailID'	=> $request->emailID,
				'ClientID'	=> $request->ClientID,
			];
			$this->db->trans_strict(TRUE);
			$this->db->trans_start();
			// $this->db->trans_start(TRUE);	// Query will be rolled back (Test Mode)
			foreach ($tbl as $table => $fields) {
				if ($table == 'master_client_kyc') {
					
					continue;
				}

				$data = array_intersect_key((array)$request->params, array_flip($fields)); // The Magic Script
				if ($data) {
					$this->db->update($table, $data, $where);

					if ($table == 'master_client_individu') {
						empty($fields['IDCardExpired']) OR $fields['IDCardExpired'] = '9998-12-31';
						$this->db->query("update master_client_individu set IDCardExpired = '9998-12-31' where simpiID = ? and ClientID = ? and IDCardExpired is null;", [$request->simpiID, $request->ClientID]);
					}

					// if (!$return = $this->db->update($table, $data, $where)) 
					// 	return [FALSE, ['message' => $this->db->error()['message']]];
				}
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE)
			{
				return [FALSE, ['message' => $ci->f->lang('err_commit_data')]];
				// return [FALSE, ['message' => $ci->db->last_query()]];
				// return [FALSE, ['message' => $ci->db->error()['message']]];
			}
		} else {
			// #1
			$table = 'mobc_prospect';
			$fields = $this->db->list_fields($table);
			$data = array_intersect_key((array)$request->params, array_flip($fields)); // The Magic Script
			if ($data) {
				if (!$return = $this->db->update($table, $data, ['simpiID' => $request->simpiID, 'emailID' => $request->emailID])) 
					return [FALSE, ['message' => $this->db->error()['message']]];
				
				$this->db->query("update mobc_prospect set IDCardExpired = '9998-12-31' where simpiID = ? and emailID = ? and IDCardExpired is null", [$request->simpiID, $request->emailID]);
			}

		}

		return [TRUE, ['message' => $this->f->lang('success_update')]];			
	}
		
	function set_profile($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		list($success, $return) = $this->_set_profile($request);
		if (!$success) return [FALSE, $return];
		
		return [TRUE, $return];
	}

	function pdf_print($request)
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$row = $this->_get_profile($request))
			return [FALSE, ['message' => 'Records not found']];

		list($success, $return) = $this->f->get_report($request, NULL, ['name' => 'formulir_opening_account']);
		// list($success, $return) = $this->f->get_report($request, $row, ['name' => 'formulir_opening_account']);
		if (!$success) return [FALSE, $return];
	
		return [TRUE, ['result' => $return]];
	}

	function pdf_email($request) 
	{
		list($success, $return) = $this->f->is_valid_token($request);
		if (!$success) return [FALSE, $return];
		
		if (!$row = $this->_get_profile($request))
			return [FALSE, ['message' => 'Records not found']];

		list($success, $return) = $this->f->get_report($request, NULL, ['name' => 'formulir_opening_account']);
		// list($success, $return) = $this->f->get_report($request, $row, ['name' => 'formulir_opening_account']);
		if (!$success) return [FALSE, $return];
	
		$email = [
			'_to' 			=> $row->CorrespondenceEmail,
			'_subject' 	=> $this->f->lang('email_subject_opening_account'),
			'_body'			=> $this->f->lang('email_body_opening_account', [
				'name' 			=> $row->TitleFirst ? $row->TitleFirst.' '.$row->full_name : $row->full_name, 
			]),
			'_attachment'	=> [$return['path']],
		];
		list($success, $message) = $this->f->mail_queue($email);
		if (!$success) return [FALSE, $message];

		return [TRUE, ['message' => $this->f->lang('success_email_report')]];
	}
}
