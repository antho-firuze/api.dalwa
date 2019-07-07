<?php defined('BASEPATH') OR exit('No direct script access allowed');
// sample gmail ssl
$config['useragent'] 	= 'CI Webservice';	
$config['charset'] 	  = 'utf-8';	  // Character set (utf-8, iso-8859-1, etc.).
$config['protocol'] 	= 'smtp';	    // mail, sendmail, or smtp		
$config['mailtype'] 	= 'html';	    // text or html	
$config['priority'] 	= '1';	      // 1, 2, 3, 4, 5
$config['newline'] 	  = "\r\n";	    // “\r\n” or “\n” or “\r”
$config['crlf'] 	    = "\r\n";	    // “\r\n” or “\n” or “\r”
$config['smtp_host'] 	= 'smtp.gmail.com';	
$config['smtp_port'] 	= '465';		  // ssl=465 or tls=587
$config['smtp_user'] 	= 'simpi.tfs@gmail.com';		
$config['smtp_pass'] 	= 'ranwid94';		
$config['smtp_crypto'] 	= 'ssl';    // ssl/tls		
$config['smtp_timeout'] = 7;	
$config['email_from'] = 'simpi.tfs@gmail.com';		// email address for system email sender

// sample gmail tls
// $config['useragent'] 	= 'CI Webservice';	
// $config['charset'] 	  = 'utf-8';	  // Character set (utf-8, iso-8859-1, etc.).
// $config['protocol'] 	= 'smtp';	    // mail, sendmail, or smtp		
// $config['mailtype'] 	= 'html';	    // text or html	
// $config['priority'] 	= '1';	      // 1, 2, 3, 4, 5
// $config['newline'] 	  = "\r\n";	    // “\r\n” or “\n” or “\r”
// $config['crlf'] 	    = "\r\n";	    // “\r\n” or “\n” or “\r”
// $config['smtp_host'] 	= 'smtp.gmail.com';	
// $config['smtp_port'] 	= '587';		  // ssl=465 or tls=587
// $config['smtp_user'] 	= 'simpi.tfs@gmail.com';		
// $config['smtp_pass'] 	= 'ranwid94';		
// $config['smtp_crypto'] 	= 'tls';    // ssl/tls		
// $config['smtp_timeout'] 	= 7;	
// $config['email_from'] = 'simpi.tfs@gmail.com';		// email address for system email sender
