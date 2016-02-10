<?php

namespace Thunderhawk\API;

use Phalcon\Mvc\Application;
use Phalcon\Loader;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Config as ConfigArray;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Thunderhawk\API\Engine\EngineInterface;
use Thunderhawk\API\Di\FactoryDefault;
use Thunderhawk\API\Engine\Listener as EngineListener;
use Thunderhawk\API\Dispatcher\Listener as DispatcherListener;
use Thunderhawk\API\Manifest\Manager as ManifestManager;
// REQUIRE SECTION //
require 'Engine/constants.php';
require 'Engine/EngineInterface.php';
require 'Throwable.php';
/**
 * Thunderhawk Engine class
 *
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *        
 */
final class Engine extends Application implements EngineInterface, Throwable {
	private static $_instance = null;
	private static $_alreadyInit = false;
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
			self::$_instance = $this ;
			$loader = new Loader ();
			$loader->registerNamespaces ( array (
					'Thunderhawk\API' => '../core/API/' 
			) )->register ();
			if ($di == null) {
				$di = new FactoryDefault ();
			}
			$di->set ( 'loader', $loader, true );
			
			$this->_registerServices ( $di );
			$this->_registerListeners ();
			$this->_registerModules ();
			// $this->setDefaultModule($this->config->modules->default);
		} else {
			$this->throwException ( null, 10 );
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
	/**
	 * Run the engine
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::run()
	 */
	public function run() {
		echo $this->handle ()->getContent ();
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
	public function isResisteredModule($moduleName) {
		// TODO: Auto-generated method stub
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
		// TODO: Auto-generated method stub
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
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Engine\EngineInterface::getBaseUri()
	 */
	public function getBaseUri() {
		return $this->url->getBaseUri ();
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
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Throwable::throwException()
	 */
	public function throwException($message = null, $code = 0, \Exception $previous = null) {
		throw new Engine\Exception ( $message, $code, $previous );
	}
	
	// *********************************************************//
	protected function _registerServices($di) {
		// READ CONFIGIGURATIONS INI
		$di->set ( 'config', function () {
			$app = new ConfigIni ( APP_PATH . 'core/config/app.ini.php' );
			$dirs = new ConfigIni ( APP_PATH . 'core/config/dirs.ini.php' );
			$db = new ConfigIni ( APP_PATH . 'core/config/db.ini.php' );
			require (APP_PATH . 'core/config/modules.php');
			$modules = new ConfigArray ( $modulesInstalled );
			$app->merge ( $dirs );
			$app->merge ( $db );
			$app->merge ( $modules );
			return $app;
		}, true );
		$config = $this->config;
		// ROUTER SERVICE
		$di->set ( 'router', function () use($config){
			$router = new Router ( );
			return $router;
		}, true );
		// DISPATCHER SERVICE
		$di->set ( 'dispatcher', function () {
			$dispatcher = new Dispatcher ();
			// $dispatcher->setDefaultNamespace('Thunderhawk\API');
			return $dispatcher;
		}, true );
		// DB CONFIGURATION
		$di->set ( 'db', function () use($config) {
			$dbConfig = ( array ) $config->db;
			$dbAdapter = 'Phalcon\Db\Adapter\PDO\\' . $dbConfig ['adapter'];
			unset ( $dbConfig ['adapter'] );
			unset ( $dbConfig ['table'] );
			$dbConfig ['dbname'] = $dbConfig ['name'];
			unset ( $dbConfig ['name'] );
			$db = new $dbAdapter ( $dbConfig );
			return $db;
		} );
		// URL PROVIDER
		$di->set ( 'url', function () use($config) {
			$url = new UrlProvider ();
			$url->setBaseUri ( $config->app->base->uri );
			$url->setStaticBaseUri('//127.0.0.1'.$config->app->base->uri);
			return $url;
		}, true );
		// VIEW SERVICE
		$di->set ( 'view', function () use($config) {
			$view = new View ();
			$view->setBasePath(APP_PATH .'core/');
			//$view->setMainView('../index');
			//$view->setViewsDir('themes/'.$config->app->theme->name.'/');
			//$view->setLayoutsDir('layouts/');
			//$view->setBasePath(APP_PATH .'core/');
			//$view->setLayoutsDir ( '../../../themes/' . $config->app->theme->name . '/');
			/*$view->setPartialsDir ( '../../../themes/' . 
					$config->app->theme->name . '/' . 
					$config->app->theme->partials . '/');*/
			//$view->setTemplateAfter( $config->app->theme->main );
			/*$view->setMainView('../../../themes/' . 
					$config->app->theme->name . '/' .
					$config->app->theme->main);*/
			return $view;
		}, true );
		$di->set ( 'manifestManager', function () use($di) {
			$manifestManager = new ManifestManager ( $di );
			return $manifestManager;
		}, true );
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
					$this->throwException ( $moduleName, 20 );
			}
			if (! file_exists ( $modulePath . 'Manifest.xml' )) {
				$this->throwException ( $moduleName, 30 );
			}
			$this->manifestManager->load ( $moduleName );
			$manifest = $this->manifestManager->getManifest ( $moduleName );
			$modules [$moduleName] = array (
					'className' => $manifest->getModuleNamespace () . '\Module',
					'path' => $modulePath . 'Module.php',
					'namespace' => $manifest->getModuleNamespace(),
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
		$defaultNamespace = $this->getModules()[$this->config->modules->default]['namespace'];
		$this->router->setDefaultNamespace($defaultNamespace.'\Controllers');
	}
}