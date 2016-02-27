<?php

namespace Thunderhawk\API;

use Phalcon\Mvc\Router\Route;

/**
 * Manifest class
 *
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *        
 */
class Manifest extends \SimpleXMLElement {
	const TAG_MODULE = 'Module';
	const TAG_APPLICATION = 'Application';
	const TAG_NAMESPACE = 'namespace';
	const TAG_NAME = 'name';
	const TAG_ROUTING = 'routing';
	const TAG_ROUTE = 'route';
	const TAG_VERSION = 'version';
	const TAG_RELEASE = 'release';
	const TAG_MAJOR = 'major';
	const TAG_MINOR = 'minor';
	const TAG_CODE = 'code';
	const TAG_PERMISSIONS = 'permissions';
	const TAG_PERMISSION = 'permission';
	const TAG_GROUP = 'group';
	const TAG_RULE = 'rule';
	const TAG_REQUIRED = 'required';
	const TAG_REQUIRE = 'require';
	const TAG_REQUIRED_MODULE = 'module';
	const TAG_PLUGIN = 'plugin';
	const TAG_TEMPLATE = 'template';
	const TAG_ENGINE = 'engine';
	const TAG_INFO = 'info';
	const TAG_AUTHOR = 'author';
	const ATTRIBUTE_MODULE = 'module';
	const ATTRIBUTE_PLUGIN = 'plugin';
	const ATTRIBUTE_COMPONENT = 'component';
	const ATTRIBUTE_CONTROLLER = 'controller';
	const ATTRIBUTE_ACTION = 'action';
	const ATTRIBUTE_PARAMS = 'params';
	const ATTRIBUTE_NAME = 'name';
	const ATTRIBUTE_NAMESPACE = 'namespace';
	const ATTRIBUTE_HTTP_METHODS = 'http-methods';
	const VALUE_INDEX = 'index';
	const VALUE_GET = 'GET';
	const VALUE_POST = 'POST';
	const VALUE_PUT = 'PUT';
	const VALUE_PATCH = 'PATCH';
	const VALUE_DELETE = 'DELETE';
	const VALUE_OPTIONS = 'OPTIONS';
	const VALUE_HEAD = 'HEAD';
	
	/**
	 * Validates the manifest based on a schema
	 *
	 * @return bool true on success or false on failure
	 */
	public function validate() {
		$document = new \DOMDocument ();
		$document->loadXML ( $this->_xmlObject->asXML () );
		$filename = 'path/to/schema.xsd';
		return $document->schemaValidate ( $filename );
	}
	
	/**
	 * Get a specific tag element content
	 *
	 * @param string $tag
	 *        	: The tag name
	 */
	public function getTag($tag) {
		return ( string ) $this->$tag;
	}
	/**
	 * Return the module name
	 *
	 * @return string : the module name
	 */
	public function getModuleName() {
		return trim($this->getTag ( self::TAG_NAME ));
	}
	/**
	 * Return the module namespace name
	 *
	 * @return string : the namespace name
	 */
	public function getModuleNamespace() {
		return trim($this->getTag ( self::TAG_NAMESPACE ));
	}
	/**
	 * Return the full module version
	 *
	 * @return string : the full module version
	 */
	public function getVersion() {
		$version = ( string ) ($this->version->release . '.' . $this->version->major . '.' . $this->version->minor);
		return trim($version);
	}
	public function getAuthor(){
		return trim((string)$this->{self::TAG_INFO}->{self::TAG_AUTHOR});
	}
	/**
	 * Return a specific part of the module version<br>
	 *
	 * @see Manifest::TAG_RELEASE
	 * @see Manifest::TAG_MAJOR
	 * @see Manifest::TAG_MINOR
	 * @param unknown $part        	
	 */
	public function getVersionInt($part) {
		return ( int ) $this->version->{$part};
	}
	public function hasRouting() {
		return isset ( $this->routing );
	}
	public function hasPermissions() {
		return isset ( $this->permissions );
	}
	public function hasRequired() {
		return isset ( $this->required );
	}
	public function hasTemplate() {
		return isset ( $this->template );
	}
	public function isModule() {
		return $this->getName () === self::TAG_MODULE;
	}
	public function isApplication() {
		return $this->getName () === self::TAG_APPLICATION;
	}
	public function getRoutes() {
		return $this->hasRouting () ? $this->_getRoutes () : null;
	}
	protected function _getRoutes() {
		$routes = array ();
		foreach ( $this->routing->route as $route ) {
			
			$paths = array (
					self::ATTRIBUTE_NAMESPACE => $this->getModuleNamespace () . '\Controllers',
					self::ATTRIBUTE_MODULE => $this->getModuleName (),
					self::ATTRIBUTE_CONTROLLER => self::VALUE_INDEX,
					self::ATTRIBUTE_ACTION => self::VALUE_INDEX 
			);
			$pattern = '/' . trim ( ( string ) $route );
			foreach ( $route->attributes () as $attribute => $value ) {
				$value = ( string ) $value;
				if ($attribute == self::ATTRIBUTE_HTTP_METHODS){
					$value = explode ( ',', $value );
				}
				$paths [( string ) $attribute] = is_numeric ( $value ) ? ( int ) $value : $value;
			}
			if ($paths [self::ATTRIBUTE_MODULE] != $this->getModuleName ()) {
				unset ( $paths [self::ATTRIBUTE_NAMESPACE] );
			}
			$routes [] = array('pattern' => $pattern , 'paths' => $paths);
		}
		return $routes;
	}
	public function getPermissions() {
		return $this->hasPermissions () ? $this->_getPermissions () : null;
	}
	protected function _getPermissions() {
		$permissions = array (
				'permissions' => array (),
				'groups' => array () 
		);
		
		foreach ( $this->permissions->group as $group ) {
			$permissions ['group'] [] = ( string ) $group;
		}
		
		foreach ( $this->permissions->permission as $permission ) {
			$permissions ['permissions'] [] = ( string ) $permission;
		}
		
		return $permissions;
	}
	public function getRequired() {
		return $this->hasRequired () ? $this->_getRequired () : null;
	}
	protected function _getRequired() {
		$required = array ();
		
		foreach ( $this->required->require as $require ) {
			$directives = array ();
			foreach ( $require->attributes () as $attribute => $value ) {
				$directives [( string ) $attribute] = ( string ) $value;
			}
			$required [] = $directives;
		}
		return $required;
	}
	public function getTemplateEngines() {
		return $this->hasTemplate () ? $this->_getTemplateEngines () : null;
	}
	protected function _getTemplateEngines() {
		$engines = array ();
		foreach ( $this->template->engine as $engine ) {
			$directives = array (
					'engine' => ( string ) $engine 
			);
			foreach ( $engine->attributes () as $attribute => $value ) {
				$directives [( string ) $attribute] = ( string ) $value;
			}
			$engines [] = $directives;
		}
		return $engines;
	}
	/**
	 * <b>This function is disabled</b><br>
	 *
	 * {@inheritDoc}
	 *
	 * @see SimpleXMLElement::addAttribute()
	 */
	public function addAttribute($name, $value = null, $namespace = null) { /* do nothing */
	}
	/**
	 * <b>This function is disabled</b><br>
	 *
	 * {@inheritDoc}
	 *
	 * @see SimpleXMLElement::addChild()
	 */
	public function addChild($name, $value = null, $namespace = null) { /* do nothing */
	}
}