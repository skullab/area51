<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersForgotPassword extends Model{
	public $id;
	public $users_id;
	public $created_at;
	public $token;
	public $private_key;
	public $expires;
	protected function onInitialize(){
		$this->belongsTo('users_id',__NAMESPACE__.'\Users','id',array(
				'alias' => 'user',
				'resusable' => true
		));
	}
}