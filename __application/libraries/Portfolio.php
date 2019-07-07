<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio {
	
	public $ci;

    function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database(DATABASE_MASTER);
		$this->ci->load->library('System');
	}

	function check_portfolio($request)
	{
		$ci = $this->ci;
		$ci->db->select('PortfolioID, TypeID');  
		$ci->db->from('master_portfolio');
		$ci->db->where('simpiID', $request->simpi_id);
		if (isset($request->params->PortfolioID) && !empty($request->params->PortfolioID)) {
			$ci->db->where('PortfolioID', $request->params->PortfolioID);
		} elseif (isset($request->params->PortfolioCode) && !empty($request->params->PortfolioCode)) {
			$ci->db->where('PortfolioCode', $request->params->PortfolioCode);
		} else {
			$return = $ci->system->error_data('00-1', $request->LanguageID, 'portfolio parameter');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
		
		$row = $ci->db->get()->row();
		if ($row) {
			$request->params->PortfolioID = $row->PortfolioID;
			$request->params->TypeID = $row->TypeID;
			return [TRUE, NULL];
		} else {
			$return = $ci->system->error_data('00-2', $request->LanguageID, 'master portfolio');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}
	}

	function id_portfolio($request)
	{
		$ci = $this->ci;

		$ci->db->select('CreditPortfolio');  
		$ci->db->from('simpi_credit');
		$ci->db->where('simpiID', $request->simpi_id);
		if ($row = $ci->db->get()->row())  {
			$request->params->CreditPortfolio = $row->CreditPortfolio;
		} else {
			$return = $ci->system->error_data('03-1', $request->LanguageID, 'master portfolio');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$ci->db->from('master_portfolio');
		$ci->db->where('simpiID', $request->simpi_id);
		$ci->db->where('StatusID', 2);
		$request->params->TotalPortfolio = $ci->db->count_all_results();

		if ($request->params->TotalPortfolio >= $request->params->CreditPortfolio){
			$return = $ci->system->error_data('03-1', $request->LanguageID, 'master portfolio');
			return [FALSE, ['message' => $return['message'], 'error' => $return['error']]];		 
		}

		$request->params->PortfolioID  = $ci->system->id_simpi($request->simpi_id, 'MASTER PORTFOLIO', 2);
		return [TRUE, NULL];
	}

}