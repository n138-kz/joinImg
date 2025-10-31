<?php
namespace n138kz;
class Discord {
	function login($qlist=[]) {
		if( ! isset($qlist['access_token']) ) {
			return null;
		}
		return $this->getUserInfo($qlist['access_token']);
	}
	function getUserInfo($token) {
		$url = 'https://discordapp.com/api/users/@me';
		$header = [
			'Authorization: Bearer '.$token,
		];
		$req = curl_init();
		curl_setopt($req, CURLOPT_URL, $url);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($req, CURLOPT_HTTPHEADER, $header);
		$res = curl_exec($req);
		$res = json_decode($res, true);
		$res = array_merge([
			'id'=>'',
			'username'=>'',
			'avatar'=>'',
			'global_name'=>'',
			'mfa_enabled'=>false,
			'locale'=>'',
		], $res);
		$res_info = curl_getinfo($req);
		return [
			$res,
			$res_info,
		];
	}
}
