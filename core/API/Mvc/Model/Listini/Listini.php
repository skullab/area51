<?php

namespace Thunderhawk\API\Mvc\Model\Listini;
use Thunderhawk\API\Mvc\Model;
class Listini extends Model{
	
	public $id;
	public $nome;
	public $update_time;
	public $attivo;
	
	protected function onInitialize(){
		$this->setAsVendorModel();
	}
	
	public function isActive(){
		return $this->attivo == 1 ? true : false ;
	}
	
	public function beforeValidationOnCreate(){
		$this->update_time = date("Y-m-d H:i:s");
	}
	public function beforeValidationOnUpdate(){
		$this->update_time = date("Y-m-d H:i:s");
	}
	public function getLastVersion(){
		$version = ListiniVersioni::maximum(array(
				'column' => 'versione_int',
				'conditions' => 'pr_listini_id = '.$this->id  
		));
		return $version ;
	}
	
	public function getLastVersionModel(){
		$version = ListiniVersioni::findFirst(array(
				'pr_listini_id = ?0 AND versione_int = ?1',
				'bind' => array($this->id,$this->getLastVersion())
		));
		return $version ;
	}
}