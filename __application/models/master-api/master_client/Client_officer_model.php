<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Client_officer_model extends CI_Model
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
            $this->db->select('T1.simpiID, T1.ClientID, T1.SalesID, T2.SalesCode, T2.TreePrefix, T2.SInvestCode, T3.OfficerName, T3.OfficerTitle, T3.OfficerPhone, 
                    T3.OfficerEmail, T3.OfficerBirthDate, T3.OfficerReligionID, T3.OfficerSpouseName, 
                    T3.OfficerSpouseBirthDate, T3.OfficerSpouseAnniversary, T3.IDCardTypeID, T3.IDCardNo, 
                    T3.IDCardIssuer, T3.IDCardIsExpired, T3.IDCardExpired');  
		$this->db->from('master_client T1');
		$this->db->join('master_sales T2', 'T1.simpiID = T2.simpiID And T1.SalesID = T2.SalesID');  
		$this->db->join('master_client_institution_officer T3', 'T1.simpiID = T3.simpiID And T1.ClientID = T3.ClientID');  
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

		if (!isset($request->params->OfficerName) || empty($request->params->OfficerName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter OfficerName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
    
		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		$this->db->where('T3.OfficerName', $request->params->OfficerName);
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
		if (isset($request->params->officer_keyword) && !empty($request->params->officer_keyword)) 
			$this->db->like('T3.OfficerName', $request->params->officer_keyword);
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
		if (isset($request->params->officer_keyword) && !empty($request->params->officer_keyword)) 
			$this->db->like('T3.OfficerName', $request->params->officer_keyword);
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

	function team_head($request)
	{
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		list($success, $return) =  $this->system->database_server($request, 'master');
		if (!$success) return [FALSE, $return];
		$this->load->database($return);
		$this->_sql($request);
		if (isset($request->params->officer_keyword) && !empty($request->params->officer_keyword)) 
			$this->db->like('T3.OfficerName', $request->params->officer_keyword);
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
		if (isset($request->params->officer_keyword) && !empty($request->params->officer_keyword)) 
			$this->db->like('T3.OfficerName', $request->params->officer_keyword);
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

	private function check_officer($request)
	{
		$this->db->select('OfficerName');  
		$this->db->from('master_client_institution_officer');
		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('ClientID', $request->params->ClientID);			
		$this->db->where('OfficerName', $request->params->OfficerName);
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

		if ($request->params->TypeID <> 2) {
			$return = $this->system->error_data('05-1', $request->LanguageID, $request->params->TypeID);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}	 
		if (!isset($request->params->OfficerName) || empty($request->params->OfficerName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter OfficerName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->OfficerReligionID) || empty($request->params->OfficerReligionID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter OfficerReligionID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->IDCardTypeID) || empty($request->params->IDCardTypeID)) 
			$request->params->IDCardTypeID=1;
		
		if ($this->check_officer($request)) {
			$return = $this->system->error_data('00-3', $request->LanguageID, $request->params->OfficerName);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		
		$this->db->set('simpiID', $request->simpi_id);
		$content = 'simpiID='.$request->simpi_id; 
		$this->db->set('ClientID', $request->params->ClientID);
		$content .= ', ClientID='.$request->params->ClientID; 
		$this->db->set('OfficerName', $request->params->OfficerName);
		$content .= ', OfficerName='.$request->params->OfficerName; 
		if (isset($request->params->OfficerTitle) && !empty($request->params->OfficerTitle)) {
			$this->db->set('OfficerTitle', $request->params->OfficerTitle);
			$content .= ', OfficerTitle='.$request->params->OfficerTitle;
		}
		if (isset($request->params->OfficerPhone) && !empty($request->params->OfficerPhone)) {
			$this->db->set('OfficerPhone', $request->params->OfficerPhone);
			$content .= ', OfficerPhone='.$request->params->OfficerPhone;
		}
		if (isset($request->params->OfficerEmail) && !empty($request->params->OfficerEmail)) {
			$this->db->set('OfficerEmail', $request->params->OfficerEmail);
			$content .= ', OfficerEmail='.$request->params->OfficerEmail;
		}
		if (isset($request->params->OfficerBirthDate) && !empty($request->params->OfficerBirthDate)) {
			$this->db->set('OfficerBirthDate', $request->params->OfficerBirthDate);
			$content .= ', OfficerBirthDate='.$request->params->OfficerBirthDate;
		}
		$this->db->set('OfficerReligionID', $request->params->OfficerReligionID);
		$content .= ', OfficerReligionID='.$request->params->OfficerReligionID;
		if (isset($request->params->OfficerSpouseName) && !empty($request->params->OfficerSpouseName)) {
			$this->db->set('OfficerSpouseName', $request->params->OfficerSpouseName);
			$content .= ', OfficerSpouseName='.$request->params->OfficerSpouseName;
		}
		if (isset($request->params->OfficerSpouseBirthDate) && !empty($request->params->OfficerSpouseBirthDate)) {
			$this->db->set('OfficerSpouseBirthDate', $request->params->OfficerSpouseBirthDate);
			$content .= ', OfficerSpouseBirthDate='.$request->params->OfficerSpouseBirthDate;
		}
		if (isset($request->params->OfficerSpouseAnniversary) && !empty($request->params->OfficerSpouseAnniversary)) {
			$this->db->set('OfficerSpouseAnniversary', $request->params->OfficerSpouseAnniversary);
			$content .= ', OfficerSpouseAnniversary='.$request->params->OfficerSpouseAnniversary;
		}
		$this->db->set('IDCardTypeID', $request->params->IDCardTypeID);
		$content .= ', IDCardTypeID='.$request->params->IDCardTypeID;
		if (isset($request->params->IDCardNo) && !empty($request->params->IDCardNo)) {
			$this->db->set('IDCardNo', $request->params->IDCardNo);
			$content .= ', IDCardNo='.$request->params->IDCardNo;
		}
		if (isset($request->params->IDCardIssuer) && !empty($request->params->IDCardIssuer)) {
			$this->db->set('IDCardIssuer', $request->params->IDCardIssuer);
			$content .= ', IDCardIssuer='.$request->params->IDCardIssuer;
		}
		if (isset($request->params->IDCardIsExpired) && !empty($request->params->IDCardIsExpired)) {
			$this->db->set('IDCardIsExpired', $request->params->IDCardIsExpired);
			$content .= ', IDCardIsExpired='.$request->params->IDCardIsExpired;
		}
		if (isset($request->params->IDCardExpired) && !empty($request->params->IDCardExpired)) {
			$this->db->set('IDCardExpired', $request->params->IDCardExpired);
			$content .= ', IDCardExpired='.$request->params->IDCardExpired;
		}
		$sql[0] = $this->db->get_compiled_insert('master_client_institution_officer');
		 
		$this->db->reset_query();
		list($success, $return) = $this->system->commit_data($request, $sql, 'Add new client officer', $content, $dbsave);
		if (!$success) return [FALSE, $return];
		
		$request->log_size = 0;
		$request->log_type	= 'process';	
		$this->system->save_billing($request);
		return [TRUE, NULL];
	}

	function edit($request)
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

		if (!isset($request->params->OfficerName) || empty($request->params->OfficerName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter OfficerName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->OfficerReligionID) || empty($request->params->OfficerReligionID)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter OfficerReligionID');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		if (!isset($request->params->IDCardTypeID) || empty($request->params->IDCardTypeID)) 
			$request->params->IDCardTypeID=1;

		if (! $this->check_officer($request)) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->OfficerName);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$content = 'simpiID='.$request->simpi_id; 
		$content .= ', ClientID='.$request->params->ClientID; 
		$content .= ', OfficerName='.$request->params->OfficerName; 

		if (isset($request->params->NewOfficerName) && !empty($request->params->NewOfficerName)) {
			$this->db->set('OfficerName', $request->params->NewOfficerName);
			$content .= ', NewOfficerName='.$request->params->NewOfficerName;
		}
		if (isset($request->params->OfficerTitle) && !empty($request->params->OfficerTitle)) {
			$this->db->set('OfficerTitle', $request->params->OfficerTitle);
			$content .= ', OfficerTitle='.$request->params->OfficerTitle;
		}
		if (isset($request->params->OfficerPhone) && !empty($request->params->OfficerPhone)) {
			$this->db->set('OfficerPhone', $request->params->OfficerPhone);
			$content .= ', OfficerPhone='.$request->params->OfficerPhone;
		}
		if (isset($request->params->OfficerEmail) && !empty($request->params->OfficerEmail)) {
			$this->db->set('OfficerEmail', $request->params->OfficerEmail);
			$content .= ', OfficerEmail='.$request->params->OfficerEmail;
		}
		if (isset($request->params->OfficerBirthDate) && !empty($request->params->OfficerBirthDate)) {
			$this->db->set('OfficerBirthDate', $request->params->OfficerBirthDate);
			$content .= ', OfficerBirthDate='.$request->params->OfficerBirthDate;
		}
		$this->db->set('OfficerReligionID', $request->params->OfficerReligionID);
		$content .= ', OfficerReligionID='.$request->params->OfficerReligionID;
		if (isset($request->params->OfficerSpouseName) && !empty($request->params->OfficerSpouseName)) {
			$this->db->set('OfficerSpouseName', $request->params->OfficerSpouseName);
			$content .= ', OfficerSpouseName='.$request->params->OfficerSpouseName;
		}
		if (isset($request->params->OfficerSpouseBirthDate) && !empty($request->params->OfficerSpouseBirthDate)) {
			$this->db->set('OfficerSpouseBirthDate', $request->params->OfficerSpouseBirthDate);
			$content .= ', OfficerSpouseBirthDate='.$request->params->OfficerSpouseBirthDate;
		}
		if (isset($request->params->OfficerSpouseAnniversary) && !empty($request->params->OfficerSpouseAnniversary)) {
			$this->db->set('OfficerSpouseAnniversary', $request->params->OfficerSpouseAnniversary);
			$content .= ', OfficerSpouseAnniversary='.$request->params->OfficerSpouseAnniversary;
		}
		$this->db->set('IDCardTypeID', $request->params->IDCardTypeID);
		$content .= ', IDCardTypeID='.$request->params->IDCardTypeID;
		if (isset($request->params->IDCardNo) && !empty($request->params->IDCardNo)) {
			$this->db->set('IDCardNo', $request->params->IDCardNo);
			$content .= ', IDCardNo='.$request->params->IDCardNo;
		}
		if (isset($request->params->IDCardIssuer) && !empty($request->params->IDCardIssuer)) {
			$this->db->set('IDCardIssuer', $request->params->IDCardIssuer);
			$content .= ', IDCardIssuer='.$request->params->IDCardIssuer;
		}
		if (isset($request->params->IDCardIsExpired) && !empty($request->params->IDCardIsExpired)) {
			$this->db->set('IDCardIsExpired', $request->params->IDCardIsExpired);
			$content .= ', IDCardIsExpired='.$request->params->IDCardIsExpired;
		}
		if (isset($request->params->IDCardExpired) && !empty($request->params->IDCardExpired)) {
			$this->db->set('IDCardExpired', $request->params->IDCardExpired);
			$content .= ', IDCardExpired='.$request->params->IDCardExpired;
		}
		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('ClientID', $request->params->ClientID);			
		$this->db->where('OfficerName', $request->params->OfficerName);
		$sql[0] = $this->db->get_compiled_update('master_client_institution_officer');

		$this->db->reset_query();	
		list($success, $return) = $this->system->commit_data($request, $sql, 'Edit client officer', $content, $dbsave);
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

		//cek parameter: OfficerName< religion 
		if (!isset($request->params->OfficerName) || empty($request->params->OfficerName)) {
			$return = $this->system->error_data('00-1', $request->LanguageID, 'parameter OfficerName');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		if (! $this->check_officer($request)) {
			$return = $this->system->error_data('00-2', $request->LanguageID, $request->params->OfficerName);
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$content = 'simpiID='.$request->simpi_id; 
		$content .= ', ClientID='.$request->params->ClientID; 
		$content .= ', OfficerName='.$request->params->OfficerName; 

		$this->db->where('simpiID', $request->simpi_id);
		$this->db->where('ClientID', $request->params->ClientID);			
		$this->db->where('OfficerName', $request->params->OfficerName);
		$sql[0] = $this->db->get_compiled_delete('master_client_institution_officer');

		$this->db->reset_query();	
		list($success, $return) = $this->system->commit_data($request, $sql, 'Delete client officer', $content, $dbsave);
		if (!$success) return [FALSE, $return];

		$request->log_size = 0;
		$request->log_type	= 'process';	
		$this->system->save_billing($request);
		return [TRUE, NULL];
	}	

}    