<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Benchmark_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
    }

	function benchmark($request)
	{	
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
	
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else 
			$this->db->select('T1.BenchmarkID, T1.BenchmarkCode, T1.BenchmarkName, T1.BenchmarkDays, 
								T1.BenchmarkCalculationID, T2.BenchmarkCalculationCode, T1.BenchmarkTypeID, T3.BenchmarkTypeCode');
		$this->db->from('parameter_securities_benchmark T1');
		$this->db->join('parameter_securities_benchmarkcalculation T2', 'T1.BenchmarkCalculationID = T2.BenchmarkCalculationID');  
		$this->db->join('parameter_securities_benchmarktype T3', 'T1.BenchmarkTypeID = T3.BenchmarkTypeID');  
		//daftar benchmark by BenchmarkCode: 1 atau lebih
		if (isset($request->params->BenchmarkID)) {
			if (is_array($request->params->BenchmarkID)) 
				$this->db->where_in('T1.BenchmarkID', $request->params->BenchmarkID);
			else 
			 	$this->db->where('T1.BenchmarkID', $request->params->BenchmarkID);
		//daftar benchmark by BenchmarkCode: 1 atau lebih		
		} elseif (isset($request->params->BenchmarkCode) && !empty($request->params->BenchmarkCode)) {
			if (is_array($request->params->BenchmarkCode))
				$this->db->where_in('T1.BenchmarkCode', $request->params->BenchmarkCode);
			else
				$this->db->where('T1.BenchmarkCode', $request->params->BenchmarkCode);
		//daftar benchmark by keyword & type		
		} else {
			if (isset($request->params->BenchmarkCalculationID))
				$this->db->where('T1.BenchmarkCalculationID', $request->params->BenchmarkCalculationID);
			if (isset($request->params->BenchmarkTypeID))
				$this->db->where('T1.BenchmarkTypeID', $request->params->BenchmarkTypeID);
			if (isset($request->params->benchmark_keyword)) {
				$strKeyword = "(T1.BenchmarkCode LIKE '%".$this->db->escape($request->params->benchmark_keyword)."%'"
							." OR T1.BenchmarkName LIKE '%".$this->db->escape($request->params->benchmark_keyword)."%')";
				$this->db->where($strKeyword);
			}
		}
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
	}
			
		function benchmark_id($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: BenchmarkCode
			if (!isset($request->params->BenchmarkCode) || empty($request->params->BenchmarkCode)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkCode'])]];
			}
	
			$row = $this->db->get_where('parameter_securities_benchmark', ['BenchmarkCode' => $request->params->BenchmarkCode], 1)->row();
			if (!$row) {
				list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-2'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->BenchmarkCode])]];
			}
	
			$request->log_size = mb_strlen(serialize($row), '8bit');
			$request->log_type	= 'data';	
			$this->system->save_billing($request);
	
			return [TRUE, ['result' => ['BenchmarkID' => $row->BenchmarkID]]];
		}
	
		function benchmark_code($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: BenchmarkID
			if (!isset($request->params->BenchmarkID) || empty($request->params->BenchmarkID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkID'])]];
			}
	
			$row = $this->db->get_where('parameter_securities_benchmark', ['BenchmarkID' => $request->params->BenchmarkID], 1)->row();
			if (!$row) {
				list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-2'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => $request->params->BenchmarkID])]];
			}
	
			$request->log_size = mb_strlen(serialize($row), '8bit');
			$request->log_type	= 'data';	
			$this->system->save_billing($request);
	
			return [TRUE, ['result' => ['BenchmarkCode' => $row->BenchmarkCode]]];
		}
	
		function external($request)
		{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
			//cek parameter: BenchmarkID
	
			if (!isset($request->params->BenchmarkID) || empty($request->params->BenchmarkID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkID'])]];
			}
	
			$this->db->select('T1.SystemID, T2.SystemCode, T2.SystemName, T1.BenchmarkExternalCode');
				 $this->db->from('parameter_securities_benchmark_id_external T1');
			 $this->db->join('parameter_securities_externalsystem T2', 'T1.SystemID = T2.SystemID');  
				$this->db->where('T1.BenchmarkID', $request->params->BenchmarkID, NULL, FALSE);
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
		}
	
		function external_get($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: BenchmarkCode
			if (!isset($request->params->BenchmarkCode) || empty($request->params->BenchmarkCode)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkCode'])]];
			}
	
			//cek parameter: SystemID --> sumber external identification 
			if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
			}
	
			$this->db->select('T1.BenchmarkExternalCode');
			$this->db->from('parameter_securities_benchmark_id_external T1');
			$this->db->join('parameter_securities_benchmark T2', 'T1.BenchmarkID = T2.BenchmarkID');  
			$this->db->where('T1.SystemID', $request->params->SystemID);
			$this->db->where('T2.BenchmarkCode', $request->BenchmarkCode);
			$row = $this->db->get()->row();
					if (!$row) {
				list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-2'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'benchmark'])]];
					}
	
			$request->log_size = mb_strlen(serialize($row), '8bit');
			$request->log_type	= 'data';	
			$this->system->save_billing($request);
	
			return [TRUE, ['result' => ['T1.BenchmarkExternalCode' => $row->BenchmarkExternalCode]]];
		}
	
		function external_code($request)
		{
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			//cek parameter: BenchmarkExternalCode
			if (!isset($request->params->BenchmarkExternalCode) || empty($request->params->BenchmarkExternalCode)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkExternalCode'])]];
			}
	
			//cek parameter: SystemID --> sumber external identification 
			if (!isset($request->params->SystemID) || empty($request->params->SystemID)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SystemID'])]];
			}
	
			$this->db->select('T2.BenchmarkCode');
			$this->db->from('parameter_securities_benchmark_id_external T1');
			$this->db->join('parameter_securities_benchmark T2', 'T1.BenchmarkID = T2.BenchmarkID');  
			$this->db->where('T1.SystemID', $request->params->SystemID);
			$this->db->where('T1.BenchmarkExternalCode', $request->BenchmarkExternalCode);
			$row = $this->db->get()->row();
					if (!$row) {
				list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-2'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'benchmark'])]];
					}
	
			$request->log_size = mb_strlen(serialize($row), '8bit');
			$request->log_type	= 'data';	
			$this->system->save_billing($request);
	
			return [TRUE, ['result' => ['T2.BenchmarkCode' => $row->BenchmarkCode]]];
		}
	
		function class($request)
		{	
			//cek akses: by 4 method
			list($success, $return) = $this->system->is_valid_access4($request);
			if (!$success) return [FALSE, $return];
	
			$this->db->select('ClassID, ClassCode, ClassName');
			$this->db->from('parameter_securities_benchmark_class');
			//daftar benchmark class by ClassID: 1 atau lebih
			if (isset($request->params->ClassID)) {
				if (is_array($request->params->ClassID)) 
					 $this->db->where_in('ClassID', $request->params->ClassID);
				else 
					 $this->db->where('ClassID', $request->params->ClassID);
			 //daftar benchmark class by ClassCode: 1 atau lebih		
			 } elseif (isset($request->params->ClassCode) && !empty($request->params->ClassCode)) {
				 if (is_array($request->params->ClassCode))
					 $this->db->where_in('ClassCode', $request->params->ClassCode);
				 else
					 $this->db->where('ClassCode', $request->params->ClassCode);
			 //daftar benchmark class by keyword & type		
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
		 
		function composition($request)
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
				$this->db->select('T1.BenchmarkID, T1.BenchmarkCode, T1.BenchmarkName, T1.BenchmarkDays, 
							T1.BenchmarkCalculationID, T2.BenchmarkCalculationCode, T1.BenchmarkTypeID, T3.BenchmarkTypeCode');
			$this->db->from('parameter_securities_benchmark T1');
			$this->db->join('parameter_securities_benchmarkcalculation T2', 'T1.BenchmarkCalculationID = T2.BenchmarkCalculationID');  
			$this->db->join('parameter_securities_benchmarktype T3', 'T1.BenchmarkTypeID = T3.BenchmarkTypeID');  
			$this->db->join('parameter_securities_benchmark_class_composition T4', 'T1.BenchmarkID = T4.BenchmarkID');  
				$this->db->where('T4.ClassID', $request->params->ClassID);
				$data = $this->f->get_result_paging($request);
				$request->log_type	= 'data';	
				$this->system->save_billing($request);
				return $data;		
			}
			
}    