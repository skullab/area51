<?php

namespace Thunderhawk\API\Mvc\Model\Acl;

use Thunderhawk\API\Mvc\Model;

class AclRoles extends Model {
	public $name ;
	public $description ;
	protected function onInitialize(){
		$this->belongsTo('name','Thunderhawk\API\Mvc\Model\User\Users','role');
	}
}