<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Bank_interest_model extends CI_Model
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
            $this->db->select('simpiID, PortfolioID, CcyID, AccountID, InterestNo, TaxID, BankID, TDTermID, 
            InterestDate, InterestDay, InterestAmount, ReceiveDate, ReceiveAmount, ReceiveTax, ReceiveCharges');
		$this->db->from('afa_bankaccount_interest');
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

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountID) || empty($request->params->AccountID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->option_interest) || empty($request->params->option_interest)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter option_interest');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
        if (!$success) return [FALSE, $return];
        $this->db->where('PortfolioID', $request->params->PortfolioID);
        $this->db->where('AccountID', $request->params->AccountID);
		if ($request->params->option_interest == 'last') { 
			$this->db->where('ReceiveAmount = 0');
			$this->db->order_by('InterestDate ASC');
		} elseif ($request->params->option_interest == 'end') { 
			$this->db->order_by('InterestDate DESC');
		} else {
            if (isset($request->params->InterestNo) && !empty($request->params->InterestNo)) {
                $this->db->where('InterestNo', $request->params->InterestNo);
            } else {
                $return = $this->system->error_data('00-1', $request->LanguageID, 'parameter InterestNo');
                return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
            }    
        }
        $request->params->limit = 1;
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function received($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->PortfolioID) || empty($request->params->PortfolioID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountID) || empty($request->params->AccountID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
        if (!$success) return [FALSE, $return];
        $this->db->where('PortfolioID', $request->params->PortfolioID);
        $this->db->where('AccountID', $request->params->AccountID);
        $this->db->where('ReceiveAmount > 0');
        $this->db->order_by('InterestDate DESC');

		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function search($request)
	{
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'invest');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		list($success, $return) = $this->_sql($request);
        if (!$success) return [FALSE, $return];
        if (isset($request->params->AccountID) && !empty($request->params->AccountID)) {
            if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
                $this->db->where('PortfolioID', $request->params->PortfolioID);
                $this->db->where('AccountID', $request->params->AccountID);
                $this->db->order_by('InterestNo ASC');
            } else {
                $return = $this->system->error_data('00-1', $request->LanguageID, 'parameter PortfolioID');
                return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
            }           
        } else {
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
			if (isset($request->params->option_order) && !empty($request->params->option_order)) 
				$this->db->order_by($request->params->option_order);			
		}
		        
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

}    