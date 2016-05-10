<?php

namespace Thunderhawk\API\Mvc\Model\Italia;
use Thunderhawk\API\Mvc\Model;
class ItaliaComuni extends Model{
	public $id;
	public $nome;
	public $cap;
	public $prefisso_telefonico;
	public $codice_istat;
	public $codice_catastale;
	public $lat;
	public $lng;
	public $italia_province_id;
	protected function onInitialize(){
		// n-1
		$this->belongsTo('italia_province_id',__NAMESPACE__.'\ItaliaProvince','id',array(
				'alias' => 'provincia',
				'reusable' => true
		));
	}
}