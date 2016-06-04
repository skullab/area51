<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersDetails extends Model{
	
	public $name;
	public $surname;
	public $address;
	public $phone;
	public $portrait;
	public $users_id;
	protected function onInitialize(){
		$this->hasOne('users_id',__NAMESPACE__.'\Users','id',array(
				'alias' => 'user',
				'reusable' => true
		));
	}
}