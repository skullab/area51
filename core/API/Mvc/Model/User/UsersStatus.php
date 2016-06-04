<?php

namespace Thunderhawk\API\Mvc\Model\User;
use Thunderhawk\API\Mvc\Model;
class UsersStatus extends Model{
	public $id ;
	public $name ;
	public $description;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\Users','status_id',array(
				'alias' => 'users',
				'reusable' => true
		));
	}
}