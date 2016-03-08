<?php
/**
 * Copyright (c) 2016 ivan.maruca@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Skullab\Rest;
use Skullab\Rest\Exception as RestException;
require 'ServerInterface.php';
require 'Exception.php';
class Server implements ServerInterface{
	protected $_getIndex = '_url' ;
	protected $_url;
	protected $_compiledPattern;
	protected $_controller;
	protected $_controllerSuffix;
	protected $_actionSuffix;
	protected $_action;
	protected $_params;
	protected $_options;
	protected $_response;
	protected $_patterns = array();
	protected $_matches ;
	protected $_route;
	protected $_rules = array(
			"/:controller" => "/([a-zA-Z0-9\\_\\-]+)",
			"/:action" => "/([a-zA-Z0-9\\_\\-]+)",
			"/:params" => "(/.*)*",
			"/:int" => "/([0-9]+)",
	);
	public function setGetIndex($getIndex){
		$this->_getIndex = $getIndex;
	}
	public function getGetIndex(){
		return $this->_getIndex;
	}
	/**
	 * Add a rule for the pattern's compiler.
	 * Pay attention to '/' in the pattern and in the regex.<br>
	 * Example:<br>
	 * 			PATTERN	 =>       REGEX<br>
	 * 		/:controller => /([a-zA-Z0-9\\_\\-]+)
	 * @param string $pattern : the pattern to found
	 * @param regex $regex : the regular expression to resolve.
	 */
	public function addRule($pattern,$regex){
		$this->_rules[$pattern] = $regex ;
	}
	/**
	 * Drop a rule in the rules, by the pattern.
	 * @param string $pattern : the pattern to drop.
	 */
	public function dropRule($pattern){
		if(!isset($this->_rules[$pattern]))return;
		unset($this->_rules[$pattern]);
	}
	/**
	 * Return the rules used in the pattern's compiler.
	 * @return array.
	 */
	public function getRules(){
		return $this->_rules;
	}
	protected function matchPattern($url){
		foreach ($this->_patterns as $route){
			$matches = array();
			$check = preg_match($route['compiled_pattern'], $url,$matches);
			if($check){
				$this->_matches = $matches ;
				$this->_route = $route ;
				return true;
			}
		}
		return false ;
	}
	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::handle()
	 */
	public function handle($url = null) {
		$this->_url = $url ? $url : $_GET[$this->_getIndex];
		if(trim($this->_url) == '')return;
		if(!$this->matchPattern($this->_url)){
			throw new RestException('No route found');
		}
		$activeController = $this->getController().$this->getControllerSuffix();
		if(!$activeController){
			throw new RestException('Malformed Controller');
		}
		$activeAction = $this->getAction().$this->getActionSuffix();
		if(!$activeAction){
			throw new RestException('Malformed Action');
		}
		$activeParams = $this->getParams();
		// TODO FIRE EVENT
		if(!class_exists($activeController)){
			throw new RestException('No Controller found');
		}
		$handler = new $activeController();
		// TODO FIRE EVENT
		if(!method_exists($handler, $activeAction)){
			throw new RestException('No Action found');
		}
		$this->_response = call_user_func_array(array($handler,$activeAction), $this->getParams());
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::sendResponse()
	 */
	public function sendResponse() {
		if($this->_options){
			if(isset($this->_options['returnType'])){
				switch ($this->_options['returnType']){
					case 'json' :
						$this->_response = json_encode($this->_response);
					break;
				}
			}
		}
		if(is_string($this->_response)){
			echo $this->_response ;
		}else{
			return $this->_response;
		}
	}
	public function camelize($value,$firstUpper = true){
		$value = str_replace(' ','',ucwords(strtolower(str_replace('-',' ', $value))));
		if(!$firstUpper){
			$value = lcfirst($value);
		}
		return $value ;
	}
	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getController()
	 */
	public function getController() {
		if(!$this->_route)return null;
		if(!isset($this->_route['handler']['controller']))return null;
		if(is_int($this->_route['handler']['controller'])){
			$controller = $this->_matches[$this->_route['handler']['controller']];
		}else{
			$controller = $this->_route['handler']['controller'];
		}
		return $this->camelize($controller);
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getAction()
	 */
	public function getAction() {
		if(!$this->_route)return null;
		if(!isset($this->_route['handler']['action']))return null;
		if(is_int($this->_route['handler']['action'])){
			$action = $this->_matches[$this->_route['handler']['action']];
		}else{
			$action = $this->_route['handler']['action'];
		}
		return $this->camelize($action,false);
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getParams()
	 */
	public function getParams() {
		if(!$this->_route)return null;
		if(!isset($this->_route['handler']['params']))return array();
		if(is_int($this->_route['handler']['params'])){
			$pattern = $this->_matches[$this->_route['handler']['params']];
		}else{
			$pattern = $this->_route['handler']['params'];
		}
		return explode('/',ltrim($pattern,'/'));
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getOptions()
	 */
	public function getOptions() {
		if(isset($_REQUEST['options'])){
			$this->_options = json_decode($_REQUEST['options']);
			
		}
		return $this->_options;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getRequestMethod()
	 */
	public function getRequestMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::addPattern()
	 */
	public function addPattern($pattern, array $handler) {
		$this->_patterns[] = array(
				'pattern' => $pattern,
				'compiled_pattern' => $this->compilePattern($pattern),
				'handler' => $handler
		);
	}
	/**
	 * Return all the patterns.
	 * @return array
	 */
	public function getPatterns(){
		return $this->_patterns;
	}
	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::compilePattern()
	 */
	public function compilePattern($pattern) {
		foreach ($this->_rules as $rule => $regex){
			$pattern = str_replace($rule, $regex, $pattern);
		}
		$pattern = '/^'.str_replace('/', '\/', $pattern).'$/' ;
		return $pattern ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::setControllerSuffix()
	 */
	public function setControllerSuffix($suffix) {
		$this->_controllerSuffix = $suffix;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::setActionSuffix()
	 */
	public function setActionSuffix($suffix) {
		$this->_actionSuffix = $suffix;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getControllerSuffix()
	 */
	public function getControllerSuffix() {
		return $this->_controllerSuffix;
	}

	/**
	 * {@inheritDoc}
	 * @see \Skullab\Rest\ServerInterface::getActionSuffix()
	 */
	public function getActionSuffix() {
		return $this->_actionSuffix;
	}

}