<?php

namespace Thunderhawk\API\Mvc\Model\Italia;
use Thunderhawk\API\Mvc\Model;
class ItaliaRegioni extends Model{
	public $id;
	public $nome;
	public $codice_istat;
	protected function onInitialize(){
		// 1-n
		$this->hasMany('id',__NAMESPACE__.'\ItaliaProvince','italia_regioni_id',array(
				'alias' => 'provincia',
				'reusable' => true
		));
	}
	
}