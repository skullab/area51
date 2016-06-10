<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersPriceLists extends Model{
	public $id;
	public $customers_id;
	//
	public $listino_vigore ;
	public $listino_vigore_revisione;
	//
	public $listino_futuro;
	public $listino_futuro_revisione;
	//
	protected function onInitialize(){
		// n-1
		$this->belongsTo('customers_id',__NAMESPACE__.'\Customers','id',array(
				'alias' => 'customer',
				'reusable' => true
		));
		// n-1
		$this->belongsTo('listino_vigore','Thunderhawk\API\Mvc\Model\Products\PriceLists','id',array(
				'alias' => 'listino_vigore',
				'reusable' => true
		));
		// n-1
		$this->belongsTo('listino_futuro','Thunderhawk\API\Mvc\Model\Products\PriceLists','id',array(
				'alias' => 'listino_futuro',
				'reusable' => true
		));
		
	}
}