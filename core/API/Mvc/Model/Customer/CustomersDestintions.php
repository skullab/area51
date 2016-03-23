<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersDestintions extends Model{
	public $id;
	public $codice;
	public $nome;
	public $codice_indirizzo;
	public $codice_insegna;
	public $insegna;
	public $customers_id;
	protected function onInitialize(){
		$this->belongsTo('customers_id',__NAMESPACE__.'\Customers','id',array(
				'alias' => 'customer',
				'reusable' => true
		));
		$this->hasMany('id',__NAMESPACE__.'\CustomersDestinationsAddress','customers_destinations_id',array(
				'alias' => 'address',
				'reusable' => true
		));
	}
}