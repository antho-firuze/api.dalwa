<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Portfolio_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database('cloud_simpi');
	}
	
	function performance($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->position_date) || empty($request->params->position_date))
			$request->params->position_date = "(select PositionDate from mobc_last_position_date where simpiID = ".$request->params->simpi_id.")";

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			select t0.simpiID, t4.AssetTypeCode, t0.PortfolioID, PortfolioCode, PortfolioNameFull, PortfolioNameShort, CcyID, Ccy, CcyDescription, 
			t2.PositionDate, t2.NAVperUnit, r1D, rMTD, rYTD, r1Mo, r3Mo, r6Mo, r1Y, r2Y, r5Y, rInception, 
			t5.FieldData as RiskScore, 
			t6.FieldData as InvestmentGoal, 
			t7.FieldData as SubsFee, 
			t8.FieldData as RedeemFee, 
			t9.FieldData as SwitchingFee 
			from master_portfolio t0 
			inner join parameter_securities_country t1 on t0.CcyID = t1.CountryID 
			inner join afa_nav t2 on t0.PortfolioID = t2.PortfolioID 
			inner join parameter_portfolio_assettype t4 on t0.AssetTypeID = t4.AssetTypeID
			left join afa_return t3 on t0.PortfolioID = t3.PortfolioID and t2.PositionDate = t3.PositionDate
			left join codeset_portfolio_data t5 on t0.PortfolioID = t5.PortfolioID and t5.FieldID = 22
			left join codeset_portfolio_data t6 on t0.PortfolioID = t6.PortfolioID and t6.FieldID = 6
			left join codeset_portfolio_data t7 on t0.PortfolioID = t7.PortfolioID and t7.FieldID = 14
			left join codeset_portfolio_data t8 on t0.PortfolioID = t8.PortfolioID and t8.FieldID = 15
			left join codeset_portfolio_data t9 on t0.PortfolioID = t9.PortfolioID and t9.FieldID = 16
		) g0 ";
		$this->db->from($table)
			->where([
				'simpiID' => $request->params->simpi_id, 
				'PositionDate' => $request->params->position_date
			], NULL, FALSE)
			->order_by('PortfolioNameFull');

		return $this->f->get_result();
	}

	function chart($request)
	{
		// list($success, $return) = $this->f->is_valid_token($request);
		// if (!$success)
			// return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->portfolio_id) || empty($request->params->portfolio_id))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'portfolio_id')]];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			select t0.simpiID, t0.PortfolioID, PortfolioCode, PortfolioNameShort, Ccy, t2.PositionDate, t2.GeometricIndex, @b := GeometricIndex / (select GeometricIndex from afa_nav where PortfolioID = t0.PortfolioID and year(PositionDate) = (select year(PositionDate)-1 from mobc_last_position_date where simpiID = t0.simpiID) and FlagDate = 3) - 1 as line1 
			from (SELECT @b := 0) AS dummy CROSS JOIN master_portfolio t0
			inner join parameter_securities_country t1 on t0.CcyID = t1.CountryID
			inner join afa_nav t2 on t0.PortfolioID = t2.PortfolioID
			) g0 ";
		$this->db->from($table)
			->where([
				'simpiID' => $request->params->simpi_id, 
				'PortfolioID' => $request->params->portfolio_id, 
				'PositionDate >=' => '(select PositionDate from afa_nav where PortfolioID = '.$request->params->portfolio_id.' and FlagDate = 3 and year(PositionDate) = (select year(PositionDate)-1 from mobc_last_position_date where simpiID = '.$request->params->simpi_id.'))'
			], NULL, FALSE)
			->order_by('PositionDate');
		return $this->f->get_result();
	}

	function chart_nav($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->portfolio_id) || empty($request->params->portfolio_id))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'portfolio_id')]];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			select t0.simpiID, t0.PortfolioID, PortfolioCode, PortfolioNameShort, Ccy, t2.PositionDate, t2.GeometricIndex, @b := GeometricIndex / (select GeometricIndex from afa_nav where PortfolioID = t0.PortfolioID and year(PositionDate) = (select year(PositionDate)-1 from mobc_last_position_date where simpiID = t0.simpiID) and FlagDate = 3) - 1 as line1 
			from (SELECT @b := 0) AS dummy CROSS JOIN master_portfolio t0
			inner join parameter_securities_country t1 on t0.CcyID = t1.CountryID
			inner join afa_nav t2 on t0.PortfolioID = t2.PortfolioID
			) g0 ";
		$this->db->from($table)
			->where([
				'simpiID' => $request->params->simpi_id, 
				'PortfolioID' => $request->params->portfolio_id, 
				'PositionDate >=' => '(select PositionDate from afa_nav where PortfolioID = '.$request->params->portfolio_id.' and FlagDate = 3 and year(PositionDate) = (select year(PositionDate)-1 from mobc_last_position_date where simpiID = '.$request->params->simpi_id.'))'
			], NULL, FALSE)
			->order_by('PositionDate');
		return $this->f->get_result();
	}

	function top_sector_eq($request)
	{
		// list($success, $return) = $this->f->is_valid_token($request);
		// if (!$success)
			// return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->portfolio_id) || empty($request->params->portfolio_id))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'portfolio_id')]];
		
		if (!isset($request->params->position_date) || empty($request->params->position_date))
			$request->params->position_date = "(select PositionDate from mobc_last_position_date where simpiID = ".$request->params->simpi_id.")";

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			SELECT f.SectorName, SUM(a.TotalValue/b.NAV) AS Percent
			FROM afa_nav AS b
			INNER JOIN afa_securities_balance AS a ON a.PortfolioID = b.PortfolioID AND a.PositionDate = b.PositionDate
			INNER JOIN market_instrument AS c ON c.SecuritiesID = a.SecuritiesID
			INNER JOIN market_company AS d ON c.CompanyID = d.CompanyID
			INNER JOIN parameter_securities_sector_class_member_company AS e ON d.CompanyID = e.CompanyID AND e.ClassID = 2
			INNER JOIN parameter_securities_sector AS f ON e.SectorID = f.SectorID
			WHERE b.PortfolioID = ".$request->params->portfolio_id." AND b.PositionDate = ".$request->params->position_date." 
			Group By f.SectorName
			) g0 ";
		$this->db->from($table)
			// ->where([
				// 'PortfolioID' => $request->params->portfolio_id, 
				// 'PositionDate' => $request->params->position_date
			// ], NULL, FALSE)
			->order_by('percent DESC')
			->limit(5, 0);
		return $this->f->get_result();
	}

	function top_sector_non_eq($request)
	{
		// list($success, $return) = $this->f->is_valid_token($request);
		// if (!$success)
			// return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->portfolio_id) || empty($request->params->portfolio_id))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'portfolio_id')]];
		
		if (!isset($request->params->position_date) || empty($request->params->position_date))
			$request->params->position_date = "(select PositionDate from mobc_last_position_date where simpiID = ".$request->params->simpi_id.")";

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			SELECT d.TypeDescription, SUM(a.TotalValue/b.NAV) as Percent
			FROM afa_nav AS b
			INNER JOIN afa_securities_balance AS a ON a.PortfolioID = b.PortfolioID AND a.PositionDate = b.PositionDate
			INNER JOIN market_instrument AS c ON c.SecuritiesID = a.SecuritiesID 
			INNER JOIN parameter_securities_instrument_type_sub AS e ON  e.SubTypeID = c.SubTypeID
			INNER JOIN parameter_securities_instrument_type AS d ON  e.TypeID = d.TypeID
			where b.PortfolioID = ".$request->params->portfolio_id." and b.PositionDate = ".$request->params->position_date."
			GROUP BY d.TypeDescription
			) g0 ";
		$this->db->from($table);
			// ->where([
				// 'PortfolioID' => $request->params->portfolio_id, 
				// 'PositionDate' => $request->params->position_date
			// ], NULL, FALSE)
			// ->order_by('percent DESC')
			// ->limit(5, 0);
		return $this->f->get_result();
	}

	function top_stock_nav($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->portfolio_id) || empty($request->params->portfolio_id))
			return [FALSE, ['message' => $this->f->lang('err_param_required', 'portfolio_id')]];
		
		if (!isset($request->params->position_date) || empty($request->params->position_date))
			$request->params->position_date = "(select PositionDate from mobc_last_position_date where simpiID = ".$request->params->simpi_id.")";

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			SELECT  a.PortfolioID, a.PositionDate, c.SecuritiesNameShort, d.TypeDescription, a.TotalValue/b.NAV as Percent
			FROM afa_nav AS b
			INNER JOIN afa_securities_balance AS a ON a.PortfolioID = b.PortfolioID AND a.PositionDate = b.PositionDate
			INNER JOIN market_instrument AS c ON c.SecuritiesID = a.SecuritiesID 
			INNER JOIN parameter_securities_instrument_type_sub AS e ON  e.SubTypeID = c.SubTypeID
			INNER JOIN parameter_securities_instrument_type AS d ON  e.TypeID = d.TypeID
			) g0 ";
		$this->db->from($table)
			->where([
				'PortfolioID' => $request->params->portfolio_id, 
				'PositionDate' => $request->params->position_date
			], NULL, FALSE)
			->order_by('percent DESC')
			->limit(5, 0);
		return $this->f->get_result();
	}

	function running_text($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->position_date) || empty($request->params->position_date))
			$request->params->position_date = "(select PositionDate from mobc_last_position_date where simpiID = ".$request->params->simpi_id.")";

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			SELECT b.PortfolioNameShort, a.PositionDate, a.NAVPerUnit, (c.rYTD*100) AS PortfolioReturn, (d.rYTD*100) AS BenchmarkReturn
			FROM afa_nav AS a
			INNER JOIN master_portfolio AS b ON a.PortfolioID = b.PortfolioID and b.simpiID = ".$request->params->simpi_id." 
			LEFT JOIN afa_return AS c ON a.PortfolioID = c.PortfolioID AND a.PositionDate = c.PositionDate
			LEFT JOIN afa_benchmark AS d ON a.PortfolioID = d.PortfolioID AND a.PositionDate = d.PositionDate
			) g0 ";
		$this->db->from($table)
			->where([
				'PositionDate' => $request->params->position_date
			], NULL, FALSE)
			->order_by('PortfolioNameShort');
		return $this->f->get_result();
	}

	function running_text2($request)
	{
		list($success, $return) = $this->f->is_valid_appcode($request);
		if (!$success) return [FALSE, $return];
		
		if (!isset($request->params->simpi_id) || empty($request->params->simpi_id))
			$request->params->simpi_id = $request->simpi_id;
		
		if (!isset($request->params->position_date) || empty($request->params->position_date))
			$request->params->position_date = "(select PositionDate from mobc_last_position_date where simpiID = ".$request->params->simpi_id.")";

		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$table = "(
			SELECT a.BenchmarkCode, b.BenchmarkDate, b.BenchmarkValue
			FROM parameter_securities_benchmark AS a 
			INNER JOIN amd_price_benchmark AS b ON a.BenchmarkID = b.BenchmarkID
			WHERE a.BenchmarkID IN (1, 2, 27)
			) g0 ";
		$this->db->from($table)
			->where([
				'BenchmarkDate' => $request->params->position_date
			], NULL, FALSE)
			->order_by('BenchmarkCode');
		return $this->f->get_result();
	}

}
