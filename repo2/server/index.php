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
$req['discord'] = new n138kz\Discord();
$req['discord'] = $req['discord']->login(['access_token'=>$req['token']]);
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

foreach($req['files'] as $k => $v) {
	$req['files'][$k] = [
		'file' => [
			'tmp_name' => tempnam('/tmp', 'php.'),
			'name' => preg_replace('/\?.*/i', '', basename($v)),
			'ref_uri' => $v,
			'size' => 0,
			'extension' => 'dat',
		],
		'image' => [
			'size' => [],
		],
	];
	$req['files'][$k]['file']['size'] = file_put_contents($req['files'][$k]['file']['tmp_name'], file_get_contents($v));
	$req['files'][$k]['image']['size'] = array_merge([
		'mime' => 'application/octet-stream',
		'bits' => 0,
		'channels' => 0,
		'width' => 0,
		'height' => 0,
	], getimagesize($req['files'][$k]['file']['tmp_name']));
	$req['files'][$k]['image']['size']['width'] = $req['files'][$k]['image']['size'][0];
	$req['files'][$k]['image']['size']['height'] = $req['files'][$k]['image']['size'][1];
	$req['files'][$k]['image']['size'][3] = explode(' ', $req['files'][$k]['image']['size'][3]);
	switch ($req['files'][$k]['image']['size']['mime']) {
		case 'image/jpeg':
			$req['files'][$k]['file']['extension'] = '.jpg';
			break;
		case 'image/png':
			$req['files'][$k]['file']['extension'] = '.png';
			break;
		case 'image/gif':
			$req['files'][$k]['file']['extension'] = '.gif';
			break;
		case 'image/svg+xml':
			$req['files'][$k]['file']['extension'] = '.svg';
			break;
		case 'image/avif':
			$req['files'][$k]['file']['extension'] = '.avif';
			break;
		case 'image/heic':
			$req['files'][$k]['file']['extension'] = '.heic';
			break;
		case 'image/heif':
			$req['files'][$k]['file']['extension'] = '.heif';
			break;
		case 'image/webp':
			$req['files'][$k]['file']['extension'] = '.webp';
			break;
	}

}

# --Print in application/json
if( substr(strtolower($req['content-type']), 0, strlen('application/json'))=='application/json') {
	echo json_encode([
		'request'=>$req,
		'connect'=>[
			'code'=>http_response_code(),
			'method'=>strtolower($_SERVER['REQUEST_METHOD']),
		],
	]);
	exit(0);
}
# --Print in application/json--
