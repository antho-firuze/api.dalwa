<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['table_user'] = 'mobc_login';								// table user
$config['table_session'] = 'mobc_session';					// table session
$config['table_user_config'] = '';									// table user config
$config['forgot_token_expiration'] = 60*60*1;				// second*minute*hour
$config['login_token_expiration'] = 60*60*24;				// second*minute*hour

$config['android_token_expiration'] = 60*60*24;	// second*minute*hour
$config['ios_token_expiration'] = 60*60*24;			// second*minute*hour
$config['web_token_expiration'] = 60*60*24;			// second*minute*hour

$config['min_password_length'] = 5;
$config['max_password_length'] = 8;
$config['remember_users'] = true;			// Allow users to be remembered and enable auto-login
$config['max_login_attempts'] = 3;		// The maximum number of failed login attempts
$config['lockout_time'] = 600;				// The number of seconds to lockout an account due to exceeded attempts
