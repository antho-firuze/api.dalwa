<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Unit_detail_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->database(DATABASE_INVEST);
		$this->load->library('System');
    }

	function load($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: PortfolioID, ClientID, PositionDate
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
		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PortfolioID'])]];
		}	
		if (!isset($request->params->ClientID) || empty($request->params->ClientID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter ClientID'])]];
		}	
		if (!isset($request->params->AcqNo) || empty($request->params->AcqNo)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter AcqNo'])]];
		}	

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('simpiID, PortfolioID, ClientID, PositionDate, AcqNo, TreePrefix, AcqDate, AcqUnit, 
								AcqPrice, AcqTotal, FeeManagement, FeeSales, FeeOther, DailyPL, RetainedEarning');
		$this->db->from('ata_balance_detail');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('PortfolioID', $request->params->PortfolioID, NULL, FALSE);
		$this->db->where('ClientID', $request->params->ClientID, NULL, FALSE);
		$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
		$this->db->where('AcqNo', $request->params->AcqNo, NULL, FALSE);
		$request->params->limit = 1;
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function search($request)
	{	
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: PositionDate
		if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter PositionDate'])]];
		}	
		
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('simpiID, PortfolioID, ClientID, PositionDate, AcqNo, TreePrefix, AcqDate, AcqUnit, 
								AcqPrice, AcqTotal, FeeManagement, FeeSales, FeeOther, DailyPL, RetainedEarning');
		$this->db->from('ata_balance_detail');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
		if (isset($request->params->PortfolioID)) {
			if (is_array($request->params->PortfolioID)) 
				$this->db->where_in('PortfolioID', $request->params->PortfolioID);
			else 
				$this->db->where('PortfolioID', $request->params->PortfolioID);
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
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function history($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: option_date, PositionDate, from & to
		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}
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
			$this->db->select('simpiID, PortfolioID, ClientID, PositionDate, AcqNo, TreePrefix, AcqDate, AcqUnit, 
								AcqPrice, AcqTotal, FeeManagement, FeeSales, FeeOther, DailyPL, RetainedEarning');
		$this->db->from('ata_balance_detail');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		if (isset($request->params->PortfolioID)) {
			if (is_array($request->params->PortfolioID)) 
				$this->db->where_in('PortfolioID', $request->params->PortfolioID);
			else 
				$this->db->where('PortfolioID', $request->params->PortfolioID);
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