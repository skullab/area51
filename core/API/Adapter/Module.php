<?php

namespace Thunderhawk\API\Adapter;

use Phalcon\Mvc\ModuleDefinitionInterface;
use Thunderhawk\API\Engine;

abstract class Module implements ModuleDefinitionInterface {
	protected $_engine;
	
	public function __construct() {
		$this->_engine = Engine::getInstance();
	}
	public function getNamespaceName(){
		$ns = $this->_engine->router->getNamespaceName();
		return str_replace('\\'.basename($ns),'', $ns);
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
		$view = $dependencyInjector->get('view');
		$engines = array();
		foreach ( $this->getManifest()->template->engine as $engine){
			$engines['.'.$engine['extension']] = 'Phalcon\Mvc\View\Engine\\'.ucfirst(trim((string)$engine)) ;
		}
		$view->registerEngines($engines);
		$view->setViewsDir('themes/'.$this->getTheme()->name.'/'.$this->getModuleName().'/');
		/*foreach ($engines as $ext => $class){
			if(file_exists(APP_PATH.'core/themes/'.
					$this->getTheme()->name.'/'.
					$this->getTheme()->layouts.'/'.
					$this->getControllerName().$ext)){
				$view->setTemplateAfter('../../../../themes/'.
						$this->getTheme()->name.'/'.
						$this->getTheme()->layouts.'/'.
						$this->getControllerName());
				break;
			}
		}*/
		
	}
}