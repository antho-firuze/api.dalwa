<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Transaction_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->database(DATABASE_INVEST);
		$this->load->library('System');
    }

		function search($request)
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
				if (!isset($request->params->DateTrade) || empty($request->params->DateTrade)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter DateTrade'])]];
				}	
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, TrxID, PortfolioID, SecuritiesID, BrokerID, TrxTypeID,TrxLinkType, TrxLinkID, 
									TrxSourceID, TrxSource2ID, InventoryID, AssetCategoryID, CostID, DateTrade, DateSettle, 
									DatePayment, TradeQty, TradePrice, TradeValue, TradeAccrued, TradePayment, TradeCost, 
									PaymentFlag, TrxDescription');
			$this->db->from('afa_securities_transaction');
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
			//daftar unit balance by BrokerID: 1 atau lebih
			if (isset($request->params->BrokerID)) {
				if (is_array($request->params->BrokerID)) 
				 $this->db->where_in('BrokerID', $request->params->BrokerID);
				else 
				 $this->db->where('BrokerID', $request->params->BrokerID);
			 }
	
			if ($request->params->option_date == 'between') { 
				$this->db->where('DateTrade >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('DateTrade <=', $request->params->to_date, NULL, FALSE);
			} elseif ($request->params->option_date == 'last') { 
				$this->db->where('DateTrade <=', $request->params->DateTrade, NULL, FALSE);
			} elseif ($request->params->option_date == 'next') { 
				$this->db->where('DateTrade >=', $request->params->DateTrade, NULL, FALSE);
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
	
		function load($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: TrxID
			if (isset($request->params->TrxID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TrxID'])]];
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, TrxID, PortfolioID, SecuritiesID, BrokerID, TrxTypeID,TrxLinkType, TrxLinkID, 
									TrxSourceID, TrxSource2ID, InventoryID, AssetCategoryID, CostID, DateTrade, DateSettle, 
									DatePayment, TradeQty, TradePrice, TradeValue, TradeAccrued, TradePayment, TradeCost, 
									PaymentFlag, TrxDescription');
			$this->db->from('afa_securities_transaction');
			$this->db->where(['simpiID' => $request->simpi_id, 'TrxID' => $request->params->TrxID], NULL, FALSE);
			$data = $this->f->get_result_paging($request);

			$request->log_type	= 'data';	
			$this->system->save_billing($request);
			
			return $data;
		}
			
		function equity_search($request)
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
				if (!isset($request->params->DateTrade) || empty($request->params->DateTrade)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter DateTrade'])]];
				}	
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('T1.simpiID, T1.TrxID, T1.PortfolioID, T1.SecuritiesID, T1.BrokerID, T1.TrxTypeID, T1.TrxLinkType, 
									T1.TrxLinkID, T1.TrxSourceID, T1.TrxSource2ID, T1.InventoryID, T1.AssetCategoryID, T1.CostID, T1.DateTrade, 
									T1.DateSettle, T1.DatePayment, T1.TradeQty, T1.TradePrice, T1.TradeValue, T1.TradeAccrued, T1.TradePayment, 
									T1.TradeCost, T1.PaymentFlag, T1.TrxDescription, T2.EQCommPercent, T2.EQCommAmount, T2.EQCommFeeID, 
									T2.EQLevyPercent, T2.EQLevyAmount, T2.EQLevyFeeID, T2.EQVATPercent, T2.EQVATAmount, T2.EQVATFeeID, 
									T2.EQWHTPercent, T2.EQWHTAmount, T2.EQWHTFeeID, T2.EQSalesPercent, T2.EQSalesAmount, T2.EQSalesFeeID'); 
			$this->db->from('afa_securities_transaction T1');
			$this->db->join('afa_securities_transaction_eq T2', 'T1.simpiID = T2.simpiID And T1.TrxID = T2.TrxID');
			$this->db->where('T1.simpiID', $request->simpi_id, NULL, FALSE);
			//daftar unit balance by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				if (is_array($request->params->PortfolioID)) 
					 $this->db->where_in('T1.PortfolioID', $request->params->PortfolioID);
				else 
					 $this->db->where('T1.PortfolioID', $request->params->PortfolioID);
			}
			//daftar unit balance by SecuritiesID: 1 atau lebih
			if (isset($request->params->SecuritiesID)) {
				 if (is_array($request->params->SecuritiesID)) 
					$this->db->where_in('T1.SecuritiesID', $request->params->SecuritiesID);
				 else 
					$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
			}
			//daftar unit balance by BrokerID: 1 atau lebih
			if (isset($request->params->BrokerID)) {
				if (is_array($request->params->BrokerID)) 
				 $this->db->where_in('T1.BrokerID', $request->params->BrokerID);
				else 
				 $this->db->where('T1.BrokerID', $request->params->BrokerID);
			 }
	
			if ($request->params->option_date == 'between') { 
				$this->db->where('T1.DateTrade >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('T1.DateTrade <=', $request->params->to_date, NULL, FALSE);
			} elseif ($request->params->option_date == 'last') { 
				$this->db->where('T1.DateTrade <=', $request->params->DateTrade, NULL, FALSE);
			} elseif ($request->params->option_date == 'next') { 
				$this->db->where('T1.DateTrade >=', $request->params->DateTrade, NULL, FALSE);
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
	
		function equity_load($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: TrxID
			if (isset($request->params->TrxID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TrxID'])]];
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('T1.simpiID, T1.TrxID, T1.PortfolioID, T1.SecuritiesID, T1.BrokerID, T1.TrxTypeID, T1.TrxLinkType, 
									T1.TrxLinkID, T1.TrxSourceID, T1.TrxSource2ID, T1.InventoryID, T1.AssetCategoryID, T1.CostID, T1.DateTrade, 
									T1.DateSettle, T1.DatePayment, T1.TradeQty, T1.TradePrice, T1.TradeValue, T1.TradeAccrued, T1.TradePayment, 
									T1.TradeCost, T1.PaymentFlag, T1.TrxDescription, T2.EQCommPercent, T2.EQCommAmount, T2.EQCommFeeID, 
									T2.EQLevyPercent, T2.EQLevyAmount, T2.EQLevyFeeID, T2.EQVATPercent, T2.EQVATAmount, T2.EQVATFeeID, 
									T2.EQWHTPercent, T2.EQWHTAmount, T2.EQWHTFeeID, T2.EQSalesPercent, T2.EQSalesAmount, T2.EQSalesFeeID'); 
			$this->db->from('afa_securities_transaction T1');
			$this->db->join('afa_securities_transaction_eq T2', 'T1.simpiID = T2.simpiID And T1.TrxID = T2.TrxID');
			$this->db->where(['T1.simpiID' => $request->simpi_id, 'T1.TrxID' => $request->params->TrxID], NULL, FALSE);
			$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
			$this->system->save_billing($request);
			
			return $data;
		}
		
}