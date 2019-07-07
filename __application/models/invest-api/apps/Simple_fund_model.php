<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Nav_model extends CI_Model
{
	function __construct() {
		parent::__construct();
		$this->load->library('System');
    }

    function portfolio_list()
    {
		//cek akses: 
        list($success, $return) = $this->is_valid_appcode($request);
        if (!$success) return [FALSE, $return];			

        //tambahan filter: simpi apps portfolio
        $this->db->select('T1.PortfolioID, T1.NAVperUnit, T2.rYTD, T1.PositionDate');
        $this->db->from('afa_nav T1');
        $this->db->join('afa_return T2', 'T1.simpiID = T2.simpiID And T1.PortfolioID = T2.PortfolioID 
                                        And T1.PositionDate = T2.PositionDate');  
        $this->db->join('afa_mtm T3', 'T1.simpiID = T3.simpiID And T1.PortfolioID = T3.PortfolioID 
                                        And T1.PositionDate = T3.PositionDate');  
        $this->db->where('IsLast', 'Y');
        $this->db->order_by('T1.PositionDate', 'DESC');
        $this->db->order_by('T2.rYTD', 'DESC');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
    }

    function daftar_product()
    {
   
    }

    function portfolio_nav()
    {
   
    }

    function portfolio_histori()
    {
   
    }

    function market_update()
    {
		//cek akses: 
        list($success, $return) = $this->is_valid_appcode($request);
        if (!$success) return [FALSE, $return];			

        //tambahan filter: simpi apps portfolio
        $this->db->select('ReviewID, ReviewDate, ReviewText');
        $this->db->from('analyst_marketing_marketreview');
        $this->db->where('ReviewDate >=', $request->params->time_stamp, NULL, FALSE);
        $this->db->order_by('ReviewDate', 'DESC');
        $this->db->order_by('ReviewID', 'DESC');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
   
    }

    function market_update_selanjutnya()
    {
   
    }

    
}    