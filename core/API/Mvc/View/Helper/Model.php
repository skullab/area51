<?php

namespace Thunderhawk\API\Mvc\View\Helper;
class Model {
	protected $_namespaces ;
	public function __construct(){
		$dirs = scandir(API_PATH.'Mvc/Model');
		foreach ($dirs as $dir){
			if($dir != '.' && $dir != '..'){
				$this->_namespaces[] = $dir ;
			}
		}
	}
	public function __get($name){
		$name = \Phalcon\Text::camelize($name);
		foreach ($this->_namespaces as $dir){
			if(class_exists("Thunderhawk\API\Mvc\Model\\$dir\\$name")){
				$class = "Thunderhawk\API\Mvc\Model\\$dir\\$name" ;
				return new $class();
			}
		}
		
	}
}