<?php

namespace Thunderhawk\API\Mvc\Model\Listini;
use Thunderhawk\API\Mvc\Model;
class ListiniVersioni extends Model{
	const VERSION_PREFIX = 'ver_' ;
	const VERSION_START_INT = 1 ;
	const VERSION_START_STRING = '1.0' ;
	
	public $id;
	public $versione_estesa;
	public $versione_int;
	public $update_time;
	public $attivo ;
	public $pr_listini_id;
	
	protected function onInitialize(){
		$this->setAsVendorModel();
		$this->belongsTo('pr_listini_id',__NAMESPACE__.'\Listini','id',array(
				'alias' => 'genitore',
				'reusable' => true
		));
	}
	
	public function setAsFirstVersion(){
		$this->versione_int = self::VERSION_START_INT ;
		$this->versione_estesa = self::VERSION_START_STRING ;
		$this->attivo = 1 ;
	}
	public function beforeValidationOnCreate(){
		$this->update_time = date("Y-m-d H:i:s");
	}
	
	public function beforeValidationOnUpdate(){
		$this->update_time = date("Y-m-d H:i:s");
	}
}