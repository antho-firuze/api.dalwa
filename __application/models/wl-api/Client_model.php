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
