<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Balance_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->database(DATABASE_INVEST);
		$this->load->library('System');
    }

		function balance_search($request)
		{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: option_date
			if (!isset($request->params->option_date) || empty($request->params->option_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
			if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PositionDate'])]];
			}	
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, PortfolioID, SecuritiesID, PositionDate, Qty, MarketPrice, MarketValue, 
						AccruedInterest, TotalValue, CostPrice, CostTotal, Duration, HoldingPeriod, YTM, EffectiveYield, 
						TTM, MarketDate, PricingFlag, MarketID, MarketType, PricingID, PricingRate, PricingYear, 
						AssetCategoryID, EffectiveYield');
			$this->db->from('afa_securities_balance');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			if ($request->params->option_date == 'at') {
				$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
				//daftar unit balance by PortfolioID: 1 atau lebih
				if (isset($request->params->PortfolioID)) {
					 if (is_array($request->params->PortfolioID)) 
						$this->db->where_in('PortfolioID', $request->params->PortfolioID);
					 else 
						$this->db->where('PortfolioID', $request->params->PortfolioID);
				}
				//daftar unit balance by SecuritiesID: 1 atau lebih
				if (isset($request->params->SecuritiesID)) {
					if (is_array($request->params->SecuritiesID)) 
						 $this->db->where_in('SecuritiesID', $request->params->SecuritiesID);
					else 
						 $this->db->where('SecuritiesID', $request->params->SecuritiesID);
				 }
			 } elseif (($request->params->option_date == 'last') || ($request->params->option_date == 'next') || 
						($request->params->option_date == 'before') || ($request->params->option_date == 'after')) {
				//daftar nav by PortfolioID: 1  
				if (isset($request->params->PortfolioID) && isset($request->params->SecuritiesID)) { 
					$this->db->where('PortfolioID', $request->params->PortfolioID);
					$this->db->where('SecuritiesID', $request->params->SecuritiesID);
				} else {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter master portfolio and securities'])]];	
				}
				if ($request->params->option_date == 'last') {
					$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
					$this->db->order_by('PositionDate', 'DESC');
				} elseif ($request->params->option_date == 'before') {
					$this->db->where('PositionDate <', $request->params->PositionDate, NULL, FALSE);
					$this->db->order_by('PositionDate', 'DESC');
				} elseif ($request->params->option_date == 'next') {
					$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
					$this->db->order_by('PositionDate', 'ASC');
				} else {
					$this->db->where('PositionDate >', $request->params->PositionDate, NULL, FALSE);
					$this->db->order_by('PositionDate', 'ASC');
				}
				$this->db->limit(1);
	
			} elseif (($request->params->option_date == 'first') || ($request->params->option_date == 'end')) {
				//daftar nav by PortfolioID: 1  
				if (isset($request->params->PortfolioID) && isset($request->params->SecuritiesID)) { 
					$this->db->where('PortfolioID', $request->params->PortfolioID);
					$this->db->where('SecuritiesID', $request->params->SecuritiesID);
				} else {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter master portfolio and securities'])]];	
				}
				if ($request->params->option_date == 'first') {
					if (isset($request->params->PositionDate)) 
						$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
					$this->db->order_by('PositionDate', 'ASC');	
				} 
				else {
					if (isset($request->params->PositionDate)) 
						$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
					$this->db->order_by('PositionDate', 'DESC');		
				}
				$this->db->limit(1);
				
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);
			
			return $data;
		}	
	
		function balance_history($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: option_date
			if (!isset($request->params->option_date) || empty($request->params->option_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
	
			//cek parameter: PositionDate
			if ($request->params->option_date == 'between') {
				if (!isset($request->params->from_date) || empty($request->params->from_date)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter from_date'])]];
				}	
				if (!isset($request->params->to_date) || empty($request->params->to_date)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter to_date'])]];
				}	
			} else {
				if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PositionDate'])]];
				}	
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, PortfolioID, SecuritiesID, PositionDate, Qty, MarketPrice, MarketValue, 
						 AccruedInterest, TotalValue, CostPrice, CostTotal, Duration, HoldingPeriod, YTM, EffectiveYield, 
						 TTM, MarketDate, PricingFlag, MarketID, MarketType, PricingID, PricingRate, PricingYear, 
						 AssetCategoryID, EffectiveYield');
			 $this->db->from('afa_securities_balance');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			//daftar unit balance by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				if (is_array($request->params->PortfolioID)) 
					 $this->db->where_in('PortfolioID', $request->params->PortfolioID);
				else 
					 $this->db->where('PortfolioID', $request->params->PortfolioID);
			}
				//daftar unit balance by SecuritiesID: 1 atau lebih
			if (isset($request->params->SecuritiesID)) {
				 if (is_array($request->params->SecuritiesID)) 
					$this->db->where_in('SecuritiesID', $request->params->SecuritiesID);
				 else 
					$this->db->where('SecuritiesID', $request->params->SecuritiesID);
			}
	 
			if ($request->params->option_date == 'between') { 
				$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
			} elseif ($request->params->option_date == 'last') { 
				$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
			} elseif ($request->params->option_date == 'next') { 
				$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);
			
			return $data;
		}
					
		function charges_search($request)
		{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: option_date
			if (!isset($request->params->option_date) || empty($request->params->option_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
			if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PositionDate'])]];
			}	
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, PortfolioID, SecuritiesID, FeeID, PositionDate, FeeAmount');
			$this->db->from('afa_securities_balance_charges');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
			//daftar unit detail by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				 if (is_array($request->params->PortfolioID)) 
					$this->db->where_in('PortfolioID', $request->params->PortfolioID);
				 else 
					$this->db->where('PortfolioID', $request->params->PortfolioID);
			}
			//daftar unit detail by SecuritiesID: 1 atau lebih
			if (isset($request->params->SecuritiesID)) {
				if (is_array($request->params->SecuritiesID)) 
					 $this->db->where_in('SecuritiesID', $request->params->SecuritiesID);
				else 
					 $this->db->where('SecuritiesID', $request->params->SecuritiesID);
			}
			//daftar unit detail by FeeID: 1 atau lebih
			if (isset($request->params->FeeID)) {
				if (is_array($request->params->FeeID)) 
					 $this->db->where_in('FeeID', $request->params->FeeID);
				else 
					 $this->db->where('FeeID', $request->params->FeeID);
			}
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);
			
			return $data;
		}
		
		function charges_history($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: option_date
			if (!isset($request->params->option_date) || empty($request->params->option_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
	
			//cek parameter: PositionDate
			if ($request->params->option_date == 'between') {
				if (!isset($request->params->from_date) || empty($request->params->from_date)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter from_date'])]];
				}	
				if (!isset($request->params->to_date) || empty($request->params->to_date)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter to_date'])]];
				}	
			} else {
				if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PositionDate'])]];
				}	
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, PortfolioID, SecuritiesID, FeeID, PositionDate, FeeAmount');
			$this->db->from('afa_securities_balance_charges');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			//daftar unit balance by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				if (is_array($request->params->PortfolioID)) 
					 $this->db->where_in('PortfolioID', $request->params->PortfolioID);
				else 
					 $this->db->where('PortfolioID', $request->params->PortfolioID);
				}
				//daftar unit balance by SecuritiesID: 1 atau lebih
				if (isset($request->params->SecuritiesID)) {
				 if (is_array($request->params->SecuritiesID)) 
					$this->db->where_in('SecuritiesID', $request->params->SecuritiesID);
				 else 
					$this->db->where('SecuritiesID', $request->params->SecuritiesID);
			}
				//daftar unit balance by FeeID: 1 atau lebih
			if (isset($request->params->FeeID)) {
				if (is_array($request->params->FeeID)) 
					 $this->db->where_in('FeeID', $request->params->FeeID);
				else 
					 $this->db->where('FeeID', $request->params->FeeID);
			}
		
			if ($request->params->option_date == 'between') { 
				$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
			} elseif ($request->params->option_date == 'last') { 
				$this->db->where('PositionDate <=', $request->params->PositionDate, NULL, FALSE);
			} elseif ($request->params->option_date == 'next') { 
				$this->db->where('PositionDate >=', $request->params->PositionDate, NULL, FALSE);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
			}
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);
			
			return $data;
		}
	
}