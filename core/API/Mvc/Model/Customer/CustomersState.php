<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersState extends Model{
	public $id;
	public $stato;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\Customers','customers_state_id',array(
				'alias' => 'fatturatari',
				'reusable' => true
		));
	}
}