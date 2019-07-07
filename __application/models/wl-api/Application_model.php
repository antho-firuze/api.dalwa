<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Application_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}

	/**
	 * Menampilkan data running text, terurut berdasarkan type & ID
	 * type: 1 = portfolio nav, return & benchmark
	 * type: 2 = market benchmark
	 * type: 3 = input running text
	 * id: PortfolioID, BenchmarkID
	 * display only: running_text
	 */
	function running_text1($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
				
		$table = "(SELECT RunningText FROM mobc_running_text) g0 ";
		$this->db->from($table)
			->where(['AppsID' => $request->AppsID], NULL, FALSE)
			->order_by('RunningType', 'RunningID');
		return $this->f->get_result();
	}


}
