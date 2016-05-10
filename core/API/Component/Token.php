<?php

namespace Thunderhawk\API\Component;

use Phalcon\Mvc\User\Component;

final class Token extends Component {
	private $_tokenID = 'crsfTokens' ;
	
	public function generateInput($tokenKey = 'csrf',$numberBytes = null){
		return '<input type="hidden" name="'.$tokenKey.'" value="'.$this->get($numberBytes).'">' ;
	}
	public function get($numberBytes = null){
		$numberBytes = $numberBytes ? $numberBytes : 12 ;
		$token = base64_encode(openssl_random_pseudo_bytes($numberBytes));
		$_SESSION[$this->_tokenID][$token] = time();
		return $token ;
	}
	
	public function check($tokenKey = 'csrf',$expire = 86400,$reusable = false){
		$token = $this->request->getPost($tokenKey);
		$expiration = time() - $expire ;
		if(isset($_SESSION[$this->_tokenID][$token]) && $_SESSION[$this->_tokenID][$token] >=  $expiration){
			if(!$reusable)unset($_SESSION[$this->_tokenID][$token]);
			return true;
		}
		return false ;
	}
	public function checkReusable($tokenKey = 'csrf',$expire = 600){
		return $this->check($tokenKey,$expire,true);
	}
	public function garbage(){
		$_SESSION[$this->_tokenID] = null ;
	}
}