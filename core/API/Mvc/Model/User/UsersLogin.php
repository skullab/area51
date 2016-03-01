<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersLogin extends Model{
	
	public $last_access;
	public $last_operation;
	public $unix_last_operation;
	public $online;
	public $busy;
	public $users_id;
	protected function onInitialize(){
		$this->hasOne('users_id',__NAMESPACE__.'\Users','id');
	}
	public function afterFetch(){
		//$this->last_access = date("Y-m-d H:i:s",strtotime($this->last_access));
		$this->unix_last_operation = date("U",strtotime($this->last_operation));
	}
}