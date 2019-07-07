<?php

/*
 * English language
 *
 * Sample: 
 * =======
 * $lang['err_sample'] = 'Incorrect Email or Password'; 
 * $lang['sucess_sample'] = 'Login Success'; 
 * $lang['conf_sample'] = 'Are you sure want to delete this record ?'; 
 * $lang['info_sample'] = 'Your password has been sent to your email'; 
 * $lang['notif_sample'] = 'You have unread email'; 
 */

$lang['email_subject_subscription'] = 'This is your Order Subscription !';
$lang['email_body_subscription'] = 'Dear {name}, <br><br>'.
		'This is your detail transaction: <br><br>'.
		'- Product Name: {PortfolioNameShort} ({PortfolioCode})<br>'.
		'- Currency: {Ccy}<br>'.
		'- Bank Transfer: {CompanyName}<br>'.
		'- Bank Account: {AccountNo}<br>'.
		'- Trx Amount: {TrxAmount}<br>'.
		'- Status: {StatusCode}<br><br>'.
		'Thanks for your subscription, we waiting payment confirmation from you. <br><br>'.
		'This email was sent by: <b>{simpiName}</b>,<br>'.
		'{powered_by}';
