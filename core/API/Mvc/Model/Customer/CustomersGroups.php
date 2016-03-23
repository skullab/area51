<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersGroups extends Model{
	public $id;
	public $codice;
	public $nome;
	protected function onInitialize(){
		$this->hasMany('id',__NAMESPACE__.'\Customers','customers_groups_id',array(
				'alias' => 'customers',
				'reusable' => true
		));
	}
}