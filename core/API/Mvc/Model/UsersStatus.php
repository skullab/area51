<?php

namespace Thunderhawk\API\Mvc\Model;
use Thunderhawk\API\Mvc\Model;
class UsersStatus extends Model{
	protected $id ;
	public $status ;
	public $description;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\Users','status_id');
	}
}