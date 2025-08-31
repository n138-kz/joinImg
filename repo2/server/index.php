<?php
session_start();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once(__DIR__ . '/../vendor/autoload.php');
}
require_once(__DIR__ . '/module/class_Discord.php');

date_default_timezone_set('Asia/Tokyo');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Origin, Accept, Access-Control-Allow-Headers, X-Token");

header('Server: Hidden');
header('X-Powered-By: Hidden');

# --Method check
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
		'request'=>$req,
		'connect'=>[
			'code'=>http_response_code(),
			'method'=>strtolower($_SERVER['REQUEST_METHOD']),
		],
	]);
	exit(1);
}
# --Method check--

# --Set Default
$_SERVER['CONTENT_TYPE'] = (isset($_SERVER['CONTENT_TYPE']))?$_SERVER['CONTENT_TYPE']:'application/octet-stream';

$req = [
	'direction'=>'',
	'files'=>[],
	'token'=>'',
	'content-type'=>'image/png',
];

# --Set Default--

foreach(['direction','token', 'content-type', 'files',] as $k => $v) {
	if(isset($_GET[$v])) {
		$req[$v] = $_GET[$v];
	}
}

# --Authn
$api_discord = new n138kz\Discord();
$req['discord'] = $api_discord->login(['access_token'=>$req['token']]);
unset($req['token']);
if($req['discord'][0]['id']=='') {
	http_response_code(401);
	echo json_encode([
		'request'=>$req,
		'connect'=>[
			'code'=>http_response_code(),
			'method'=>strtolower($_SERVER['REQUEST_METHOD']),
		],
	]);
	exit(1);

}
# --Authn--

if( substr(strtolower($req['content-type']), 0, strlen('application/json'))=='application/json') {
	echo json_encode([
		'request'=>$req,
		'connect'=>[
			'code'=>http_response_code(),
			'method'=>strtolower($_SERVER['REQUEST_METHOD']),
		],
	]);
}
