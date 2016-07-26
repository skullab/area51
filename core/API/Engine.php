<?php

namespace Thunderhawk\API;

use Phalcon\Mvc\Application;
use Phalcon\Loader;
use Phalcon\Mvc\View\Engine\Volt\Compiler as VoltCompiler;
use Thunderhawk\API\Engine\EngineInterface;
use Thunderhawk\API\Di\FactoryDefault;
use Thunderhawk\API\Engine\Listener as EngineListener;
use Thunderhawk\API\Dispatcher\Listener as DispatcherListener;
use Thunderhawk;
// REQUIRE SECTION //
require 'Engine/constants.php';
require 'Engine/functions.php';
require 'Engine/EngineInterface.php';
require __DIR__.'../../autoload.php';
/**
 * Thunderhawk Engine class
 *
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *        
 */
final class Engine extends Application implements EngineInterface{
	const VERSION = '1.0.0';
	private static $_instance = null;
	private static $_alreadyInit = false;
	protected $_debug ;
	/**
	 * Construct an application engine.<br>
	 * Use Engine::getInstance to get the engine in the application.<br>
	 *
	 * @see Engine::getInstance
	 * @param \Phalcon\DiInterface $di
	 *        	[optional]: The dependency injector.<br>
	 *        	If the $di is not set, a default $di will be created.
	 */
	public function __construct(\Phalcon\DiInterface $di = null) {
		if (! self::$_alreadyInit) {
			self::$_alreadyInit = true;
			self::$_instance = $this;
			$loader = new Loader ();
			$loader->registerNamespaces ( array (
					'Thunderhawk\API' => API_PATH
			) )->register ();
			if ($di == null) {
				$di = new FactoryDefault ();
			}
			$di->set ( 'loader', $loader, true );
			$this->_registerServices ( $di );
			$this->_registerListeners ();
			$this->_registerModules ();
		} else {
			throw new Engine\Exception(null,10);
		}
	}
	/**
	 * Get an instance of the Engine
	 *
	 * @param \Phalcon\DiInterface $di        	
	 */
	public static function getInstance(\Phalcon\DiInterface $di = null) {
		if (self::$_instance == null) {
			self::$_instance = new Engine ( $di );
		}
		return self::$_instance;
	}
	public function getRequestTime(){
		return (microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']);
	}
	/**
	 * Run the engine
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::run()
	 */
	public function run() {
		//try{
			echo $this->handle ()->getContent ();
		//}catch(\Exception $e){
			//echo $e->getMessage();
		//}
	}
	public function enableDebug($enable){
		if($enable){
			$this->_debug = new \Phalcon\Debug();
			$this->_debug->listen();
		}else{
			$this->_debug = null ;
		}
	}
	/**
	 * Get a service in the DependencyInjector container
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::getService()
	 */
	public function getService($name) {
		return $this->getDI ()->get ( $name );
	}
	public function isService($name) {
		return $this->getDI ()->has ( $name );
	}
	
	public function getVoltCompiler(){
		$volt = new VoltCompiler();
		$volt->setOptions(array(
				'stat' => true,
				'compileAlways' => true
		));
		require CORE_PATH.'config/volt.functions.php';
		foreach ($voltFunctions as $macro => $function){
			$volt->addFunction($macro,$function);
		}
		return $volt ;
	}
	/**
	 * Check if a module is registered
	 *
	 * @param string $moduleName
	 *        	: the name of the module to check
	 * @return bool
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::isResisteredModule()
	 */
	public function isRegisteredModule($moduleName) {
		return isset ( $this->getModules () [$moduleName] );
	}
	public function isInstalledModule($moduleName) {
		return property_exists ( $this->config->modules->installed, $moduleName );
	}
	/**
	 * Return the module definition registered in the application
	 *
	 * @param string $moduleName
	 *        	: the name of the module
	 * @return array
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::getModuleDefinition()
	 */
	public function getModuleDefinition($moduleName) {
		return $this->getModules () [$moduleName];
	}
	
	/**
	 * Return the Db prefix if set in the configuration file
	 *
	 * @return string
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::getDbPrefix()
	 */
	public function getDbPrefix() {
		return $this->config->db->table->prefix;
	}
	public function getDbName() {
		return $this->config->db->dbname;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::getBaseUri()
	 */
	public function getBaseUri() {
		return $this->url->getBaseUri ();
	}
	
	public function getStaticBaseUri(){
		return $this->url->getStaticBaseUri();
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::getConfig()
	 */
	public function getConfig() {
		return $this->config;
	}
	public function getVersion(){
		return self::VERSION ;
	}
	public function runBootstrap($filename){
		if(file_exists(CORE_PATH.'boostrap/'.$filename)){
			require CORE_PATH.'boostrap/'.$filename ;
		}
		return $this->run();
	}
	// *********************************************************//
	protected function _registerServices($di) {
		if($di instanceof Thunderhawk\API\Di\FactoryDefault){
			$di->initializeServices();
		}
		$this->setDI ( $di );
	}
	protected function _registerListeners() {
		$this->eventsManager->attach ( 'application', new EngineListener () );
		$this->eventsManager->attach ( 'dispatch', new DispatcherListener () );
		$this->setEventsManager ( $this->eventsManager );
		$this->dispatcher->setEventsManager ( $this->eventsManager );
	}
	protected function _registerModules() {
		$modules = array ();
		foreach ( ( array ) $this->config->modules->installed as $moduleName => $modulePath ) {
			$this->registeredModule = $moduleName;
			if (! file_exists ( $modulePath . 'Module.php' )) {
				if ($this->eventsManager->fire ( 'application:moduleNotFound', $this ) !== false)
					throw new Engine\Exception($moduleName,20);
			}
			if (! file_exists ( $modulePath . 'Manifest.xml' )) {
				throw new Engine\Exception($moduleName,30);
			}
			$this->manifestManager->load ( $moduleName );
			$manifest = $this->manifestManager->getManifest ( $moduleName );
			$modules [$moduleName] = array (
					'className' => $manifest->getModuleNamespace () . '\Module',
					'path' => $modulePath . 'Module.php',
					'namespace' => $manifest->getModuleNamespace (),
					'name' => $manifest->getModuleName(),
					'version' => $manifest->getVersion (),
					'author' => $manifest->getAuthor () 
			);
			$routes = $manifest->getRoutes ();
			foreach ( $routes as $route ) {
				$routeName = null;
				$routeVia = null;
				if (isset ( $route ['paths'] ['name'] )) {
					$routeName = $route ['paths'] ['name'];
					unset ( $route ['paths'] ['name'] );
				}
				if (isset ( $route ['paths'] ['http-methods'] )) {
					$routeVia = $route ['paths'] ['http-methods'];
					unset ( $route ['paths'] ['http-methods'] );
				}
				$routeObject = $this->router->add ( $route ['pattern'], $route ['paths'] );
				if ($routeName != null)
					$routeObject->setName ( $routeName );
				if ($routeVia != null)
					$routeObject->via ( $routeVia );
			}
		}
		$this->registerModules ( $modules );
		$this->router->setDefaultModule ( $this->config->modules->default );
	}
}