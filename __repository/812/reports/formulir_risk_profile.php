<?php 
namespace formulir_risk_profile;

defined('BASEPATH') OR exit('No direct script access allowed');
class F_Report 
{
	/**
	 * Default config, for header & footer. Also for outputting result to file.
	 *
	 * @var array
	 */
	public $config = [
		'name' 		=> 'formulir_risk_profile',
		'title' 	=> 'Formulir Profile Risiko',
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
		"full_name" => "",
		'questions' => [
			['QuestionNo' => 1, 'QuestionText' => 'Jika Anda menjawab "Ya" pada pertanyaan no. 3 di atas, beri tanda (&#10003;) di samping jenis Reksa Dana yang pernah Anda miliki:'],
			['QuestionNo' => 2, 'QuestionText' => 'Bagaimana pemahaman anda mengenai perkembangan investasi anda di reksa dana yang anda miliki dan industri reksa dana secara umum?'],
			['QuestionNo' => 3, 'QuestionText' => 'Seberapa besar resiko yang anda dapat terima dalam investasi reksa dana?'],
			['QuestionNo' => 4, 'QuestionText' => 'Bagaimana anda mengindikasikan diri anda dalam hal berinvestasi?'],
			['QuestionNo' => 5, 'QuestionText' => 'Berapa banyak uang yang mampu anda tabung/sisihkan setiap bulannya dari pendapatan bulanan?'],
			['QuestionNo' => 6, 'QuestionText' => 'Berapa persen dana yang anda investasikan pada jenis profuk reksa dana dari total aset yang anda miliki?'],
			['QuestionNo' => 7, 'QuestionText' => 'Dimana anda menempatkan sebagian besar uang anda?'],
		],
		'questions_opt' => [
			['QuestionNo' => 1, 'OptionNo' => 1, 'OptionText' => 'Reksa dana pasar uang', 'OptionValue' => '10'],
			['QuestionNo' => 1, 'OptionNo' => 2, 'OptionText' => 'Reksa dana Pendapatan Tetap', 'OptionValue' => '20'],
			['QuestionNo' => 1, 'OptionNo' => 3, 'OptionText' => 'Reksa dana campuran', 'OptionValue' => '30'],
			['QuestionNo' => 1, 'OptionNo' => 4, 'OptionText' => 'Reksa dana saham', 'OptionValue' => '40'],
			['QuestionNo' => 2, 'OptionNo' => 1, 'OptionText' => 'Tidak tahu apa apa', 'OptionValue' => '10'],
			['QuestionNo' => 2, 'OptionNo' => 2, 'OptionText' => 'Tahu sedikit', 'OptionValue' => '20'],
			['QuestionNo' => 2, 'OptionNo' => 3, 'OptionText' => 'Memonitor dari waktu ke waktu', 'OptionValue' => '30'],
			['QuestionNo' => 2, 'OptionNo' => 4, 'OptionText' => 'Memonitor dengan cermat', 'OptionValue' => '40'],
			['QuestionNo' => 3, 'OptionNo' => 1, 'OptionText' => 'Keuntungan hingga 10%, kerugian hingga 5%', 'OptionValue' => '10'],
			['QuestionNo' => 3, 'OptionNo' => 2, 'OptionText' => 'Keuntungan hingga 20%, kerugian hingga 10%', 'OptionValue' => '20'],
			['QuestionNo' => 3, 'OptionNo' => 3, 'OptionText' => 'Keuntungan hingga 30%, kerugian hingga 20%', 'OptionValue' => '30'],
			['QuestionNo' => 3, 'OptionNo' => 4, 'OptionText' => 'Kerugian hingga > 30%, kerugian hingga > 20%', 'OptionValue' => '40'],
			['QuestionNo' => 4, 'OptionNo' => 1, 'OptionText' => 'Mengharapkan tidak ada risiko', 'OptionValue' => '10'],
			['QuestionNo' => 4, 'OptionNo' => 2, 'OptionText' => 'Mengutamakan risiko yang kecil', 'OptionValue' => '20'],
			['QuestionNo' => 4, 'OptionNo' => 3, 'OptionText' => 'Mencari cukup keuntungan dengan risiko kerugian moderat sesuai keuntungan', 'OptionValue' => '30'],
			['QuestionNo' => 4, 'OptionNo' => 4, 'OptionText' => 'Mencari hasil yang besar dengan berani mengambil risiko kerugian yang besar', 'OptionValue' => '40'],
			['QuestionNo' => 5, 'OptionNo' => 1, 'OptionText' => 'Hingga 10%', 'OptionValue' => '10'],
			['QuestionNo' => 5, 'OptionNo' => 2, 'OptionText' => 'Antara 10% s.d 25%', 'OptionValue' => '20'],
			['QuestionNo' => 5, 'OptionNo' => 3, 'OptionText' => 'Antara 25% s.d 50%', 'OptionValue' => '30'],
			['QuestionNo' => 5, 'OptionNo' => 4, 'OptionText' => 'Lebih dari 50%', 'OptionValue' => '40'],
			['QuestionNo' => 6, 'OptionNo' => 1, 'OptionText' => '< 10% dari total aset', 'OptionValue' => '10'],
			['QuestionNo' => 6, 'OptionNo' => 2, 'OptionText' => '10% s.d 25% dari total aset', 'OptionValue' => '20'],
			['QuestionNo' => 6, 'OptionNo' => 3, 'OptionText' => '25% s.d 50% dari total aset', 'OptionValue' => '30'],
			['QuestionNo' => 6, 'OptionNo' => 4, 'OptionText' => '> 50% dari total aset', 'OptionValue' => '40'],
			['QuestionNo' => 7, 'OptionNo' => 1, 'OptionText' => 'Tabungan & deposito', 'OptionValue' => '10'],
			['QuestionNo' => 7, 'OptionNo' => 2, 'OptionText' => 'Mata uang asing', 'OptionValue' => '20'],
			['QuestionNo' => 7, 'OptionNo' => 3, 'OptionText' => 'Reksa dana', 'OptionValue' => '30'],
			['QuestionNo' => 7, 'OptionNo' => 4, 'OptionText' => 'Saham', 'OptionValue' => '40'],
		],
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
		isset($var->full_name) OR $var->full_name = $this->var['full_name'];
		isset($var->questions) OR $var->questions = $this->var['questions'];
		isset($var->questions_opt) OR $var->questions_opt = $this->var['questions_opt'];
		$config = empty($config) ? (object)$this->config : (object)$config;
		isset($config->title) OR $config->title = $this->config['title'];
		isset($config->author) OR $config->author = $this->config['author'];
		isset($config->name) OR $config->name = $this->config['name'];
		$support = (object)[
			'img_01' 		=> __dir__.'/../images/reksadana - 219x88.png',
			'img_02' 		=> __dir__.'/../images/logo-raiz - 257x78.png',
			'na'				=> 'N/A',
			'abjad' 		=> ['0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'],
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
				<td style="text-align:center; font-size:20px; font-family: helvetica;">
					<strong>Formulir Pembukaan Rekening Efek Reksa Dana</strong>
				</td>
			</tr>
			<tr>
				<td style="border-bottom:1pt solid black; text-align:center; font-size:20px; font-family: helvetica;">
					<strong>Profil Pemodal Reksa Dana</strong>
				</td>
			</tr>
		</table>
		<table border="0" style="width:100%;">
			<tr>
				<td style="text-align:justify; height:auto; font-size:11px; letter-spacing:-0.5;">
					Sesuai Peraturan Bapepam Nomor IV.D2, setiap Manajer Investasi atau Agen Penjual Efek Reksa Dana wajib mensyaratkan invester Reksa Dana untuk mengisi formulir Profil Pemodal Reksa Dana ini.
				</td>
			</tr>
		</table>
		'.$support->breakline1.'
		<table border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td valign="top" style="text-align:center; width:5%; height:20px; font-size:11px;">1.</td>
				<td valign="top" style="height:20px; font-size:11px;">
					Jenis portofolio/nama Reksa Dana yang Anda minati:
				</td>
			</tr>
		</table>
		<table border="1" style="border-style:dotted; border-collapse:collapse; width:70%;">
			<tr>
				<td valign="top" style="border-left:0; border-right:0; border-bottom:0; border-top:0;  text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td valign="top" style="border:1; border-style:dotted; width:10%; height:20px; font-size:11px;"></td>
				<td valign="top" style="border-style:dotted; width:150px; border:1; height:20px; font-size:11px;"><strong>&ensp;Tipe portfolio</strong></td>
				<td valign="top" style="border:1; border-style:dotted; height:20px; font-size:11px;"><strong>&ensp;Jenis Reksa Dana</strong></td>
			</tr>
			<tr>
				<td valign="top" style="border-left:0; border-right:0; border-bottom:0; border-top:0;  text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:10%; border:1; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Konservatif</td>
				<td valign="top" style=" border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Reksa Dana Pasar Uang</td>
			</tr>
			<tr>
				<td valign="top" style="border-left:0; border-right:0; border-bottom:0; border-top:0;  text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:10%; border:1; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Moderat</td>
				<td valign="top" style=" border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Reksa Dana Pendapatan Tetap</td>
			</tr>
			<tr>
				<td valign="top" style="border-left:0; border-right:0; border-bottom:0; border-top:0;  text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:10%; border:1; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Agresif</td>
				<td valign="top" style=" border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Reksa Dana Indeks Saham</td>
			</tr>
		</table>
		'.$support->breakline1.'
		<table border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td valign="top" style="text-align:center; width:5%; height:20px; font-size:11px;">2.</td>
				<td valign="top" style="height:20px; font-size:11px;">
					Berapa lama Anda akan melakukan investasi di portofolio/Reksa Dana tersebut?
				</td>
			</tr>
		</table>
		<table border="0" style="border-collapse:collapse; width:80%; margin-left:36px;">
			<tr>
				<td valign="top" style="border:1; border-right:0; border-style:dotted; width:130px; height:auto; font-size:11px;">
					&nbsp;
					<input type="checkbox">
					<label class="container">&nbsp; < 1 Tahun
						<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style="width:130px; border:1; border-right:0; border-style:dotted; height:auto; font-size:11px;">
					&nbsp;
					<input type="checkbox">
					<label class="container">&nbsp; 1 - 3 Tahun
						<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style="width:130px; border:1; border-right:0; border-style:dotted; height:auto; font-size:11px;">
					&nbsp;	
					<input type="checkbox">
					<label class="container">&nbsp; 3 - 5 Tahun
						<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style=" border:1; border-style:dotted; height:auto; font-size:11px;">
					&nbsp;
					<input type="checkbox">
					<label class="container">&nbsp; > 5 Tahun
						<span class="checkmark"></span>
					</label>
				</td>
			</tr>
		</table>
		'.$support->breakline1.'
		<table border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td valign="top" style="text-align:center; width:5%; height:20px; font-size:11px;">3.</td>
				<td valign="top" style="height:20px; font-size:11px;">
					Apakah sebelumnya Anda pernah berinvestasi di Reksa Dana?
				</td>
			</tr>
		</table>
		<table border="0" style="border-collapse:collapse; width:40%; margin-left:36px;">
			<tr>
				<td valign="top" style="border:1; border-right:0; border-style:dotted; width:130px; height:auto; font-size:11px;">
					&nbsp;
					<input type="checkbox">
					<label class="container">&nbsp;Ya, pernah
						<span class="checkmark"></span>
					</label>
				</td>
				<td valign="top" style="width:130px; border:1; border-style:dotted; height:auto; font-size:11px;">
					&nbsp;
					<input type="checkbox">
					<label class="container">&nbsp;Belum pernah
						<span class="checkmark"></span>
					</label>
				</td>
			</tr>
		</table>
		'.$support->breakline1.'
		<table border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td valign="top" style="text-align:center; width:5%; height:20px; font-size:11px;">4.</td>
				<td valign="top" style="height:20px; font-size:11px;">
					Tujuan investasi:
				</td>
			</tr>
		</table>
		<table border="0" style="border-collapse:collapse; width:70%;">
			<tr>
				<td valign="top" style="text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:5%; border:1; border-right:0; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Keamanan dana investasi</td>
			</tr>
			<tr>
				<td valign="top" style="text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:5%; border:1; border-right:0; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Pendapatan dan keamanan dana investasi</td>
			</tr>
			<tr>
				<td valign="top" style="text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:5%; border:1; border-right:0; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Pendapatan dan pertumbuhan dalam jangka panjang</td>
			</tr>
			<tr>
				<td valign="top" style="text-align:center; width:7.4%; height:20px; font-size:11px;"><strong></strong></td>
				<td style="width:5%; border:1; border-right:0; border-style:dotted; height:20px; font-size:11px; text-align:center;">
					<input type="checkbox">
					<label class="container">
					<span class="checkmark"></span>
					</label>
				</td>
				<td style="width:150px; border:1; border-style:dotted; height:20px; font-size:11px;">&ensp;Pertumbuhan</td>
			</tr>
		</table>
		'.$support->breakline1.'
		<table>
			<tr>
				<td valign="top" style="text-align:center; width:5%; height:20px; font-size:11px;">5.</td>
				<td valign="top" style="height:20px; font-size:11px;">
					Mohon beri tanda {&#10003;} untuk jawaban yang paling mendekati profil Anda sebagai investor Reksa Dana:
				</td>
			</tr>
		</table>
		<table border="1" style="border-collapse:collapse; width:100%;">
			<tr style="background-color: #221b34;">
				<td colspan="2" style="color:white; text-align:center; width:85%; height:20px; font-size:10px;"><strong> Pertanyaan Detail/<em>Detail Questions</em> </strong></td>
				<td style="color:white; border-left:0; text-align:center; height:20px; font-size:10px;"><strong> Nilai/<em>Score</em></strong></td>
			</tr>
		';
$mpdf->WriteHTML($htmlSatu);

foreach($var->questions as $q) {
	if ($q['QuestionNo'] < 4) {
		$htmlSatu = '
		//valign="bottom" 
		<tr>
			<td valign="top" style="border-top:0; border-right:0; border-bottom:0; width:5%; height:auto; font-size:11px; letter-spacing:-0.5px; text-align:center;">
				&ensp;'.$support->abjad[$q['QuestionNo']].'.
			</td>
			<td valign="top" style="border-top:0; border-left:0; border-right:0; border-bottom:0; width:82.5%; height:auto; font-size:11px; letter-spacing:-0.5px;">
				'.$q['QuestionText'].'
			</td>
			<td valign="top" style="border-top:0; border-bottom:0; height:auto; font-size:11px; letter-spacing:-0.5px; text-align:center;">
				
			</td>
		</tr>
		';
		$mpdf->WriteHTML($htmlSatu);
		// $mpdf->WriteHTML($questions_opt['QuestionNo'] = $q['QuestionNo']);
		// $opt = $questions_opt['QuestionNo']$q['QuestionNo'];
		foreach($var->questions_opt as $qopt) {
			if ($qopt['QuestionNo'] == $q['QuestionNo']) {
				$htmlSatu = '
				<tr>
					<td valign="top" style="border-right:0; border-top:0; border-right:0; border-bottom:0; width:5%; height:auto; font-size:11px; letter-spacing:-0.5px; text-align:center;">

					</td>
					<td valign="top" style="border-left:0; border-top:0; border-right:0; border-bottom:0; height:auto; font-size:11px; letter-spacing:-0.5px;">
						<p style="font-size:6px;">&ensp;</p>
						'.$support->spasi4.'
						<input type="checkbox">
						<label class="container">&emsp;'.$qopt['OptionText'].'
							<span class="checkmark"></span>
						</label>
					</td>
					<td style="border-top:0; border-bottom:0; text-align:center; font-size:11px; height:auto;"> '.$qopt['OptionValue'].'</td>
				</tr>';
				$mpdf->WriteHTML($htmlSatu);
			}
		}
		//tricky row
		$htmlSatu = '
		<tr>
			<td valign="top" style="border-right:0; border-left:0; border-bottom:0; width:5%; height:auto; font-size:1px; letter-spacing:-0.5px; text-align:center;">
				
			</td>
			<td valign="top" style="border-right:0; border-left:0; border-bottom:0; height:auto; font-size:1px; letter-spacing:-0.5px;">
				
			</td>
			<td style="border-right:0; border-left:0; border-bottom:0; text-align:center; font-size:1px; height:auto;"> 
				
			</td>
		</tr>
		';
		$mpdf->WriteHTML($htmlSatu);		
	}
}

//another tricky row
$htmlSatu =	'
</table><pagebreak>';
$mpdf->WriteHTML($htmlSatu);


$htmlSatu ='

<table border="1" style="border-collapse:collapse; width:100%;">
<tr style="background-color: #221b34;">
	<td colspan="2" style="color:white; text-align:center; width:85%; height:20px; font-size:10px;"><strong> Pertanyaan Detail/<em>Detail Questions</em> </strong></td>
	<td style="color:white; border-left:0; text-align:center; height:20px; font-size:10px;"><strong> Nilai/<em>Score</em></strong></td>
</tr>
';
$mpdf->WriteHTML($htmlSatu);

foreach($var->questions as $q) {
	if ($q['QuestionNo'] > 3) {
		$htmlSatu = '
		//valign="bottom" 
		<tr>
			<td valign="top" style="border-top:1; border-right:0; border-bottom:0; width:5%; height:auto; font-size:11px; letter-spacing:-0.5px; text-align:center;">
				&ensp;'.$support->abjad[$q['QuestionNo']].'.
			</td>
			<td valign="top" style="border-top:1; border-left:0; border-right:0; border-bottom:0; width:82.5%; height:auto; font-size:11px; letter-spacing:-0.5px;">
				'.$q['QuestionText'].'
			</td>
			<td valign="top" style="border-top:1; border-bottom:0; height:auto; font-size:11px; letter-spacing:-0.5px; text-align:center;">
				
			</td>
		</tr>
		';
		$mpdf->WriteHTML($htmlSatu);
		// $mpdf->WriteHTML($questions_opt['QuestionNo'] = $q['QuestionNo']);
		// $opt = $questions_opt['QuestionNo']$q['QuestionNo'];
		foreach($var->questions_opt as $qopt) {
			if ($qopt['QuestionNo'] == $q['QuestionNo']) {
				$htmlSatu = '
				<tr>
					<td valign="top" style="border-right:0; border-top:0; border-right:0; border-bottom:0; width:5%; height:auto; font-size:11px; letter-spacing:-0.5px; text-align:center;">
						
					</td>
					<td valign="top" style="border-left:0; border-top:0; border-right:0; border-bottom:0; height:auto; font-size:11px; letter-spacing:-0.5px;">
						<p style="font-size:6px;">&ensp;</p>
						'.$support->spasi4.'
						<input type="checkbox">
						<label class="container">&emsp;'.$qopt['OptionText'].'
							<span class="checkmark"></span>
						</label>
					</td>
					<td style="border-top:0; border-bottom:0; text-align:center; font-size:11px; height:auto;"> '.$qopt['OptionValue'].'</td>
				</tr>';
				$mpdf->WriteHTML($htmlSatu);
			}
		}
		//tricky row
		$htmlSatu = '
		<tr>
			<td valign="top" style="border-right:0; border-left:0; border-bottom:0; width:5%; height:auto; font-size:1px; letter-spacing:-0.5px; text-align:center;">
				
			</td>
			<td valign="top" style="border-right:0; border-left:0; border-bottom:0; height:auto; font-size:1px; letter-spacing:-0.5px;">
				
			</td>
			<td style="border-right:0; border-left:0; border-bottom:0; text-align:center; font-size:1px; height:auto;"> 
				
			</td>
		</tr>
		';
		$mpdf->WriteHTML($htmlSatu);		
	}
}

//another tricky row
$htmlSatu =	'
<tr>
<td valign="top" style="border:1; border-top:0; border-right:0; width:5%; height:30px; font-size:11px; letter-spacing:-0.5px; text-align:center;">
	
</td>
<td valign="top" style="text-align:right; border:1; border-top:0; border-left:0; border-right:0; height:30px; font-size:11px; letter-spacing:-0.5px;">
	Total Nilai&ensp;
</td>
<td style="border-top:0; text-align:center; font-size:11px; height:30px;"> 
	
</td>
</tr>
</table>';
$mpdf->WriteHTML($htmlSatu);

$htmlSatu =	'
		<table border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td style="font-size:4px;">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td style="font-size:12px;">
					Total nilai yang Anda peroleh menunjukan profil risiko berinvestasi anda
				</td>
			</tr>
		</table>
		<table border="0" style="border-collapse:collapse; width:25%; margin-left:10px;">
			<tr>
				<td style="text-align:left; border:1; border-style:dotted; border-right:0; width:40%; height:20px; font-size:11px; letter-spacing:-0.5px;">
					&ensp;10 - 100
				</td>
				<td style="text-align:left; border:1; border-style:dotted; height:20px; font-size:11px; letter-spacing:-0.5px;">
					&ensp;Konservatif
				</td>
			</tr>
			<tr>
				<td style="text-align:left; border:1; border-style:dotted; border-right:0; width:40%; height:20px; font-size:11px; letter-spacing:-0.5px;">
					&ensp;110 - 200
				</td>
				<td style="text-align:left; border:1; border-style:dotted; height:20px; font-size:11px; letter-spacing:-0.5px;">
					&ensp;Moderat
				</td>
			</tr>
			<tr>
				<td style="text-align:left; border:1; border-style:dotted; border-right:0; width:40%; height:20px; font-size:11px; letter-spacing:-0.5px;">
					&ensp;200 - 180
				</td>
				<td style="text-align:left; border:1; border-style:dotted; height:20px; font-size:11px; letter-spacing:-0.5px;">
					&ensp;Agresif
				</td>
			</tr>
		</table>
		'.$support->breakline1.'
		<table border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td style="font-size:12px;">
					<strong>Pernyataan Investor Reksa Dana</strong>
				</td>
			</tr>
			<tr>
				<td style="font-size:4px;">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td style="font-size:11px; text-align:justify;">
					Investor menyadari dan mengerti bahwa jawaban yng diberikan dalam Profil Pemodal Reksa Dana ini digunakan untuk analisa Agen Penjual Efek Reksa Dana dalam rangka membantu investor mengetahui tingkat risiko investasi yang dapat diterimanya.
				</td>
			</tr>
			<tr>
				<td style="font-size:4px;">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td style="font-size:11px; text-align:justify;">
					Apabila investor telah melengkapi dengan benar Profil Reksa Dana ini, akan tetapi memilih portofolio/Reksa Dana dengan karakter yang tidak sesuai dengan Profil Pemodal Reksa Dana yang telah diisinya di atas, maka pilihan portofolio/Reksa Dana tersebut adalah semata-mata berdasarkan keinginan investor sendiri. Sehubungan dengan hal tersebut, investor mengerti dan bersedia menerima risiko atas investasi yang dilakukannya tersebut
				</td>
			</tr>
			<tr>
				<td style="font-size:8px;">
					&nbsp;
				</td>
			</tr>
		</table>
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
				<td valign="middle" style="width:40%; text-align:center; border-left:0; border-right:0; border-bottom:0; border-top:0; font-size:11px;">
					'.$var->full_name.'
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
		'.$support->breakline1.'
		<table bgcolor="#221b34" border="0" style="border-collapse:collapse; width:100%;">
			<tr>
				<td style="color:white; text-align:justify; font-size:11px; padding: 5px;">
					<strong> Pernyataan Peringatan (Disclaimer)</strong><br></br>
					Pertanyaan-pertanyaan yang tercantum dalam Profil Pemodal reksa Dana ini disediakan oleh PT Raiz Invest Indonesia semata-mata untuk membantu investor menemukan profil risiko berinvestasinya dan bukan merupakan nasihat, saran, undangan, atau bujukan untuk membeli unit penyertaan Reksa Dana mengandung risiko. Nasabah selayaknya mengerti bahwa Reksa Dana memiliki risiko.
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
