<?php
session_start();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once(__DIR__ . '/../vendor/autoload.php');
}

date_default_timezone_set('Asia/Tokyo');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Origin, Accept, Access-Control-Allow-Headers, X-Token");

header('Server: Hidden');
header('X-Powered-By: Hidden');

$has_check_pass = false;
$allow_request_method = ['get', 'connect','options', 'head'];
foreach($allow_request_method as $k => $v){
	if( substr(strtolower($_SERVER['REQUEST_METHOD']), 0, strlen($v)) == $v ){
		$has_check_pass = true;
	}
}
if(!$has_check_pass){
	http_response_code(405);
	echo json_encode([
	]);
	exit(1);
}

$_SERVER['CONTENT_TYPE'] = (isset($_SERVER['CONTENT_TYPE']))?$_SERVER['CONTENT_TYPE']:'application/octet-stream';

