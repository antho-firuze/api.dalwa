<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Simple_fund_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->library('System');
    }

  function portfolio_master($request)
  {
		$this->load->database(DATABASE_SYSTEM);
		list($success, $return) = $this->system->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];

		$this->load->database(DATABASE_MASTER);
		$this->db->select('PortfolioID, PortfolioCode, PortfolioNameShort');
		$this->db->from('master_portfolio');
		$this->db->where('simpiID', $request->simpi_id);
		$qry = $this->db->get();
		$portfolio = $qry->result();

		$this->load->database(DATABASE_INVEST);
		$this->db->select('PortfolioID, NAV');
		$this->db->from('afa_nav');
		$this->db->where('PositionDate', $request->params->PositionDate);
		$qry2 = $this->db->get();
		$nav = $qry2->result();
		
		$result = from($portfolio)
    				->groupJoin(
								from($nav), 
								'$a ==> $a->PortfolioID', 
								'$b ==> $b->PortfolioID',
								'($a, $b) ==> array(
										"id" => $a->PortfolioID,
										"id2" => $b->PortfolioID,
										"code" => $a->PortfolioCode,
										"nav" => $b->NAV)'
								)->toList();
		$data = $this->f->get_result_yalinqo($request, $result);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	 
	function portfolio_master2($request)
	{
		  $this->load->database(DATABASE_SYSTEM);
		  list($success, $return) = $this->system->is_valid_appcode($request);
		  if (!$success) return [FALSE, $return];
  
		  $this->load->database(DATABASE_MASTER);
		  $this->db->select('PortfolioID, PortfolioCode, PortfolioNameShort');
		  $this->db->from('master_portfolio');
		  $this->db->where('simpiID', $request->simpi_id);
		  $qry = $this->db->get();
		  $portfolio = $qry->result();
  
		  $this->load->database(DATABASE_INVEST);
		  $this->db->select('PortfolioID, NAV');
		  $this->db->from('afa_nav');
		  $this->db->where('PositionDate', $request->params->PositionDate);
		  $qry2 = $this->db->get();
		  $nav = $qry2->result();
		  
		  $result = from($portfolio)
					  ->join(
								  from($nav), 
								  '$a ==> $a->PortfolioID', 
								  '$b ==> $b->PortfolioID',
								  '($a, $b) ==> array(
										  "id" => $a->PortfolioID,
										  "code" => $a->PortfolioCode,
										  "name" => $a->PortfolioNameShort,
										  "nav" => $b->NAV)'
								  )->toList();
  
		  return [TRUE, ['result' => $result]];
	  }
    
}    