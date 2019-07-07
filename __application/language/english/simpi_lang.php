<?php

/* 
 * English language
 */

$lang['err_data_already_exist'] = 'Data already exist: [%s]'; 
$lang['err_default_sales'] = 'Default Salesman not found'; 
$lang['err_default_currency'] = 'Default Currency not found'; 
$lang['err_default_credit_user'] = 'Default Credit User not found';
$lang['err_default_credit_client'] = 'Default Credit Client not found';
$lang['err_credit_not_enought'] = 'Application did not enought credit %s';
$lang['err_unidentified'] = 'Data is not available: [%s]';
$lang['err_invalid_date'] = 'Key[%s]: Date format must be in: [%s]';
$lang['err_param_required_related'] = 'Key[%s] is Required, if Key[%s] was exists';
$lang['err_commit_data'] = 'Error: There is something wrong with the database, please contact admin@simpi-pro.com';
$lang['err_email_to'] = 'Email To is required !';
$lang['err_email_subject'] = 'Email Subject is required !';
$lang['err_transaction_account_not_activate'] = 'Transaction failed, because your account is not activate';
$lang['err_transaction_delete_status'] = 'Transaction failed to cancel, because it\'s has been process';
$lang['err_simpi_user_invalid'] = 'UserID is invalid';
$lang['err_mobc_login_invalid'] = 'EmailID is invalid';

$lang['email_subject_new_accountindividual'] = 'SIMPIPRO Account Activation !';
$lang['email_body_new_accountindividual'] = 'Dear {name}, <br><br>'.
		'Your registration has been completed. <br><br>'.
		'This is your login email & password: <br><br>'.
		'Email : <b>{email}</b><br>'.
		'Password : <b>{new_password}</b><br><br>'.
		'Before you login, please activate first by clicking this link below :<br><br>'.
		'{domain_frontend}frontend/x_auth?mode=activation&token={token}&appcode={appcode}<br><br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';
