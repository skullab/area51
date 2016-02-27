<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class Users extends Model{
	public $id,$email,$password,$created_at,$status_id,$role;
	protected function onInitialize(){
		$this->belongsTo('status_id',__NAMESPACE__.'\UsersStatus','id',array(
				'alias' => 'status',
				'reusable' => true
		));
		$this->belongsTo('role',__NAMESPACE__.'\Acl\AclRoles','name');
		$this->hasMany('id',__NAMESPACE__.'\UsersFailedAttempts','users_id',array(
				'alias' => 'failed',
				'reusable' => true
		));
	}
}