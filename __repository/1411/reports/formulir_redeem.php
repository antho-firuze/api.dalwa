<?php 
namespace formulir_redeem;

defined('BASEPATH') OR exit('No direct script access allowed');

class F_Report 
{
	/**
	 * Default config, for header & footer. Also for outputting result to file.
	 *
	 * @var array
	 */
	public $config = [
		'name' 		=> 'formulir_redeem',
		'title' 	=> 'Formulir Penjualan Reksadana',
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
			'img_01' 		=> __dir__.'/../images/reksadana - 219x88.png',
			'img_02' 		=> __dir__.'/../images/logo-raiz - 257x78.png',
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
			'.$support->breakline1.'
			<table border="0" style="width:100%;">
				<tr>
					<td style="border-bottom:1pt solid black; text-align:center; font-size:20px; font-family: helvetica;">
						<strong>Formulir Penjualan Kembali dan Pengalihan<br></br>Unit Penyertaan Reksa Dana</strong>
					</td>
                </tr>
                <tr>
					<td style="text-align:center; font-size:6px; font-family: helvetica;">
						&nbsp;
					</td>
				</tr>
			</table>
            <table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
				<tr>
					<td style="color:white; font-size:14px; height:25px;">
						&ensp;DATA NASABAH
					</td>
				</tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nama lengkap</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                </tr>
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nomor Investor</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style=" border-bottom:1pt solid black; font-size:12px;"> </td>
				</tr>
				<tr>
                    <td style=" width:20%; height:20px; font-size:12px;"> </td>
                    <td style=" width:10px; height:20px; font-size:12px;">  </td>
                    <td style=" font-size:12px;"> </td>
                </tr>
            </table>
            <table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
				<tr>
					<td style="color:white; font-size:14px; height:25px;">
						&ensp;PENJUALAN KEMBALI
					</td>
				</tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nama Reksa Dana</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
				</tr>
			</table>
			<table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Jumlah Penjualan Kembali</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style=" width:8%; font-size:12px;"> 
                        <input type="checkbox">
                        <label class="container">
							<span class="checkmark">Unit</span>
						</label>
					</td>
					<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
					<td style=" width:10%; font-size:12px;"> 
						<input type="checkbox">
						<label class="container">
						<span class="checkmark">Rupiah</span>
						</label>
					</td>
					<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
				</tr>
				<tr>
                    <td style=" width:20%; height:20px; font-size:12px;"></td>
                    <td style=" width:10px; height:20px; font-size:12px;"> </td>
                    <td style=" width:8%; font-size:12px;"> 
                        
					</td>
					<td style=" font-size:12px;"> </td>
					<td style=" width:10%; font-size:12px;"> 
						
					</td>
					<td style=" font-size:12px;"> </td>
				</tr>
            </table>
            <table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
				<tr>
					<td style="color:white; font-size:14px; height:25px;">
						&ensp;PENGALIHAN
					</td>
				</tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nama Reksa Dana</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                </tr>
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Jumlah Penjualan Kembali</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="font-size:12px;"> Semua Unit Penyertaan
                    </td>
				</tr>
				<tr>
                    <td style=" width:20%; height:10px; font-size:12px;"></td>
                    <td style=" width:10px; height:10px; font-size:12px;"> </td>
                    <td style="font-size:12px;"> </td>
                </tr>
            </table>
            <table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
				<tr>
					<td style="color:white; font-size:14px; height:25px;">
                        &ensp;PEMBAYARAN DENGAN TRANSFER<br></br>
                        <em>&ensp;sesuai dengan data yang terdaftar di S-INVEST</em>
					</td>
				</tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nomor Rekening</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                </tr>
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nama Bank</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                </tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Cabang</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                    <td style=" width:20%; height:40px; font-size:12px;">&ensp;Kota</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                </tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td style=" width:20%; height:40px; font-size:12px;">Nama Pemilik Rekening</td>
                    <td style=" width:10px; height:40px; font-size:12px;">: </td>
                    <td style="border-bottom:1pt solid black; font-size:12px;"> </td>
                </tr>
            </table>
            '.$support->breakline1.'
			';
$mpdf->WriteHTML($htmlSatu);

$htmlSatu = '
            '.$support->breakline1.'
			<table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
				<tr>
					<td style="color:white; font-size:14px; height:25px;">
						&ensp;LEMBAR PERSETUJUAN
					</td>
				</tr>
			</table>
			<table border="1" style=" border-collapse:collapse; width:20%;">
				<tr>
					<td style="border-left:0; border-right:0; border-top:0; font-size:18px;">&nbsp;</td>
				</tr>
			</table>
			<table border="0" style=" border-collapse:collapse; width:100%;">
				<tr>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; font-size:11px; height:20px;">
						'.$support->spasi8.'Tempat & Tanggal
					</td>
					<td valign="middle" style="text-align:center; border-left:0; border-right:0; border-bottom:0; font-size:11px; height:20px;">
						Diisi oleh PT Raiz Invest Indonesia
					</td>
				</tr>
				<tr>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="text-align:center; border-left:0; border-right:0; border-bottom:0; font-size:6px;">
						&nbsp;
					</td>
				</tr>
			</table>
			<table border="1" style=" border-collapse:collapse; width:100%;">
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						Marketing
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						:
					</td>
				</tr>
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						Operations
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						:
					</td>
				</tr>
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						Compliance
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						:
					</td>
				</tr>
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:6px;">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td valign="middle" style="width:40%; text-align:left; border-left:0; border-right:0; border-bottom:0; font-size:11px;">
						'.$support->spasi14.'Tanda Tangan Nasabah dan Nama Jelas
					</td>
					<td valign="middle" style="width:10%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						&nbsp;
					</td>
					<td valign="middle" style="width:15%; text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						Tanggal Disetujui
					</td>
					<td valign="middle" style="text-align:left; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
						:
					</td>
				</tr>
			</table>
			';
		$mpdf->WriteHTML($htmlSatu);
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
