<?php

namespace Thunderhawk\API\Component;
use Thunderhawk\API\Component\Settings\BaseModel;
use Thunderhawk\API\Di\Service\Manager as ServiceManager;
class Settings extends BaseModel {
	protected $id;
	protected $namespace;
	protected $controller;
	
	public function initialize(){
		$this->setSource($this->getDI()->get(ServiceManager::CONFIG)->app->settings->controller);
	}
	public function onConstruct() {
		$this->setNamespace ( $this->getDI () ['dispatcher']->getNamespaceName () );
		$this->setController ( $this->getDI () ['dispatcher']->getControllerName () );
	}
	
	public function getId(){
		return $this->id ;
	}
	public function setNamespace($namespace) {
		$this->namespace = $namespace ? $namespace : '';
	}
	public function getNamespace(){
		return $this->namespace ;
	}
	public function setController($controller) {
		$this->controller = $controller;
	}
	public function getController(){
		return $this->controller;
	}
	
}