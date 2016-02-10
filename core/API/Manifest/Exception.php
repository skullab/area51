<?php

namespace Thunderhawk\API\Manifest;
use Thunderhawk\API\Exception\BaseException;
class Exception extends BaseException{
	protected function defineExtra(array $extra){return $extra;}
	protected function defineMessages(array $messages, array $extra){
		$messages[self::CODE_MISSING] = 'The file "%s" doesn\'t exist' ;
		return $messages;
	}
}