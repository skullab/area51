<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class Users extends Model{
	public $id,$email,$password,$created_at,$users_status_id,$acl_roles_name;
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
		$this->belongsTo('users_status_id',__NAMESPACE__.'\UsersStatus','id',array(
				'alias' => 'status',
				'reusable' => true
		));
		$this->belongsTo('acl_roles_name','Thunderhawk\API\Mvc\Model\Acl\AclRoles','name',array(
				'alias' => 'roles',
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
		
	}
	public static function exists($where){
		return self::findFirst($where);
	}
	public static function getProgressiveCode($maxLength = 7,$substitute = 0){
		$max = Users::maximum(array(
				'column' => 'id'
		));
		$code = str_pad((int)substr($max, -4) + 1,$maxLength,$substitute,STR_PAD_LEFT);
		return $code ;
	}
}