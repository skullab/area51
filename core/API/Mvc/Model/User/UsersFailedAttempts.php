<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersFailedAttempts extends Model{
	public $id;
	public $users_id;
	public $ip_address;
	public $attempted;
	protected function onInitialize(){
		$this->belongsTo('users_id',__NAMESPACE__.'\Users','id',array(
				'alias' => 'user',
				'reusable' => true
		));
	}
}