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

$lang['err_field_required'] = 'Field [%s] is required'; 
$lang['err_min_password_length'] = 'Minimum password length is %s'; 
$lang['err_max_password_length'] = 'Maximum password length is %s'; 
$lang['err_login_attempt_reached'] = 'Maksimum kesalahan login sudah tercapai, akun anda akan terkunci sementara. Silahkan coba lagi nanti, setelah %s'; 
$lang['err_login_failed'] = 'Email atau Password salah, silahkan coba kembali'; 
$lang['err_unlocked_failed'] = 'Incorrect Password'; 
$lang['err_old_password'] = 'Password lama anda salah'; 
$lang['err_email_not_found'] = 'Email belum terdaftar'; 
$lang['err_username_not_found'] = 'UserName belum terdaftar'; 
$lang['err_username_or_email_not_found'] = 'UserName atau Email belum terdaftar'; 
$lang['err_not_registered_user'] = 'You has not been registered, please register first'; 
$lang['err_email_has_register'] = 'Your email have registered, please login with your email & password !'; 
$lang['err_email_has_register_not_active'] = 'Your email have registered but not activate yet, please check your email to activate !'; 
$lang['err_old_client_lost_email'] = 'Your email is not recognized, please replace with your another email !'."\r\n".' Or you can ask our CS admin.'; 
$lang['err_activate_account'] = 'Token not found, or maybe your account has already activate'; 
$lang['err_activate_account_email_password'] = 'Email atau Password salah, silahkan coba kembali'; 

$lang['success_login'] = 'Login Success'; 
$lang['success_unlocked'] = 'This account has been unlocked'; 
$lang['success_reset'] = 'Your password has been reset'; 
$lang['success_chg_password'] = 'Your password has been changed'; 
$lang['success_register'] = 'Registrasi berhasil, silahkan cek email anda'; 
$lang['success_activation'] = 'Thank you, your account has been active.<br>Now you can login in our Web/Android/IOS Apps !'; 

$lang['info_sent_email_password'] = 'Your password has been sent to your email'; 
$lang['info_sent_email_reset_password_link'] = 'Link address for reset password has been sent to your email'; 
$lang['info_sent_email_rst_password'] = 'Password has been reset successfully, & new password has been sent to user email'; 
$lang['info_copyright'] = 'Copyright by %s'; 
$lang['info_poweredby'] = 'Powered by %s'; 

$lang['email_subject_register'] = '{app_name} - REGISTRASI AKUN !';
$lang['email_body_register'] = 'Dear {name}, <br><br>'.
		'Registrasi akun anda telah berjalan sukses. <br><br>'.
		'Berikut ini adalah data login anda: <br><br>'.
		'Username/Email : <b>{email}</b><br>'.
		'Password : <b>{password}</b><br><br>'.
		"Anda bisa merubah password tersebut setelah anda login.<br>".
		"Terima kasih telah menjadi bagian dari PP Darullughah Wadda'wah.<br><br>".
		"Wassalam.<br><br><br>".
		'Email ini dikirim otomatis oleh: <b>{app_name}</b>,<br>'.
		'{powered_by}';
		
$lang['email_subject_forgot_password'] = '{app_name} - LUPA PASSWORD !';
$lang['email_body_forgot_password'] = 'Dear {name}, <br><br>'.
		'Berikut ini adalah Password anda yang baru: <b>{password}</b><br><br><br>'.
		'Anda dapat merubahnya setelah anda masuk ke aplikasi.<br><br>'.
		"Wassalam.<br><br><br>".
		'Email ini dikirim otomatis oleh: <b>{app_name}</b>,<br>'.
		'{powered_by}';
		
$lang['email_subject_chg_password'] = '{app_name} - PERUBAHAN PASSWORD !';
$lang['email_body_chg_password'] = 'Dear {name}, <br><br>'.
		'Perubahan Password anda telah berjalan sukses. <br><br>'.
		'Password baru anda adalah: <b>{password}</b><br><br>'.
		"Wassalam.<br><br><br>".
		'Email ini dikirim otomatis oleh: <b>{app_name}</b>,<br>'.
		'{powered_by}';
		
		