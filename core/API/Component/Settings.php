<?php

namespace Thunderhawk\API\Component;
use Thunderhawk\API\Component\Settings\BaseModel;
class Settings extends BaseModel implements \ArrayAccess {
	protected $id;
	protected $namespace;
	protected $controller;
	protected $data;
	protected $_dataAccess = array ();
	
	public function onConstruct() {
		$this->setNamespaceName ( $this->getDI () ['dispatcher']->getNamespaceName () );
		$this->setControllerName ( $this->getDI () ['dispatcher']->getControllerName () );
	}
	public function setNamespaceName($namespace) {
		$this->namespace = $namespace ? $namespace : '';
	}
	public function setControllerName($controller) {
		$this->controller = $controller;
	}
}