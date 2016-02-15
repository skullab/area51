<?php

namespace Thunderhawk\API\Mvc\Model;
use Thunderhawk\API\Mvc\Model;
class Users extends Model{
	protected $id,$username,$email,$password,$created_at,$status_id;
	public $usersStatus ;
	protected function onInitialize(){
		$this->hasOne('status_id',__NAMESPACE__.'\UsersStatus','id');
		$this->hasOne('profile_id',__NAMESPACE__.'\UsersProfiles','id');
	}
	public function getUsersStatus(){
		return $this->{__NAMESPACE__.'\UsersStatus'};
	}
	public function getUsersProfiles(){
		return $this->{__NAMESPACE__.'\UsersProfiles'};
	}
}