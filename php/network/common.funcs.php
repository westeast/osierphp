<?php
/**
 * 获取客户端ip
 */
function getIp(){
	$ip = false;
	if (!empty(getenv('HTTP_CLIENT_IP'))){
		$ip = getenv('HTTP_CLIENT_IP');
	}
	if (!empty(getenv('HTTP_X_FORWARDED_FOR'))){
		$ips = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
		if ($ip){
			array_unshift($ips, $ip);
			$ip = false;
		}
		for ($i = 0,$nums = count($ips); $i < $nums; $i++){
			if (!preg_match('^(10|172\.16|192\.168)\.', $ips[$i])){
				$ip = $ips[$i];
				break;
			}
		}
	}
	return ($ip ? $ip : getenv('REMOTE_ADDR'));//可能 是客户端ip也可能是代理的ip不能伪造
}