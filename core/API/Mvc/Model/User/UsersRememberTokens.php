<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersRememberTokens extends Model{
	public $id;
	public $users_id;
	public $selector;
	public $token;
	public $expires;
	protected function onInitialize() {
		$this->belongsTo('users_id',__NAMESPACE__.'\Users','id',array(
				'alias' => 'user',
				'reusable' => true
		));
	}
}