<?php session_start();
require_once './vendor/autoload.php';

class n138 {
	private $exit_params;
	function __construct(){
		$this->exit_params = [
			'time' => time(),
			'text' => '',
			'code' => 0,
			'remote' => [
				'address' => '',
			],
			'debug' => FALSE,
			'size' => [
				'width' => 0.8,
				'height' => 0.8,
			],
		];
	}
	function getExitStatus() {
		$this->setVal('http', http_response_code());
		return $this->exit_params;
	}
	function getVal($key) {
		return $this->exit_params[$key];
	}
	function setVal($key, $val) {
		$this->exit_params[$key] = $val;
	}
}
ini_set('upload_max_filesize', '25M');
ini_set('post_max_size', '100M');
header('Content-Type: text/plain');
$exitStatus = new n138();
$exitStatus->setVal('time', time());
$exitStatus->setVal('remote', ['address'=>$_SERVER['REMOTE_ADDR']]);
$json_encode_option = 0;
if( isset($_SERVER['HTTP_X_SCRIPT_DEBUG']) ){
	$exitStatus->setVal('debug', (bool)($_SERVER['HTTP_X_SCRIPT_DEBUG']));
	$json_encode_option = JSON_PRETTY_PRINT;
}
define('DEBUG', $exitStatus->getVal('debug'));

if( mb_strtolower($_SERVER['REQUEST_METHOD']) != 'post' ){
	http_response_code(405);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Method Not Allowed.');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !isset($_FILES['image']) || !is_array($_FILES['image']) ) {
	http_response_code(400);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Bad Request.');
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

if ( !isset($_FILES['image']["name"]['cm']) || mb_strlen($_FILES['image']["tmp_name"]['cm'])==0 || $_FILES['image']["size"]['cm']==0 || $_FILES['image']["error"]['cm']!=0 ) {
	http_response_code(400);
	$exitStatus->setVal('time', time());
	$exitStatus->setVal('text', 'Bad Request.'.__LINE__);
	if ( DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	
	echo json_encode($exitStatus->getExitStatus(), $json_encode_option);
	if ( !DEBUG ) {
		$exitStatus->setVal('text', $exitStatus->getVal('text') . '#' . __LINE__);
	}
	error_log(json_encode($exitStatus->getExitStatus()));
	exit();
}

$files_before_1 = $_FILES['image'];
$files_before_1valid = [];
$item = [
    'lt', 'ct', 'rt',
    'lm', 'cm', 'rm',
    'lb', 'cb', 'rb',
];
foreach ($item as $key => $val) {
    if ($files_before_1["error"][$val]!=0) {
        unset($files_before_1['error'][$val]);
        unset($files_before_1['name'][$val]);
        unset($files_before_1['tmp_name'][$val]);
        unset($files_before_1['full_path'][$val]);
        unset($files_before_1['type'][$val]);
        unset($files_before_1['error'][$val]);
        unset($files_before_1['size'][$val]);
    } else {
        $files_before_1valid[$val]['error']     = $files_before_1['error'][$val];
        $files_before_1valid[$val]['name']      = $files_before_1['name'][$val];
        $files_before_1valid[$val]['tmp_name']  = $files_before_1['tmp_name'][$val];
        $files_before_1valid[$val]['full_path'] = $files_before_1['full_path'][$val];
        $files_before_1valid[$val]['type']      = $files_before_1['type'][$val];
        $files_before_1valid[$val]['error']     = $files_before_1['error'][$val];
        $files_before_1valid[$val]['size']      = $files_before_1['size'][$val];
    }
}

var_dump($files_before_1valid);