<?php

namespace Thunderhawk\API\Mvc;
use Phalcon\Mvc\Model as PhalconModel;
use Thunderhawk\API\Di\Service\Manager as ServiceManager;
class Model extends PhalconModel{
	protected $_globalConfig ;
	public function initialize(){
		$this->_globalConfig = $this->getDI()->get(ServiceManager::CONFIG);
		$this->setSource($this->_globalConfig->db->table->prefix.$this->getSource());
		$this->onInitialize();
	}
	protected function switchToRemoteConnection(){
		$this->setConnectionService(ServiceManager::REMOTE_DB);
		$this->setSource($this->_globalConfig->remotedb->table->prefix.$this->getSource());
	}
	protected function onInitialize(){}
	public function beforeSave(){}
	public function save($data = null, $whiteList = null){
		if($this->beforeSave() === false)return false;
		return parent::save($data,$whiteList);
	}
}