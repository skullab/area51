<?php

namespace Thunderhawk\API\Manifest;

use Phalcon\Di\InjectionAwareInterface;
use Thunderhawk\API\Manifest;

class Manager implements ManagerInterface, InjectionAwareInterface {
	protected $_dependencyInjector;
	protected $_manifests = array ();
	public function __construct(\Phalcon\DiInterface $dependencyInjector = null) {
		if ($dependencyInjector != null)
			$this->setDI ( $dependencyInjector );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Manifest\ManagerInterface::load()
	 */
	public function load($moduleName) {
		if (! $this->isLoaded ( $moduleName )) {
			$filename = $this->getDI()->get('config')->modules->installed->{$moduleName}.'Manifest.xml';
			if (file_exists ( $filename )) {
				$manifest= simplexml_load_file ( $filename,'Thunderhawk\API\Manifest');
				$this->_manifests [$moduleName] = $manifest;
			} else {
				throw new Exception ( $filename, Exception::CODE_MISSING );
			}
		}
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Manifest\ManagerInterface::isLoaded()
	 */
	public function isLoaded($moduleName) {
		return isset ( $this->_manifests [$moduleName] );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Manifest\ManagerInterface::getManifest()
	 */
	public function getManifest($moduleName) {
		return $this->_manifests [$moduleName];
	}
	public function setDI(\Phalcon\DiInterface $dependencyInjector) {
		$this->_dependencyInjector = $dependencyInjector;
	}
	public function getDI() {
		return $this->_dependencyInjector;
	}
}