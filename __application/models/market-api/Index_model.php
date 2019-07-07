<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Index_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
    }

	function index($request)
	{	
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
	
		$str = "T1.IndexID, T1.IndexCode, T1.IndexName, T1.BenchmarkID, T2.TypeID, T2.TypeCode";	 
		$this->db->select($str);
		$this->db->from('parameter_securities_index T1');
		$this->db->join('parameter_securities_indextype T2', 'T1.TypeID = T2.TypeID');  
		if (isset($request->params->IndexID)) {
			if (is_array($request->params->IndexID)) 
				$this->db->where_in('T1.IndexID', $request->params->IndexID);
			else 
				$this->db->where('T1.IndexID', $request->params->IndexID);
		} elseif (isset($request->params->IndexCode) && !empty($request->params->IndexCode)) {
			if (is_array($request->params->IndexCode))
				$this->db->where_in('T1.IndexCode', $request->params->IndexCode);
			else
				$this->db->where('T1.IndexCode', $request->params->IndexCode);
		} else {
			if (isset($request->params->index_keyword)) {
				$strKeyword = "(T1.IndexCode LIKE '%".this->db->escape($request->params->index_keyword)."%'"
							." OR T1.IndexName LIKE '%".this->db->escape($request->params->index_keyword)."%')";
				$this->db->where($strKeyword);
			}
		}
		$data = $this->f->get_result_paging($request);
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		return $data;		
	}
			
}    