<?php

namespace Thunderhawk\API\Mvc;
use Phalcon\Mvc\Model as PhalconModel;
class Model extends PhalconModel{
	public function initialize(){
		$config = $this->getDI()->get('config');
		$this->setSource($config->db->table->prefix.$this->getSource());
		$this->onInitialize();
	}
	protected function onInitialize(){}
}