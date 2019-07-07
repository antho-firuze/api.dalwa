<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Market_database_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}

	function market_price_search($request)
	{	
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: MarketID
		if (!isset($request->params->MarketID) || empty($request->params->MarketID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketID'])]];
		}

		//cek parameter: option_date
		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}
 
		//cek parameter: MarketDate
		if (!($request->params->option_date == 'first') && !($request->params->option_date == 'end')) {
			if (!isset($request->params->MarketDate) || empty($request->params->MarketDate)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketDate'])]];
			}	
		}
		
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('T1.SecuritiesID, T1.SecuritiesCode, T1.SecuritiesNameFull, T1.SecuritiesNameShort, 
			 	T1.DateIssue, T1.CompanyID, T2.CompanyCode, T2.CompanyName, T1.SubTypeID, T3.SubTypeCode, T3.TypeID, 
			 	T4.TypeCode, T1.CcyID, T5.Ccy, T1.CountryID, T2.MarketID, T2.MarketDate, T2.MarketPrice3, T2.MarketPrice4, 
			 	T2.MarketPrice5, T2.MarketPrice6, T2.MarketPrice7, T2.pC, T2.pI, T2.MarketPrice, T2.MarketPrice1, 
			 	T2.MarketPrice2, T2.Adjustment');
		$this->db->from('market_instrument T1');
		$this->db->join('amd_price_securities T2', 'T1.SecuritiesID = T2.SecuritiesID');  
		$this->db->join('parameter_securities_instrument_type_sub T3', 'T1.SubTypeID = T3.SubTypeID');  
		$this->db->join('parameter_securities_instrument_type T4', 'T3.TypeID = T4.TypeID');  
		$this->db->join('parameter_securities_ccy T5', 'T1.CcyID = T5.CcyID');  
		$this->db->where('T2.MarketID', $request->params->MarketID, NULL, FALSE);
		if ($request->params->option_date == 'at') {
			$this->db->where('T2.MarketDate', $request->params->MarketDate, NULL, FALSE);
			//daftar price by SecuritiesID: 1 atau lebih
			if (isset($request->params->SecuritiesID)) {
				 if (is_array($request->params->SecuritiesID)) 
					$this->db->where_in('T1.SecuritiesID', $request->params->SecuritiesID);
				 else 
					$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
			//daftar price by SecuritiesCode: 1 atau lebih		
			} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
				if (is_array($request->params->SecuritiesCode))
					$this->db->where_in('T1.SecuritiesCode', $request->params->SecuritiesCode);
				else
					$this->db->where('T1.SecuritiesCode', $request->params->SecuritiesCode);
			//daftar price by keyword, country & type		
			} else {
				if (isset($request->params->CcyID))
					$this->db->where('T1.CcyID', $request->params->CcyID);
				if (isset($request->params->CountryID))
					$this->db->where('T1.CountryID', $request->params->CountryID);
				if (isset($request->params->SubTypeID))
					$this->db->where('T1.SubTypeID', $request->params->SubTypeID);
				elseif (isset($request->params->TypeID))
					$this->db->where('T1.TypeID', $request->params->TypeID);
				if (isset($request->params->securities_keyword)) {
					$strKeyword = "(T1.SecuritiesCode LIKE '%".$this->db->escape($request->params->securities_keyword)."%'"
						  ." OR T1.SecuritiesNameFull LIKE '%".$this->db->escape($request->params->securities_keyword)."%'"
						  ." OR T1.SecuritiesNameShort LIKE '%".$this->db->escape($request->params->securities_keyword)."%')";
					$this->db->where($strKeyword);
				}
			}
			$this->db->order_by('T1.SecuritiesCode');

		} elseif (($request->params->option_date == 'last') || ($request->params->option_date == 'next') || 
				  ($request->params->option_date == 'before') || ($request->params->option_date == 'after')) {
			//daftar price by SecuritiesID: 1  
			if (isset($request->params->SecuritiesID)) {
				$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
			//daftar price by SecuritiesCode: 1  
			} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
				$this->db->where('T1.SecuritiesCode', $request->params->SecuritiesCode);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter market instrument'])]];	
			}
			if ($request->params->option_date == 'last') {
				$this->db->where('T2.MarketDate <=', $request->params->MarketDate, NULL, FALSE);
				$this->db->order_by('T2.MarketDate', 'DESC');
			} elseif ($request->params->option_date == 'before') {
				$this->db->where('T2.MarketDate <', $request->params->MarketDate, NULL, FALSE);
				$this->db->order_by('T2.MarketDate', 'DESC');
			} elseif ($request->params->option_date == 'next') {
				$this->db->where('T2.MarketDate >=', $request->params->MarketDate, NULL, FALSE);
				$this->db->order_by('T2.MarketDate', 'ASC');
			} else {
				$this->db->where('T2.MarketDate >', $request->params->MarketDate, NULL, FALSE);
				$this->db->order_by('T2.MarketDate', 'ASC');
			}
			$this->db->limit(1);

		} elseif (($request->params->option_date == 'first') || ($request->params->option_date == 'end')) {
			//daftar price by SecuritiesID: 1  
			if (isset($request->params->SecuritiesID)) {
				$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
			//daftar price by SecuritiesCode: 1  
			} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
				$this->db->where('T1.SecuritiesCode', $request->params->SecuritiesCode);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter market instrument'])]];	
			}
			if ($request->params->option_date == 'first')  
				$this->db->order_by('T2.MarketDate', 'ASC');
			else 
				$this->db->order_by('T2.MarketDate', 'DESC');		
			$this->db->limit(1);
			
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		$data = $this->f->get_result();

		$request->log_size = mb_strlen(serialize($data), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function market_price_history($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: MarketID
		if (!isset($request->params->MarketID) || empty($request->params->MarketID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketID'])]];
		}

		//cek parameter: SecuritiesID
		if (!isset($request->params->SecuritiesID) || empty($request->params->SecuritiesID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SecuritiesID'])]];
		}
		//cek parameter: option_date
		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		//cek parameter: MarketDate
		if ($request->params->option_date == 'between') {
			if (!isset($request->params->from_date) || empty($request->params->from_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter from_date'])]];
			}	
			if (!isset($request->params->to_date) || empty($request->params->to_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter to_date'])]];
			}	
		} else {
			if (!isset($request->params->MarketDate) || empty($request->params->MarketDate)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketDate'])]];
			}	
		}
		
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else
			$this->db->select('MarketDate, MarketPrice3, MarketPrice4, MarketPrice5, MarketPrice6, 
								MarketPrice7, pC, pI, MarketPrice, MarketPrice1, MarketPrice2, Adjustment');
		$this->db->from('amd_price_securities');
		$this->db->where('MarketID', $request->params->MarketID, NULL, FALSE);
		$this->db->where('SecuritiesID', $request->params->SecuritiesID, NULL, FALSE);
		if ($request->params->option_date == 'between') { 
			$this->db->where('MarketDate >=', $request->params->from_date, NULL, FALSE);
			$this->db->where('MarketDate <=', $request->params->to_date, NULL, FALSE);
			$this->db->order_by('MarketDate', 'ASC');
		} elseif ($request->params->option_date == 'last') { 
			$this->db->where('MarketDate <=', $request->params->MarketDate, NULL, FALSE);
			$this->db->order_by('MarketDate', 'DESC');
		} elseif ($request->params->option_date == 'next') { 
			$this->db->where('MarketDate >=', $request->params->MarketDate, NULL, FALSE);
			$this->db->order_by('MarketDate', 'ASC');
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		$data = $this->f->get_result();

		$request->log_size = mb_strlen(serialize($data), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

	function benchmark_price_search($request)
	{	
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: option_date
		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		//cek parameter: BenchmarkDate
		if (!($request->params->option_date == 'first') && !($request->params->option_date == 'end')) {
			if (!isset($request->params->BenchmarkDate) || empty($request->params->BenchmarkDate)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkDate'])]];
			}	
		}
		
		//cek pilihan fields
		if (isset($request->params->fields) && !empty($request->params->fields))
		 	$this->db->select($request->params->fields);
		else 
		 	$this->db->select('T1.BenchmarkID, T1.BenchmarkCode, T1.BenchmarkName, T1.BenchmarkDays, 
						T1.BenchmarkCalculationID, T2.BenchmarkCalculationCode, T1.BenchmarkTypeID, 
						T3.BenchmarkTypeCode, T4.BenchmarkDate, T4.BenchmarkValue, T4.Adjustment, T4.pC, T4.pI');
		$this->db->from('parameter_securities_benchmark T1');
		$this->db->join('parameter_securities_benchmarkcalculation T2', 'T1.BenchmarkCalculationID = T2.BenchmarkCalculationID');  
		$this->db->join('parameter_securities_benchmarktype T3', 'T1.BenchmarkTypeID = T3.BenchmarkTypeID');  
		$this->db->join('amd_price_benchmark T4', 'T1.BenchmarkID = T4.BenchmarkID');  
		if ($request->params->option_date == 'at') {
			$this->db->where('T2.BenchmarkDate', $request->params->BenchmarkDate, NULL, FALSE);
			//daftar price by BenchmarkID: 1 atau lebih
			if (isset($request->params->BenchmarkID)) {
				 if (is_array($request->params->BenchmarkID)) 
					$this->db->where_in('T1.BenchmarkID', $request->params->BenchmarkID);
				 else 
					$this->db->where('T1.BenchmarkID', $request->params->BenchmarkID);
			//daftar price by BenchmarkCode: 1 atau lebih		
			} elseif (isset($request->params->BenchmarkCode) && !empty($request->params->BenchmarkCode)) {
				if (is_array($request->params->BenchmarkCode))
					$this->db->where_in('T1.BenchmarkCode', $request->params->BenchmarkCode);
				else
					$this->db->where('T1.BenchmarkCode', $request->params->BenchmarkCode);
			//daftar benchmark by keyword, country & type		
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
			$this->db->order_by('T1.BenchmarkCode');

		} elseif (($request->params->option_date == 'last') || ($request->params->option_date == 'next') || 
				  ($request->params->option_date == 'before') || ($request->params->option_date == 'after')) {
			//daftar price by BenchmarkID: 1  
			if (isset($request->params->BenchmarkID)) {
				$this->db->where('T1.BenchmarkID', $request->params->BenchmarkID);
			//daftar price by BenchmarkCode: 1  
			} elseif (isset($request->params->BenchmarkCode) && !empty($request->params->BenchmarkCode)) {
				$this->db->where('T1.BenchmarkCode', $request->params->BenchmarkCode);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter benchmark'])]];	
			}
			if ($request->params->option_date == 'last') {
				$this->db->where('T2.BenchmarkDate <=', $request->params->BenchmarkDate, NULL, FALSE);
				$this->db->order_by('T2.BenchmarkDate', 'DESC');
			} elseif ($request->params->option_date == 'before') {
				$this->db->where('T2.BenchmarkDate <', $request->params->BenchmarkDate, NULL, FALSE);
				$this->db->order_by('T2.BenchmarkDate', 'DESC');
			} elseif ($request->params->option_date == 'next') {
				$this->db->where('T2.BenchmarkDate >=', $request->params->BenchmarkDate, NULL, FALSE);
				$this->db->order_by('T2.BenchmarkDate', 'ASC');
			} else {
				$this->db->where('T2.BenchmarkDate >', $request->params->BenchmarkDate, NULL, FALSE);
				$this->db->order_by('T2.BenchmarkDate', 'ASC');
			}
			$this->db->limit(1);

		} elseif (($request->params->option_date == 'first') || ($request->params->option_date == 'end')) {
			//daftar price by BenchmarkID: 1  
			if (isset($request->params->BenchmarkID)) {
				$this->db->where('T1.BenchmarkID', $request->params->BenchmarkID);
			//daftar price by BenchmarkCode: 1  
			} elseif (isset($request->params->BenchmarkCode) && !empty($request->params->BenchmarkCode)) {
				$this->db->where('T1.BenchmarkCode', $request->params->BenchmarkCode);
			} else {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter benchmark'])]];	
			}
			if ($request->params->option_date == 'first')  
				$this->db->order_by('T2.BenchmarkDate', 'ASC');
			else 
				$this->db->order_by('T2.BenchmarkDate', 'DESC');		
			$this->db->limit(1);
			
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		$data = $this->f->get_result();

		$request->log_size = mb_strlen(serialize($data), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}
	
	function benchmark_price_history($request)
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
		//cek parameter: option_date
		if (!isset($request->params->option_date) || empty($request->params->option_date)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		//cek parameter: BenchmarkDate
		if ($request->params->option_date == 'between') {
			if (!isset($request->params->from_date) || empty($request->params->from_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter from_date'])]];
			}	
			if (!isset($request->params->to_date) || empty($request->params->to_date)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter to_date'])]];
			}	
		} else {
			if (!isset($request->params->BenchmarkDate) || empty($request->params->BenchmarkDate)) {
				list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
				if (!$success) return [FALSE, 'message' => '00-1'];
				return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter BenchmarkDate'])]];
			}	
		}
		
	 	$this->db->select('BenchmarkID, BenchmarkDate, BenchmarkValue, Adjustment, pC, pI');
		$this->db->from('amd_price_benchmark');
		$this->db->where('BenchmarkID', $request->params->BenchmarkID, NULL, FALSE);
		if ($request->params->option_date == 'between') { 
			$this->db->where('BenchmarkDate >=', $request->params->from_date, NULL, FALSE);
			$this->db->where('BenchmarkDate <=', $request->params->to_date, NULL, FALSE);
			$this->db->order_by('BenchmarkDate', 'ASC');
		} elseif ($request->params->option_date == 'last') { 
			$this->db->where('BenchmarkDate <=', $request->params->BenchmarkDate, NULL, FALSE);
			$this->db->order_by('BenchmarkDate', 'DESC');
		} elseif ($request->params->option_date == 'next') { 
			$this->db->where('BenchmarkDate >=', $request->params->BenchmarkDate, NULL, FALSE);
			$this->db->order_by('BenchmarkDate', 'ASC');
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter option_date'])]];
		}

		$data = $this->f->get_result();

		$request->log_size = mb_strlen(serialize($data), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;
	}

}