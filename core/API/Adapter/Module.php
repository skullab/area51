<?php

namespace Thunderhawk\API\Adapter;

use Phalcon\Mvc\ModuleDefinitionInterface;
use Thunderhawk\API\Engine;

abstract class Module implements ModuleDefinitionInterface {
	protected $_engine;
	protected $_namespace;
	public function __construct() {
		
		$ref = new \ReflectionClass($this);
		$this->_namespace = $ref->getNamespaceName();
		
		$this->_engine = Engine::getInstance();
	}
	public function getNamespaceName(){
		//$ns = $this->_engine->router->getNamespaceName();
		//return str_replace('\\'.basename($ns),'', $ns);
		return $this->_namespace;
	}
	public function getModuleName(){
		return $this->_engine->router->getModuleName();
	}
	public function getModulePath(){
		return($this->_engine->config->modules->installed->{$this->getModuleName()});
	}
	public function getControllerName(){
		return $this->_engine->router->getControllerName();
	}
	public function getActionName(){
		return $this->_engine->router->getActionName();
	}
	public function getTheme(){
		return $this->_engine->config->app->theme ;
	}
	public function getManifest(){
		return $this->_engine->manifestManager->getManifest($this->getModuleName());
	}
	public function getModuleDir(){
		return basename($this->getModulePath());
	}
	public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = null) {
		$loader = $dependencyInjector->get ( 'loader' );
		$loader->registerNamespaces ( array (
				$this->getNamespaceName() . '\Controllers' => $this->getModulePath() . 'controllers/',
				$this->getNamespaceName() . '\Models' => $this->getModulePath() . 'models/' 
		), true );
	}
	public function registerServices(\Phalcon\DiInterface $dependencyInjector = null) {
		
		$dispatcher = $dependencyInjector->get('dispatcher');
		$dispatcher->setDefaultNamespace($this->getNamespaceName().'\Controllers');
		
		$view = $dependencyInjector->get('view');
		$engines = array();
		foreach ( $this->getManifest()->template->engine as $engine){
			$engines['.'.$engine['extension']] = 'Phalcon\Mvc\View\Engine\\'.ucfirst(trim((string)$engine)) ;
		}
		$view->registerEngines($engines);
		$view->setViewsDir('themes/'.$this->getTheme()->name.'/'.$this->getModuleName().'/');
		$view->setPartialsDir('../'.$this->getTheme()->partials.'/');
		$view->setLayoutsDir('../'.$this->getTheme()->layouts.'/');
		
	}
}