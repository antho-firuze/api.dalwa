<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Portfolio_mtm_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->library('System');
    }

	private function _sql($request)
	{
		$subQuery =  'Select PortfolioID From system_access_portfolio 
					Where UserID = '.$request->user_id.' And simpiID = '.$request->simpi_id;

		$this->db->select('simpiID, PortfolioID, CcyID, PositionDate, NextDate, FlagDate, IsLast');
		$this->db->from('afa_mtm');
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

		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->as_date) || empty($request->params->as_date)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter as_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
		if (!isset($request->params->position_or_next) || empty($request->params->position_or_next)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter position_or_next');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
		if (!$success) return [FALSE, $return];
		if ($request->params->option_date == 'at') {
			if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
				$this->db->where('PortfolioID', $request->params->PortfolioID, NULL, FALSE);
			} else {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
		} else {
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
			}	 
		}	

		if ($request->params->option_date == 'at') {
			if ($request->params->position_or_next == 'next') 
				$this->db->where('NextDate', $request->params->as_date, NULL, FALSE);
			else 
				$this->db->where('PositionDate', $request->params->as_date, NULL, FALSE);
		} elseif ($request->params->option_date == 'last') {
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate <=', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('NextDate', 'DESC');
			} else {
				$this->db->where('PositionDate <=', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('PositionDate', 'DESC');
			}
		} elseif ($request->params->option_date == 'before') {
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate <', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('NextDate', 'DESC');
			} else {
				$this->db->where('PositionDate <', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('PositionDate', 'DESC');
			}
		} elseif ($request->params->option_date == 'next') {
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate >=', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('NextDate', 'ASC');
			} else {
				$this->db->where('PositionDate >=', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('PositionDate', 'ASC');
			}
		} elseif ($request->params->option_date == 'after') {
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate >', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('NextDate', 'ASC');	
			} else {
				$this->db->where('PositionDate >', $request->params->as_date, NULL, FALSE);
				$this->db->order_by('PositionDate', 'ASC');					
			}
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
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->as_date) || empty($request->params->as_date)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter as_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	
		if (!isset($request->params->FlagDate) || empty($request->params->FlagDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FlagDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->position_or_next) || empty($request->params->position_or_next)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter position_or_next');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		
		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
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
		}	 

		$date_from = date_create($request->params->as_date);
		if ($request->params->FlagDate == 1) {
			$this->db->where('(FlagDate = 1 or FlagDate = 2 or FlagDate = 3)');
			$date_from->modify('-1 month');
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate >=', $date_from, NULL, FALSE);
				$this->db->where('NextDate <=', $request->params->as_date, NULL, FALSE);
			} else {
				$this->db->where('PositionDate >=', $date_from, NULL, FALSE);
				$this->db->where('PositionDate <=', $request->params->as_date, NULL, FALSE);
			}
		} elseif ($request->params->FlagDate == 2) { 
			$this->db->where('(FlagDate = 2 or FlagDate = 3)');
			$date_from->modify('-3 months');
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate >=', $date_from, NULL, FALSE);
				$this->db->where('NextDate <=', $request->params->as_date, NULL, FALSE);
			} else {
				$this->db->where('PositionDate >=', $date_from, NULL, FALSE);
				$this->db->where('PositionDate <=', $request->params->as_date, NULL, FALSE);
			}
		} elseif ($request->params->FlagDate == 3) { 
			$this->db->where('FlagDate = 3');
			$date_from->modify('-1 year');
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate >=', $date_from, NULL, FALSE);
				$this->db->where('NextDate <=', $request->params->as_date, NULL, FALSE);
			} else {
				$this->db->where('PositionDate >=', $date_from, NULL, FALSE);
				$this->db->where('PositionDate <=', $request->params->as_date, NULL, FALSE);
			}
		} else {
			if ($request->params->position_or_next == 'next') 
				$this->db->where('NextDate', $request->params->as_date, NULL, FALSE);
			else 
				$this->db->where('PositionDate', $request->params->as_date, NULL, FALSE);
		}
		$this->db->order_by('CcyID ASC, PortfolioID ASC');				
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
		if (!isset($request->params->FlagDate) || empty($request->params->FlagDate)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter FlagDate');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->position_or_next) || empty($request->params->position_or_next)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter position_or_next');
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
			if (!isset($request->params->as_date) || empty($request->params->as_date)) {
				$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter as_date');
				return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
			}	
		}
		
		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
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
		}	 

	  	if ($request->params->FlagDate == 1) {
        	$this->db->where('(FlagDate = 1 or FlagDate = 2 or FlagDate = 3)');
		} elseif ($request->params->FlagDate == 2) {
        	$this->db->where('(FlagDate = 2 or FlagDate = 3)');
		} elseif ($request->params->FlagDate == 3) { 
        	$this->db->where('FlagDate = 3');
    	} 

		if ($request->params->option_date == 'between') { 
			if ($request->params->position_or_next == 'next') {
				$this->db->where('NextDate >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('NextDate <=', $request->params->to_date, NULL, FALSE);
			} else {
				$this->db->where('PositionDate >=', $request->params->from_date, NULL, FALSE);
				$this->db->where('PositionDate <=', $request->params->to_date, NULL, FALSE);
			}
		} elseif ($request->params->option_date == 'last') { 
			if ($request->params->position_or_next == 'next') 
				$this->db->where('NextDate <=', $request->params->as_date, NULL, FALSE);
			else 
				$this->db->where('PositionDate <=', $request->params->as_date, NULL, FALSE);
		} elseif ($request->params->option_date == 'next') { 
			if ($request->params->position_or_next == 'next') 
				$this->db->where('NextDate >=', $request->params->as_date, NULL, FALSE);
			else 
				$this->db->where('PositionDate >=', $request->params->as_date, NULL, FALSE);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_date');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		if (isset($request->params->option_order_bydate) && !empty($request->params->option_order_bydate)) {
			if (strtolower($request->params->option_order_bydate)=='y') 
				$this->db->order_by('PositionDate ASC, CcyID ASC, PortfolioID ASC');
			else 
				$this->db->order_by('CcyID ASC, PortfolioID ASC, PositionDate ASC');			
		} else {
			$this->db->order_by('CcyID ASC, PortfolioID ASC, PositionDate ASC');	
		}	

		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
 
}