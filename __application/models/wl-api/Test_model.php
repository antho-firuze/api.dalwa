<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Test_model extends CI_Model
{
	function __construct(){
		parent :: __construct();
		 $this->load->database(DATABASE_GATEWAY);
	}
	
  function show_db()
  {
		return [TRUE, ['database' => $this->db->database]];
  }

	function from_olap()
	{
		$this->load->database('simpi_olap');
		$tables = $this->db->from('simpi_portfolio')->get()->result();
		return [TRUE, ['result' => $tables]];
	}

	function from_master()
	{
		$this->load->database('simpi_master');
		$tables = $this->db->select('PortfolioID, PortfolioCode, PortfolioNameShort')->from('master_portfolio')->get()->result();
		return [TRUE, ['result' => $tables]];
	}

	function a1($request)
	{
		$request->params->_body = $this->f->lang('email_body_new_accountindividual', [
			'name' => 'First Last', 
			'email' => 'ahmad@simpi-pro.com',
			'new_password' => '12345',
			'token' => $this->f->gen_token(),
			'domain_frontend' => 'http => //www.simpipro.com/',
		]);
		return [TRUE, ['result' => $request]];
	}

	function salt()
	{

		return [TRUE, ['result' => [
			'salt'=>$this->f->gen_salt(), 
			'token'=>$this->f->gen_token(),
			'md5'=>md5($this->f->gen_salt()),
			]]
		];
	}

	function lat1($request)
	{
		// list($success, $return) = $this->f->is_valid_token($request);
		// if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);
		
		$this->db->from('parameter_client_businessactivity');
		return $this->f->get_result($request);
	}
	
	function gen_pass($request)
	{
		die($this->f->gen_pwd(6));	
	}

	function get_rows($request)
	{
		$arr = [
			["id" => 1,"firstName" => "Annemarie","lastName" => '<span class="label label-danger">Designer</span>',"something" => 1381105566987,"jobTitle" => "Cloak Room Attendant","started" => 1367700388909,"dob" => 122365714987,"status" => "Suspended"],
			["id" => 2,"firstName" => "Nelly","lastName" => "Lusher","something" => 1267237540208,"jobTitle" => "Broadcast Maintenance Engineer","started" => 1382739570973,"dob" => 183768652128,"status" => "Disabled"],
			["id" => 3,"firstName" => "Lorraine","lastName" => "Kyger","something" => 1263216405811,"jobTitle" => "Geophysicist","started" => 1265199486212,"dob" => 414197000409,"status" => "Active"],
			["id" => 4,"firstName" => "Maire","lastName" => "Vanatta","something" => 1317652005631,"jobTitle" => "Gaming Cage Cashier","started" => 1359190254082,"dob" => 381574699574,"status" => "Disabled"],
			["id" => 5,"firstName" => "Whiney","lastName" => "Keasler","something" => 1297738568550,"jobTitle" => "High School Librarian","started" => 1377538533615,"dob" => -11216050657,"status" => "Active"],
			["id" => 6,"firstName" => "Nikia","lastName" => "Badgett","something" => 1283192889859,"jobTitle" => "Clown","started" => 1348067291754,"dob" => -236655382175,"status" => "Active"],
			["id" => 7,"firstName" => "Renea","lastName" => "Stever","something" => 1289586239969,"jobTitle" => "Work Ticket Distributor","started" => 1312738712940,"dob" => 483475202947,"status" => "Disabled"],
			["id" => 8,"firstName" => "Rayna","lastName" => "Resler","something" => 1351969871214,"jobTitle" => "Ordnance Engineer","started" => 1300981406722,"dob" => 267565804332,"status" => "Disabled"],
			["id" => 9,"firstName" => "Sephnie","lastName" => "Cooke","something" => 1318107009703,"jobTitle" => "Accounts Collector","started" => 1348566414201,"dob" => 84698632860,"status" => "Suspended"],
			["id" => 10,"firstName" => "Lauri","lastName" => "Kyles","something" => 1298847936600,"jobTitle" => "Commercial Lender","started" => 1306984494872,"dob" => 647549298565,"status" => "Disabled"],
			["id" => 11,"firstName" => "Maria","lastName" => "Hosler","something" => 1372447291002,"jobTitle" => "Auto Detailer","started" => 1295239832657,"dob" => 92796339552,"status" => "Suspended"],
			["id" => 12,"firstName" => "Lakeshia","lastName" => "Sprinkle","something" => 1296451003728,"jobTitle" => "Garment Presser","started" => 1350695946669,"dob" => 6068444160,"status" => "Suspended"],
			["id" => 13,"firstName" => "Isidra","lastName" => "Dragoo","something" => 1285852466255,"jobTitle" => "Window Trimmer","started" => 1264658548150,"dob" => 129659544744,"status" => "Active"],
			["id" => 14,"firstName" => "Marquia","lastName" => "Ardrey","something" => 1336968147859,"jobTitle" => "Broadcast Maintenance Engineer","started" => 1281348596711,"dob" => 69513590957,"status" => "Disabled"],
			["id" => 15,"firstName" => "Jua","lastName" => "Bottom","something" => 1322560108993,"jobTitle" => "Broadcast Maintenance Engineer","started" => 1350354712910,"dob" => 397465403667,"status" => "Active"],
			["id" => 16,"firstName" => "Delana","lastName" => "Sprouse","something" => 1367925208609,"jobTitle" => "High School Librarian","started" => 1360754556666,"dob" => -101355021375,"status" => "Disabled"],
			["id" => 17,"firstName" => "Annamaria","lastName" => "Pennock","something" => 1385602980951,"jobTitle" => "Photocopying Equipment Repairer","started" => 1267426062440,"dob" => 129358493928,"status" => "Active"],
			["id" => 18,"firstName" => "Junie","lastName" => "Leinen","something" => 1270540402378,"jobTitle" => "Roller Skater","started" => 1343534987824,"dob" => 405467757390,"status" => "Suspended"],
			["id" => 19,"firstName" => "Charles","lastName" => "Hayton","something" => 1309910398220,"jobTitle" => "Ships Electronic Warfare Officer","started" => 1297511155831,"dob" => 603442557419,"status" => "Disabled"],
			["id" => 20,"firstName" => "Lorriane","lastName" => "Roling","something" => 1278850931389,"jobTitle" => "Industrial Waste Treatment Technician","started" => 1279697681249,"dob" => 236380359513,"status" => "Disabled"],
			["id" => 21,"firstName" => "Alice","lastName" => "Goodlow","something" => 1268720188765,"jobTitle" => "State Archivist","started" => 1381306773987,"dob" => 455731231484,"status" => "Disabled"],
			["id" => 22,"firstName" => "Carie","lastName" => "Dragoo","something" => 1384770174557,"jobTitle" => "Financial Accountant","started" => 1277771127047,"dob" => -219020252497,"status" => "Active"],
			["id" => 23,"firstName" => "Gran","lastName" => "Valles","something" => 1337645396364,"jobTitle" => "Childrens Pastor","started" => 1288986457843,"dob" => -227796663726,"status" => "Suspended"],
			["id" => 24,"firstName" => "Jacqulyn","lastName" => "Polo","something" => 1326444321746,"jobTitle" => "Window Trimmer","started" => 1301386589024,"dob" => 35495285174,"status" => "Suspended"],
			["id" => 25,"firstName" => "Whiney","lastName" => "Schug","something" => 1307849405355,"jobTitle" => "Financial Accountant","started" => 1306555903074,"dob" => 435274848084,"status" => "Disabled"],
			["id" => 26,"firstName" => "Dennise","lastName" => "Halladay","something" => 1337981034973,"jobTitle" => "Geophysicist","started" => 1322643709717,"dob" => 181548946421,"status" => "Active"],
			["id" => 27,"firstName" => "Celia","lastName" => "Leister","something" => 1309315284479,"jobTitle" => "Commercial Lender","started" => 1331516367758,"dob" => -264359348487,"status" => "Disabled"],
			["id" => 28,"firstName" => "Karon","lastName" => "Klotz","something" => 1320236999249,"jobTitle" => "Route Sales Person","started" => 1317976956544,"dob" => -305463328126,"status" => "Suspended"],
			["id" => 29,"firstName" => "Myesha","lastName" => "Kyger","something" => 1314407559398,"jobTitle" => "LAN Systems Administrator","started" => 1376934306176,"dob" => -218657222188,"status" => "Disabled"],
			["id" => 30,"firstName" => "Beariz","lastName" => "Ortego","something" => 1310918048393,"jobTitle" => "Commercial Lender","started" => 1326301928745,"dob" => 17930742800,"status" => "Suspended"],
		];
		// header("HTTP/1.0 200");
		// === for Allow Cross Domain Webservice ===
		header('Access-Control-Allow-Origin: *');
		// header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		// header("Access-Control-Allow-Headers: Origin");
		// === for Allow Cross Domain Webservice ===
		header('Content-Type: application/json');
		header('Accept-Ranges: bytes');
		exit(json_encode($arr));
		// return [TRUE, ['result' => $arr]];
	}

	function get_rows2($request)
	{
		$arr = [
			["id" => 1,"firstName" => "Annemarie","lastName" => '<span class="label label-danger">Designer</span>',"something" => 1381105566987,"jobTitle" => "Cloak Room Attendant","started" => 1367700388909,"dob" => 122365714987,"status" => "Suspended"],
			["id" => 2,"firstName" => "Nelly","lastName" => "Lusher","something" => 1267237540208,"jobTitle" => "Broadcast Maintenance Engineer","started" => 1382739570973,"dob" => 183768652128,"status" => "Disabled"],
			["id" => 3,"firstName" => "Lorraine","lastName" => "Kyger","something" => 1263216405811,"jobTitle" => "Geophysicist","started" => 1265199486212,"dob" => 414197000409,"status" => "Active"],
			["id" => 4,"firstName" => "Maire","lastName" => "Vanatta","something" => 1317652005631,"jobTitle" => "Gaming Cage Cashier","started" => 1359190254082,"dob" => 381574699574,"status" => "Disabled"],
			["id" => 5,"firstName" => "Whiney","lastName" => "Keasler","something" => 1297738568550,"jobTitle" => "High School Librarian","started" => 1377538533615,"dob" => -11216050657,"status" => "Active"],
			["id" => 6,"firstName" => "Nikia","lastName" => "Badgett","something" => 1283192889859,"jobTitle" => "Clown","started" => 1348067291754,"dob" => -236655382175,"status" => "Active"],
			["id" => 7,"firstName" => "Renea","lastName" => "Stever","something" => 1289586239969,"jobTitle" => "Work Ticket Distributor","started" => 1312738712940,"dob" => 483475202947,"status" => "Disabled"],
			["id" => 8,"firstName" => "Rayna","lastName" => "Resler","something" => 1351969871214,"jobTitle" => "Ordnance Engineer","started" => 1300981406722,"dob" => 267565804332,"status" => "Disabled"],
			["id" => 9,"firstName" => "Sephnie","lastName" => "Cooke","something" => 1318107009703,"jobTitle" => "Accounts Collector","started" => 1348566414201,"dob" => 84698632860,"status" => "Suspended"],
			["id" => 10,"firstName" => "Lauri","lastName" => "Kyles","something" => 1298847936600,"jobTitle" => "Commercial Lender","started" => 1306984494872,"dob" => 647549298565,"status" => "Disabled"],
			["id" => 11,"firstName" => "Maria","lastName" => "Hosler","something" => 1372447291002,"jobTitle" => "Auto Detailer","started" => 1295239832657,"dob" => 92796339552,"status" => "Suspended"],
			["id" => 12,"firstName" => "Lakeshia","lastName" => "Sprinkle","something" => 1296451003728,"jobTitle" => "Garment Presser","started" => 1350695946669,"dob" => 6068444160,"status" => "Suspended"],
			["id" => 13,"firstName" => "Isidra","lastName" => "Dragoo","something" => 1285852466255,"jobTitle" => "Window Trimmer","started" => 1264658548150,"dob" => 129659544744,"status" => "Active"],
			["id" => 14,"firstName" => "Marquia","lastName" => "Ardrey","something" => 1336968147859,"jobTitle" => "Broadcast Maintenance Engineer","started" => 1281348596711,"dob" => 69513590957,"status" => "Disabled"],
			["id" => 15,"firstName" => "Jua","lastName" => "Bottom","something" => 1322560108993,"jobTitle" => "Broadcast Maintenance Engineer","started" => 1350354712910,"dob" => 397465403667,"status" => "Active"],
			["id" => 16,"firstName" => "Delana","lastName" => "Sprouse","something" => 1367925208609,"jobTitle" => "High School Librarian","started" => 1360754556666,"dob" => -101355021375,"status" => "Disabled"],
			["id" => 17,"firstName" => "Annamaria","lastName" => "Pennock","something" => 1385602980951,"jobTitle" => "Photocopying Equipment Repairer","started" => 1267426062440,"dob" => 129358493928,"status" => "Active"],
			["id" => 18,"firstName" => "Junie","lastName" => "Leinen","something" => 1270540402378,"jobTitle" => "Roller Skater","started" => 1343534987824,"dob" => 405467757390,"status" => "Suspended"],
			["id" => 19,"firstName" => "Charles","lastName" => "Hayton","something" => 1309910398220,"jobTitle" => "Ships Electronic Warfare Officer","started" => 1297511155831,"dob" => 603442557419,"status" => "Disabled"],
			["id" => 20,"firstName" => "Lorriane","lastName" => "Roling","something" => 1278850931389,"jobTitle" => "Industrial Waste Treatment Technician","started" => 1279697681249,"dob" => 236380359513,"status" => "Disabled"],
			["id" => 21,"firstName" => "Alice","lastName" => "Goodlow","something" => 1268720188765,"jobTitle" => "State Archivist","started" => 1381306773987,"dob" => 455731231484,"status" => "Disabled"],
			["id" => 22,"firstName" => "Carie","lastName" => "Dragoo","something" => 1384770174557,"jobTitle" => "Financial Accountant","started" => 1277771127047,"dob" => -219020252497,"status" => "Active"],
			["id" => 23,"firstName" => "Gran","lastName" => "Valles","something" => 1337645396364,"jobTitle" => "Childrens Pastor","started" => 1288986457843,"dob" => -227796663726,"status" => "Suspended"],
			["id" => 24,"firstName" => "Jacqulyn","lastName" => "Polo","something" => 1326444321746,"jobTitle" => "Window Trimmer","started" => 1301386589024,"dob" => 35495285174,"status" => "Suspended"],
			["id" => 25,"firstName" => "Whiney","lastName" => "Schug","something" => 1307849405355,"jobTitle" => "Financial Accountant","started" => 1306555903074,"dob" => 435274848084,"status" => "Disabled"],
			["id" => 26,"firstName" => "Dennise","lastName" => "Halladay","something" => 1337981034973,"jobTitle" => "Geophysicist","started" => 1322643709717,"dob" => 181548946421,"status" => "Active"],
			["id" => 27,"firstName" => "Celia","lastName" => "Leister","something" => 1309315284479,"jobTitle" => "Commercial Lender","started" => 1331516367758,"dob" => -264359348487,"status" => "Disabled"],
			["id" => 28,"firstName" => "Karon","lastName" => "Klotz","something" => 1320236999249,"jobTitle" => "Route Sales Person","started" => 1317976956544,"dob" => -305463328126,"status" => "Suspended"],
			["id" => 29,"firstName" => "Myesha","lastName" => "Kyger","something" => 1314407559398,"jobTitle" => "LAN Systems Administrator","started" => 1376934306176,"dob" => -218657222188,"status" => "Disabled"],
			["id" => 30,"firstName" => "Beariz","lastName" => "Ortego","something" => 1310918048393,"jobTitle" => "Commercial Lender","started" => 1326301928745,"dob" => 17930742800,"status" => "Suspended"],
		];
		// header("HTTP/1.0 200");
		// === for Allow Cross Domain Webservice ===
		// header('Access-Control-Allow-Origin: *');
		// header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		// header("Access-Control-Allow-Headers: Origin");
		// === for Allow Cross Domain Webservice ===
		// header('Content-Type: application/json');
		// header('Accept-Ranges: bytes');
		// exit(json_encode($arr));
		return [TRUE, ['result' => $arr]];
	}
}
