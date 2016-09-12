<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersDetails extends Model{
	
	public $name;
	public $surname;
	public $address;
	public $phone;
	public $portrait;
	public $code;
	public $update_time;
	public $users_id;
	protected function onInitialize(){
		$this->hasOne('users_id',__NAMESPACE__.'\Users','id',array(
				'alias' => 'user',
				'reusable' => true
		));
	}
	public function beforeValidationOnUpdate(){
		$this->update_time = date('Y-m-d H:i:s');
	}
}