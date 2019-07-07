<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Simple_fund_model extends CI_Model
{
	function __construct() {
		parent :: __construct();
		$this->load->library('System');
	}
	
	function reksadana_master($request)
	{
		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.PortfolioID, T1.PortfolioCode, T1.PortfolioNameShort, T1.CcyID, T1.AssetTypeID,
						T1.IsSyariah, COALESCE(T2.FieldData, "") as RiskTolerance, COALESCE(T3.FieldData, "") as RiskLevel, 
						COALESCE(T4.FieldData, 0) as RiskScore, T1.InceptionDate, COALESCE(T5.FieldData, "") as CustodianBank, 
						T1.StatusID, COALESCE(T6.FieldData, "") as InvestmentObjective, COALESCE(T7.FieldData, "") as MinimumInitialSubscription, 
						COALESCE(T8.FieldData, "") as MinimumAdditionalSubscription, COALESCE(T9.FieldData, "") as MinimumRedemption, 
						COALESCE(T10.FieldData, "") as FeeCustodian, COALESCE(T11.FieldData, "") as FeeSelling, 
						COALESCE(T12.FieldData, "") as FeeRedemption, COALESCE(T13.FieldData, "") as FeeSwicthing, COALESCE(T14.FieldData, "") as FeeManagement,
						T15.apply_subscription, T15.apply_redemption, T15.apply_switching, T15.apply_booking, 
						T15.start_booking, COALESCE(T16.FieldData, "") as MinimumSwitching');
		$this->db->from('master_portfolio T1');
		$this->db->join('simpi_portfolio T15', 'T1.simpiID = T15.simpiID And T1.PortfolioID = T15.PortfolioID');
		$this->db->join('codeset_portfolio_data T2', 'T2.FieldID = 3 And T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T3', 'T3.FieldID = 10 And T1.simpiID = T3.simpiID And T1.PortfolioID = T3.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T4', 'T4.FieldID = 22 And T1.simpiID = T4.simpiID And T1.PortfolioID = T4.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T5', 'T5.FieldID = 2 And T1.simpiID = T5.simpiID And T1.PortfolioID = T5.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T6', 'T6.FieldID = 6 And T1.simpiID = T6.simpiID And T1.PortfolioID = T6.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T7', 'T7.FieldID = 17 And T1.simpiID = T7.simpiID And T1.PortfolioID = T7.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T8', 'T8.FieldID = 18 And T1.simpiID = T8.simpiID And T1.PortfolioID = T8.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T9', 'T9.FieldID = 19 And T1.simpiID = T9.simpiID And T1.PortfolioID = T9.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T10', 'T10.FieldID = 13 And T1.simpiID = T10.simpiID And T1.PortfolioID = T10.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T11', 'T11.FieldID = 14 And T1.simpiID = T11.simpiID And T1.PortfolioID = T11.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T12', 'T12.FieldID = 15 And T1.simpiID = T12.simpiID And T1.PortfolioID = T12.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T13', 'T13.FieldID = 16 And T1.simpiID = T13.simpiID And T1.PortfolioID = T13.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T14', 'T14.FieldID = 12 And T1.simpiID = T14.simpiID And T1.PortfolioID = T14.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T16', 'T16.FieldID = 31 And T1.simpiID = T16.simpiID And T1.PortfolioID = T16.PortfolioID', 'left');
		$this->db->where('T1.simpiID', $request->simpi_id);
		$this->db->where('T15.AppsID', $request->AppsID);
		if (isset($request->params->PortfolioID)) $this->db->where('T1.PortfolioID', $request->params->PortfolioID);
		$portfolio = $this->db->get()->result();
		if (!$portfolio) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'master reksa dana'])]];
		}

		list($success, $return) =  $this->system->database_server($request, 'market');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		$this->db->select('CcyID, Ccy');
   		$this->db->from('parameter_securities_ccy');
	  	$ccy = $this->db->get()->result();

		$this->db->select('AssetTypeID, AssetTypeCode, AssetTypeDescription');
	  	$this->db->from('parameter_portfolio_assettype');
	  	$asset = $this->db->get()->result();

		$this->db->select('StatusID, StatusCode');
	  	$this->db->from('parameter_portfolio_status');
	  	$status = $this->db->get()->result();

		$result = from($portfolio)
    				->groupJoin(from($ccy), 
								'$a ==> $a->CcyID', 
								'$b ==> $b->CcyID',
								'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioCode" => $a->PortfolioCode, 
									"PortfolioNameShort" => $a->PortfolioNameShort, 
									"Ccy" => $b->Ccy, 
									"AssetTypeID" => $a->AssetTypeID,
									"IsSyariah" => $a->IsSyariah, 
									"RiskTolerance" => $a->RiskTolerance, 
									"RiskLevel" => $a->RiskLevel, 
									"RiskScore" => $a->RiskScore,
									"InceptionDate" => $a->InceptionDate, 
									"CustodianBank" => $a->CustodianBank, 
									"StatusID" => $a->StatusID, 
									"InvestmentObjective" => $a->InvestmentObjective,
									"MinimumInitialSubscription" => $a->MinimumInitialSubscription, 
									"MinimumAdditionalSubscription" => $a->MinimumAdditionalSubscription, 
									"MinimumRedemption" => $a->MinimumRedemption, 
									"FeeCustodian" => $a->FeeCustodian, 
									"FeeSelling" => $a->FeeSelling,
									"FeeRedemption" => $a->FeeRedemption,
									"FeeSwicthing" => $a->FeeSwicthing,
									"FeeManagement" => $a->FeeManagement,
									"apply_subscription" => $a->apply_subscription, 
									"apply_redemption" => $a->apply_redemption, 
									"apply_switching" => $a->apply_switching, 
									"apply_booking" => $a->apply_booking, 
									"start_booking" => $a->start_booking, 
									"MinimumSwitching" => $a->MinimumSwitching)'
							)
    				->groupJoin(from($asset), 
								'$a ==> $a->AssetTypeID', 
								'$b ==> $b->AssetTypeID',
								'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioCode" => $a->PortfolioCode, 
									"PortfolioNameShort" => $a->PortfolioNameShort, 
									"Ccy" => $a->Ccy, 
									"AssetTypeCode" => $b->AssetTypeCode,
									"AssetTypeDescription" => $b->AssetTypeDescription,
									"IsSyariah" => $a->IsSyariah, 
									"RiskTolerance" => $a->RiskTolerance, 
									"RiskLevel" => $a->RiskLevel, 
									"RiskScore" => $a->RiskScore,
									"InceptionDate" => $a->InceptionDate, 
									"CustodianBank" => $a->CustodianBank, 
									"StatusID" => $a->StatusID, 
									"InvestmentObjective" => $a->InvestmentObjective,
									"MinimumInitialSubscription" => $a->MinimumInitialSubscription, 
									"MinimumAdditionalSubscription" => $a->MinimumAdditionalSubscription, 
									"MinimumRedemption" => $a->MinimumRedemption, 
									"FeeCustodian" => $a->FeeCustodian, 
									"FeeSelling" => $a->FeeSelling,
									"FeeRedemption" => $a->FeeRedemption,
									"FeeSwicthing" => $a->FeeSwicthing,
									"FeeManagement" => $a->FeeManagement,
									"apply_subscription" => $a->apply_subscription, 
									"apply_redemption" => $a->apply_redemption, 
									"apply_switching" => $a->apply_switching, 
									"apply_booking" => $a->apply_booking, 
									"start_booking" => $a->start_booking, 
									"MinimumSwitching" => $a->MinimumSwitching)'
							)
    				->groupJoin(from($status), 
								'$a ==> $a->StatusID', 
								'$b ==> $b->StatusID',
								'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioCode" => $a->PortfolioCode, 
									"PortfolioNameShort" => $a->PortfolioNameShort, 
									"Ccy" => $a->Ccy, 
									"AssetTypeCode" => $a->AssetTypeCode,
									"AssetTypeDescription" => $a->AssetTypeDescription,
									"IsSyariah" => $a->IsSyariah, 
									"RiskTolerance" => $a->RiskTolerance, 
									"RiskLevel" => $a->RiskLevel, 
									"RiskScore" => $a->RiskScore,
									"InceptionDate" => $a->InceptionDate, 
									"CustodianBank" => $a->CustodianBank, 
									"StatusCode" => $b->StatusCode, 
									"InvestmentObjective" => $a->InvestmentObjective,
									"MinimumInitialSubscription" => $a->MinimumInitialSubscription, 
									"MinimumAdditionalSubscription" => $a->MinimumAdditionalSubscription, 
									"MinimumRedemption" => $a->MinimumRedemption, 
									"FeeCustodian" => $a->FeeCustodian, 
									"FeeSelling" => $a->FeeSelling,
									"FeeRedemption" => $a->FeeRedemption,
									"FeeSwicthing" => $a->FeeSwicthing,
									"FeeManagement" => $a->FeeManagement,
									"apply_subscription" => $a->apply_subscription, 
									"apply_redemption" => $a->apply_redemption, 
									"apply_switching" => $a->apply_switching, 
									"apply_booking" => $a->apply_booking, 
									"start_booking" => $a->start_booking, 
									"MinimumSwitching" => $a->MinimumSwitching)'
							);

		$data = $this->f->get_result_yalinqo($request, $result->toList());

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function reksadana_beranda($request)
	{
		$records = 4;
		if (isset($request->params->limit) && !empty($request->params->limit)) $records = $request->params->limit ;

		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('PortfolioID, CcyID, PortfolioNameShort');
		$this->db->from('master_portfolio');
		$this->db->where('simpiID', $request->simpi_id);
		$portfolio = $this->db->get()->result();
		if (!$portfolio) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'reksa dana beranda'])]];
		}

		list($success, $return) =  $this->system->database_server($request, 'market');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('CcyID, Ccy');
   		$this->db->from('parameter_securities_ccy');
	  	$ccy = $this->db->get()->result();

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.PortfolioID, T1.PositionDate, T2.NAVperUnit, COALESCE(T3.rYTD, 0) As rYTD');
		$this->db->from('afa_mtm T1');
		$this->db->join('afa_nav T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate');
		$this->db->join('afa_return T3', 'T1.simpiID = T3.simpiID And T1.PortfolioID = T3.PortfolioID And T1.PositionDate = T3.PositionDate', 'left');
		$this->db->where('T1.IsLast', 'Y');
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$nav = $this->db->get()->result();

		$result = from($portfolio)
    				->join(from($ccy), 
							'$a ==> $a->CcyID', 
							'$b ==> $b->CcyID',
							'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioNameShort" => $a->PortfolioNameShort,
									"Ccy" => $b->Ccy)'
								)
					->join(from($nav)
						->where('$b ==> $b->NAVperUnit > 0'), 
							'$a ==> $a->PortfolioID', 
							'$b ==> $b->PortfolioID',
							'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioNameShort" => $a->PortfolioNameShort,
									"Ccy" => $a->Ccy,
									"PositionDate" => $b->PositionDate,
									"NAVperUnit" => $b->NAVperUnit,
									"rYTD" => $b->rYTD)'
								)
					->orderByDescending('$b ==> $b->rYTD')
					->take($records)
					;					

		$data = $this->f->get_result_yalinqo($request, $result->toList());

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}	
	 
	function reksadana_daftar($request)
	{
		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.PortfolioID, T1.PortfolioCode, T1.PortfolioNameShort, T1.CcyID, T1.AssetTypeID, T1.IsSyariah, 
							COALESCE(T2.FieldData, "") as RiskTolerance, COALESCE(T3.FieldData, "") as RiskLevel, COALESCE(T4.FieldData, 0) as RiskScore');
		$this->db->from('master_portfolio T1');
		$this->db->join('simpi_portfolio T5', 'T1.simpiID = T5.simpiID And T1.PortfolioID = T5.PortfolioID');
		$this->db->join('codeset_portfolio_data T2', 'T2.FieldID = 3 And T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T3', 'T3.FieldID = 10 And T1.simpiID = T3.simpiID And T1.PortfolioID = T3.PortfolioID', 'left');
		$this->db->join('codeset_portfolio_data T4', 'T4.FieldID = 22 And T1.simpiID = T4.simpiID And T1.PortfolioID = T4.PortfolioID', 'left');
		$this->db->where('T1.simpiID', $request->simpi_id);
		$this->db->where('T5.AppsID', $request->AppsID);
		$portfolio = $this->db->get()->result();
		if (!$portfolio) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'master reksa dana'])]];
		}

		list($success, $return) =  $this->system->database_server($request, 'market');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		$this->db->select('CcyID, Ccy');
   		$this->db->from('parameter_securities_ccy');
	  	$ccy = $this->db->get()->result();

		$this->db->select('AssetTypeID, AssetTypeCode, AssetTypeDescription');
	  	$this->db->from('parameter_portfolio_assettype');
	  	$asset = $this->db->get()->result();

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.PortfolioID, T1.PositionDate, T2.NAVperUnit, COALESCE(T3.rYTD, 0) As rYTD');
		$this->db->from('afa_mtm T1');
		$this->db->join('afa_nav T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate');
		$this->db->join('afa_return T3', 'T1.simpiID = T3.simpiID And T1.PortfolioID = T3.PortfolioID And T1.PositionDate = T3.PositionDate', 'left');
		$this->db->where('T1.IsLast', 'Y');
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$nav = $this->db->get()->result();

		$result = from($portfolio)
    				->groupJoin(from($ccy), 
								'$a ==> $a->CcyID', 
								'$b ==> $b->CcyID',
								'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioNameShort" => $a->PortfolioNameShort, 
									"Ccy" => $b->Ccy, 
									"AssetTypeID" => $a->AssetTypeID,
									"IsSyariah" => $a->IsSyariah, 
									"RiskTolerance" => $a->RiskTolerance, 
									"RiskLevel" => $a->RiskLevel, 
									"RiskScore" => $a->RiskScore)'
							)		
    				->groupJoin(from($asset), 
								'$a ==> $a->AssetTypeID', 
								'$b ==> $b->AssetTypeID',
								'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioNameShort" => $a->PortfolioNameShort, 
									"Ccy" => $a->Ccy, 
									"AssetTypeID" => $b->AssetTypeID,
									"AssetTypeCode" => $b->AssetTypeCode,
									"AssetTypeDescription" => $b->AssetTypeDescription,
									"IsSyariah" => $a->IsSyariah, 
									"RiskTolerance" => $a->RiskTolerance, 
									"RiskLevel" => $a->RiskLevel, 
									"RiskScore" => $a->RiskScore)'
							)		
					->join(from($nav)
						->where('$b ==> $b->NAVperUnit > 0'), 
							'$a ==> $a->PortfolioID', 
							'$b ==> $b->PortfolioID',
							'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PortfolioNameShort" => $a->PortfolioNameShort,
									"Ccy" => $a->Ccy, 
									"AssetTypeID" => $a->AssetTypeID,
									"AssetTypeCode" => $a->AssetTypeCode,
									"AssetTypeDescription" => $a->AssetTypeDescription,
									"IsSyariah" => $a->IsSyariah, 
									"RiskTolerance" => $a->RiskTolerance, 
									"RiskLevel" => $a->RiskLevel, 
									"RiskScore" => $a->RiskScore,
									"PositionDate" => $b->PositionDate,
									"NAVperUnit" => $b->NAVperUnit,
									"rYTD" => $b->rYTD)'
							)
							;					

		$data = $this->f->get_result_yalinqo($request, $result->toList());

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function reksadana_nav($request)
	{
		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PortfolioID'])]];
		}	

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.PortfolioID, T1.PositionDate, T2.NAVperUnit, COALESCE(T3.r6Mo, 0) as r6Mo, 
							COALESCE(T3.r1Y, 0) as r1Y, COALESCE(T3.r3Y, 0) as r3Y, COALESCE(T3.r5Y, 0) as r5Y');
		$this->db->from('afa_mtm T1');
		$this->db->join('afa_nav T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate');
		$this->db->join('afa_return T3', 'T1.simpiID = T3.simpiID And T1.PortfolioID = T3.PortfolioID And T1.PositionDate = T3.PositionDate', 'left');
		$this->db->where('T1.IsLast', 'Y');
		$this->db->where('T1.PortfolioID', $request->params->PortfolioID);
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}	

	function efek_asset($request)
	{
		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PortfolioID'])]];
		}	

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		$this->db->select('T2.NAV');
		$this->db->from('afa_mtm T1');
		$this->db->join('afa_nav T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate');
		$this->db->where('T1.IsLast', 'Y');
		$this->db->where('T1.PortfolioID', $request->params->PortfolioID);
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$row = $this->db->get()->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'portfolio NAV'])]];
		}	
		else {
			$nav = $row->NAV;
			if ($nav == 0) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'portfolio NAV'])]];
			}
		}

		$str = 'Select T1.PortfolioID, T2.SecuritiesID, T1.PositionDate, T2.MarketValue / ?  as nilai 
				from afa_mtm T1 inner join afa_securities_balance T2 on T1.simpiID = T2.simpiID 
				And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate 
				Where T1.IsLast = ? And T1.PortfolioID = ?';
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$str = $this->f->compile_qry($str, [$nav, 'Y', $request->params->PortfolioID]);
		$qry = $this->db->query($str);	
		$efek = $qry->result();
		$efek2 = $qry->result_array();
		
		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.SecuritiesID, T3.TypeDescription');
	  	$this->db->from('market_instrument T1');
	  	$this->db->join('parameter_securities_instrument_type_sub T2', 'T1.SubTypeID = T2.SubTypeID');
	  	$this->db->join('parameter_securities_instrument_type T3', 'T2.TypeID = T3.TypeID');
		$this->db->where_in('T1.SecuritiesID', array_column($efek2,'SecuritiesID'));  
	  	$securities = $this->db->get()->result();
	  	 
  		$result = from($efek)
    				->join(from($securities), 
							'$a ==> $a->SecuritiesID', 
							'$b ==> $b->SecuritiesID',
							'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PositionDate" => $a->PositionDate,
									"TypeDescription" => $b->TypeDescription,
									"nilai" => $a->nilai)'
								)
					->groupBy('$p ==> $p->TypeDescription')
					->select('$v, $k ==> (object) array(
									"PortfolioID" => $v[0]->PortfolioID,
									"PositionDate" => $v[0]->PositionDate,
									"TypeDescription" => $k,
									"nilai" => from($v)->sum(\'$p ==> $p->nilai\'))' 
					);	
		
		$data = $this->f->get_result_yalinqo($request, $result->toList());

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}		

	function efek_sector($request)
	{
		$records = 5;
		if (isset($request->params->limit) && !empty($request->params->limit)) $records = $request->params->limit ;

		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PortfolioID'])]];
		}	

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		$this->db->select('T2.NAV');
		$this->db->from('afa_mtm T1');
		$this->db->join('afa_nav T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate');
		$this->db->where('T1.IsLast', 'Y');
		$this->db->where('T1.PortfolioID', $request->params->PortfolioID);
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$row = $this->db->get()->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'portfolio NAV'])]];
		}	
		else {
			$nav = $row->NAV;
			if ($nav == 0) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'portfolio NAV'])]];
			}
		}

		$str = 'Select T1.PortfolioID, T2.SecuritiesID, T1.PositionDate, T2.TotalValue / ?  as nilai 
				from afa_mtm T1 inner join afa_securities_balance T2 on T1.simpiID = T2.simpiID 
				And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate 
				Where T1.IsLast = ? And T1.PortfolioID = ?';
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$str = $this->f->compile_qry($str, [$nav, 'Y', $request->params->PortfolioID]);
		$qry = $this->db->query($str);	
		$efek = $qry->result();
		$efek2 = $qry->result_array();

		if (!isset($request->params->ClassID) || empty($request->params->ClassID)) $request->params->ClassID = 1;

		
		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.SecuritiesID, T3.SectorName');
	  	$this->db->from('market_instrument T1');
	  	$this->db->join('parameter_securities_sector_class_member_company T2', 'T1.CompanyID = T2.CompanyID');
	  	$this->db->join('parameter_securities_sector T3', 'T2.SectorID = T3.SectorID');
		$this->db->where('T2.ClassID', $request->params->ClassID);  
		$this->db->where_in('T1.SecuritiesID', array_column($efek2,'SecuritiesID'));  
	  	$sector = $this->db->get()->result();

  		$result = from($efek)
    				->join(from($sector), 
							'$a ==> $a->SecuritiesID', 
							'$b ==> $b->SecuritiesID',
							'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PositionDate" => $a->PositionDate,
									"SectorName" => $b->SectorName,
									"nilai" => $a->nilai)'
								)
					->groupBy('$p ==> $p->SectorName')
					->select('$v, $k ==> (object) array(
									"PortfolioID" => $v[0]->PortfolioID,
									"PositionDate" => $v[0]->PositionDate,
									"SectorName" => $k,
									"nilai" => from($v)->sum(\'$p ==> $p->nilai\'))' 
					)
					->orderByDescending('$a ==> $a->nilai')
					->take($records)
					;	
		
		$data = $this->f->get_result_yalinqo($request, $result->toList());

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}		

	function efek_top($request)
	{
		$records = 5;
		if (isset($request->params->limit) && !empty($request->params->limit)) $records = $request->params->limit ;

		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PortfolioID'])]];
		}	

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		$this->db->select('T2.NAV');
		$this->db->from('afa_mtm T1');
		$this->db->join('afa_nav T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate');
		$this->db->where('T1.IsLast', 'Y');
		$this->db->where('T1.PortfolioID', $request->params->PortfolioID);
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$row = $this->db->get()->row();
		if (!$row) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'portfolio NAV'])]];
		}	
		else {
			$nav = $row->NAV;
			if ($nav == 0) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'portfolio NAV'])]];
			}
		}

		$str = 'Select T1.PortfolioID, T2.SecuritiesID, T1.PositionDate, T2.TotalValue / ?  as nilai 
				from afa_mtm T1 inner join afa_securities_balance T2 on T1.simpiID = T2.simpiID 
				And T1.PortfolioID = T2.PortfolioID And T1.PositionDate = T2.PositionDate 
				Where T1.IsLast = ? And T1.PortfolioID = ?';
		//$this->db->where('T1.simpiID', $request->simpi_id);
		$str = $this->f->compile_qry($str, [$nav, 'Y', $request->params->PortfolioID]);
		$qry = $this->db->query($str);	
		$efek = $qry->result();
		$efek2 = $qry->result_array();
		
		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->db->select('T1.SecuritiesID, T1.SecuritiesNameShort, T3.TypeDescription');
	  	$this->db->from('market_instrument T1');
	  	$this->db->join('parameter_securities_instrument_type_sub T2', 'T1.SubTypeID = T2.SubTypeID');
	  	$this->db->join('parameter_securities_instrument_type T3', 'T2.TypeID = T3.TypeID');
		$this->db->where_in('T1.SecuritiesID', array_column($efek2,'SecuritiesID'));  
	  	$securities = $this->db->get()->result();
	  	 
  		$result = from($efek)
    				->join(from($securities), 
							'$a ==> $a->SecuritiesID', 
							'$b ==> $b->SecuritiesID',
							'($a, $b) ==> (object) array(
									"PortfolioID" => $a->PortfolioID,
									"PositionDate" => $a->PositionDate,
									"SecuritiesNameShort" => $b->SecuritiesNameShort,
									"TypeDescription" => $b->TypeDescription,
									"nilai" => $a->nilai)'
								)
					->orderByDescending('$a ==> $a->nilai')
					->take($records)
					;	
		
		$data = $this->f->get_result_yalinqo($request, $result->toList());

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}		

	function market_update($request)
	{
		if (!isset($request->params->limit) || empty($request->params->limit)) $request->params->limit = 4;

		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		if (isset($request->params->ReviewID)) {
			$this->db->select('ReviewID, ReviewDate, ReviewTitle, ReviewAuthor, ReviewText');
			$this->db->from('analyst_marketing_marketreview');
			$this->db->where('simpiID', $request->simpi_id);	
			$this->db->where('ReviewID', $request->params->ReviewID, NULL, FALSE);
		} else {
			$this->db->select('ReviewID, ReviewDate, ReviewTitle, ReviewAuthor');
			$this->db->from('analyst_marketing_marketreview');
			$this->db->where('simpiID', $request->simpi_id);	
			$this->db->order_by('ReviewDate', 'DESC');
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}	
}  