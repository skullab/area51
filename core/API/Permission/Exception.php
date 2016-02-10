<?php

namespace Thunderhawk\API\Permission;

use Thunderhawk\API\Exception\BaseException;

class Exception extends BaseException {
	public function defineExtra($extra){return $extra;}
	public function defineMessages($messages, $extra){
		$messages[100] = 'You can not change the name of the service if it is already assigned' ;
		return $messages;
	}
}