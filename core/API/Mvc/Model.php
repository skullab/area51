<?php

namespace Thunderhawk\API\Mvc;
use Phalcon\Mvc\Model as PhalconModel;
use Thunderhawk\API\Di\Service\Manager as ServiceManager;
class Model extends PhalconModel{
	protected $_globalConfig ;
	protected $_tableName ;
	public function initialize(){
		$this->_globalConfig = $this->getDI()->get(ServiceManager::CONFIG);
		$this->_tableName = $this->getSource();
		$this->setAsFrameworkModel();
		$this->onInitialize();
	}
	protected function setAsVendorModel(){
		$this->setSource($this->_globalConfig->vendor->table->prefix.$this->_tableName);
	}
	protected function setAsFrameworkModel(){
		$this->setSource($this->_globalConfig->db->table->prefix.$this->_tableName);
	}
	protected function switchToRemoteConnection(){
		$this->setConnectionService(ServiceManager::REMOTE_DB);
		$this->setSource($this->_globalConfig->remotedb->table->prefix.$this->getSource());
	}
	protected function onInitialize(){}
	/*public function beforeSave(){}
	public function save($data = null, $whiteList = null){
		if($this->beforeSave() === false)return false;
		return parent::save($data,$whiteList);
	}*/
}