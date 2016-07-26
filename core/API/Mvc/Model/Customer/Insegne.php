<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class Insegne extends Model{
	public $id;
	public $codice_insegna;
	public $nome;
	public $immagine;
	public $customers_id;
	protected function onInitialize(){
		$this->belongsTo('customers_id',__NAMESPACE__.'\Customers','id',array(
				'alias' => 'fatturatario',
				'reusable' => true
		));
	}
	
}