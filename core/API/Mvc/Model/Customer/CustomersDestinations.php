<?php

namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersDestinations extends Model{
	public $id;
	public $codice_destinazione;
	public $nome;
	public $customers_id;
	public $agent_id;
	public $promotions_manager_id;
	public $insegne_id;
	protected function onInitialize(){
		$this->belongsTo('customers_id',__NAMESPACE__.'\Customers','id',array(
				'alias' => 'fatturatario',
				'reusable' => true
		));
		$this->belongsTo('agent_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'agente',
				'reusable' => true
		));
		$this->belongsTo('promotions_manager_id','Thunderhawk\API\Mvc\Model\User\Users','id',array(
				'alias' => 'responsabile',
				'reusable' => true
		));
		$this->hasMany('id',__NAMESPACE__.'\CustomersDestinationsAddress','customers_destinations_id',array(
				'alias' => 'indirizzi',
				'reusable' => true
		));
	}
}