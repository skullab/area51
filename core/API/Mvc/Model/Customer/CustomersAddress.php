<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersAddress extends Model{
	public $codice;
	public $indirizzo;
	public $cap;
	public $citta;
	public $provincia;
	public $regione;
	public $nazione;
	public $customers_id;
	protected function onInitialize(){
		$this->hasOne('customers_id',__NAMESPACE__.'\Customers','id');
	}
}