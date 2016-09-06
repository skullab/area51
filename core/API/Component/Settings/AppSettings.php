<?php

namespace Thunderhawk\API\Component\Settings;
use Thunderhawk\API\Di\Service\Manager as ServiceManager;
class AppSettings extends BaseModel{
	protected $id;
	protected $name;
	
	public function initialize(){
		$this->setSource($this->getDI()->get(ServiceManager::CONFIG)->app->settings->general);
	}
	public function getId(){
		return $this->id ;
	}
	
	public function setName($name){
		$this->name = $name ;
	}
	public function getName(){
		return $this->name ;
	}
}