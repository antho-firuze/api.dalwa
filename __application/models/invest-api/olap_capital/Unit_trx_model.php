<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Unit_trx_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->library('System');
	}

	private function _sql($request)
	{
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('simpiID, TrxID, PortfolioID, CcyID, TreeParentID, TreePrefix, SID, ClientID, 
							TrxLinkType, TrxLinkID, TrxDate, NAVDate, TrxDescription, 
							TrxAmount, TrxUnit, TrxPrice, TrxCost, AverageCost, TrxType1, TrxType2, TrxFlagID, 
							SellingFeePercentage, RedemptionFeePercentage, AcqNo');
		$this->db->from('ata_transaction');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
	}
	
	private function _access($request)
	{
		if ($request->log_access == 'license') {
			return [TRUE, NULL];
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
			return [TRUE, NULL];
		} elseif ($request->log_access == 'session') {
			$this->db->like('TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$this->db->where('SID', $request->SID);
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

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->TrxID)) {
				$this->db->where('TrxID', $request->params->TrxID);
		} elseif (isset($request->params->TrxLinkType) && isset($request->params->TrxLinkID)) {
				$this->db->where('TrxLinkType', $request->params->TrxLinkType);	
				$this->db->where('TrxLinkID', $request->params->TrxLinkID);	
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter trx');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];

		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function first_join($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		if (isset($request->params->trx_or_nav) && !empty($request->params->trx_or_nav) && ($request->params->trx_or_nav == 'nav')) 
			$this->db->select('NAVDate as first_join');
		else 
			$this->db->select('TrxDate as first_join');
		$this->db->from('ata_transaction');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		if (isset($request->params->ClientID)) {
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('ClientID', $request->params->ClientID);
			else 
				$this->db->where('ClientID', $request->params->ClientID);
		} elseif (isset($request->params->SID)) {
			$this->db->where('SID', $request->params->SID);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		$this->db->order_by('TrxDate', 'ASC');
		$row = $this->db->get()->row();
		if (!$row) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'client first_join');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['first_join' => $row->first_join]]];
	}
	
	function search($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if ($request->params->option_date == 'between') {
			if (!isset($request->params->from_date) || empty($request->params->from_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}	
			if (!isset($request->params->to_date) || empty($request->params->to_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}	
		} else {
			if (!isset($request->params->TrxDate) || empty($request->params->TrxDate)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
				}	
		}
		
		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->PortfolioID)) {
			if (is_array($request->params->PortfolioID)) 
				$this->db->where_in('PortfolioID', $request->params->PortfolioID);
			else 
				$this->db->where('PortfolioID', $request->params->PortfolioID);
		} elseif (isset($request->params->TreePrefix) && isset($request->params->team_option)) {

		}	
		if (isset($request->params->ClientID)) {
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('ClientID', $request->params->ClientID);
			else 
				$this->db->where('ClientID', $request->params->ClientID);
		} elseif (isset($request->params->TreePrefix) && isset($request->params->team_option)) {
			if ($request->params->team_option = 'direct') {
				$this->db->where('TreePrefix', $request->params->TreePrefix);	
			} elseif ($request->params->team_option = 'team') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
				$this->db->where('TreePrefix <>', $request->params->TreePrefix);	
			} elseif ($request->params->team_option = 'all') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter team_option'])]];	
			}
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'client parameter'])]];
		}

		if ($request->params->option_date == 'between') { 
			$this->db->where('TrxDate >=', $request->params->from_date, NULL, FALSE);
			$this->db->where('TrxDate <=', $request->params->to_date, NULL, FALSE);
		} elseif ($request->params->option_date == 'last') { 
			$this->db->where('TrxDate <=', $request->params->TrxDate, NULL, FALSE);
		} elseif ($request->params->option_date == 'next') { 
			$this->db->where('TrxDate >=', $request->params->TrxDate, NULL, FALSE);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (isset($request->params->option_order))
			$this->db->order_by($request->params->option_order);
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}


	function load_sum_ccy($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->TrxType1) || empty($request->params->TrxType1)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TrxType1'])]];
		}	
		if (!isset($request->params->CcyID) || empty($request->params->CcyID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CcyID'])]];
		}	
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

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('SUM(TrxAmount) As TrxAmount, SUM(TrxUnit) As TrxUnit, SUM(TrxCost) As TrxCost');
		$this->db->from('ata_transaction');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('CcyID', $request->params->CcyID, NULL, FALSE);
		$this->db->where('TrxType1', $request->params->TrxType1, NULL, FALSE);
		$this->db->where('TrxDate >=', $request->params->from_date, NULL, FALSE);
		$this->db->where('TrxDate <=', $request->params->to_date, NULL, FALSE);
		if (isset($request->params->ClientID)) { 
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('ClientID', $request->params->ClientID);
			else 
				$this->db->where('ClientID', $request->params->ClientID);
		} elseif (isset($request->params->SID)) { 
			$this->db->where('ClientID', $request->params->ClientID, NULL, FALSE);
		} elseif (isset($request->params->TreePrefix) && isset($request->params->option_team)) {
			if ($request->params->option_team = 'direct') {
				$this->db->where('TreePrefix', $request->params->TreePrefix);	
			} elseif ($request->params->option_team = 'team') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
				$this->db->where('TreePrefix <>', $request->params->TreePrefix);	
			} elseif ($request->params->option_team = 'all') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_team'])]];	
			}
		} 
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function search_sum_ccy($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

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

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			 $this->db->select('CcyID, TrxType1, SUM(TrxAmount) As TrxAmount, SUM(TrxUnit) As TrxUnit, SUM(TrxCost) As TrxCost');
		 $this->db->from('ata_transaction');
		 $this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('TrxDate >=', $request->params->from_date, NULL, FALSE);
		$this->db->where('TrxDate <=', $request->params->to_date, NULL, FALSE);
		if (isset($request->params->ClientID)) { 
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('ClientID', $request->params->ClientID);
			else 
				$this->db->where('ClientID', $request->params->ClientID);
		} elseif (isset($request->params->SID)) { 
			$this->db->where('ClientID', $request->params->ClientID, NULL, FALSE);
		} elseif (isset($request->params->TreePrefix) && isset($request->params->option_team)) {
			if ($request->params->option_team = 'direct') {
				$this->db->where('TreePrefix', $request->params->TreePrefix);	
			} elseif ($request->params->option_team = 'team') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
				$this->db->where('TreePrefix <>', $request->params->TreePrefix);	
			} elseif ($request->params->option_team = 'all') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_team'])]];	
			}
		} 
		$this->db->group_by(["CcyID", 'TrxType1']); 
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function history_sum_ccy($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->TrxType1) || empty($request->params->TrxType1)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter TrxType1'])]];
		}	
		if (!isset($request->params->CcyID) || empty($request->params->CcyID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CcyID'])]];
		}	
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

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('TrxDate', 'NAVDate', 'SUM(TrxAmount) As TrxAmount, SUM(TrxUnit) As TrxUnit, SUM(TrxCost) As TrxCost');
		$this->db->from('ata_transaction');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('CcyID', $request->params->CcyID, NULL, FALSE);
		$this->db->where('TrxType1', $request->params->TrxType1, NULL, FALSE);
		$this->db->where('TrxDate >=', $request->params->from_date, NULL, FALSE);
		$this->db->where('TrxDate <=', $request->params->to_date, NULL, FALSE);
		if (isset($request->params->ClientID)) { 
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('ClientID', $request->params->ClientID);
			else 
				$this->db->where('ClientID', $request->params->ClientID);
		} elseif (isset($request->params->SID)) { 
			$this->db->where('ClientID', $request->params->ClientID, NULL, FALSE);
		} elseif (isset($request->params->TreePrefix) && isset($request->params->option_team)) {
			if ($request->params->option_team = 'direct') {
				$this->db->where('TreePrefix', $request->params->TreePrefix);	
			} elseif ($request->params->option_team = 'team') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
				$this->db->where('TreePrefix <>', $request->params->TreePrefix);	
			} elseif ($request->params->option_team = 'all') { 
				$this->db->like('TreePrefix', $request->params->TreePrefix, 'after');
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_team'])]];	
			}
		} 
		$this->db->group_by(["TrxDate", 'NAVDate']); 
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	

}