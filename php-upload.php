<?php
// @ini_set('memory_limit', '125M');
// @ini_set('post_max_size', '50M');
// @ini_set('upload_max_filesize', '50M');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

set_error_handler(function($errno, $errstr) { 
  // if ($errno == 8)
    throw new ErrorException('Memcached Server not available');
  // else
    // throw new ErrorException($errstr);
});

try {
  print_r($_POST);
  print_r($_FILES);
  restore_error_handler();
}	catch (ErrorException $e) {
  die($e->getMessage());
} 
?>