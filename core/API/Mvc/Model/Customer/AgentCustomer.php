<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;

class AgentCustomer extends Model{
	public $id ;
	public $agent_id;
	public $customers_destinations_address_id;
	protected function onInitialize(){
		$this->belongsTo('agent_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'agent',
				'reusable' => true
		));
		$this->belongsTo('customers_destinations_address_id',__NAMESPACE__.'\CustomersDestinationsAddress','id',array(
				'alias' => 'address',
				'reusable' => true
		));
	}
}