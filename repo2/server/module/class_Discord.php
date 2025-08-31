<?php
namespace n138kz;
class Discord {
	function login($qlist=[]) {
		if( ! isset($qlist['access_token']) ) {
			return null;
		}
		return $this->getUserInfo($qlist['access_token']);
	}
}
