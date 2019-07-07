<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Unit_balance_model extends CI_Model
{
	private $sql_all = 'simpiID, PortfolioID, ClientID, PositionDate, TreeParentID, TreePrefix, SID, CcyID, UnitBalance, 
						UnitPrice, CostPrice, UnitValue, CostTotal, UnitAdjustment, WealthRatio, GeometricIndex, 
						UnitRate, UnitInvestment, FeeManagement, FeeSales, FeeOther, DailyPL, RetainedEarning';
	private $sql_sum = '';
	private $sql_sum_client = '';

	function __construct() {
		parent::__construct();
		$this->load->library('System');
	}

	function load($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);

		//cek parameter: option_date, PortfolioID, ClientID, PositionDate
		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->PositionDate) || empty($request->params->PositionDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PositionDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	

		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select($this->$sql_all);
		$this->db->from('ata_balance');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('PortfolioID', $request->params->PortfolioID, NULL, FALSE);
		if (isset($request->params->ClientID)) { 
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('ClientID', $request->params->ClientID);
			else 
				$this->db->where('ClientID', $request->params->ClientID);
		} elseif (isset($request->params->SID)) { 
			$this->db->where('ClientID', $request->params->ClientID, NULL, FALSE);
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'client parameter'])]];
		}

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
			$this->db->select('simpiID, PortfolioID, ClientID, PositionDate, TreePrefix, SID, CcyID, UnitBalance, UnitPrice, CostPrice, 
								UnitValue, CostTotal, UnitAdjustment, WealthRatio, GeometricIndex, UnitRate, 
								UnitInvestment, FeeManagement, FeeSales, FeeOther, DailyPL, RetainedEarning');
		$this->db->from('ata_balance');
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
			$this->db->select('simpiID, PortfolioID, ClientID, PositionDate, TreePrefix, SID, CcyID, UnitBalance, UnitPrice, CostPrice, 
								UnitValue, CostTotal, UnitAdjustment, WealthRatio, GeometricIndex, UnitRate, 
								UnitInvestment, FeeManagement, FeeSales, FeeOther, DailyPL, RetainedEarning');
		$this->db->from('ata_balance');
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

	function load_sum_ccy($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

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

		$this->db->select('SUM(UnitBalance) As UnitBalance, SUM(UnitValue) As UnitValue, SUM(CostTotal) As CostTotal,
					 SUM(UnitAdjustment) As UnitAdjustment, SUM(FeeManagement) As FeeManagement, SUM(FeeSales) As FeeSales, 
					 SUM(FeeOther) As FeeOther, SUM(DailyPL) As DailyPL, SUM(RetainedEarning) As RetainedEarning');
		$this->db->from('ata_balance');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('CcyID', $request->params->CcyID, NULL, FALSE);
		$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
		$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
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

		$this->db->select('CcyID, SUM(UnitBalance) As UnitBalance, SUM(UnitValue) As UnitValue, SUM(CostTotal) As CostTotal,
					 SUM(UnitAdjustment) As UnitAdjustment, SUM(FeeManagement) As FeeManagement, SUM(FeeSales) As FeeSales, 
					 SUM(FeeOther) As FeeOther, SUM(DailyPL) As DailyPL, SUM(RetainedEarning) As RetainedEarning');
		$this->db->from('ata_balance');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
		$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
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
		$this->db->group_by("CcyID"); 
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

		$this->db->select('PositionDate, SUM(UnitBalance) As UnitBalance, SUM(UnitValue) As UnitValue, SUM(CostTotal) As CostTotal,
					 SUM(UnitAdjustment) As UnitAdjustment, SUM(FeeManagement) As FeeManagement, SUM(FeeSales) As FeeSales, 
					 SUM(FeeOther) As FeeOther, SUM(DailyPL) As DailyPL, SUM(RetainedEarning) As RetainedEarning');
		$this->db->from('ata_balance');
		$this->db->where('simpiID', $request->simpi_id, NULL, FALSE);
		$this->db->where('CcyID', $request->params->CcyID, NULL, FALSE);
		$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
		$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
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
		$this->db->group_by("PositionDate"); 
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function load_sum_portfolio($request)
	{

	}

	function load_sum_client($request)
	{

	}

	function load_sum_sid($request)
	{

	}

	function load_sum_sales($request)
	{

	}



}