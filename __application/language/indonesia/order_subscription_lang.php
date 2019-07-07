<?php

/*
 * Indonesia language
 *
 * Sample: 
 * =======
 * $lang['err_sample'] = 'Incorrect Email or Password'; 
 * $lang['sucess_sample'] = 'Login Success'; 
 * $lang['conf_sample'] = 'Are you sure want to delete this record ?'; 
 * $lang['info_sample'] = 'Your password has been sent to your email'; 
 * $lang['notif_sample'] = 'You have unread email'; 
 */

$lang['email_subject_subscription'] = 'Ini adalah Order Subscription Anda !';
$lang['email_body_subscription'] = 'Kepada {name}, <br><br>'.
		'Berikut ini adalah detail transaksi anda: <br><br>'.
		'- Nama Produk: {PortfolioNameShort} ({PortfolioCode})<br>'.
		'- Mata Uang: {Ccy}<br>'.
		'- Bank Transfer: {CompanyName}<br>'.
		'- Bank Account: {AccountNo}<br>'.
		'- Nilai Transaksi: {TrxAmount}<br>'.
		'- Status: BELUM SELESAI<br><br>'.
		'Thanks for your subscription, we waiting payment confirmation from you. <br><br>'.
		'This email was sent by: <b>{simpiName}</b>,<br>'.
		'{powered_by}';
