<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Portfolio_nav_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->library('System');
    }

	private function _sql($request)
	{
		$subQuery =  'Select PortfolioID From system_access_portfolio 
					Where UserID = '.$request->user_id.' And simpiID = '.$request->simpi_id;

		if (isset($request->params->fields) && !empty($request->params->fields)) 
			$this->db->select($request->params->fields);
		else
			$this->db->select('simpiID, PortfolioID, CcyID, PositionDate, FlagDate, NAV, TotalUnit, TotalCost, NAVperUnit, 
                    AdjustmentNAV, AdjustmentNAVperUnit, WealthRatio, GeometricIndex, AssetEQValue, AssetFIValue, 
                    AssetFIAccrued, AssetFundValue, AssetPAValue, AssetCAValue, AssetCAAccrued, AssetPendingPurchase, 
                    PendingSubscription, AssetTDValue, AssetTDAccrued, AssetOthers, AssetReceivableTrading, 
                    AssetReceivableSubscription, PayableCharges, PayableOthers, PayableTrading, PayableRedemption, 
                    PayableSellingFee, PayableRedemptionFee, CapitalSubscriptionValue, CapitalReinvestmentValue, 
                    CapitalRedemptionValue, CapitalDividendValue, DailyPL, CapitalSubscriptionUnit, CapitalReinvestmentUnit, 
                    CapitalRedemptionUnit, IncomeOthers, ExpenseOthers, rIO, rEO, giIO, giEO');
		$this->db->from('afa_nav');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		
		if ($request->log_access == 'license') {
			return [TRUE, NULL]; //full akses
		} elseif ($request->log_access == 'session') {
		 	$this->db->where("'PortfolioID' IN ($subQuery)", NULL, FALSE); //user assignment
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} elseif ($request->log_access == 'apps') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
	}

	private function _sum($request, $groupSelect = NULL)
	{
		$subQuery =  'Select PortfolioID From system_access_portfolio 
					Where UserID = '.$request->user_id.' And simpiID = '.$request->simpi_id;

		if (isset($request->params->fields) && !empty($request->params->fields)) 
			$this->db->select($request->params->fields);
		else {
			$sum_sql = '';
			if (!is_null($groupSelect)) $sum_sql = $groupSelect.', ';
			$sum_sql .= 'SUM(NAV) As NAV, SUM(TotalCost) As TotalCost, SUM(AdjustmentNAV) As AdjustmentNAV, SUM(AssetEQValue) As AssetEQValue, SUM(AssetFIValue) As AssetFIValue 
			, SUM(AssetFIAccrued) As AssetFIAccrued, SUM(AssetFundValue) As AssetFundValue, SUM(AssetPAValue) As AssetPAValue, SUM(AssetCAValue) As AssetCAValue 
			, SUM(AssetCAAccrued) As AssetCAAccrued, SUM(AssetPendingPurchase) As AssetPendingPurchase, SUM(PendingSubscription) As PendingSubscription 
			, SUM(AssetTDValue) As AssetTDValue, SUM(AssetTDAccrued) As AssetTDAccrued, SUM(AssetOthers) As AssetOthers, SUM(AssetReceivableTrading) As AssetReceivableTrading 
			, SUM(AssetReceivableSubscription) As AssetReceivableSubscription, SUM(PayableCharges) As PayableCharges, SUM(PayableOthers) As PayableOthers 
			, SUM(PayableTrading) As PayableTrading, SUM(PayableRedemption) As PayableRedemption, SUM(PayableSellingFee) As PayableSellingFee 
			, SUM(PayableRedemptionFee) As PayableRedemptionFee, SUM(CapitalSubscriptionValue) As CapitalSubscriptionValue, SUM(CapitalReinvestmentValue) As CapitalReinvestmentValue 
			, SUM(CapitalRedemptionValue) As CapitalRedemptionValue, SUM(CapitalDividendValue) As CapitalDividendValue, SUM(DailyPL) As DailyPL 
			, SUM(CapitalSubscriptionUnit) As CapitalSubscriptionUnit, SUM(CapitalReinvestmentUnit) As CapitalReinvestmentUnit, SUM(CapitalRedemptionUnit) As CapitalRedemptionUnit 
			, SUM(IncomeOthers) As IncomeOthers, SUM(ExpenseOthers) As ExpenseOthers';
			$this->db->select($sum_sql);
		}
		$this->db->from('afa_nav');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		
		if ($request->log_access == 'license') {
			return [TRUE, NULL]; //full akses
		} elseif ($request->log_access == 'session') {
		 	$this->db->where("'PortfolioID' IN ($subQuery)", NULL, FALSE); //user assignment
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} elseif ($request->log_access == 'apps') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
	}

	private function _avg($request, $groupSelect = NULL)
	{
		$subQuery =  'Select PortfolioID From system_access_portfolio 
					Where UserID = '.$request->user_id.' And simpiID = '.$request->simpi_id;

		if (isset($request->params->fields) && !empty($request->params->fields)) 
			$this->db->select($request->params->fields);
		else {
			$avg_sql = '';
			if (!is_null($groupSelect)) $avg_sql = $groupSelect.', ';
			$avg_sql .= 'AVG(NAV) As NAV, AVG(TotalCost) As TotalCost, AVG(AdjustmentNAV) As AdjustmentNAV, AVG(AssetEQValue) As AssetEQValue, AVG(AssetFIValue) As AssetFIValue 
			, AVG(AssetFIAccrued) As AssetFIAccrued, AVG(AssetFundValue) As AssetFundValue, AVG(AssetPAValue) As AssetPAValue, AVG(AssetCAValue) As AssetCAValue 
			, AVG(AssetCAAccrued) As AssetCAAccrued, AVG(AssetPendingPurchase) As AssetPendingPurchase, AVG(PendingSubscription) As PendingSubscription 
			, AVG(AssetTDValue) As AssetTDValue, AVG(AssetTDAccrued) As AssetTDAccrued, AVG(AssetOthers) As AssetOthers, AVG(AssetReceivableTrading) As AssetReceivableTrading 
			, AVG(AssetReceivableSubscription) As AssetReceivableSubscription, AVG(PayableCharges) As PayableCharges, AVG(PayableOthers) As PayableOthers 
			, AVG(PayableTrading) As PayableTrading, AVG(PayableRedemption) As PayableRedemption, AVG(PayableSellingFee) As PayableSellingFee 
			, AVG(PayableRedemptionFee) As PayableRedemptionFee, AVG(CapitalSubscriptionValue) As CapitalSubscriptionValue, AVG(CapitalReinvestmentValue) As CapitalReinvestmentValue 
			, AVG(CapitalRedemptionValue) As CapitalRedemptionValue, AVG(CapitalDividendValue) As CapitalDividendValue, AVG(DailyPL) As DailyPL 
			, AVG(CapitalSubscriptionUnit) As CapitalSubscriptionUnit, AVG(CapitalReinvestmentUnit) As CapitalReinvestmentUnit, AVG(CapitalRedemptionUnit) As CapitalRedemptionUnit 
			, AVG(IncomeOthers) As IncomeOthers, AVG(ExpenseOthers) As ExpenseOthers';
			$this->db->select($avg_sql);
		}	
		$this->db->from('afa_nav');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		
		if ($request->log_access == 'license') {
			return [TRUE, NULL]; //full akses
		} elseif ($request->log_access == 'session') {
		 	$this->db->where("'PortfolioID' IN ($subQuery)", NULL, FALSE); //user assignment
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} elseif ($request->log_access == 'apps') {
			//can not akses: must via apps
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'access right');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
	}
	
	function load($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->option_aggregate) || empty($request->params->option_aggregate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_aggregate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PositionDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		if ((strtolower($request->params->option_aggregate)=='sum') || 
		    (strtolower($request->params->option_aggregate)=='avg')) {
			if (strtolower($request->params->option_aggregate)=='sum')	
				list($success, $return) = $this->_sum($request);
			else
				list($success, $return) = $this->_avg($request);
			if (!$success) return [FALSE, $return];
			if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
				$this->db->where('PortfolioID', $request->params->PortfolioID);
			} elseif (isset($request->params->PortfolioList) && !empty($request->params->PortfolioList)) {
				if (is_array($request->params->PortfolioList)) {
					$this->db->where_in('PortfolioID', $request->params->PortfolioList);
				} else {
					$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioList');
					return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}
			} elseif (isset($request->params->CcyID) && !empty($request->params->CcyID)) {
				$this->db->where('CcyID', $request->params->CcyID);				
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter portfolio');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
			$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);	
		} else {
			if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
			if (!isset($request->params->option_date) || empty($request->params->option_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}

			list($success, $return) = $this->_sql($request);
			if (!$success) return [FALSE, $return];
			$this->db->where('PortfolioID', $request->params->PortfolioID, NULL, FALSE);
			if ($request->params->option_date == 'at') {
				$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
			} elseif ($request->params->option_date == 'last') {
				$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
				$this->db->order_by('PositionDate', 'DESC');
			} elseif ($request->params->option_date == 'before') {
				$this->db->where('PositionDate <', $request->params->PositionDate, NULL, FALSE);
				$this->db->order_by('PositionDate', 'DESC');
			} elseif ($request->params->option_date == 'next') {
				$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
				$this->db->order_by('PositionDate', 'ASC');
			} elseif ($request->params->option_date == 'after') {
				$this->db->where('PositionDate >', $request->params->PositionDate, NULL, FALSE);
				$this->db->order_by('PositionDate', 'ASC');
			} elseif ($request->params->option_date == 'first') {
				$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
				$this->db->where('WealthRatio = 0');
				$this->db->order_by('PositionDate', 'DESC');
			} elseif ($request->params->option_date == 'end') {
				$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
				$this->db->where('WealthRatio = 0');
				$this->db->order_by('PositionDate', 'ASC');
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
	
		}

		$request->params->limit = 1;
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function search($request)
	{	
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PositionDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
		if (!isset($request->params->option_aggregate) || empty($request->params->option_aggregate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_aggregate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->FlagDate) || empty($request->params->FlagDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FlagDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		if (isset($request->params->fields) && !empty($request->params->fields)) {
			$this->db->select($request->params->fields);
		} elseif (strtolower($request->params->option_aggregate)=='sum') {
			list($success, $return) = $this->_sum($request, 'CcyID');
			if (!$success) return [FALSE, $return];
		} elseif (strtolower($request->params->option_aggregate)=='avg') {
			list($success, $return) = $this->_avg($request, 'CcyID');
			if (!$success) return [FALSE, $return];
		} else {
			list($success, $return) = $this->_sql($request);
			if (!$success) return [FALSE, $return];
		}
		if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
			$this->db->where('PortfolioID', $request->params->PortfolioID);
		} elseif (isset($request->params->PortfolioList) && !empty($request->params->PortfolioList)) {
			if (is_array($request->params->PortfolioList)) 
				$this->db->where_in('PortfolioID', $request->params->PortfolioList);
			else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioList');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
		} elseif (isset($request->params->CcyID) && !empty($request->params->CcyID)) {
			$this->db->where('CcyID', $request->params->CcyID);
		}	 
		
		$date_from = date_create($request->params->PositionDate);
		if ($request->params->FlagDate == 1) {
			$this->db->where('(FlagDate = 1 or FlagDate = 2 or FlagDate = 3)');
			$date_from->modify('-1 month');
			$this->db->where('PositionDate >=', $date_from, NULL, FALSE);
			$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
		} elseif ($request->params->FlagDate == 2) { 
			$this->db->where('(FlagDate = 2 or FlagDate = 3)');
			$date_from->modify('-3 months');
			$this->db->where('PositionDate >=', $date_from, NULL, FALSE);
			$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
		} elseif ($request->params->FlagDate == 3) { 
			$this->db->where('FlagDate = 3');
			$date_from->modify('-1 year');
			$this->db->where('PositionDate >=', $date_from, NULL, FALSE);
			$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
		} else {
			$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
		}		

		if ((strtolower($request->params->option_aggregate)=='sum') || 
		    (strtolower($request->params->option_aggregate)=='avg')) {
			$this->db->group_by('CcyID');	
			$this->db->order_by('CcyID ASC');	
		} else {
			$this->db->order_by('CcyID ASC, PortfolioID ASC');	
		} 
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function history($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->option_aggregate) || empty($request->params->option_aggregate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_aggregate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->FlagDate) || empty($request->params->FlagDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FlagDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if ($request->params->option_date == 'between') {
			if (!isset($request->params->from_date) || empty($request->params->from_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter from_date');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}	
			if (!isset($request->params->to_date) || empty($request->params->to_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter to_date');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}	
		} else {
			if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PositionDate');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
		}

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		if (isset($request->params->fields) && !empty($request->params->fields)) {
			$this->db->select($request->params->fields);
		} elseif (strtolower($request->params->option_aggregate)=='sum') {
			list($success, $return) = $this->_sum($request, 'CcyID, PositionDate');
			if (!$success) return [FALSE, $return];
		} elseif (strtolower($request->params->option_aggregate)=='avg') {
			list($success, $return) = $this->_avg($request, 'CcyID, PositionDate');
			if (!$success) return [FALSE, $return];
		} else {
			list($success, $return) = $this->_sql($request);
			if (!$success) return [FALSE, $return];
		}
		if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
			$this->db->where('PortfolioID', $request->params->PortfolioID);
		} elseif (isset($request->params->PortfolioList) && !empty($request->params->PortfolioList)) {
			if (is_array($request->params->PortfolioList)) 
				$this->db->where_in('PortfolioID', $request->params->PortfolioList);
			else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioList');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
		} elseif (isset($request->params->CcyID) && !empty($request->params->CcyID)) {
			$this->db->where('CcyID', $request->params->CcyID);
		}	 
	
		if ($request->params->FlagDate == 1) {
            $this->db->where('(FlagDate = 1 or FlagDate = 2 or FlagDate = 3)');
		} elseif ($request->params->FlagDate == 2) {
            $this->db->where('(FlagDate = 2 or FlagDate = 3)');
		} elseif ($request->params->FlagDate == 3) { 
            $this->db->where('FlagDate = 3');
        } 

		if ($request->params->option_date == 'between') { 
			$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
			$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
		} elseif ($request->params->option_date == 'last') { 
			$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
		} elseif ($request->params->option_date == 'next') { 
			$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		if ((strtolower($request->params->option_aggregate)=='sum') || 
		    (strtolower($request->params->option_aggregate)=='avg')) {
			$this->db->group_by('CcyID, PositionDate');	
			if (isset($request->params->option_order_bydate) && !empty($request->params->option_order_bydate)) {
				if (strtolower($request->params->option_order_bydate)=='y') 
					$this->db->order_by('PositionDate ASC, CcyID ASC');
				else 
					$this->db->order_by('CcyID ASC, PositionDate ASC');			
			} else {
				$this->db->order_by('CcyID ASC, PositionDate ASC');	
			}		
		} else {
			if (isset($request->params->option_order_bydate) && !empty($request->params->option_order_bydate)) {
				if (strtolower($request->params->option_order_bydate)=='y') 
					$this->db->order_by('PositionDate ASC, CcyID ASC, PortfolioID ASC');
				else 
					$this->db->order_by('CcyID ASC, PortfolioID ASC, PositionDate ASC');			
			} else {
				$this->db->order_by('CcyID ASC, PortfolioID ASC, PositionDate ASC');	
			}		
		}	

		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}



}