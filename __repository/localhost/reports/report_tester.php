<?php define('BASEPATH', '-');

$name = isset($_GET['name']) ? strtolower($_GET['name']) : '';
if (!$name)
	die('Parameter required: [name]');

$output = isset($_GET['output']) ? $_GET['output'] : false;
if ($output)
	$output = strtolower($output) == 'true' || $output == '1' ? true : false;

require $name.'.php';

$report = new F_Report();
$report->create(NULL, ['output' => $output]);

// http://localhost/web_reports/report_tester.php?name=formulir_opening_account
// http://localhost/web_reports/report_tester.php?name=formulir_opening_account&output=true