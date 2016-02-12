<?php

namespace Thunderhawk\API\Assets;

use Phalcon\Assets\Manager as PhalconAssetsManager;
use Phalcon\Di\InjectionAwareInterface;

class Manager extends PhalconAssetsManager implements InjectionAwareInterface {
	const RESOURCE_JS = 'js' ;
	const RESOURCE_CSS = 'css' ;
	protected $_cssList = array();
	protected $_jsList = array();
	protected $_dependencyInjector;
	protected $_basePath;
	protected $_assetsDir;
	protected $_jsDir;
	protected $_cssDir;
	
	public function setBasePath($basePath){
		$this->_basePath = (string)$basePath;
	}
	public function getBasePath(){
		return $this->_basePath;
	}
	public function setAssetsDir($assetsDir){
		$this->_assetsDir = (string)$assetsDir;
	}
	public function getAssetsDir(){
		return $this->_assetsDir;
	}
	public function setJsDir($jsDir){
		$this->_jsDir = (string)$jsDir;
	}
	public function getJsDir(){
		return $this->_jsDir;
	}
	public function setCssDir($cssDir){
		$this->_cssDir = (string)$cssDir;
	}
	public function getCssDir(){
		return $this->_cssDir;
	}
	public function getFullCssPath(){
		return $this->getBasePath().$this->getAssetsDir().$this->getCssDir();
	}
	public function getFullJsPath(){
		return $this->getBasePath().$this->getAssetsDir().$this->getJsDir();
	}
	public function requireCss($path, $local = true, $filter = true, $attributes = null) {
		if(!isset($this->_cssList[$path])){
			$this->_cssList[$path] = true ;
			return parent::addCss($this->getFullCssPath().$path,$local,$filter,$attributes);
		}
		return $this ;
	}
	public function requireJs($path, $local = true, $filter = true, $attributes = null) {
		if(!isset($this->_jsList[$path])){
			$this->_jsList[$path] = true ;
			return parent::addJs($this->getFullJsPath().$path,$local,$filter,$attributes);
		}
		return $this ;
	}
	public function requireCollection($collectionName,$typeOfResource,array $list,$join = false,$filters = array()){
		switch ($typeOfResource){
			case SELF::RESOURCE_CSS:
				$dir = $this->getCssDir();
				$ext = '.css';
				$uri = $this->getFullCssPath().'collections/';
				$method = 'addCss';
				break;
			case SELF::RESOURCE_JS:
				$dir = $this->getJsDir();
				$ext = '.js';
				$uri = $this->getFullJsPath().'collections/';
				$method = 'addJs';
				break;
			default:
				$dir = '' ;
				$ext = '' ;
				$uri = '' ;
		}
		$path = APP_PATH.'public/'.$this->getAssetsDir().$dir.'collections/' ;
		if(!file_exists($path))mkdir($path);
		$file = $collectionName.$ext ;
		$collection = $this->collection($collectionName);
		if(!file_exists($path.$file)){
			$collection->setTargetPath($path.$file)->setTargetUri($uri.$file);
			foreach ($list as $resource){
				$collection->{$method}(APP_PATH.'public/'.$this->getAssetsDir().$dir.$resource);
			}
			$collection->join($join);
			foreach ($filters as $filter){
				if($filter instanceof \Phalcon\Assets\FilterInterface){
					$collection->addFilter($filter);
				}
			}
		}else{
			$collection->setPrefix($uri)->{$method}($file);
		}
	}
	public function addCss($path, $local = true, $filter = true, $attributes = null) {
		return parent::addCss($this->getFullCssPath().$path,$local,$filter,$attributes);
	}
	public function addJs($path, $local = true, $filter = true, $attributes = null) {
		return parent::addJs($this->getFullJsPath().$path,$local,$filter,$attributes);
	}
	
	public function setDI (\Phalcon\DiInterface $dependencyInjector){
		$this->_dependencyInjector = $dependencyInjector;
	}
	public function getDI(){
		return $this->_dependencyInjector;
	}
}