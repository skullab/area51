<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersDetails extends Model{
	public $telefono;
	public $fax;
	public $email;
	public $piva;
	public $cf;
	public $epal;
	public $monoref;
	public $note;
	public $customers_id;
	protected function onInitialize(){
		$this->hasOne('customers_id',__NAMESPACE__.'\Customers','id');
	}
}