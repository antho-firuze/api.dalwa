<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Income_balance_model extends CI_Model
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
			$this->db->select('simpiID, PortfolioID, CcyID, AccountID, PositionDate, BankID, BankTypeID, 
								TDTermID, DailyInterest, DailyNonInterest, rDI, rDNI, giDI, giDNI');
		$this->db->from('afa_income_bank_balance');
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
			$sum_sql .= 'SUM(DailyInterest) As DailyInterest, SUM(DailyNonInterest) As DailyNonInterest';
			$this->db->select($sum_sql);
		}
		$this->db->from('afa_income_bank_balance');
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
			$sum_sql = '';
			if (!is_null($groupSelect)) $sum_sql = $groupSelect.', ';
			$sum_sql .= 'AVG(DailyInterest) As DailyInterest, AVG(DailyNonInterest) As DailyNonInterest';
			$this->db->select($sum_sql);
		}
		$this->db->from('afa_income_bank_balance');
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
			if (isset($request->params->BankID) && !empty($request->params->BankID)) 
				$this->db->where('BankID', $request->params->BankID);	
			if (isset($request->params->BankTypeID) && !empty($request->params->BankTypeID)) 
				$this->db->where('BankTypeID', $request->params->BankTypeID);	
			if (isset($request->params->TDTermID) && !empty($request->params->TDTermID)) 
				$this->db->where('TDTermID', $request->params->TDTermID);	
			$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);					
		} else {
			if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
			if (!isset($request->params->AccountID) || empty($request->params->AccountID)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
			if (!isset($request->params->option_date) || empty($request->params->option_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}
			
			list($success, $return) = $this->_sql($request);
			if (!$success) return [FALSE, $return];
			$this->db->where('PortfolioID', $request->params->PortfolioID, NULL, FALSE);	
			$this->db->where('AccountID', $request->params->AccountID, NULL, FALSE);
			if ($request->params->option_date == 'at') {
				$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);
			} elseif ($request->params->option_date == 'before') {
				$this->db->where('PositionDate <', $request->params->PositionDate, NULL, FALSE);
				$this->db->order_by('PositionDate', 'DESC');
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

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		if (isset($request->params->fields) && !empty($request->params->fields)) {
			$this->db->select($request->params->fields);
		} elseif (strtolower($request->params->option_aggregate)=='sum') {
			if (isset($request->params->option_group) && !empty($request->params->option_group)) 
				list($success, $return) = $this->_sum($request, 'CcyID, '.$request->params->option_group);
			else	
				list($success, $return) = $this->_sum($request, 'CcyID');
			if (!$success) return [FALSE, $return];
		} elseif (strtolower($request->params->option_aggregate)=='avg') {
			if (isset($request->params->option_group) && !empty($request->params->option_group)) 
				list($success, $return) = $this->_avg($request, 'CcyID, '.$request->params->option_group);
			else	
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

		if (isset($request->params->BankID) && !empty($request->params->BankID)) 
			$this->db->where('BankID', $request->params->BankID);	
		if (isset($request->params->BankTypeID) && !empty($request->params->BankTypeID)) 
			$this->db->where('BankTypeID', $request->params->BankTypeID);	
		if (isset($request->params->TDTermID) && !empty($request->params->TDTermID)) 
			$this->db->where('TDTermID', $request->params->TDTermID);	
			
		$this->db->where('PositionDate', $request->params->PositionDate, NULL, FALSE);

		if ((strtolower($request->params->option_aggregate)=='sum') || 
		    (strtolower($request->params->option_aggregate)=='avg')) {
			if (isset($request->params->option_group) && !empty($request->params->option_group))
				$this->db->group_by('CcyID, '.$request->params->option_group);
			else 
				$this->db->group_by('CcyID');
		} 
		if (isset($request->params->option_order) && !empty($request->params->option_order)) 
			$this->db->order_by($request->params->option_order);

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
			if (isset($request->params->option_group) && !empty($request->params->option_group)) 
				list($success, $return) = $this->_sum($request, 'CcyID, PositionDate, '.$request->params->option_group);
			else	
				list($success, $return) = $this->_sum($request, 'CcyID, PositionDate');
			if (!$success) return [FALSE, $return];
		} elseif (strtolower($request->params->option_aggregate)=='avg') {
			if (isset($request->params->option_group) && !empty($request->params->option_group)) 
				list($success, $return) = $this->_avg($request, 'CcyID, PositionDate, '.$request->params->option_group);
			else	
				list($success, $return) = $this->_avg($request, 'CcyID, PositionDate');
			if (!$success) return [FALSE, $return];
		} else {
			list($success, $return) = $this->_sql($request);
			if (!$success) return [FALSE, $return];
		}

		if (isset($request->params->BankID) && !empty($request->params->BankID)) 
			$this->db->where('BankID', $request->params->BankID);	
		if (isset($request->params->BankTypeID) && !empty($request->params->BankTypeID)) 
			$this->db->where('BankTypeID', $request->params->BankTypeID);	
		if (isset($request->params->TDTermID) && !empty($request->params->TDTermID)) 
			$this->db->where('TDTermID', $request->params->TDTermID);	

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

			if (isset($request->params->option_group) && !empty($request->params->option_group))
				$this->db->group_by('CcyID, PositionDate, '.$request->params->option_group);
			else 
				$this->db->group_by('CcyID, PositionDate');
		} else {
			if (isset($request->params->AccountID) && !empty($request->params->AccountID)) {
				$this->db->where('AccountID', $request->params->AccountID);
				$this->db->where('PortfolioID', $request->params->PortfolioID);
			} elseif (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
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
		}

		if (isset($request->params->option_order) && !empty($request->params->option_order)) 
			$this->db->order_by($request->params->option_order);

		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}


 

}    