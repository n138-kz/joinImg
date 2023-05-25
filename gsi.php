<?php session_start();
require_once './vendor/autoload.php';

if (mb_strtolower($_SERVER ['REQUEST_METHOD']) != 'post' ) {
    http_response_code(405);
    $curl_res['ts']   = time();
    $curl_res['mesg'] = 'Method Not Allowed.';

    die(json_encode($curl_res));
}

if ( !isset($_POST) || !is_array($_POST) ) {
    http_response_code(400);
    $curl_res['ts']   = time();
    $curl_res['mesg'] = 'Bad request.';

    die(json_encode($curl_res));
}

if ( !isset($_SERVER['HTTP_X_USER_ID']) ) {
    http_response_code(400);
    $curl_res['ts']   = time();
    $curl_res['mesg'] = 'Bad request.';

    die(json_encode($curl_res));
}

define('HTTP_X_USER_ID', json_decode($_SERVER['HTTP_X_USER_ID'], true));

if ( !isset(HTTP_X_USER_ID['clientId']) && !isset(HTTP_X_USER_ID['client_id']) ) {
    http_response_code(400);
    $curl_res['ts']   = time();
    $curl_res['mesg'] = 'Bad request.';

    die(json_encode($curl_res));
}

$google_oauth2_info = [];
if(false){
} elseif ( isset(HTTP_X_USER_ID['clientId']) ) {
    $google_oauth2_info['clientid'] = HTTP_X_USER_ID['clientId'];
} elseif ( isset(HTTP_X_USER_ID['client_id']) ) {
    $google_oauth2_info['clientid'] = HTTP_X_USER_ID['client_id'];
}

if ( !isset(HTTP_X_USER_ID['credential']) ) {
    http_response_code(400);
    $curl_res['ts']   = time();
    $curl_res['mesg'] = 'Bad request.';

    die(json_encode($curl_res));
}

$google_oauth2_info['credential'] = HTTP_X_USER_ID['credential'];

$google_oauth2 = new Google_Client(['client_id' => $google_oauth2_info['credential']]);
$payload = $google_oauth2->verifyIdToken($google_oauth2_info['clientid']);

$curl_res['ts']   = time();
$curl_res['mesg'] = '';
$curl_res['goauth2'] = $google_oauth2_info;
if ($payload) {
    array_push($curl_res['goauth2'],$payload);
} else {
    $curl_res['mesg'] = 'Bad request.';

    die(json_encode($curl_res));
}
var_dump([$google_oauth2_info]);
