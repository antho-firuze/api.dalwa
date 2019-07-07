<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Payable_redemption_fee_model extends CI_Model
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
				$this->db->select('simpiID, PortfolioID, SalesID, PositionDate, PayableBalance');
			$this->db->from('afa_payable_redemptionfee_balance');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
			//daftar unit referral by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				 if (is_array($request->params->PortfolioID)) 
					$this->db->where_in('PortfolioID', $request->params->PortfolioID);
				 else 
					$this->db->where('PortfolioID', $request->params->PortfolioID);
			}
			//daftar unit referral by SalesID: 1 atau lebih
			if (isset($request->params->SalesID)) {
				if (is_array($request->params->SalesID)) 
					 $this->db->where_in('SalesID', $request->params->SalesID);
				else 
					 $this->db->where('SalesID', $request->params->SalesID);
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
				$this->db->select('simpiID, PortfolioID, SalesID, PositionDate, PayableBalance');
			$this->db->from('afa_payable_redemptionfee_balance');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			//daftar unit referral by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				if (is_array($request->params->PortfolioID)) 
					 $this->db->where_in('PortfolioID', $request->params->PortfolioID);
				else 
					 $this->db->where('PortfolioID', $request->params->PortfolioID);
				}
				//daftar unit referral by SalesID: 1 atau lebih
				if (isset($request->params->SalesID)) {
				 if (is_array($request->params->SalesID)) 
					$this->db->where_in('SalesID', $request->params->SalesID);
				 else 
					$this->db->where('SalesID', $request->params->SalesID);
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
		
		function transaction_search($request)
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
				if (!isset($request->params->TrxDate) || empty($request->params->TrxDate)) {
					list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
					if (!$success) return [FALSE, 'message' => '00-1'];
					return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TrxDate'])]];
				}	
			}
			
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else
				$this->db->select('simpiID, TrxID, PortfolioID, SalesID, TrxLinkType, TrxLinkID, TrxDate, 
													TrxDescription, TrxDebit, TrxCredit, TrxSourceID, TrxSource2ID');
			$this->db->from('afa_payable_redemptionfee_transaction');
			$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
			//daftar unit balance by PortfolioID: 1 atau lebih
			if (isset($request->params->PortfolioID)) {
				if (is_array($request->params->PortfolioID)) 
					 $this->db->where_in('PortfolioID', $request->params->PortfolioID);
				else 
					 $this->db->where('PortfolioID', $request->params->PortfolioID);
				}
				//daftar unit balance by SalesID: 1 atau lebih
				if (isset($request->params->SalesID)) {
				 if (is_array($request->params->SalesID)) 
					$this->db->where_in('SalesID', $request->params->SalesID);
				 else 
					$this->db->where('SalesID', $request->params->SalesID);
				}
	
			if ($request->params->option_date == 'between') { 
				$this->db->where('TrxDate >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('TrxDate <=', $request->params->to_date, NULL, FALSE);
			} elseif ($request->params->option_date == 'last') { 
				$this->db->where('TrxDate <=', $request->params->TrxDate, NULL, FALSE);
			} elseif ($request->params->option_date == 'next') { 
				$this->db->where('TrxDate >=', $request->params->TrxDate, NULL, FALSE);
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
	
		function transaction_load($request)
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
				$this->db->select('simpiID, TrxID, PortfolioID, SalesID, TrxLinkType, TrxLinkID, TrxDate, 
													TrxDescription, TrxDebit, TrxCredit, TrxSourceID, TrxSource2ID');
			$this->db->from('afa_payable_redemptionfee_transaction');
			 $this->db->where(['simpiID' => $request->simpi_id, 'TrxID' => $request->params->TrxID], NULL, FALSE);
			 $data = $this->f->get_result_paging($request);

			 $request->log_type	= 'data';	
				 $this->system->save_billing($request);
			
			return $data;
		}
	

	
}