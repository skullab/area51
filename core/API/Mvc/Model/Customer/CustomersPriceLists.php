<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersPriceLists extends Model{
	public $id;
	public $price_lists_id ;
	public $customers_destinations_id ;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('price_lists_id','Thunderhawk\API\Mvc\Model\Products\PriceLists','id',array(
				'alias' => 'listino',
				'reusable' => true
		));
		$this->belongsTo('customers_destinations_id',__NAMESPACE__.'\CustomersDestinations','id',array(
				'alias' => 'pdv',
				'reusable' => true
		));
	}
}