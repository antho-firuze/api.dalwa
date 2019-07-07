<?php defined('BASEPATH') OR exit('No direct script access allowed');

class F_Report 
{
	/**
	 * Default config, for header & footer. Also for outputting result to file.
	 *
	 * @var array
	 */
	public $config = [
		'name' 		=> 'formulir_subscription',
		'title' 	=> 'Formulir Pembelian Reksadana',
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
					<strong>Formulir Pembelian Unit Penyertaan Reksa Dana</strong>
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
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
			</tr>
			<tr>
				<td style=" width:20%; height:40px; font-size:12px;">Jenis Penempatan</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="font-size:12px;"> 
					'.$support->spasi8.'
					<input type="checkbox">
					<label class="container">
						<span class="checkmark">Penempatan Awal</span>
					</label>
					'.$support->spasi20.'
					<input type="checkbox">
					<label class="container">
					   <span class="checkmark">Penempatan Dana</span>
					</label>
				</td>
			</tr>
			<tr>
				<td style=" width:20%; height:40px; font-size:12px;">Nama Reksa Dana</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
			</tr>
		</table>
		<table border="0" style="width:100%;">
			<tr>
				<td style=" width:20%; height:40px; font-size:12px;">Jumlah Pembelian</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
				<td style=" width:20%; height:40px; font-size:12px;">&ensp;Mata Uang</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
			</tr>
		</table>
		<table border="0" style="width:100%;">
			<tr>
				<td style=" width:20%; height:40px; font-size:12px;">Terbilang</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
			</tr>
			<tr>
				<td style=" width:20%; height:40px; font-size:12px;">Bank Pengirim</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
			</tr>
			<tr>
				<td style=" width:20%; height:40px; font-size:12px;">Bank Penerima</td>
				<td style=" width:10px; height:40px; font-size:12px;">: </td>
				<td style="border-bottom:1pt solid black; font-size:12px;"> </td>
			</tr>
			<tr>
				<td style=" width:20%; height:20px; font-size:6px;"> </td>
				<td style=" width:10px; height:20px; font-size:6px;"> </td>
				<td style=" font-size:12px;"> </td>
			</tr>
		</table>
		<table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
			<tr>
				<td style="color:white; font-size:14px; height:25px;">
					&ensp;REKENING INVESTOR*
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
		<table border="0" style="width:100%;">
			<tr>
				<td style=" height:30px; font-size:12px;">
					*Diisi khusus nasabah yang melakukan penempatan awal
				</td>
			</tr>
		</table>
		'.$support->breakline1.'
		';
$mpdf->WriteHTML($htmlSatu);

$htmlSatu ='
		<table border="1" style="background-color: #221b34; border-collapse:collapse; width:100%;">
			<tr>
				<td style="color:white; font-size:14px; height:25px;">
					&ensp;PERNYATAAN
				</td>
			</tr>
		</table>
		<table border="0" style="width:100%;">
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">1.</td>
				<td style=" font-size:12px;">
					Saya menyatakan telah memahami bahwa Reksa Dana adalah produk pasar modal dan bukan merupakan produk Bank, tidak dijamin oleh Bank dan tidak termasuk simpanan yang merupakan objek program penjaminan Pemerintah atau penjaminan simpanan.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">2.</td>
				<td style=" font-size:12px;">
					Saya menyatakan telah memahami bahwa Reksa Dana bukan merupakan produk PT Raiz Invest Indonesia sebagai agen penjual efek Reksa Dana. Reksa Dana merupakan produk yang dikelola oleh Manajer Investasi.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">3.</td>
				<td style=" font-size:12px;">
					Saya memahami bahwa Bank Mitra hanya berfungsi sebagai gerbang pembayaran dari transaksi efek Reksa Dana yang saya lakukan di PT Raiz Invest Indonesia.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">4.</td>
				<td style=" font-size:12px;">
					Saya menyatakan telah memahami bahwa investasi melalui Reksa Dana mengandung risiko dan kinerja masa lalu tidak mencerminkan kinerja masa datang, sehingga PT Raiz Invest Indonesia tidak bertanggung jawab atas segala kerugian akibat pilihan investasi Saya.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">5.</td>
				<td style=" font-size:12px;">
					Saya menyatakan telah memahami bahwa PT Raiz Invest Indonesia tidak bertanggung jawab atas segala kerugian, kehilangan, tuntutan, maupun gugatan yang Saya alami yang mungkin timbul sebagai akibat penyampaian informasi yang tidak lengkap atau akibat tidak dilaksanakannya instruksi Saya, antara lain pemabatalan, perubahan instruksi (untuk instruksi yang belum dijalankan) yang disampaikan kepada PT Raiz Invest Indonesia kecuali jika kerugian tersebut terbukti diakibatkan oleh kesalahan dan/atau kelalaian PT Raiz Invest Indoensia.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">6.</td>
				<td style=" font-size:12px;">
					Saya menyatakan bahwa telah secara sadar bersedia bertanggung jawab secara penuh atas segala tindakan yang saya lakukan, dan membebaskan PT Raiz Invest Indonesia, termasuk anak perusahaan, afiliasi, pemegang saham, direksi, pejabat, karyawan sepenuhnya dari segala tuntutan, kerugian, pembayaran maupun ongkos apapun yang timbul yang diderita oleh PT Raiz Invest Indonesia baik secara langsung maupun tidak langsung, termasuk antara lain biaya penasehat hukum dan biaya perkara, yang berkaitan dengan dilaksanakannya atau tidak dilaksanakannya instruksi Saya oleh Manajer Investasi.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">7.</td>
				<td style=" font-size:12px;">
					Saya menyatakan bahwa telah membaca dan menyetujui seluruh isi Prospektus dan Keterangan Ringkas produk Reksa Dana yang saya pilih serta memahami risiko atas keputusan investasi yang saya buat.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">8.</td>
				<td style=" font-size:12px;">
					Saya menyatakan bahwa telah membaca dan menelaah, dan menyetujui seluruh syarat dan ketentuan penggunaan situs dan aplikasi PT Raiz Invest Indonesia dan seluruh pernyataan maupun peraturan sebagaimana terlampir bersama formulir ini.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">9.</td>
				<td style=" font-size:12px;">
					Saya menyatakan bahwa semua informasi yang telah diberikan dalam formulir ini adalah benar dan sah.
				</td>
			</tr>
			<tr>
				<td valign="top" style=" text-align:center; width:5%; font-size:12px;">10.</td>
				<td style=" font-size:12px;">
					Saya menyatakan bahwa telah memahami bahwa pembelian dianggap sah apabila seluruh dokumen diterima PT Raiz Invest Indonesia dengan lengkap dan dana sudah efektif di rekening Reksa Dana sesuai dengan aturan yang berlaku.
				</td>
			</tr>
		</table>
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
