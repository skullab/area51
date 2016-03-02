<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class Users extends Model{
	public $id,$email,$password,$created_at,$status_id,$role;
	protected function onInitialize(){
		// 1-1
		$this->hasOne('id',__NAMESPACE__.'\UsersDetails','users_id',array(
				'alias' => 'details',
				'reusable' => true
		));
		$this->hasOne('id',__NAMESPACE__.'\UsersLogin','users_id',array(
				'alias' => 'login'
		));
		// n-1
		$this->belongsTo('status_id',__NAMESPACE__.'\UsersStatus','id',array(
				'alias' => 'status',
				'reusable' => true
		));
		// 1-n
		$this->hasMany('id',__NAMESPACE__.'\UsersFailedAttempts','users_id',array(
				'alias' => 'failed',
				'reusable' => true
		));
		$this->hasMany('id',__NAMESPACE__.'\UsersForgotPassword','users_id',array(
				'alias' => 'forgot',
				'reusable' => true
		));
		$this->hasMany('role',__NAMESPACE__.'\Acl\AclRoles','name');
	}
	public static function exists($where){
		return self::findFirst($where);
	}
}