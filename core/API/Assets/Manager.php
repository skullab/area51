<?php

namespace Thunderhawk\API\Assets;

use Phalcon\Assets\Manager as PhalconAssetsManager;
use Phalcon\Di\InjectionAwareInterface;

class Manager extends PhalconAssetsManager implements InjectionAwareInterface {
	
	protected $_cssList = array();
	protected $_jsList = array();
	protected $_dependencyInjector;
	protected $_basePath;
	public function setBasePath($path){
		$this->_basePath = (string)$path;
	}
	public function getBasePath(){
		return $this->_basePath;
	}
	public function requireCss($path, $local = true, $filter = true, $attributes = null) {
		if(!isset($this->_cssList[$path])){
			$this->_cssList[$path] = true ;
			return parent::addCss($this->_basePath.'assets/css/'.$path,$local,$filter,$attributes);
		}
		return $this ;
	}
	public function requireJs($path, $local = true, $filter = true, $attributes = null) {
		if(!isset($this->_jsList[$path])){
			$this->_jsList[$path] = true ;
			return parent::addJs($this->_basePath.'assets/js/'.$path,$local,$filter,$attributes);
		}
		return $this ;
	}
	public function setDI (\Phalcon\DiInterface $dependencyInjector){
		$this->_dependencyInjector = $dependencyInjector;
	}
	public function getDI(){
		return $this->_dependencyInjector;
	}
}