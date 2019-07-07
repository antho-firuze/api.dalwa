<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Client_bank_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->library('System');
		$this->load->library('Client');
	}

	private function _sql($request)
	{
		if (isset($request->params->fields) && !empty($request->params->fields)) 
		   $this->db->select($request->params->fields);
		else
            $this->db->select('T1.simpiID, T1.ClientID, T1.SalesID, T2.SalesCode, T2.TreePrefix, T2.SInvestCode,  
								T3.BankName, T3.BankCountryID, T3.BankBranch, T3.BankCodeType, 
								T3.AccountNo, T3.AccountName, T3.AccountNotes, T3.AccountCcyID, T3.CreatedAt');  
		$this->db->from('master_client T1');
		$this->db->join('master_sales T2', 'T1.simpiID = T2.simpiID And T1.SalesID = T2.SalesID');  
		$this->db->join('master_client_bankaccount T3', 'T1.simpiID = T3.simpiID And T1.ClientID = T3.ClientID');  
		$this->db->where('T1.simpiID', $request->simpi_id);
	}

	private function _keyword($request)
	{
		if (isset($request->params->TypeID) && !empty($request->params->TypeID)) $this->db->where('T1.TypeID', $request->params->TypeID);
		if (isset($request->params->StatusID) && !empty($request->params->StatusID)) $this->db->where('T1.StatusID', $request->params->StatusID);
		if (isset($request->params->RiskID) && !empty($request->params->RiskID)) $this->db->where('T1.RiskID', $request->params->RiskID);
		if (isset($request->params->LF) && !empty($request->params->LF)) $this->db->where('T1.LF', $request->params->LF);
		if (isset($request->params->client_keyword) && !empty($request->params->client_keyword)) $this->db->like('T1.ClientName', $request->params->client_keyword);
	}
	
	private function _access($request)
	{
		if ($request->log_access == 'license') {
			return [TRUE, NULL];
		} elseif (($request->log_access == 'session') && ($request->TreePrefix == '')) {
			return [TRUE, NULL];
		} elseif ($request->log_access == 'session') {
			$this->db->like('T2.TreePrefix', $request->TreePrefix,'after');
			return [TRUE, NULL];
		} elseif ($request->log_access == 'token') {
			$this->db->where('T1.SID', $request->SID);
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

		if (!isset($request->params->BankName) || empty($request->params->BankName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter BankName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountNo) || empty($request->params->AccountNo)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountNo');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
    
		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T3.BankName', $request->params->BankName);
		$this->db->where('T3.AccountNo', $request->params->AccountNo);
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			$this->db->where('T1.ClientID', $request->params->ClientID);
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			$this->db->where('T1.ClientCode', $request->params->ClientCode);
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			$this->db->where('T1.SID', $request->params->SID);
			$this->db->where('(T1.TypeID = 1 or T1.TypeID = 2)');
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			$this->db->where('T1.IFUA', $request->params->IFUA);
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter client');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function search($request)
	{	
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->bank_keyword) && !empty($request->params->bank_keyword)) 
			$this->db->like('T3.BankName', $request->params->bank_keyword);	
		if (isset($request->params->ClientID) && !empty($request->params->ClientID)) {
			if (is_array($request->params->ClientID)) 
				$this->db->where_in('T1.ClientID', $request->params->ClientID);
			else 
				$this->db->where('T1.ClientID', $request->params->ClientID);	
		} elseif (isset($request->params->ClientCode) && !empty($request->params->ClientCode)) {
			if (is_array($request->params->ClientCode)) 
				$this->db->where_in('T1.ClientCode', $request->params->ClientCode);
			else 
				$this->db->where('T1.ClientCode', $request->params->ClientCode);	
		} elseif (isset($request->params->SID) && !empty($request->params->SID)) {
			if (is_array($request->params->SID)) 
				$this->db->where_in('T1.SID', $request->params->SID);
			else 
				$this->db->where('T1.SID', $request->params->SID);	
		} elseif (isset($request->params->IFUA) && !empty($request->params->IFUA)) {
			if (is_array($request->params->IFUA)) 
				$this->db->where_in('T1.IFUA', $request->params->IFUA);
			else 
				$this->db->where('T1.IFUA', $request->params->IFUA);	
		} else {
			if (isset($request->params->SalesID) && !empty($request->params->SalesID)) {
				if (is_array($request->params->SalesID)) 
					$this->db->where_in('T1.SalesID', $request->params->SalesID);
		 		else 
					$this->db->where('T1.SalesID', $request->params->SalesID);
			}
			$this->_keyword($request);
		}
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function team_direct($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		if (!isset($request->params->SalesID) || empty($request->params->SalesID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter SalesID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T1.SalesID', $request->params->SalesID);
		if (isset($request->params->bank_keyword) && !empty($request->params->bank_keyword)) 
			$this->db->like('T3.BankName', $request->params->bank_keyword);	
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function team_head($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->bank_keyword) && !empty($request->params->bank_keyword)) 
			$this->db->like('T3.BankName', $request->params->bank_keyword);	
		if (isset($request->params->TreeParentID) && !empty($request->params->TreeParentID))
			$this->db->where('T2.TreeParentID', $request->params->TreeParentID);
		else 
			$this->db->where('T2.TreeParentID = 0');		
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}

	function team_member($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->bank_keyword) && !empty($request->params->bank_keyword)) 
			$this->db->like('T3.BankName', $request->params->bank_keyword);	
		if (isset($request->params->TreePrefix) && !empty($request->params->TreePrefix)) {
			$this->db->like('T2.TreePrefix', $request->params->TreePrefix, 'after');
			if (strtolower($request->params->option_without) == 'y') 
				$this->db->where('T2.TreePrefix != ', $request->params->TreePrefix);			
		} else {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'TreePrefix');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}			 		
		$this->_keyword($request);
		list($success, $return) = $this->_access($request);
		if (!$success) return [FALSE, $return];
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}


	private function check_bank($request)
	{
		$this->db->select('AccountNo');  
		$this->db->from('master_client_bankaccount');
		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('ClientID', $request->params->ClientID);			
		$this->db->where('BankName', $request->params->BankName);
		$this->db->where('AccountNo', $request->params->AccountNo);
		$row = $this->db->get()->row();
		if ($row) return TRUE;
		return FALSE;
	}

	function add($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$dbsave = $return;

		//cek parameter: OfficerName< religion 
		list($success, $return) = $this->client->check_client($request);
		if (!$success) return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];	

		if (!isset($request->params->BankName) || empty($request->params->BankName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter BankName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountNo) || empty($request->params->AccountNo)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountNo');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->BankCountryID) || empty($request->params->BankCountryID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter BankCountryID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountCcyID) || empty($request->params->AccountCcyID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountCcyID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountNotes) || empty($request->params->AccountNotes)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountNotes');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->BankCodeType) || empty($request->params->BankCodeType)) 
			$request->params->BankCodeType=1;
		
		if ($this->check_bank($request)) {
			$return = $this->system->error_data('00-3', $request->LanguageID, 'parameter bank account');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		
		$this->db->set('simpiID', $request->simpi_id);
		$content = 'simpiID='.$request->simpi_id; 
		$this->db->set('ClientID', $request->params->ClientID);
		$content .= ', ClientID='.$request->params->ClientID; 
		$this->db->set('BankName', $request->params->BankName);
		$content .= ', BankName='.$request->params->BankName; 
		$this->db->set('AccountNo', $request->params->AccountNo);
		$content .= ', AccountNo='.$request->params->AccountNo; 
		$this->db->set('BankCountryID', $request->params->BankCountryID);
		$content .= ', BankCountryID='.$request->params->BankCountryID; 
		$this->db->set('AccountCcyID', $request->params->AccountCcyID);
		$content .= ', AccountCcyID='.$request->params->AccountCcyID; 
		$this->db->set('AccountNotes', $request->params->AccountNotes);
		$content .= ', AccountNotes='.$request->params->AccountNotes; 
		$this->db->set('BankCodeType', $request->params->BankCodeType);
		$content .= ', BankCodeType='.$request->params->BankCodeType; 
		if (isset($request->params->BankBranch) && !empty($request->params->BankBranch)) {
			$this->db->set('BankBranch', $request->params->BankBranch);
			$content .= ', BankBranch='.$request->params->BankBranch;
		}
		if (isset($request->params->AccountName) && !empty($request->params->AccountName)) {
			$this->db->set('AccountName', $request->params->AccountName);
			$content .= ', AccountName='.$request->params->AccountName;
		}
		$this->db->set('CreatedAt', date('Y-m-d H:i:s'));
		$content .= ', CreatedAt='.date('Y-m-d H:i:s'); 
		$sql[0] = $this->db->get_compiled_insert('master_client_bankaccount');
		 
		$this->db->reset_query();
		list($success, $return) = $this->system->commit_data($request, $sql, 'Add new client bank', $content, $dbsave);
		if (!$success) return [FALSE, $return];
		
		$request->log_size = 0;
		$request->log_type	= 'process';	
		$this->system->save_billing($request);
		return [TRUE, NULL];
	}

	function delete($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$dbsave = $return;

		list($success, $return) = $this->client->check_client($request);
		if (!$success) return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];	

		if (!isset($request->params->BankName) || empty($request->params->BankName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter BankName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->AccountNo) || empty($request->params->AccountNo)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter AccountNo');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		if (! $this->check_bank($request)) {
			$return = $this->system->error_data('00-2', $request->LanguageID, 'parameter bank account');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$content = 'simpiID='.$request->simpi_id; 
		$content .= ', ClientID='.$request->params->ClientID; 
		$content .= ', BankName='.$request->params->BankName; 
		$content .= ', AccountNo='.$request->params->AccountNo; 

		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('ClientID', $request->params->ClientID);			
		$this->db->where('BankName', $request->params->BankName);
		$this->db->where('AccountNo', $request->params->AccountNo);
		$sql[0] = $this->db->get_compiled_delete('master_client_bankaccount');

		$this->db->reset_query();	
		list($success, $return) = $this->system->commit_data($request, $sql, 'Delete client bank', $content, $dbsave);
		if (!$success) return [FALSE, $return];

		$request->log_size = 0;
		$request->log_type	= 'process';	
		$this->system->save_billing($request);
		return [TRUE, NULL];
	}	

}