<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Sector_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
    }

	function sector($request)
	{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			 $this->db->select('SectorID, SectorName');
			$this->db->from('parameter_securities_sector');
			//daftar sector by SectorID: 1 atau lebih
			if (isset($request->params->SectorID)) {
				if (is_array($request->params->SectorID)) 
					 $this->db->where_in('SectorID', $request->params->SectorID);
				else 
					 $this->db->where('SectorID', $request->params->SectorID);
				//daftar sector by SectorName: 1 atau lebih		
				} elseif (isset($request->params->SectorName) && !empty($request->params->SectorName)) {
				 if (is_array($request->params->SectorName))
					 $this->db->where_in('SectorName', $request->params->SectorName);
				 else
					 $this->db->where('SectorName', $request->params->SectorName);
				//daftar sector by keyword & type		
				} else {
				 if (isset($request->params->sector_keyword))
					 $this->db->like('SectorName', $this->db->escape($request->params->sector_keyword));
				}
				$data = $this->f->get_result_paging($request);
				$request->log_type	= 'data';	
				$this->system->save_billing($request);
				return $data;		
	}
				
	function subsector($request)
	{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			 $this->db->select('SubSectorID, SubSectorName');
			$this->db->from('parameter_securities_subsector');
			//daftar sub sector by SubSectorID: 1 atau lebih
			if (isset($request->params->SubSectorID)) {
				if (is_array($request->params->SubSectorID)) 
					 $this->db->where_in('SubSectorID', $request->params->SubSectorID);
				else 
					 $this->db->where('SubSectorID', $request->params->SubSectorID);
				//daftar sub sector by SubSectorName: 1 atau lebih		
				} elseif (isset($request->params->SubSectorName) && !empty($request->params->SubSectorName)) {
				 if (is_array($request->params->SubSectorName))
					 $this->db->where_in('SubSectorName', $request->params->SubSectorName);
				 else
					 $this->db->where('SubSectorName', $request->params->SubSectorName);
			//daftar sub sector by keyword & type		
				} else {
				 if (isset($request->params->subsector_keyword))
					 $this->db->like('SubSectorName', $this->db->escape($request->params->subsector_keyword));
				}
				$data = $this->f->get_result_paging($request);
				$request->log_type	= 'data';	
				$this->system->save_billing($request);
				return $data;		
	}
			
	function class($request)
	{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			 $this->db->select('ClassID, ClassCode, ClassName');
			$this->db->from('parameter_securities_sector_class');
			//daftar sector class by ClassID: 1 atau lebih
			if (isset($request->params->ClassID)) {
				if (is_array($request->params->ClassID)) 
					 $this->db->where_in('ClassID', $request->params->ClassID);
				else 
					 $this->db->where('ClassID', $request->params->ClassID);
				//daftar sector class by ClassCode: 1 atau lebih		
				} elseif (isset($request->params->ClassCode) && !empty($request->params->ClassCode)) {
				 if (is_array($request->params->ClassCode))
					 $this->db->where_in('ClassCode', $request->params->ClassCode);
				 else
					 $this->db->where('ClassCode', $request->params->ClassCode);
				//daftar sector class by keyword & type		
				} else {
				 if (isset($request->params->class_keyword)) {
					 $strKeyword = "(ClassCode LIKE '%".$this->db->escape($request->params->class_keyword)."%'"
											 ." OR ClassName LIKE '%".$this->db->escape($request->params->class_keyword)."%')";
					 $this->db->where($strKeyword);
				 }
				}
				$data = $this->f->get_result_paging($request);
				$request->log_type	= 'data';	
				$this->system->save_billing($request);
				return $data;		
	}
			
	function class_sector($request)
	{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
			//cek parameter: BenchmarkID
	
			if (!isset($request->params->ClassID) || empty($request->params->ClassID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter ClassID'])]];
			}
	
			$this->db->select('T1.SectorID, T2.SectorName');
			$this->db->from('parameter_securities_sector_class_member_sector T1');
			$this->db->join('parameter_securities_sector T2', 'T1.SectorID = T2.SectorID');  
				$this->db->where('T1.ClassID', $request->params->ClassID, NULL, FALSE);
			if (isset($request->params->sector_keyword))
					$this->db->like('T2.SectorName', $this->db->escape($request->params->sector_keyword));
					$data = $this->f->get_result_paging($request);
					$request->log_type	= 'data';	
					$this->system->save_billing($request);
					return $data;		
	}
				
	function class_subsector($request)
	{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
			//cek parameter: BenchmarkID
	
			if (!isset($request->params->ClassID) || empty($request->params->ClassID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter ClassID'])]];
			}
	
			$this->db->select('T1.SectorID, T2.SectorName, T1.SubSectorID, T3.SubSectorName');
			$this->db->from('parameter_securities_sector_class_member_subsector T1');
			$this->db->join('parameter_securities_sector T2', 'T1.SectorID = T2.SectorID');  
			$this->db->join('parameter_securities_subsector T3', 'T1.SubSectorID = T3.SubSectorID');  
			$this->db->where('T1.ClassID', $request->params->ClassID, NULL, FALSE);
			if (isset($request->params->SectorID))
				$this->db->where('T1.SectorID', $request->params->SectorID);
			if (isset($request->params->subsector_keyword))
					$this->db->like('T3.SubSectorName', $this->db->escape($request->params->subsector_keyword));
					$data = $this->f->get_result_paging($request);
					$request->log_type	= 'data';	
					$this->system->save_billing($request);
					return $data;		
	}
				
	function class_company($request)
	{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
			//cek parameter: BenchmarkID
	
			if (!isset($request->params->ClassID) || empty($request->params->ClassID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter ClassID'])]];
			}
	
			//cek pilihan fields
			if (isset($request->params->fields) && !empty($request->params->fields))
				 $this->db->select($request->params->fields);
			else 
				$this->db->select('T1.SectorID, T2.SectorName, T1.SubSectorID, T3.SubSectorName, T1.CompanyID, 
									T4.CompanyCode, T4.CompanyName, T4.TypeID, T5.TypeCode, T5.TypeDescription,   
									T4.CompanyAddress, T4.CompanyPhone, T4.CompanyFax, T4.CompanyWeb, T4.CompanyEmail, 
									T4.IsPrivate, T4.CountryID, T6.CountryCode, T6.CountryName');
			$this->db->from('parameter_securities_sector_class_member_company T1');
			$this->db->join('parameter_securities_sector T2', 'T1.SectorID = T2.SectorID');  
			$this->db->join('parameter_securities_subsector T3', 'T1.SubSectorID = T3.SubSectorID');  
			$this->db->join('market_company T4', 'T1.CompanyID = T4.CompanyID');  
			$this->db->join('parameter_securities_company_type T5', 'T4.TypeID = T5.TypeID');  
			$this->db->join('parameter_securities_country T6', 'T4.CountryID = T6.CountryID');  
			$this->db->where('T1.ClassID', $request->params->ClassID, NULL, FALSE);
			if (isset($request->params->SubSectorID))
				$this->db->where('T1.SubSectorID', $request->params->SubSectorID);
			elseif (isset($request->params->SectorID))
				$this->db->where('T1.SectorID', $request->params->SectorID);
			if (isset($request->params->CountryID))
				$this->db->where('T1.CountryID', $request->params->CountryID);
			if (isset($request->params->TypeID))
				$this->db->where('T4.TypeID', $request->params->TypeID);
			if (isset($request->params->company_keyword)) {
				$strKeyword = "(T4.CompanyCode LIKE '%".$this->db->escape($request->params->company_keyword)."%'"
								." OR T4.CompanyName LIKE '%".$this->db->escape($request->params->company_keyword)."%')";
				$this->db->where($strKeyword);
			}
			$data = $this->f->get_result_paging($request);
			$request->log_type	= 'data';	
			$this->system->save_billing($request);
			return $data;		
	}
		
}    