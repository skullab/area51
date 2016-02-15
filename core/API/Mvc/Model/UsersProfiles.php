<?php

namespace Thunderhawk\API\Mvc\Model;

use Thunderhawk\API\Mvc\Model;

class UsersProfiles extends Model {
	protected $id;
	public $profile;
	public $description;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\Users','profile_id');
	}
}