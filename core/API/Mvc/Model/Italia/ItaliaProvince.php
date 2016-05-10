<?php
namespace Thunderhawk\API\Mvc\Model\Italia;
use Thunderhawk\API\Mvc\Model;
class ItaliaProvince extends Model{
	public $id;
	public $nome;
	public $sigla;
	public $codice_istat;
	public $italia_regioni_id;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('italia_regioni_id',__NAMESPACE__.'\ItaliaRegioni','id',array(
				'alias' => 'regione',
				'reusable' => true
		));
	}
}