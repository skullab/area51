<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class Customers extends Model{
	public $id;
	public $codice;
	public $nome;
	public $customers_groups_id;
	public $customers_state_id;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('customers_groups_id',__NAMESPACE__.'\CustomersGroups','id',array(
				'alias' => 'group',
				'reusable' => true
		));
		$this->belongsTo('customers_state_id',__NAMESPACE__.'\CustomersState','id',array(
				'alias' => 'state',
				'reusable' => true
		));
		// 1-1
		$this->hasOne('id',__NAMESPACE__.'\CustomersAddress','customers_id',array(
				'alias' => 'address',
				'reusable' => true
		));
		$this->hasOne('id',__NAMESPACE__.'\CustomersDetails','customers_id',array(
				'alias' => 'details',
				'reusable' => true
		));
		//1-n
		$this->hasMany('id',__NAMESPACE__.'\CustomersDestinations','customers_id',array(
				'alias' => 'destinations',
				'reusable' => true
		));
	}
}