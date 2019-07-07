<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Instrument_marketid_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
    }

	function load($request)
	{
		//cek akses:  
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: MarketID 
		if (!isset($request->params->MarketID) || empty($request->params->MarketID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketID'])]];
		}

		$this->db->select('T1.SecuritiesID, T2.MarketID, T3.MarketCode, T3.MarketName, T2.SecuritiesMarketCode');  
		$this->db->from('market_instrument T1');
		$this->db->join('market_instrument_id_market T2', 'T1.SecuritiesID = T2.SecuritiesID');  
		$this->db->join('parameter_securities_market T3', 'T2.MarketID = T3.MarketID');  
		$this->db->join('parameter_securities_instrument_type_sub T4', 'T1.SubTypeID = T4.SubTypeID');  
		$this->db->join('market_company T5', 'T1.CompanyID = T5.CompanyID');  
		$this->db->where('T2.MarketID', $request->params->MarketID);
		if (isset($request->params->SecuritiesID) && !empty($request->params->SecuritiesID)) {
			$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
		} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
			$this->db->where('T1.SecuritiesCode', $request->params->SecuritiesCode);
			if (isset($request->params->TypeID) && !empty($request->params->TypeID)) 
				$this->db->where('T4.TypeID', $request->params->TypeID);
			if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
				$this->db->where('T1.CompanyID', $request->params->CompanyID);				
			} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
				$this->db->where('T5.CompanyCode', $request->params->CompanyCode);
			}	
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'instrument parameter'])]];
		}
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;		
	}	

	function search($request)
	{	
		//cek akses:  
		list($success, $return) = $this->system->is_valid_access2($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('T1.SecuritiesID, T2.MarketID, T3.MarketCode, T3.MarketName, T2.SecuritiesMarketCode');  
		$this->db->from('market_instrument T1');
		$this->db->join('market_instrument_id_market T2', 'T1.SecuritiesID = T2.SecuritiesID');  
		$this->db->join('parameter_securities_market T3', 'T2.MarketID = T3.MarketID');  
		$this->db->join('parameter_securities_instrument_type_sub T4', 'T1.SubTypeID = T4.SubTypeID');  
		$this->db->join('market_company T5', 'T1.CompanyID = T5.CompanyID');  
		if (isset($request->params->MarketID) && !empty($request->params->MarketID)) 
			$this->db->where('T2.MarketID', $request->params->MarketID);
		if (isset($request->params->SecuritiesID)) {
			if (is_array($request->params->SecuritiesID)) 
				$this->db->where_in('T1.SecuritiesID', $request->params->SecuritiesID);
			else 
				$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
		} else {
			if (isset($request->params->SubTypeID)) {
				$this->db->where('T4.SubTypeID', $request->params->SubTypeID);
			}
			elseif (isset($request->params->TypeID)) {				 
				$this->db->where('T4.TypeID', $request->params->TypeID); 
			}	
			if (isset($request->params->CountryID))
				$this->db->where('T1.CountryID', $request->params->CountryID);
			if (isset($request->params->CompanyID))
				$this->db->where('T1.CompanyID', $request->params->CompanyID);
			if (isset($request->params->securities_keyword)) {
				$data = $this->security->xss_clean($request->params->securities_keyword);
				$strKeyword = "(T1.SecuritiesCode LIKE '%".$data."%'"
							 ." or T1.SecuritiesNameFull LIKE '%".$data."%'"
							 ." or T1.SecuritiesNameShort LIKE '%".$data."%')";
				$this->db->where($strKeyword);
			}
		}			
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);
		
		return $data;			 
	}

	function external_get($request)
	{
		//cek akses 
		list($success, $return) = $this->system->is_valid_access3($request);
		if (!$success) return [FALSE, $return];

		//cek parameter:  MarketID --> sumber external identification 
		if (!isset($request->params->MarketID) || empty($request->params->MarketID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketID'])]];
		}

		$this->db->select('T1.SecuritiesMarketCode');
		$this->db->from('market_instrument_id_market T1');
		$this->db->join('market_instrument T2', 'T1.SecuritiesID = T2.SecuritiesID');  
		$this->db->join('parameter_securities_instrument_type_sub T3', 'T2.SubTypeID = T3.SubTypeID');  
		$this->db->join('market_company T4', 'T2.CompanyID = T4.CompanyID');  
		$this->db->where('T1.MarketID', $request->params->MarketID);
		if (isset($request->params->SecuritiesID) && !empty($request->params->SecuritiesID)) {
			$this->db->where('T1.SecuritiesID', $request->params->SecuritiesID);
		} elseif (isset($request->params->SecuritiesCode) && !empty($request->params->SecuritiesCode)) {
			$this->db->where('T2.SecuritiesCode', $request->params->SecuritiesCode);
			if (isset($request->params->TypeID) && !empty($request->params->TypeID)) 
				$this->db->where('T3.TypeID', $request->params->TypeID);
			if (isset($request->params->CompanyID) && !empty($request->params->CompanyID)) {
				$this->db->where('T2.CompanyID', $request->params->CompanyID);				
			} elseif (isset($request->params->CompanyCode) && !empty($request->params->CompanyCode)) {
				$this->db->where('T4.CompanyCode', $request->params->CompanyCode);
			}	
		} else {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'instrument parameter'])]];
		}
		$row = $this->db->get()->row();
        if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market company'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SecuritiesMarketCode' => $row->SecuritiesMarketCode]]];
	}

	function external_code($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SecuritiesMarketCode
		if (!isset($request->params->SecuritiesMarketCode) || empty($request->params->SecuritiesMarketCode)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SecuritiesMarketCode'])]];
		}

		//cek parameter: MarketID --> sumber external identification 
		if (!isset($request->params->MarketID) || empty($request->params->MarketID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketID'])]];
		}

		$this->db->select('T2.SecuritiesCode');
		$this->db->from('market_instrument_id_market T1');
		$this->db->join('market_instrument T2', 'T1.SecuritiesID = T2.SecuritiesID');  
		$this->db->where('T1.MarketID', $request->params->MarketID);
		$this->db->where('T1.SecuritiesMarketCode', $request->params->SecuritiesMarketCode);
		$row = $this->db->get()->row();
        if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market company'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SecuritiesCode' => $row->SecuritiesCode]]];
	}	

	function external_id($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: SecuritiesMarketCode
		if (!isset($request->params->SecuritiesMarketCode) || empty($request->params->SecuritiesMarketCode)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter SecuritiesMarketCode'])]];
		}

		//cek parameter: MarketID --> sumber external identification 
		if (!isset($request->params->MarketID) || empty($request->params->MarketID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter MarketID'])]];
		}

		$this->db->select('SecuritiesID');
		$this->db->from('market_instrument_id_market');
		$this->db->where('MarketID', $request->params->MarketID);
		$this->db->where('SecuritiesMarketCode', $request->params->SecuritiesMarketCode);
		$row = $this->db->get()->row();
        if (!$row) {
			list($success, $return) = $this->system->error_message('00-2', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-2'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'market company'])]];
        }

		$request->log_size = mb_strlen(serialize($row), '8bit');
		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return [TRUE, ['result' => ['SecuritiesID' => $row->SecuritiesID]]];
	}	
							
}