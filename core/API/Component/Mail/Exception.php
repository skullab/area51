<?php

namespace Thunderhawk\API\Component\Mail;
use Thunderhawk\API\Exception\BaseException;
class Exception extends BaseException{
	protected function defineExtra(array $extra){return $extra;}
	protected function defineMessages(array $messages, array $extra){
		$messages[100] = "" ;
		return $messages;
	}
}