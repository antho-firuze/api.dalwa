<?php defined('BASEPATH') OR exit('No direct script access allowed');

class F_Report 
{
	/**
	 * Default config, for header & footer. Also for outputting result to file.
	 *
	 * @var array
	 */
	public $config = [
		'name' 		=> 'formulir_opening_account',
		'title' 	=> 'Formulir Opening Account',
		'author' 	=> 'Simpipro Indonesia',
		'urlPath'	=> NULL,
		'absPath'	=> NULL,
		'output' 	=> FALSE,
	];
	/**
	 * Default variables, for testing report data
	 *
	 * @var array
	 */
	public $var = [
		"simpiID" => "1411",
		"emailID" => "31",
		"ClientID" => "0",
		"NameFirst" => "Fulan",
		"NameMiddle" => null,
		"NameLast" => "Fulanah",
		"TitleFirst" => "Mr.",
		"TitleLast" => "SPd.I",
		"BirthPlace" => "Jakarta",
		"BirthDate" => "2001-01-01",
		"CountryOfBirth" => "Indonesia",
		"IDCardNo" => "123456789",
		"IDCardIssuer" => null,
		"IDCardExpired" => "2020-01-01",
		"IDCardTypeID" => null,
		"KTPCityCode" => "Depok",
		"KTPAddress" => "Jl. Raya Bogor KM.31 No.33",
		"KTPPostalCode" => "16418",
		"NationalityID" => "1",
		"TaxID" => null,
		"Gender" => "M",
		"ReligionID" => "1",
		"ReligionCode" => "Islam",
		"OccupationID" => "9",
		"OccupationCode" => "Enterpreneur",
		"OfficeName" => "PT. SIMPI PROFESIONAL INDONESIA",
		"OfficeAddress" => "Jl. Palakali Raya, No.33",
		"OfficePhone" => "021-12345678",
		"OfficeBusinessActivityID" => null,
		"MaritalStatusID" => "2",
		"SpouseName" => "Fulansyah",
		"SpouseBirthDate" => "2001-01-01",
		"MothersMaidenName" => "Fulansiah",
		"LevelID" => "4",
		"ClientCode" => "AVR-217-000002",
		"ReferralCode" => null,
		"ClientName" => "Fulan Fulanah",
		"CcyID" => null,
		"XRateID" => null,
		"CorrespondenceAddress" => "Jl. Raya Bogor KM.31 No.33",
		"CorrespondenceCity" => null,
		"CorrespondenceCityCode" => "Depok",
		"CorrespondenceProvince" => "Jawa Barat",
		"CorrespondenceCountryID" => null,
		"CorrespondencePostalCode" => "11123",
		"CorrespondencePhone" => "085777974703",
		"CorrespondenceEmail" => "fulan@simpi-pro.com",
		"RiskID" => null,
		"RiskScore" => null,
		"LF" => null,
		"InvestmentObjective" => "4",
		"SourceofFund" => "10",
		"AssetOwner" => "1",
		"IncomeLevel" => "2",
		"AccountNotes" => "558",
		"BankName" => "BANK PEMBANGUNAN DAERAH BANTEN Tbk",
		"BankBranch" => null,
		"BankCountryID" => "1",
		"AccountCcyID" => "IDR",
		"AccountNo" => "12345",
		"AccountName" => "Ahmad",
		"DomicileAddress" => "Jl. Raya Bogor KM.31 No.33",
		"DomicileCityCode" => null,
		"DomicilePostalCode" => null,
		"DomicileCountry" => null,
		"FATCAStatus" => null,
		"TIN" => null,
		"TINCountry" => null,
		"email" => "fulan@simpi-pro.com",
		"full_name" => "Fulan Fulanah"
	];

	function __construct()
	{
		require __dir__ . '/autoload.php';
	}

	/**
	 * Make the report you want.
	 *
	 * @param array $var
	 * @param array $config
	 * @return void
	 */
	function create($var = NULL, $config = NULL)
	{
		// ================================= START DEFINING VARIABLES ======================================
		$var = empty($var) ? (object)$this->var : (object)$var;
		$config = empty($config) ? (object)$this->config : (object)$config;
		isset($config->title) OR $config->title = $this->config['title'];
		isset($config->author) OR $config->author = $this->config['author'];
		isset($config->name) OR $config->name = $this->config['name'];
		$support = (object)[
			'img_01' 		=> __dir__.'/../images/reksadana.png',
			'img_02' 		=> __dir__.'/../images/logo-raiz-invest-indonesia - 126x69.png',
			'na'	=> 'N/A',
			'breakline1' => '<br></br>',
			'breakline2' => '<br></br><br></br>',
			'breakline3' => '<br></br><br></br><br></br>',
			'breakline4' => '<br></br><br></br><br></br><br></br>',
			'breakline5' => '<br></br><br></br><br></br><br></br><br></br>',
			'breakline6' => '<br></br><br></br><br></br><br></br><br></br><br></br>',
			'breakline8' => '<br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br>',	
			'breakline9' => '<br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br>',
			'breakline10' => '<br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br><br></br>',
			'spasi2' => '&ensp;',
			'spasi3' => '&ensp;&nbsp;',
			'spasi4' => '&emsp;',
			'spasi5' => '&emsp;&nbsp;',
			'spasi6' => '&emsp;&nbsp;&nbsp;',
			'spasi7' => '&emsp;&nbsp;&nbsp;&nbsp;',
			'spasi8' => '&emsp;&emsp;',
			'spasi9' => '&emsp;&emsp;&nbsp;',
			'spasi10' => '&emsp;&emsp;&nbsp;&nbsp;',
			'spasi12' => '&emsp;&emsp;&emsp;',
			'spasi13' => '&emsp;&emsp;&emsp;&nbsp;',
			'spasi14' => '&emsp;&emsp;&nbsp;&nbsp;&emsp;',
			'spasi16' => '&emsp;&emsp;&emsp;&emsp;',
			'spasi18' => '&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;',
			'spasi20' => '&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi22' => '&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;',
			'spasi24' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi25' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;',
			'spasi26' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;',
			'spasi28' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi30' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;',
			'spasi32' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi34' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;',
			'spasi40' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi44' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi46' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;',
			'spasi48' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi49' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;',
			'spasi50' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&emsp;',
			'spasi51' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&emsp;&nbsp;',
			'spasi60' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi72' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
			'spasi80' => '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;',
		];
		$mpdf = new \Mpdf\Mpdf([
			'default_font' => 'arial',
			'setAutoTopMargin' => 'stretch',
			'mode' 					=> 'utf-8',
			'format' 				=> 'A4-P',
			'margin_left' 	=> 15,
			'margin_right' 	=> 15,
			'margin_top' 		=> 16,
			'margin_top' 		=> 16,
			'margin_header' => 9,
			'margin_footer' => 9,
			]);
		$mpdf->SetTitle($config->title);
		$mpdf->SetAuthor($config->author);
		// ================================= END DEFINING VARIABLES ======================================

		// ================================= START REPORTING LINE ======================================
		$htmlSatu = '<br></br>
					<table border="0" style="width:100%;">
						<tr>
							<td style="width:80%;">
								<img src="'.$support->img_01.'">
							</td>
							<td>
								<img src="'.$support->img_02.'">
							</td>
						</tr>
					</table>
					'.$support->breakline6.'
					<table border="0" style="width:100%;">
						<tr>
							<td style="text-align:center; font-size:28px;">
								<strong>FORMULIR PEMBUKAAN REKENING</strong><br></br>
								<div id="fontbaru" style="font-size:20px;">
								<em>ACCOUNT OPENING FORM</em><br></br>
								</div>
								<strong>REKENING INDIVIDU</strong><br></br>
								<div id="fontbaru" style="font-size:20px;">
								<em>INDIVIDUAL ACCOUNT</em>
								</div>
							</td>
						</tr>
					</table>
					'.$support->breakline6.'
					<table border="0" style="width:100%;">
										<tr>
												<td style=" width:170px; height:auto; font-size:14px;">No.CIF</td>
												<td style=" width:10px; height:auto; font-size:14px;">: </td>
												<td style="border-bottom:1pt solid black; font-size:14px;"></td>
										</tr>
										<tr>
												<td style=" width:170px; height:auto; font-size:20px;">&nbsp;</td>
												<td style=" width:10px; height:auto; font-size:20px;">&nbsp;</td>
												<td style="font-size:20px;">&nbsp;</td>
										</tr>
										<tr>
												<td style="height:auto;">Kode Nasabah</td>
												<td style=" width:10px; height:auto; font-size:14px;">: </td>
												<td style="border-bottom:1pt solid black; font-size:14px;"></td>
										</tr>
										<tr>
												<td style=" width:170px; height:auto; font-size:20px;">&nbsp;</td>
												<td style=" width:10px; height:auto; font-size:20px;">&nbsp;</td>
												<td style="font-size:20px;">&nbsp;</td>
										</tr>
										<tr>
												<td style="height:auto;">Nama Nasabah</td>
												<td style=" width:10px; height:auto; font-size:14px;">: </td>
												<td style="border-bottom:1pt solid black; font-size:14px;">'.$var->full_name.'</td>
										</tr>
										<tr>
												<td style=" width:170px; height:auto; font-size:20px;">&nbsp;</td>
												<td style=" width:10px; height:auto; font-size:20px;">&nbsp;</td>
												<td style="font-size:20px;">&nbsp;</td>
										</tr>
										<tr>
												<td style="height:auto;">Nama dan Kode Sales</td>
												<td style=" width:10px; height:auto; font-size:14px;">: </td>
												<td style="border-bottom:1pt solid black; font-size:14px;"></td>
										</tr>
										<tr>
												<td style=" width:170px; height:auto; font-size:20px;">&nbsp;</td>
												<td style=" width:10px; height:auto; font-size:20px;">&nbsp;</td>
												<td style="font-size:20px;">&nbsp;</td>
										</tr>
										<tr>
												<td style="height:auto;">Kantor Pemasaran</td>
												<td style=" width:10px; height:auto; font-size:14px;">: </td>
												<td style="border-bottom:1pt solid black; font-size:14px;"></td>
										</tr>
					</table>
					'.$support->breakline10.'
					<p style="text-align:center"><strong>PT RAIZ INVEST INDONESIA</strong><br></br>
					The Energy Building, lantai 9, SCBD Lot 11A Jl.Jend. Sudirman kav 52-53, Jakarta 12190<br></br>
					Tel +62 21 2991 9053; Fax : +62 21 2991 9054, www.raizinvest.id</p>
				<pagebreak>';
		$mpdf->WriteHTML($htmlSatu);
		
		$htmlDua = '	
					<table border="0" style="border-collapse:collapse; width:100%;">
					<tr>
						<td valign="top" style="height:auto; font-size:14px;">
							<strong>PROFIL PEMBUKAAN REKENING/</strong><em> ACCOUNT OPENING FORM</em><br></br>
							<strong>REKENING INDIVIDU/</strong><em> INDIVIDUAL ACCOUNT</em>
						</td>
					</tr>
					<tr>
						<td valign="top" style="height:auto; font-size:14px;">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="height:auto; font-size:14px;">
							<strong>PT RAIZ INVEST INDONESIA/</strong><br></br>
							The Energy Building, lantai 9, SCBD Lot 11A Jl.Jend. Sudirman kav 52-53, Jakarta 12190<br></br>
							Tel +62 21 2991 9053; Fax : +62 21 2991 9054, www.raizinvest.id
						</td>
					</tr>
					<tr>
						<td valign="top" style="height:auto; height: 70px;">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="height:auto; font-size:14px;">
							<strong>I.'.$support->spasi4.'INFORMASI NASABAH/</strong><em> CUSTOMER INFORMATION</em>
						</td>
					</tr>
					</table>
					<table border="0" style="width:100%; margin-left:30px;">
										<tr>
												<td style="width:255px; height:30px; font-size:11px;"><strong>1.'.$support->spasi4.'Nama lengkap sesuai Kartu identitas/</strong><br></br><em>'.$support->spasi8.'&nbsp;Name According to ID Card</em></td>
												<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->full_name.'</td>
										</tr>
										<tr>
												<td style="height:auto; font-size:11px"><strong>2.'.$support->spasi4.'Nama Depan/</strong><em> First Name</em></td>
												<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->NameFirst.($var->NameMiddle ? ' '.$var->NameMiddle : '').'</td>
										</tr>
										<tr>
												<td style="height:auto; font-size:11px"><strong>3.'.$support->spasi4.'Nama Keluarga/</strong><em> Family Name</em></td>
												<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->NameLast.'</td>
										</tr>
										<tr>
												<td style="height:auto; font-size:11px"><strong>4.'.$support->spasi4.'Nama Ibu Kandung sebelum menikah/</strong><em> '.$support->spasi8.'&nbsp;Mothers name before married</em></td>
												<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->MothersMaidenName.'</td>
										</tr>
						<tr>
							<td style="height:25px; font-size:11px"><strong>5.'.$support->spasi4.'Jenis Kelamin/</strong><em> Sex</em></td>
							<td style="font-size:11px; ">
								<input type="checkbox" '.($var->Gender == 'M' ? 'checked="checked"' : '').'>
								<label class="container">&emsp;<strong>Pria/</strong><em> Male</em>
									<span class="checkmark"></span>
								</label>
								&emsp;
								<input type="checkbox" '.($var->Gender == 'F' ? 'checked="checked"' : '').'>
								<label class="container">&emsp;<strong>Wanita/</strong><em> Female</em>
									<span class="checkmark"></span>
								</label>
							</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px"><strong>6.'.$support->spasi4.'Kewarganegaraan/</strong><em> Nationality</em></td>
							<td style="font-size:11px; ">
								<input type="checkbox" '.($var->LF == 'L' ? 'checked="checked"' : '').'>
								<label class="container">&emsp;<strong>Indonesia/</strong><em> Indonesian</em>
									<span class="checkmark"></span>
								</label>
								&emsp;
								<input type="checkbox" '.($var->LF == 'F' ? 'checked="checked"' : '').'>
								<label class="container">&emsp;<strong>Asing/</strong><em> Foreigner</em>
									<span class="checkmark"></span>
								</label>
							</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px"><strong>7.'.$support->spasi4.'Tempat & tanggal lahir/</strong><em> <br></br>'.$support->spasi8.'&nbsp;Place and Date of Birth</em></td>
							<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->BirthPlace.' / '.$var->BirthDate.'</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px"><strong>8.'.$support->spasi4.'Kartu Identitas*/</strong><em> ID Card</em></td>
						</tr>
					</table>
					<table border="0" style="width:100%; margin-left:8%;">
						<tr>
							<td style="width:230px; height:25px; font-size:11px;"><strong>a. Jenis kartu identitas/</strong>'.$support->breakline1.$support->spasi4.'<em>&nbsp;Identity Card Type</em></td>
							<td style="font-size:11px; vertical-align:top;">
								<input type="checkbox">
								<label class="container">&emsp;<strong>KTP/</strong><em> National ID Card</em>
									<span class="checkmark"></span>
								</label>														
							</td>
							<td style="font-size:11px; vertical-align:top;">
								<input type="checkbox">
								<label class="container">&emsp;<strong>Paspor/</strong><em> Passport</em>
									<span class="checkmark"></span>
								</label>
							</td>
						</tr>
					</table>
					
								<table border="0" style="width:100%; margin-left:8%;">
								<tr>
										<td valign="top" style="width:230px; height:25px; font-size:11px;"><strong>b. No Kartu Identitas/</strong>'.$support->breakline1.$support->spasi4.'<em>&nbsp;Identity Number</em></td>
										<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->IDCardNo.'</td>
								</tr>
								<tr>
										<td style="width:230px; height:25px; font-size:11px;"><strong>c. Berlaku sampai dengan/</strong>'.$support->breakline1.$support->spasi4.'<em>&nbsp;Valid until</em></td>
										<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->IDCardExpired.'</td>
								</tr>
					</table>
					
					<table border="0" style="width:100%; margin-left:8%;">
						<tr>
							<td style="height:30px; font-size:11px; letter-spacing: -0.5px;"><strong> Bagi WNI kartu identitas utama yang digunakan hanya KTP, sedangkan bagi WNA yang digunakan adalah Paspor/</strong>'.$support->breakline1.'<em> For Indonesian citizens the main identity card used is only KTP, while for the foreigners used are passports</em></td>
						</tr>
					</table>
					
					<table border="0" style="width:100%; margin-left:30px;">
						<tr>
							<td style="width:250px; height:30px; font-size:11px;"><strong>9.'.$support->spasi4.'No NPWP/</strong><em> NPWP Number</em></td>
							<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->TaxID.'</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px;"><strong>'.$support->spasi8.'<font size="1"> </font>Tanggal registrasi/</strong><em> Registrastion date</em></td>
							<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px;"><strong>10.'.$support->spasi4.'Alamat sesuai kartu identitas/</strong>'.$support->breakline1.$support->spasi10.'<em> Address according to ID Card</em></td>
							<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->KTPAddress.'</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px;"></td>
							<td style="font-size:11px;">RT '.$support->spasi4.$support->na.$support->spasi4.' RW '.$support->spasi4.$support->na.$support->spasi4.' Kelurahan '.$support->spasi4.$support->na.$support->spasi4.'</td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px;"></td>
							<td style="font-size:11px;"><strong>Kecamatan/ </strong>'.$support->spasi4.$support->na.$support->spasi4.' <strong>Kota/ </strong>'.$support->spasi4.$var->KTPCityCode.' <br></br><em> Sub-district</em>'.$support->spasi46.'<em> City</em></td>
						</tr>
						<tr>
							<td style="height:25px; font-size:11px;"></td>
							<td style="font-size:11px;"><strong>Provinsi/ </strong>'.$support->spasi10.$support->na.$support->spasi4.' <strong>Kode Pos/ </strong>'.$support->spasi4.$var->KTPPostalCode.' <br></br><em> Province</em>'.$support->spasi51.'<em> Postal Code</em></td>
						</tr>
					</table>
					'.$support->breakline6.'
					<table border="0" style="width:150px; margin-left:505px;">
						<tr>
							<td style="border:1px solid #000"><p style="font-size:11px">Paraf/Initial:'.$support->breakline4.'</p></td>
						</tr>
					</table>
					<pagebreak>';
		$mpdf->WriteHTML($htmlDua);
		
		$htmlTiga = '<br></br>
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								<td style="width:255px; height:25px; font-size:11px;"><strong>11.'.$support->spasi4.'Alamat korespondensi/</strong>'.$support->breakline1.$support->spasi10.'<em> Correspondent Address</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->CorrespondenceAddress.'</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px;"></td>
								<td style="font-size:11px;">RT '.$support->na.$support->spasi4.' RW '.$support->spasi4.$support->na.$support->spasi4.' Kelurahan '.$support->spasi4.$support->na.$support->spasi4.'</td>
												</tr>
												<tr>
														<td style="height:25px; font-size:11px;"></td>
														<td style="font-size:11px;"><strong>Kecamatan/ </strong>'.$support->spasi4.$support->na.$support->spasi4.' <strong>Kota/ </strong>'.$support->spasi4.$var->CorrespondenceCityCode.' <br></br><em> Sub-district</em>'.$support->spasi46.'<em> City</em></td>
												</tr>
												<tr>
														<td style="height:25px; font-size:11px;"></td>
														<td style="font-size:11px;"><strong>Provinsi/ </strong>'.$support->spasi10.$var->CorrespondenceProvince.$support->spasi4.' <strong>Kode Pos/ </strong>'.$support->spasi4.$var->CorrespondencePostalCode.' <br></br><em> Province</em>'.$support->spasi51.'<em> Postal Code</em></td>
												</tr>
							<tr>
								<td style="height:auto; font-size:11px"><strong>12.'.$support->spasi4.'Nomor telepon rumah/</strong><br></br><em> '.$support->spasi10.'&nbsp;Home Phone Number</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="height:auto; font-size:11px"><strong>13.'.$support->spasi4.'Nomor faksimili/</strong><em>&nbsp;Fax</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="height:auto; font-size:11px"><strong>14.'.$support->spasi4.'Nomor telepon genggam/</strong><br></br><em> '.$support->spasi10.'&nbsp;Phone Number</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->CorrespondencePhone.'</td>
							</tr>
							<tr>
								<td style="height:auto; font-size:11px"><strong>15.'.$support->spasi4.'Alamat e-mail/</strong><em>&nbsp;Email address</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->CorrespondenceEmail.'</td>
							</tr>
							<tr>
								<td style="height:auto; font-size:11px"><strong>16.'.$support->spasi4.'Agama/</strong><em>&nbsp;Religion</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->ReligionCode.'</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong>17.'.$support->spasi4.'Pendidikan terakhir/</strong><em>&nbsp;Education</em></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->LevelID == 1 ? 'checked="checked"' : '').'>
									<label class="container"><strong> SD/</strong><em> Primary School</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->LevelID == 5 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Sarjana/</strong><em> Bachelor</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->LevelID == 2 ? 'checked="checked"' : '').'>
									<label class="container"><strong> SMP/</strong><em> Junior High School</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->LevelID == 6 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Pascasarjana/</strong><em> Postgraduate</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->LevelID == 3 ? 'checked="checked"' : '').'>
									<label class="container"><strong> SMA/</strong><em> High School</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->LevelID == 7 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Doktor/</strong><em> Doctor</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->LevelID == 4 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Diploma/</strong><em> Diploma</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->LevelID == 8 ? 'checked="checked"' : '').'>
									<label class="container"><strong> lainnya/</strong><em> Others</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong>18.'.$support->spasi4.'Pekerjaan/</strong><em> Occupation</em></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->OccupationID == 1 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Pelajar atau mahasiswa/</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->OccupationID == 5 ? 'checked="checked"' : '').'>
									<label class="container"><strong> TNI atau POLRI/</strong><br></br> '.$support->spasi9.'<em> Student '.$support->spasi49.' Army/Police</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->OccupationID == 8 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Karyawan/</strong><em> Employee</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->OccupationID == 2 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Ibu Rumah Tangga/</strong><em> Housewife</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->OccupationID == 3 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Wiraswasta/</strong><em> Entrepreneur</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->OccupationID == 6 ? 'checked="checked"' : '').'>
									<label class="container"><strong> Pensiun/</strong><em> Pension</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" '.($var->OccupationID == 8 ? 'checked="checked"' : '').'>
									<label class="container"><strong> PNS & Pegawai BUMN/</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->OccupationID == 9 ? 'checked="checked"' : '').'>
									<label class="container"><strong> lainnya/</strong><em>Others</em><br></br><em>'.$support->spasi9.' Government employees </em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:8%;">
							<tr>
								<td style="width:230px; height:auto; font-size:11px;">&ensp;<strong>a. Nama Perusahaan/</strong><em>&nbsp;Company</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->OfficeName.'</td>
							</tr>
							<tr>
								<td style="width:230px; height:auto; font-size:11px;">&ensp;<strong>b. Bidang Usaha/</strong><em>&nbsp;Business fields</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:230px; height:auto; font-size:11px;">&ensp;<strong>c. Jabatan/</strong><em>&nbsp;Position</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->OccupationCode.'</td>
							</tr>
							<tr>
								<td style="width:230px; height:auto; font-size:11px;">&ensp;<strong>d. Alamat perusahaan/</strong>'.$support->breakline1.$support->spasi6.'<em>&nbsp;Company address</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->OfficeAddress.'</td>
							</tr>
							<tr>
								<td style="width:230px; height:auto; font-size:11px;">&ensp;<strong>e. Nomor telp perusahaan/</strong>'.$support->breakline1.$support->spasi6.'<em>&nbsp;Company phone number</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->OfficePhone.'</td>
							</tr>
							<tr>
								<td style="width:230px; height:auto; font-size:11px;">&ensp;<strong>f. Nomor faksimili/</strong><em>&nbsp;Fax</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
						</table>
						
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								//letter-spacing:-1px;
								<td style="width:255px; height:25px; font-size:11px"><strong>19.'.$support->spasi4.'Penghasilan setahun/</strong>'.$support->breakline1.$support->spasi10.'<em> Annual income</em></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox" '.($var->IncomeLevel == 2 ? 'checked="checked"' : '').'>
									<label class="container"><strong> < Rp. 50.000.000</strong>
										<span class="checkmark"></span>
									</label>
									&ensp;
									<input type="checkbox" '.($var->IncomeLevel == 5 ? 'checked="checked"' : '').'>
									<label class="container"><strong>Rp. 500.000.000 < Rp 1.000.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox" '.($var->IncomeLevel == 3 ? 'checked="checked"' : '').'>
									<label class="container"><strong>Rp. 50.000.000 < Rp 100.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox" '.($var->IncomeLevel == 6 ? 'checked="checked"' : '').'>
									<label class="container"><strong>Rp. 1.000.000.000 < Rp 5.000.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>							
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox" '.($var->IncomeLevel == 4 ? 'checked="checked"' : '').'>
									<label class="container"><strong>Rp. 100.000.000 < Rp 500.000.000</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" '.($var->IncomeLevel == 7 ? 'checked="checked"' : '').'>
									<label class="container"><strong>> Rp 5.000.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong>20.'.$support->spasi4.'Nama ahli waris/</strong><em>&nbsp;name of heir</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.' <strong>Hubungan</strong>/<em> Relation </em>'.$support->na.'</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.' <strong>Hubungan</strong>/<em> Relation </em>'.$support->na.'</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								<td style="width:400px; height:25px; font-size:11px"><strong>21.'.$support->spasi4.'Pihak keluarga tidak serumah yang dapat dihubungi dalam kondisi darurat/</strong>'.$support->breakline1.$support->spasi10.'<em> Non-homemakers can be contacted in an emergency</em></td>
							</tr>
						</table>
						
						<table border="0" style="width:100%; margin-left:8.5%;">
							<tr>
								<td style="width:225px; height:auto; font-size:11px"><strong>Nama Lengkap/</strong><em> Full name</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px"><strong>Hubungan/</strong><em> Relation</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px"><strong>Alamat/</strong><em> Address</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px"><strong>No telepon/</strong><em> Phone number</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
						</table>
						
						<table border="0" style="width:150px; margin-left:505px;">
							<tr>
								<td style="border:1px solid #000"><p style="font-size:11px">Paraf/Initial:'.$support->breakline4.'</p></td>
							</tr>
						</table>
				<pagebreak>';
		$mpdf->WriteHTML($htmlTiga);
		
		$htmlEmpat = '	<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>22.'.$support->spasi4.'Sumber dana investasi/</strong><br></br><em>'.$support->spasi12.'Investment fund source</em></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Gaji/</strong><em> Salary</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox">
									<label class="container"><strong> Hasil Investasi/</strong><em> Investment Results</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Hasil usaha/</strong><em> Company results</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" checked="checked">
									<label class="container"><strong> Lainnya/</strong><em> Others</em><br></br>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Orang tua/</strong><em> Parents</em>'.$support->spasi34.'&nbsp;<u></u>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong>23. Tujuan investasi/</strong><em> investment goal</em></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Keamanan dana investasi/</strong><em> security of investment funds</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox" checked="checked">
									<label class="container"><strong> Pendapatan dan pertumbuhan dalam jangka panjang/</strong><br></br>'.$support->spasi6.'<em> Long-term income and growth</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Pendapatan dari keamanan dana investasi/</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox">
									<label class="container"><strong> Pertumbuhan/ </strong><br></br><em>'.$support->spasi8.'Income from security of investment funds'.$support->spasi25.' Growth</em> 
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
						</table>
						<table border="0" style="width:100%;">
							<tr>
								<td style="width:100%; height:auto; font-size:14px;">
									<strong>II.'.$support->spasi4.'INFORMASI KELUARGA/</strong><em> FAMILY INFORMATION</em>
								</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								<td style="width:250px; height:auto; font-size:11px;"><strong>1.'.$support->spasi4.'Status perkawinan/</strong><em>&nbsp;Marital status</em></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox">
									<label class="container"><strong> Lajang/</strong><em> Single</em>
										<span class="checkmark"></span>
									</label>
									<input type="checkbox">
									<label class="container"><strong> Menikah/</strong><em> Married</em>
										<span class="checkmark"></span>
									</label>
									<input type="checkbox">
									<label class="container"><strong> Janda/Duda/</strong><em> Widow/Widower</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong>2.'.$support->spasi4.'Nama pasangan/Orang Tua/</strong>'.$support->breakline1.$support->spasi7.'<em> Partner/Parents Name</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"><strong>3.'.$support->spasi4.'Kartu Identitas*/</strong><em> ID Card</em></td>
							</tr>
						</table>
						
						<table border="0" style="width:100%; margin-left:8%;">
							<tr>
								<td style="width:225px; height:25px; font-size:11px;"><strong>a. Jenis kartu identitas/</strong>'.$support->breakline1.$support->spasi4.'<em>&nbsp;Identity Card Type</em></td>
								<td style="font-size:11px; vertical-align:top;">
									<input type="checkbox" checked="checked">
									<label class="container"> <strong>KTP/</strong><em> National ID Card</em>
										<span class="checkmark"></span>
									</label>														
								</td>
								<td style="font-size:11px; vertical-align:top;">
									<input type="checkbox">
									<label class="container"> <strong>Paspor /</strong><em> Passport </em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
						</table>
		
						<table border="0" style="width:100%; margin-left:8%;">
							<tr>
								<td style="width:225px; height:auto; font-size:11px;"><strong>b. No Kartu Identitas/</strong>'.$support->breakline1.$support->spasi4.'<em>&nbsp;Identity Number</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px;"><strong>c. Berlaku sampai dengan/</strong>'.$support->breakline1.$support->spasi4.'<em>&nbsp;Valid until</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
						</table>
						
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>4.'.$support->spasi4.'Pekerjaan/</strong><em> Occupation</em></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Pelajar atau mahasiswa/</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox">
									<label class="container"><strong> TNI atau POLRI/</strong><br></br> '.$support->spasi13.'<em> Student '.$support->spasi49.' Army/Police</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Karyawan/</strong><em> Employee</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox" checked="checked">
									<label class="container"><strong> Ibu Rumah Tangga/</strong><em> Housewife</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> Wiraswasta/</strong><em> Entrepreneur</em>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox">
									<label class="container"><strong> Pensiun/</strong><em> Pension</em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; ">
									<input type="checkbox">
									<label class="container"><strong> PNS & Pegawai BUMN/</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox">
									<label class="container"><strong> lainnya/</strong><em>Others</em><br></br><em>'.$support->spasi13.' Government employees </em>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:8%;">
							<tr>
								<td style="width:225px; height:auto; font-size:11px;">&ensp;<strong>a. Nama Perusahaan/</strong><em>&nbsp;Company</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px;">&ensp;<strong>b. Bidang Usaha/</strong><em>&nbsp;Business fields</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px;">&ensp;<strong>c. Jabatan/</strong><em>&nbsp;Position</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:225px; height:auto; font-size:11px;">&ensp;<strong>d. Alamat perusahaan/</strong>'.$support->breakline1.$support->spasi6.'<em>&nbsp;Company address</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								//letter-spacing:-1px;
								<td style="width:250px; height:25px; font-size:11px"><strong>5.'.$support->spasi4.'Penghasilan setahun/</strong>'.$support->breakline1.$support->spasi8.'<em> Annual income</em></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox">
									<label class="container"><strong> < Rp. 50.000.000</strong>
										<span class="checkmark"></span>
									</label>
									&ensp;
									<input type="checkbox">
									<label class="container"><strong>Rp. 500.000.000 < Rp 1.000.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox">
									<label class="container"><strong>Rp. 50.000.000 < Rp 100.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox">
									<label class="container"><strong>Rp. 1.000.000.000 < Rp 5.000.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>							
							</tr>
							<tr>
								<td style="height:25px; font-size:11px"></td>
								<td style="font-size:11px; letter-spacing:-0.5px;">
									<input type="checkbox">
									<label class="container"><strong>Rp. 100.000.000 < Rp 500.000.000</strong>
										<span class="checkmark"></span>
									</label>
									&emsp;
									<input type="checkbox">
									<label class="container"><strong>> Rp 5.000.000.000</strong>
										<span class="checkmark"></span>
									</label>
								</td>
							</tr>
						</table>
						<table border="0" style="width:100%;">
							<tr>
								<td style="width:100%; height:auto; font-size:14px;">
									<strong>III.'.$support->spasi4.'KETERANGAN BANK/</strong><em> BANK DETAIL</em>
								</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:30px;">
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>1.'.$support->spasi4.'Nomor rekening rupiah/</strong>'.$support->breakline1.$support->spasi8.'<em>&nbsp;Account number in rupiah</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>'.$support->spasi6.' Nama Bank/</strong><em>&nbsp;Bank Name</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$var->BankName.' <strong>Cabang</strong>/<em> Branch </em>'.$var->BankBranch.'</td>
							</tr>
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>'.$support->spasi6.' Jenis rekening/</strong><em>&nbsp;Account type</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>2.'.$support->spasi4.'Nomor rekening Dolar AS/</strong>'.$support->breakline1.$support->spasi8.'<em>&nbsp;Account number in US Dollar</em></td>
								<td valign="bottom" style="border-bottom:0.5pt solid black; solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>'.$support->spasi6.' Nama Bank/</strong><em>&nbsp;Bank Name</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.' <strong>Cabang</strong>/<em> Branch </em>'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:250px; height:25px; font-size:11px"><strong>'.$support->spasi6.' Jenis rekening/</strong><em>&nbsp;Account type</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
						</table>
						<table border="0" style="width:100%; margin-left:7.25%;">
							<tr>
								<td style="height:30px; font-size:11px; letter-spacing: -0.5px;"><strong> Bagi WNI kartu identitas utama yang digunakan hanya KTP, sedangkan bagi WNA yang digunakan adalah Paspor/</strong>'.$support->breakline1.'<em> For Indonesian citizens the main identity card used is only KTP, while for the foreigners used are passports</em></td>
							</tr>
						</table>
						<table border="0" style="width:150px; margin-left:505px;">
							<tr>
								<td style="border:1px solid #000"><p style="font-size:11px">Paraf/Initial:'.$support->breakline4.'</p></td>
							</tr>
						</table>
					<pagebreak>';
		$mpdf->WriteHTML($htmlEmpat);
		
		$htmlLima = '
					<table border="0" style="width:100%;">
						<tr>
							<td style="height:auto; font-size:14px; letter-spacing:-0.5px;">
								<strong>IV.'.$support->spasi4.'KETERANGAN BANK/</strong><em> BANK DESCRIPTION</em></td>
							<td style="font-size:11px; letter-spacing:-0.5px; vertical-align:top;">&nbsp;</td>
						</tr>
						<tr>
							<td style="width:500px; height:auto; font-size:10px; letter-spacing:-0.5px;"><strong>'.$support->spasi16.'Apakah Anda atau keluarga dekat Anda sedangatau pernah memegang jabatan di pemerintahan ?/</strong><br></br><em>'.$support->spasi16.'Are you or your immediate family or have held a position in government?</em></td>
							<td style="font-size:11px; letter-spacing:-0.5px; vertical-align:top;">
								'.$support->spasi2.'
								<input type="checkbox">
								<label class="container"><strong>Ya/ </strong><em>Yes</em>
									<span class="checkmark"></span>
								</label>
								&emsp;
								<input type="checkbox" checked="checked">
								<label class="container"><strong>Tidak/ </strong><em>No</em>
									<span class="checkmark"></span>
								</label>
							</td>
						</tr>
						<tr>
							<td style="width:500px; height:auto; font-size:10px; letter-spacing:-0.5px;"><strong>'.$support->spasi16.' Jika Ya, mohon diisi pertanyaan berikut ini/ </strong><em>If Yes, please fill in the following question</em></td>
						</tr>
					</table>
					<table border="0" style="width:100%; margin-left:12px;">
							<tr>
								<td style="width:270px; height:20px; font-size:11px"><strong>'.$support->spasi8.' Nama/</strong><em>&nbsp;Name</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:270px; height:20px; font-size:11px"><strong>'.$support->spasi8.' Nama instansi/</strong><em>&nbsp;Name of agency</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:270px; height:20px; font-size:11px"><strong>'.$support->spasi8.' Jabatan/</strong><em>&nbsp;Occupation</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:270px; height:20px; font-size:11px"><strong>'.$support->spasi8.' Masa jabatan/</strong><em>&nbsp;Length of service</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:270px; height:20px; font-size:11px"><strong>'.$support->spasi8.' Hubungan keluarga/</strong><em>&nbsp;Family relationship</em></td>
								<td style="border-bottom:0.5pt solid black; font-size:11px;">'.$support->na.'</td>
							</tr>
							<tr>
								<td style="width:270px; height:auto; font-size:4px"><strong>&nbsp;</td>
								<td style="font-size:4px;">&nbsp;</td>
							</tr>
					</table>
		
					<table border="0" style="border-collapse:collapse; width:100%;">
						<tr>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:left; width:70%; height:20px;; font-size:14px;">
								<strong>V.'.$support->spasi4.'PERNYATAAN NASABAH/</strong><em> CUSTOMER DECLARATION</em>'.$support->breakline1.'
							</td>
						</tr>
						<tr>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:justify; height:20px; font-size:11px; letter-spacing:-0.5px;">
								<strong>Dengan menandatangani formulir ini Saya/Kami menyatakan bahwa semua data yang Saya/Kami isi adalah benar. Saya/Kami telah bertemu dengan marketing officer PT Raiz Invest Indonesia yang memiliki ijin WAPERD, dan telah membaca dan mengerti isi prospektus dan dengan ini menyatakan terikat pada persyaratan-persyaratan serta ketentuan yang tercantum dalam prospektus, Kontrak Investasi Kolektif, dan formulir ini. Informasi yang diberikan dalam Formulir Pembukaan Rekening Nasabah ini adalah benar dan lengkap. Nasabah akan segera memberitahukan PT Raiz Invest Indonesia dan setiap klaim, gugatan, ganti rugi dan tanggung jawab hukum dalam bentuk apapun yang mungkin timbul apabila terdapat kekeliruan atau kesalahan dalam penanganan rekening nasabah di PT Raiz Invest Indonesia sebagai akibat kelalaian atau kekeliruan nasabah dalam melakukan pengkinian data secara lengkap. Saya mengerti bahwa penggunaan uang tunai atau cek/giro tidak dapat dilakukan untuk investasi ini./</strong>
								<em>By signing this form I / We declare that all the data that I / We have filled in is correct. I / We have met with the marketing officer of PT Raiz Invest Indonesia who has a WAPERD license, and has read and understood the contents of the prospectus and hereby states that it is bound to the terms and conditions listed in the prospectus, Collective Investment Contract and this form. The information provided in this Customer Account Opening Form is correct and complete. The Customer will immediately notify PT Raiz Invest Indonesia and any claim, lawsuit, compensation and legal liability in any form that may arise if there is a mistake or error in handling customer accounts at PT Raiz Invest Indonesia as a result of negligence or customer error in updating data completely. I understand that the use of cash or checks / demand deposits cannot be made for this investment.</em>
								'.$support->breakline1.'
								<strong>Tanggal/</strong><em> Date </em><u>'.$support->spasi20.$support->na.$support->spasi20.'</u>
								'.$support->breakline4.$support->spasi32.'
								<u>Nama & Tandatangan Nasabah</u>
								'.$support->breakline1.$support->spasi32.'
								<em>Name & Appoint Customer</em>
							</td>
						</tr>
						<tr>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:justify; height:auto; font-size:9px;">&nbsp;</td>
						</tr>
					</table>
		
					<table border="1" style="border-collapse:collapse; width:100%;">
						<tr>
							<td colspan="2" style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:left; width:100%; height:25px; font-size:14px;">
								<strong>VI.'.$support->spasi4.'DOKUMEN YANG DIPERLUKAN/</strong><em> REQUIRED DOCUMENTS</em>'.$support->breakline1.'
							</td>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:center; height:25px; font-size:14px;"></td>
						</tr>
						<tr>
							<td style="text-align:center; width:25%; height:25px; font-size:10px;"><strong> Ketersediaan/<em> Availability</em></strong></td>
							<td style="text-align:center; width:45%; height:25px; font-size:10px;"><strong> Nama Dokumen/<em>Name of Document</em></strong></td>
							<td style="text-align:center; height:25px; font-size:10px;"><strong> Catatan/<em>Note</em></strong></td>
						</tr>
						<tr>
							<td style="text-align:center; width:25%; height:25px; font-size:10px;">
								<input type="checkbox" checked="checked">
								<label class="container">
									<span class="checkmark"></span>
								</label>
							</td>
							<td style="width:45%; height:25px; font-size:10px;"><strong>&ensp;Fotokopi KTP/Paspor/</strong>'.$support->breakline1.$support->spasi2.'<em>Photocopy of KTP/Passport</em></td>
							<td style="height:25px; font-size:10px;"></td>
						</tr>
						<tr>
							<td style="text-align:center; width:25%; height:25px; font-size:10px;">
								<input type="checkbox">
								<label class="container">
									<span class="checkmark"></span>
								</label>
							</td>
							<td style="width:45%; height:25px; font-size:10px;"><strong>&ensp;Fotokopi NPWP/</strong>'.$support->breakline1.$support->spasi2.'<em>Photocopy of NPWP</em></td>
							<td style="height:25px; font-size:10px;"></td>
						</tr>
						<tr>
							<td style="text-align:center; width:25%; height:25px; font-size:10px;">
								<input type="checkbox">
								<label class="container">
									<span class="checkmark"></span>
								</label>
							</td>
							<td style="width:45%; height:25px; font-size:10px;"><strong>&ensp;Dokumen lainnya/</strong>'.$support->breakline1.$support->spasi2.'<em>Other document</em></td>
							<td style="height:25px; font-size:10px;"></td>
						</tr>
					</table>
					'.$support->breakline1.'
		
					<table border="0" style="border-collapse:collapse; width:100%;">
						<tr>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:left; width:70%; height:20px;; font-size:14px;">
								<strong>VII.'.$support->spasi4.'PERNYATAAN SALES/</strong><em> SALES DECLARATION</em>'.$support->breakline1.'
							</td>
						</tr>
						<tr>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:justify; height:20px; font-size:11px; letter-spacing:-0.5px;">
								<strong>Saya telah melakukan verifikasi awal terhadap seluruh dokumen dan informasi yang diberikan oleh nasabah, termasuk informasi mengenai keberadaan beneficial owner. Saya menyadari PT Raiz Invest Indonesia berhak melakukan verifikasi lanjutan dan dokumen pendukung lainnya atas informasi yang telah diberikan oleh nasabah./ </strong>
								<em>I have made initial verification of all documents and information provided by the customer, including information regarding the existence of the beneficial owner. I am aware that PT Raiz Invest Indonesia has the right to carry out further verification and other supporting documents for information provided by the customer.</em>	
							</td>
						</tr>
						<tr>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:justify; height:20px; font-size:8px;"></td>
						</tr>
					</table>
					<table border="1" style="border-collapse:collapse; width:100%;">
						<tr>
							<td colspan="2" style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:left; width:70%; height:20px;; font-size:14px;">
								<strong>VIII.'.$support->spasi4.'KOLOM PERSETUJUAN/ACKNOWLEDGEMENT</strong><em> APPROVAL</em>'.$support->breakline1.'
							</td>
							<td style="border-left:0; border-top:0; border-right:0; border-bottom:0; text-align:center; height:20px; font-size:14px;"></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center; width:70%; height:20px;; font-size:10px;"><strong> FRONT LINE </strong></td>
							<td style="text-align:center; height:20px; font-size:10px;"><strong> BACK OFFICE </strong></td>
						</tr>
						<tr>
							<td style="text-align:center; width:35%; height:20px; font-size:10px;"><strong> Sales </strong></td>
							<td style="width:35%; height:20px; font-size:10px;"><strong>&ensp;Team Leader/ Head of Sales </strong></td>
							<td style="text-align:center; height:20px; font-size:10px;"><strong> Operations Department </strong></td>
						</tr>
						<tr>
							<td valign="bottom" style="width:35%; height:80px; font-size:10px;"><strong>&ensp;Nama/ </strong><em>Name </em></td>
							<td valign="bottom" style="width:35%; height:80px; font-size:10px;"><strong>&ensp;Nama/ </strong><em>Name </td>
							<td valign="bottom" style="height:80px; font-size:10px;"><strong>&ensp;Nama/ </strong><em>Name </td>
						</tr>
						<tr>
							<td valign="bottom" style="width:35%; height:20px; font-size:10px;"><strong>&ensp;Tanggal/ </strong><em>Date </em></td>
							<td valign="bottom" style="width:35%; height:20px; font-size:10px;"><strong>&ensp;Tanggal/ </strong><em>Date </td>
							<td valign="bottom" style="height:20px; font-size:10px;"><strong>&ensp;Tanggal/ </strong><em>Date </td>
						</tr>
					</table>
					<p style="font-size:10px; text-align:justify; letter-spacing:-0.5px;">
						<strong>PT Raiz Invest Indonesia memiliki hak untuk menolak pembukaan rekening nasabah, dengan pertimbangannya sendiri,tanpa memberikan penjelasan./ </strong>
						<em>PT Raiz Invest Indonesia has the right to refuse opening customer accounts, with its own consideration, without giving an explanation</em>
					</p>
					<table border="0" style="width:150px; margin-left:540px;">
							<tr>
								<td style="border:1px solid #000"><p style="font-size:11px">Paraf/Initial:'.$support->breakline4.'</p></td>
							</tr>
						</table>
					';
		$mpdf->WriteHTML($htmlLima);
		// ================================= END REPORTING LINE ======================================

		return $this->_output($mpdf, $config);
	}

	/**
	 * Private function for outputting report
	 * 
	 * ! DON'T CHANGE THIS FUNCTION OR METHOD
	 * 
	 * @return void
	 */
	function _output($mpdf, $config)
	{
		if ($config->output) {
			isset($config->urlPath) && !empty($config->urlPath) OR $config->urlPath = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['DOCUMENT_URI']).'/';
			isset($config->absPath) && !empty($config->absPath) OR $config->absPath = __dir__.'/';
			$filename = $config->name.'.pdf';

			$mpdf->Output($config->absPath.$filename, \Mpdf\Output\Destination::FILE);
			if (defined('FCPATH'))
				return [TRUE, ['name' => $config->name, 'title' => $config->title, 'link' => $config->urlPath.$filename, 'path' => $config->absPath.$filename]];	
			else 
				exit(json_encode(['name' => $config->name, 'title' => $config->title, 'link' => $config->urlPath.$filename, 'path' => $config->absPath.$filename]));
		}
		$mpdf->Output();
	}
}
