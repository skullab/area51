<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersBook extends Model{
	public $id;
	public $bookkeeper_id;
	public $customers_id;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('bookkeeper_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'bookkeeper',
				'reusable' => true
		));
		$this->belongsTo('customers_id',__NAMESPACE__.'\Customers','id',array(
				'alias' => 'customer',
				'reusable' => true
		));
	}
}