<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Parameter_securities_model extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->database(DATABASE_MARKET);
		$this->load->library('System');
	}
	
	function benchmark_calculation($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('BenchmarkCalculationID, BenchmarkCalculationCode, BenchmarkCalculationDescription');
   	$this->db->from('parameter_securities_benchmarkcalculation');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}
	
	function benchmark_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('BenchmarkTypeID, BenchmarkTypeCode, BenchmarkTypeDescription');
   	$this->db->from('parameter_securities_benchmarktype');
		 $data = $this->f->get_result_paging($request);
 
		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function company_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, TypeCode, TypeDescription');
   	$this->db->from('parameter_securities_company_type');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function corporate_action_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, TypeCode, TypeDescription');
   	$this->db->from('parameter_securities_corporateactiontype');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function cost_method($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('CostID, CostCode, CostDescription');
   	$this->db->from('parameter_securities_costmethod');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function coupon_calculation($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('CouponCalculationID, CouponCalculationCode, CouponCalculationDescription');
   	$this->db->from('parameter_securities_couponcalculation');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function coupon_frequency($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('CouponFrequencyID, CouponFrequencyCode, CouponFrequencyDescription');
   	$this->db->from('parameter_securities_couponfrequency');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function days_in_month($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('DaysInMonthID, DaysInMonthCode, DaysInMonthDescription');
   	$this->db->from('parameter_securities_daysinmonth');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
    }	 
	
	function days_in_year($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('DaysInYearID, DaysInYearCode, DaysInYearDescription');
   	$this->db->from('parameter_securities_daysinyear');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 
	
	function fi_tax_method($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TaxID, TaxCode, TaxDescription');
   	$this->db->from('parameter_securities_fitaxmethod');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 

	function index_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, TypeCode, TypeDescription');
   	$this->db->from('parameter_securities_indextype');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
	}	 

	function instrument_type($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, TypeCode, TypeDescription');
   	$this->db->from('parameter_securities_instrument_type');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
    }	 

	function instrument_type_sub($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('TypeID, SubTypeID, SubTypeCode, SubTypeDescription');
   	$this->db->from('parameter_securities_instrument_type_sub');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
    }	 

	function inventory_method($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('InventoryID, InventoryCode, InventoryDescription');
   	$this->db->from('parameter_securities_inventorymethod');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
    }	 

	function language($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];
  
		$this->db->select('LanguageID, LanguageCode, LanguageDescription');
   	$this->db->from('parameter_securities_language');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);
  
		return $data;
    }	 

	function securities_status($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('StatusID, StatusCode, StatusDescription');
   	$this->db->from('parameter_securities_status');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}

	function ccy($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('CcyID, Ccy, CcyDescription');
   	$this->db->from('parameter_securities_ccy');
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function region($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('RegionID, RegionCode, RegionName');
   	$this->db->from('parameter_securities_region');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function country($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('CountryID, CountryCode, CountryName, Nationality, PhoneCode, CountryCode3, CountryCodeNumeric');
   	$this->db->from('parameter_securities_country');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }
    
	function province($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CountryID
		if (!isset($request->params->CountryID) || empty($request->params->CountryID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CountryID'])]];
		}

		$this->db->select('ProvinceID, CountryID, Province');
   	$this->db->from('parameter_securities_country_province');
    $this->db->where('CountryID', $request->params->CountryID, NULL, FALSE);	
		$data = $this->f->get_result_paging($request);

		$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }
    
	function city($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		//cek parameter: CountryID
		if (!isset($request->params->CountryID) || empty($request->params->CountryID)) {
			list($success, $return) = $this->system->error_message('00-1', $request->LanguageID);
			if (!$success) return [FALSE, 'message' => '00-1'];
			return [FALSE, ['message' => $this->system->refill_message($return['message'], ['data' => 'parameter CountryID'])]];
		}

		$this->db->select('T2.CountryID, T1.ProvinceID, T1.CityCode, T1.CityName');
   	$this->db->from('parameter_securities_country_city T1');
 	  $this->db->join('parameter_securities_country_province T2', 'T1.ProvinceID = T2.ProvinceID');  
		$this->db->where('T2.CountryID', $request->params->CountryID);
		if (isset($request->params->ProvinceID)) 
        $this->db->where('T1.ProvinceID', $request->params->ProvinceID);
				$data = $this->f->get_result_paging($request);

				$request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function company_position($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('PositionID, PositionCode, PositionDescription');
   	$this->db->from('parameter_securities_company_position');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function external_system($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('SystemID, SystemCode, SystemName');
   	$this->db->from('parameter_securities_externalsystem');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function market($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('MarketID, MarketCode, MarketName');
   	$this->db->from('parameter_securities_market');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function oms($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('omsID, omsCode, omsDescription');
   	$this->db->from('parameter_afa_oms');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
  }

	function xrate($request)
	{
		//cek akses: by 4 method
		list($success, $return) = $this->system->is_valid_access4($request);
		if (!$success) return [FALSE, $return];

		$this->db->select('XRateID, XRateCode, XRateName');
   	$this->db->from('parameter_securities_xrate');
		 $data = $this->f->get_result_paging($request);

		 $request->log_type	= 'data';	
		$this->system->save_billing($request);

		return $data;
	}
	
}