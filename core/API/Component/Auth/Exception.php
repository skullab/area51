<?php

namespace Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Exception\BaseException;
class Exception extends BaseException{
	protected function defineExtra(array $extra){return $extra;}
	protected function defineMessages(array $messages, array $extra){
		$messages[10] = 'Bad credentials !';
		$messages[100] = 'Wrong email/password combination';
		$messages[200] = 'Bad user\'s status : %s ';
		$messages[300] = 'This email doesn\'t exists !';
		$messages[400] = 'No request available for this key';
		$messages[500] = 'This request is expired';
		$messages[600] = 'Bad token for this request';
		$messages[700] = 'The user has been removed';
		return $messages;
	}
}