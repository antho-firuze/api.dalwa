<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	LIBRARY CIPTAAN JINGGA LINTAS IMAJI
	KONTEN LIBRARY :
	- Upload File
	- Upload File Multiple
	- RandomString
	- CutString
	- Kirim Email
	- Konversi Bulan
	- Fillcombo
	- Json Datagrid
	
*/
class Lib {
	public function __construct(){
		
	}
	
	// Class Cek Keylog
	function cekkeylog($key){
		$ci =& get_instance();
		$ci->load->database();
		$data = [];
		
		$cekdb = $ci->db->get_where("tbl_client", array("KeyLog"=>$key) )->row_array();
		if($cekdb){
			return true;
		}else{
			$data["pesan"] = "Invalid Key Param";
			$respon = 2;
			$result = array('data'=>$data, 'respon'=>$respon);
			echo json_encode($result);
			exit;
		}
	}
	// Class Cek Keylog
	
	//class Upload File Version 1.0 - Beta
	function uploadnong($upload_path="", $object="", $file=""){
		//$upload_path = "./__repository/".$folder."/";
		
		$ext = explode('.',$_FILES[$object]['name']);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		$filename =  $file.'.'.$extension;
		
		$files = $_FILES[$object]['name'];
		$tmp  = $_FILES[$object]['tmp_name'];
		if(file_exists($upload_path.$filename)){
			unlink($upload_path.$filename);
			$uploadfile = $upload_path.$filename;
		}else{
			$uploadfile = $upload_path.$filename;
		} 
		
		move_uploaded_file($tmp, $uploadfile);
		if (!chmod($uploadfile, 0775)) {
			echo "Gagal mengupload file";
			exit;
		}
		
		return $filename;
	}
	// end class Upload File
	
	//class Upload File Multiple Version 1.0 - Beta
	function uploadmultiplenong($upload_path="", $object="", $file="", $idx=""){
		$ext = explode('.',$_FILES[$object]['name'][$idx]);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		$filename =  $file.'.'.$extension;
		
		$files = $_FILES[$object]['name'][$idx];
		$tmp  = $_FILES[$object]['tmp_name'][$idx];
		if(file_exists($upload_path.$filename)){
			unlink($upload_path.$filename);
			$uploadfile = $upload_path.$filename;
		}else{
			$uploadfile = $upload_path.$filename;
		} 
		
		move_uploaded_file($tmp, $uploadfile);
		if (!chmod($uploadfile, 0775)) {
			echo "Gagal mengupload file";
			exit;
		}
		
		return $filename;
	}
	//end Class Upload File
	
	//class Random String Version 1.0
	function randomString($length,$parameter="") {
        $str = "";
		$rangehuruf = range('A','Z');
		$rangeangka = range('0','9');
		if($parameter == 'angka'){
			$characters = array_merge($rangeangka);
		}elseif($parameter == 'huruf'){
			$characters = array_merge($rangehuruf);
		}else{
			$characters = array_merge($rangehuruf, $rangeangka);
		}
         $max = count($characters) - 1;
         for ($i = 0; $i < $length; $i++) {
              $rand = mt_rand(0, $max);
              $str .= $characters[$rand];
         }
         return $str;
    }
	//end Class Random String
	
	//Class CutString
	function cutstring($text, $length) {
		//$isi_teks = htmlentities(strip_tags($text));
		$isi = substr($text, 0,$length);
		//$isi = substr($isi_teks, 0,strrpos($isi," "));
		$isi = $isi.' ...';
		return $isi;
	}
	//end Class CutString
	
	//Class Kirim Email
	function kirimemail($type="", $email="", $p1="", $p2="", $p3=""){
		$ci =& get_instance();
		
		$ci->load->library('email');
		$html = "";
		$subject = "";
		switch($type){
			case "email_redeem":
				$html = $ci->nsmarty->fetch('frontend/email_redeem.html');
				$subject = "EMAIL TRANSAKSI REDEEM SIMPIPRO ";
			break;
			case "email_forgot_password":
				$ci->nsmarty->assign('email', $p1);
				$ci->nsmarty->assign('password', $p2);
				$html = $ci->nsmarty->fetch('frontend/email_forgot_password.html');
				$subject = "EMAIL LUPA PASSWORD SIMPIPRO ";
			break;
			case "email_registrasi":
				$ci->nsmarty->assign('email', $p1);
				$ci->nsmarty->assign('password', $p2);
				$html = $ci->nsmarty->fetch('frontend/email_registrasi.html');
				$subject = "EMAIL REGISTRASI SIMPIPRO ";
			break;	
		}
				
		$config = array(
			"protocol"	=>"smtp"
			,"mailtype" => "html"
			,"smtp_host" => "ssl://smtp.gmail.com"
			,"smtp_user" => "triwahyunugros@gmail.com"
			,"smtp_pass" => "bnw3631suq"
			,"smtp_port" => "465",
			'charset' => 'utf-8',
            'wordwrap' => TRUE,
		);
		
		
		$ci->email->initialize($config);
		$ci->email->from("notifikasi@simpipro.com", "Simpi Pro Notifikasi");
		$ci->email->to($email);
		$ci->email->subject($subject);
		$ci->email->message($html);
		$ci->email->set_newline("\r\n");
		
		
		if($type == "email_redeem"){
			$filename = "file-".$p1."-transaksiredeem-".$p2.".pdf";
			$ci->email->attach( $_SERVER["DOCUMENT_ROOT"].'/__repository/redeem_pdf/'.$filename );
		}
		
		if($ci->email->send())
			//echo "<h3> SUKSES EMAIL ke $email </h3>";
			return 1;
		else
			//echo $this->email->print_debugger();
			return $ci->email->print_debugger();
	}	
	//End Class KirimEmail
	
	//Class Konversi Bulan
	function konversi_bulan($bln,$type=""){
		if($type == 'fullbulan'){
			switch($bln){
				case 1:$bulan='Januari';break;
				case 2:$bulan='Februari';break;
				case 3:$bulan='Maret';break;
				case 4:$bulan='April';break;
				case 5:$bulan='Mei';break;
				case 6:$bulan='Juni';break;
				case 7:$bulan='Juli';break;
				case 8:$bulan='Agustus';break;
				case 9:$bulan='September';break;
				case 10:$bulan='Oktober';break;
				case 11:$bulan='November';break;
				case 12:$bulan='Desember';break;
			}
		}else{
			switch($bln){
				case 1:$bulan='Jan';break;
				case 2:$bulan='Feb';break;
				case 3:$bulan='Mar';break;
				case 4:$bulan='Apr';break;
				case 5:$bulan='Mei';break;
				case 6:$bulan='Jun';break;
				case 7:$bulan='Jul';break;
				case 8:$bulan='Agst';break;
				case 9:$bulan='Sept';break;
				case 10:$bulan='Okt';break;
				case 11:$bulan='Nov';break;
				case 12:$bulan='Des';break;
			}
		}
		return $bulan;
	}
	//End Class Konversi Bulan
	
	//Class Konversi Tanggal
	function konversi_tgl($date){
		$ci =& get_instance();
		$ci->load->helper('terbilang');
		$data=array();
		$timestamp = strtotime($date);
		$day = date('D', $timestamp);
		$day_angka = (int)date('d', $timestamp);
		$month = date('m', $timestamp);
		$years = date('Y', $timestamp);
		switch($day){
			case "Mon":$data['hari']='Senin';break;
			case "Tue":$data['hari']='Selasa';break;
			case "Wed":$data['hari']='Rabu';break;
			case "Thu":$data['hari']='Kamis';break;
			case "Fri":$data['hari']='Jumat';break;
			case "Sat":$data['hari']='Sabtu';break;
			case "Sun":$data['hari']='Minggu';break;
		}
		switch($month){
			case "01":$data['bulan']='Januari';break;	
			case "02":$data['bulan']='Februari';break;	
			case "03":$data['bulan']='Maret';break;	
			case "04":$data['bulan']='April';break;	
			case "05":$data['bulan']='Mei';break;	
			case "06":$data['bulan']='Juni';break;	
			case "07":$data['bulan']='Juli';break;	
			case "08":$data['bulan']='Agustus';break;	
			case "09":$data['bulan']='September';break;	
			case "10":$data['bulan']='Oktober';break;	
			case "11":$data['bulan']='November';break;	
			case "12":$data['bulan']='Desember';break;	
		}
		$data['tahun']=ucwords(number_to_words($years));
		$data['tgl_text']=ucwords(number_to_words($day_angka));
		return $data;
	}
	//End Class Konversi Tanggal
	
	//Class Fillcombo
	function fillcombo($type="", $balikan="", $p1="", $p2="", $p3=""){
		$ci =& get_instance();
		$ci->load->model('mbackend');
		
		$v = $ci->input->post('v');
		if($v != ""){
			$selTxt = $v;
		}else{
			$selTxt = $p1;
		}
		
		$optTemp = '<option value=""> -- Pilih -- </option>';
		switch($type){
			case "cl_diameter_filter":
				$optTemp = '<option value=""> -- PILIH DIAMETER -- </option>';
				$data = $ci->mbackend->get_combo("cl_diameter", $p1, $p2);
			break;
			case "role_operator":
				$data = array(
					'0' => array('id'=>'L','txt'=>'Operator Load'),
					'1' => array('id'=>'R','txt'=>'Operator Service/Repair'),
					'2' => array('id'=>'C','txt'=>'Operator Calibrate'),
					'3' => array('id'=>'S','txt'=>'Operator In Stok'),
					'4' => array('id'=>'I','txt'=>'Operator Install'),
				);
			break;
			case "jenis_kelamin":
				$data = array(
					'0' => array('id'=>'L','txt'=>'Laki-Laki'),
					'1' => array('id'=>'P','txt'=>'Perempuan'),
				);
			break;
			case "tipe_status":
				$data = array(
					'0' => array('id'=>'1','txt'=>'Aktif'),
					'1' => array('id'=>'0','txt'=>'Tidak Aktif'),
				);
			break;
			case "bulan" :
				$data = $this->arraydate('bulan');
				$optTemp = '<option value=""> -- Month -- </option>';
			break;
			case "tahun" :
				$data = $this->arraydate('tahun');
				$optTemp = '<option value=""> -- Year -- </option>';
			break;			
			default:
				$data = $ci->mbackend->get_combo($type, $p1, $p2);
			break;
		}
		
		if($data){
			foreach($data as $k=>$v){
				if($selTxt == $v['id']){
					$optTemp .= '<option selected value="'.$v['id'].'">'.$v['txt'].'</option>';
				}else{ 
					$optTemp .= '<option value="'.$v['id'].'">'.$v['txt'].'</option>';	
				}
			}
		}
		
		if($balikan == 'return'){
			return $optTemp;
		}elseif($balikan == 'echo'){
			echo $optTemp;
		}
		
	}
	//End Class Fillcombo
	
	//Function Json Grid
	function json_grid($sql,$type="",$table="",$koding=""){
		$ci =& get_instance();
		$ci->load->database();
		$ci->load->model((array('mbackend')));
		
		$page = (integer) (($ci->input->post('page')) ? $ci->input->post('page') : "1");
		$limit = (integer) (($ci->input->post('rows')) ? $ci->input->post('rows') : "10");
		$count = $ci->db->query($sql)->num_rows();
		
		if( $count >0 ) { $total_pages = ceil($count/$limit); } else { $total_pages = 0; } 
		if ($page > $total_pages) $page=$total_pages; 
				
		$dbdriver = $ci->db->dbdriver;
		
		if($dbdriver == "mysql" || $dbdriver == "mysqli"){
			//MYSQL
			$start = $limit*$page - $limit; // do not put $limit*($page - 1)
			if($start<0) $start=0;
			$sql = $sql . " LIMIT $start,$limit";			
			$data = $ci->db->query($sql)->result_array();  
		}
		
		if($dbdriver == "postgre"){
			//POSTGRESSS
			$end = $page * $limit; 
			$start = $end - $limit + 1;
			if($start < 0) $start = 0;
			$sql = "
				SELECT * FROM (
					".$sql."
				) AS X WHERE X.rowID BETWEEN $start AND $end
			";	
		}
		
		$data = $ci->db->query($sql)->result_array();  
		
		if($type == "tbl_metering"){
			foreach($data as $k => $v){
				$data[$k]["id_encrypt"] = $this->base64url_encode($v["id"]);
			}
		}
		
		if($data){
		   $responce = new stdClass();
		   $responce->rows= $data;
		   $responce->total =$count;
		   return json_encode($responce);
		}else{ 
		   $responce = new stdClass();
		   $responce->rows = 0;
		   $responce->total = 0;
		   return json_encode($responce);
		} 
	}
	//end Json Grid
	
	//Generate Form Via Field Table
	function generateform($table){
		$ci =& get_instance();
		$ci->load->database();
		
		$field = $ci->db->list_fields($table);
		$arrayform = array();
		$i = 0;
		foreach($field as $k => $v){							
			if($v == 'create_date' || $v == 'create_by'){
				continue;
			}
			
			$label = str_replace('_', ' ', $v);
			$label = strtoupper($label);
			
			if($v == 'id'){
				$arrayform[$k]['tipe'] = "hidden";
			}else{	
				if(strpos($v, 'cl_') !== false){
					$label = str_replace("CL ", "", $label);
					$label = str_replace(" ID", "", $label);
					
					$arrayform[$k]['tipe'] = "combo";
					$arrayform[$k]['ukuran_class'] = "span4";
					$arrayform[$k]['isi_combo'] =  $ci->lib->fillcombo($v, 'return', ($sts_crud == 'edit' ? $data[$y] : "") );
				}elseif(strpos($v, 'tipe_') !== false){
					$arrayform[$k]['tipe'] = "combo";
					$arrayform[$k]['ukuran_class'] = "span4";
					$arrayform[$k]['isi_combo'] =  $ci->lib->fillcombo($v, 'return', ($sts_crud == 'edit' ? $data[$y] : "") );
				}elseif(strpos($v, 'tgl_') !== false){
					$label = str_replace("TGL", "TANGGAL", $label);
					
					$arrayform[$k]['tipe'] = "text";
					$arrayform[$k]['ukuran_class'] = "span2";
				}elseif(strpos($v, 'isi_') !== false){
					$arrayform[$k]['tipe'] = "textarea";
					$arrayform[$k]['ukuran_class'] = "span8";
				}elseif(strpos($v, 'gambar_') !== false){
					$arrayform[$k]['tipe'] = "file";
					$arrayform[$k]['ukuran_class'] = "span8";	
				}else{
					$arrayform[$k]['tipe'] = "text";
					$arrayform[$k]['ukuran_class'] = "span8";
				}
			}
										
			$arrayform[$k]['name'] = $v;
			$arrayform[$k]['label'] = $label;
			$i++;
		}
		
		return $arrayform;
	}
	//End Generate Form Via Field Table
	function uniq_id(){
		$s = strtoupper(md5(uniqid(rand(),true))); 
		//echo $s;
		$guidText = substr($s,0,6);
		return $guidText;
	}
	
	//Class String Sanitizer
	function clean($string) {
		$string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}	
	
	//Class ToAscii
	function toAscii($str) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
	
		return $clean;
	}
	
	function get_ldap_user($mod="",$user="",$pwd="",$group=""){
		$ci =& get_instance();
		//echo $user.$pwd;
		$res=array();
		$res["msg"]=1;
		$ldapconn = ldap_connect($ci->config->item("ldap_host"),$ci->config->item("ldap_port"));
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		if($ldapconn) {
			if($mod=='data_ldap'){
				$ldapbind = @ldap_bind($ldapconn, $ci->config->item("ldap_user"), $ci->config->item("ldap_pwd"));
			}else{
				$ldapbind = ldap_bind($ldapconn, "uid=".$user.",".$ci->config->item("ldap_tree"), $pwd);
			}
			if ($ldapbind) {
				
				$ldap_fields=array("uid","samaccountname","name","primarygroupid","displayname","distinguishedname","cn","description","memberof","userprincipalname");           
				if($mod=='data_ldap'){					
					$result=@ldap_search($ldapconn,'ou=People,dc=pgn-solution,dc=co,dc=id','(uid='.$user.')',$ldap_fields); 
				}else if($mod=='login'){
                    $result=ldap_search($ldapconn,"uid=".$user.",".$ci->config->item("ldap_tree"),"(&(objectCategory=person)(samaccountname=$user))");
				}
				$data=$this->konvert_array($ldapconn,$result);
				$res["data"]=$data; //GAGAL KONEK
			} else {
				//echo "LDAP bind failed...";
				$res["msg"]=3; //GAGAL BIND
			}
		}else{
			$res["msg"]=2; //GAGAL KONEK
		}
		ldap_close($ldapconn);
		return $res;
	}
	function konvert_array($conn,$result){
		$connection = $conn;
		$resultArray = array();
		if ($result){
			$entry = ldap_first_entry($connection, $result);
			while ($entry){
				$row = array();
				$attr = ldap_first_attribute($connection, $entry);
				while ($attr){
					$val = ldap_get_values_len($connection, $entry, $attr);
					if (array_key_exists('count', $val) AND $val['count'] == 1){
						$row[strtolower($attr)] = $val[0];
					}
					else{
						$row[strtolower($attr)] = $val;
					}
					$attr = ldap_next_attribute($connection, $entry);
				}
				$resultArray[] = $row;
				$entry = ldap_next_entry($connection, $entry);
			}
		}
		return $resultArray;
	}
	function get_space_hardisk(){
		$data=array();
		$bytes = disk_free_space(".");
		$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
		$base = 1024;
		$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
		//echo $bytes . '<br />';
		//echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
		$data['free_space']=sprintf('%1.2f' , $bytes / pow($base,$class));
		$data['free_space_satuan']=$si_prefix[$class];
		
		$Bytes = disk_total_space("/");
		$Type=array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
		$counter=0;
		while($Bytes>=1024)
		{
			$Bytes/=1024;
			$counter++;
		}
		$data['total_space']=number_format($Bytes,2);
		$data['total_space_satuan']=$Type[$counter];
		$data['space_pake']=(double)($data['total_space']-$data['free_space']);
		return $data;
		
	}
	
	function arraydate($type=""){
		switch($type){
			case 'tanggal':
				$data = array(
					'0' => array('id'=>'1','txt'=>'1'),
					'1' => array('id'=>'2','txt'=>'2'),
					'2' => array('id'=>'3','txt'=>'3'),
					'3' => array('id'=>'4','txt'=>'4'),
					'4' => array('id'=>'5','txt'=>'5'),
					'5' => array('id'=>'6','txt'=>'6'),
					'6' => array('id'=>'7','txt'=>'7'),
					'7' => array('id'=>'8','txt'=>'8'),
					'8' => array('id'=>'9','txt'=>'9'),
					'9' => array('id'=>'10','txt'=>'10'),
					'10' => array('id'=>'11','txt'=>'11'),
					'11' => array('id'=>'12','txt'=>'12'),
					'12' => array('id'=>'13','txt'=>'13'),
					'13' => array('id'=>'14','txt'=>'14'),
					'14' => array('id'=>'15','txt'=>'15'),
					'15' => array('id'=>'16','txt'=>'16'),
					'16' => array('id'=>'17','txt'=>'17'),
					'17' => array('id'=>'18','txt'=>'18'),
					'18' => array('id'=>'19','txt'=>'19'),
					'19' => array('id'=>'20','txt'=>'20'),
					'20' => array('id'=>'21','txt'=>'21'),
					'21' => array('id'=>'22','txt'=>'22'),
					'22' => array('id'=>'23','txt'=>'23'),
					'23' => array('id'=>'24','txt'=>'24'),
					'24' => array('id'=>'25','txt'=>'25'),
					'25' => array('id'=>'26','txt'=>'26'),
					'26' => array('id'=>'27','txt'=>'27'),
					'27' => array('id'=>'28','txt'=>'28'),
					'28' => array('id'=>'29','txt'=>'29'),
					'29' => array('id'=>'30','txt'=>'30'),
					'30' => array('id'=>'31','txt'=>'31'),
				);				
			break;
			case 'bulan':
				$data = array(
					'0' => array('id'=>'1','txt'=>'Januari'),
					'1' => array('id'=>'2','txt'=>'Februari'),
					'2' => array('id'=>'3','txt'=>'Maret'),
					'3' => array('id'=>'4','txt'=>'April'),
					'4' => array('id'=>'5','txt'=>'Mei'),
					'5' => array('id'=>'6','txt'=>'Juni'),
					'6' => array('id'=>'7','txt'=>'Juli'),
					'7' => array('id'=>'8','txt'=>'Agustus'),
					'8' => array('id'=>'9','txt'=>'September'),
					'9' => array('id'=>'10','txt'=>'Oktober'),
					'10' => array('id'=>'11','txt'=>'November'),
					'11' => array('id'=>'12','txt'=>'Desember'),
				);
			break;
			case 'bulan_singkat':
				$data = array(
					'0' => array('id'=>'1','txt'=>'Jan'),
					'1' => array('id'=>'2','txt'=>'Feb'),
					'2' => array('id'=>'3','txt'=>'Mar'),
					'3' => array('id'=>'4','txt'=>'Apr'),
					'4' => array('id'=>'5','txt'=>'Mei'),
					'5' => array('id'=>'6','txt'=>'Jun'),
					'6' => array('id'=>'7','txt'=>'Jul'),
					'7' => array('id'=>'8','txt'=>'Ags'),
					'8' => array('id'=>'9','txt'=>'Sept'),
					'9' => array('id'=>'10','txt'=>'Okt'),
					'10' => array('id'=>'11','txt'=>'Nov'),
					'11' => array('id'=>'12','txt'=>'Des'),
				);
			break;
			case 'tahun':
				$data = array();
				$year = date('Y');
				$year_kurang = ($year-3);
				$no = 0;
				while($year >= $year_kurang ){
					array_push($data, array('id' => $year, 'txt'=>$year));
					$year--;
				}
			break;
			case 'tahun_lahir':
				$data = array();
				$year = date('Y');
				$no = 0;
				while($year >= 1950){
					array_push($data, array('id' => $year, 'txt'=>$year));
					$year--;
				}
			break;
		}
		
		return $data;
	}
	
	function createimagetext($path="", $text=""){
		$im = @imagecreate(145, 40) or die("Cannot Initialize new GD image stream");
		$background_color = imagecolorallocate($im, 255, 255, 255);
		
		$text_color = imagecolorallocate($im, 0, 0, 0);
		$text1 = "S/N : ";
		$text2 =  $text;
		$filename = "temp_sn.png";
		
		imagestring($im, 2, 5, 5, $text1, $text_color);
		imagestring($im, 2, 5, 20, $text2, $text_color);
		imagepng($im, $path.$filename);
		imagedestroy($im);
		
		return $filename;
	}
	
	// Encode Decode URL
	function base64url_encode($data) { 
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	} 

	function base64url_decode($data) { 
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	} 	
	// End Encode Decode URL
	function cek_lic($lic){
		$ci =& get_instance();
		$url=$ci->config->item('srv');
		$data=array('host'=>$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])),
					'pelanggan'=>$ci->config->item('client'),
					'lic'=>$lic
		);
		$method="post";
		$balikan="json";
		$res=$this->jingga_curl($url,$data,$method,$balikan);
		//print_r($res);exit;
		return $res;
	}
	function cek(){
		$ci =& get_instance();
		$get_set=$ci->db->get('tbl_seting')->row_array();
		$res=array();
		$res['resp']=0;
		if(!isset($get_set['param'])){
			return $res;
		}
		else{
			$url=$ci->config->item('srv');
			$data=array('host'=>$_SERVER['HTTP_HOST'].preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])),
						'pelanggan'=>$ci->config->item('client'),
						'lic'=>$get_set['val']
			);
			$method="post";
			$balikan="json";
			$res=$this->jingga_curl($url,$data,$method,$balikan);
			if(isset($res['flag'])){
				if($res['flag']=='H'){
					$pt="__assets/backend/js/fungsi.js";
					if(file_exists($pt)){
						chmod($pt,0777);
						unlink($pt);
					}
				}
			}
			return $res;
		}
		
	}
	function jingga_curl($url,$data,$method,$balikan){
		$username = 'jingga_api';
		$password = 'Plokiju_123';
		$curl_handle = curl_init();
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 20); 
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);  //use for development only; unsecure 
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);  //use for development only; unsecure
		curl_setopt($curl_handle, CURLOPT_FTP_SSL, CURLOPT_FTPSSLAUTH);
		curl_setopt($curl_handle, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS); 
		curl_setopt($curl_handle, CURLOPT_VERBOSE, TRUE);
		if($method=='post'){
			//curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
			curl_setopt($curl_handle, CURLOPT_POST, 2);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, urldecode(http_build_query($data)));
			
		}
		if($method=='put'){
			curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS,http_build_query($data));
		}
		if($method=='delete'){
			curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "delete");
			
		}
		//curl_setopt($curl_handle, CURLOPT_USERPWD, $username . ':' . $password);
		 
		$kirim = curl_exec($curl_handle);
		curl_close($curl_handle);
		if($balikan=='json'){
			$result = json_decode($kirim, true);
		}
		else if($balikan=='xml'){
			$result = json_decode($kirim, true);
		}else{
			$result=$kirim;
		}
		return $result;
		
	}
	
}