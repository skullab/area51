<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class AgentManager extends Model{
	public $id;
	public $agent_id;
	public $manager_id;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('agent_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'agent',
				'reusable' => true
		));
		$this->belongsTo('manager_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'manager',
				'reusable' => true
		));
	}
}