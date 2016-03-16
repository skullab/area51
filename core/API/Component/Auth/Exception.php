<?php

namespace Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Exception\BaseException;
class Exception extends BaseException{
	protected function defineExtra(array $extra){return $extra;}
	protected function defineMessages(array $messages, array $extra){
		$messages[10] = _('Bad credentials !');
		$messages[100] = _('Wrong email/password combination');
		$messages[200] = _('Bad user\'s status : %s ');
		$messages[300] = _('This email doesn\'t exists !');
		$messages[400] = _('No request available for this key');
		$messages[500] = _('This request is expired');
		$messages[600] = _('Bad token for this request');
		$messages[700] = _('The user has been removed');
		$messages[800] = _('No users available in database');
		return $messages;
	}
}