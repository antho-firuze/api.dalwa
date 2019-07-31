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

$lang['err_setting_not_found'] = 'Payment Setting tidak valid'; 
$lang['err_invalid_array'] = 'Field [%s]: File type array tidak valid'; 
$lang['err_billing_invalid'] = 'Data tagihan tidak valid'; 
$lang['err_santri_invalid'] = 'Data Santri/NIS tidak valid'; 
$lang['err_payment_method_invalid'] = 'Metode pembayaran tidak valid'; 
$lang['err_payment_no_invalid'] = 'No pembayaran tidak valid'; 

$lang['email_subject_confirmation'] = '{app_name} - KONFIRMASI PEMBAYARAN !';
$lang['email_body_confirmation'] = 'Dear {name}, <br><br>'.
		'Berikut ini adalah Pembayaran Pondok Pesantren yang harus anda bayarkan.<br><br><br>'.
		'No. Tagihan: <b>{payment_no}</b><br><br>'.
		'Total Tagihan: <b>{grand_total}</b><br><br>'.
		'No. Virtual Account BCA: <b>{account_no}</b><br><br><br>'.
		'Dicek dalam 10 menit setelah pembayaran berhasil.<br><br>'.
		'Hanya menerima dari Bank BCA.<br><br><br>'.
		"Wassalam.<br><br>".
		'Email ini dikirim otomatis oleh: <b>{app_name}</b>,<br>'.
		'{powered_by}';
		
