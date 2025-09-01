<?php
namespace n138kz;
class IpInfo {
	function getInfo($mode='withoutauth', $token='') {
		switch ($mode) {
			case 'withoutauth':
				$url = 'http://ipinfo.io/'.$_SERVER['REMOTE_ADDR'];
				break;
			case 'lite':
				$url = 'https://api.ipinfo.io/lite/'.$_SERVER['REMOTE_ADDR'].'?token='.$token;
				break;
			case 'core':
				$url = 'https://api.ipinfo.io/lookup/'.$_SERVER['REMOTE_ADDR'].'?token='.$token;
				break;
			case 'plus':
				$url = 'https://api.ipinfo.io/lookup/'.$_SERVER['REMOTE_ADDR'].'?token='.$token;
				break;
			default:
				break;
		}
		$req = curl_init();
		curl_setopt($req, CURLOPT_URL, $url);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($req);
		$res = json_decode($res, true);
		$res = array_merge([
			'ip'=>'',
			'hostname'=>'',
			'city'=>'',
			'region'=>'',
			'country'=>'',
			'loc'=>'',
			'org'=>'',
			'postal'=>'',
			'timezone'=>'',
			'readme'=>'',
			'asn'=>'',
			'as_name'=>'',
			'as_domain'=>'',
			'country_code'=>'',
			'continent_code'=>'',
			'continent'=>'',
			'status'=>'',
			'error'=>[],
		], $res);
		ksort($res);
		$res_info = curl_getinfo($req);
		$res_info['url'] = str_replace($token, '***', $res_info['url']);
		return [
			$res,
			$res_info,
		];
	}
}
