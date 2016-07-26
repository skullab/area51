<?php
namespace Thunderhawk\API\Mvc\Model\Customer;
use Thunderhawk\API\Mvc\Model;
class CustomersDestinationsAddress extends Model{
	public $id;
	public $codice_indirizzo;
	public $indirizzo;
	public $cap;
	public $citta;
	public $provincia;
	public $regione;
	public $nazione;
	public $telefono;
	public $note;
	public $customers_destinations_id;
	protected function onInitialize(){
		$this->belongsTo('customers_destinations_id',__NAMESPACE__.'\CustomersDestinations','id',array(
				'alias' => 'pdv',
				'reusable' => true
		));
	}
}