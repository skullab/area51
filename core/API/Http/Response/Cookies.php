<?php

namespace Thunderhawk\API\Http\Response;
use Phalcon\Http\Response\Cookies as PhalconCookies;
class Cookies extends PhalconCookies{
	public function set($name, $value = null, $expire = 0, $path = "/", $secure = null, $domain = null, $httpOnly = null){
		if($this->isUsingEncryption()){
			$value = base64_encode($value);
		}
		return parent::set($name,$value,$expire,$path,$secure,$domain,$httpOnly);
	}
	public function get($name){
		$cookie = parent::get($name) ;
		if($this->isUsingEncryption()){
			$cookie->setValue(base64_decode($cookie->getValue()));
		}
		return $cookie ;
	}
}