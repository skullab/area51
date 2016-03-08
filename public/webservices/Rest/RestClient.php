<?php
require __DIR__.'\RestServer.php';
class RestClient {
	protected $_baseUri ;
	protected $_exceptions = array();
	
	public function __construct($baseUri = ''){
		$this->setBaseUri($baseUri);
	}
	public function setBaseUri($baseUri){
		$this->_baseUri = $baseUri;
	}
	public function getBaseUri(){
		return $this->_baseUri;
	}
	public function getServerMessages($route = '/errors'){
		return $this->request($route);
	}
	public function getMessages(){
		$messages = array();
		foreach ($this->_exceptions as $e){
			$messages[] = $e->getMessage();
		}
		return $messages ;
	}
	public function request($route,$include_path = false, $context = null){
		try{
			return file_get_contents($this->_baseUri.$route,$include_path,$context);
		}catch (Exception $e){
			$this->_exceptions[] = $e ;
			return false ;
		}
	}
	public function createContext($method,$header = null,array $content = array()){
		$context = array('http'=>array(
				'method' => $method
		));
		switch ($method){
			case RestServer::METHOD_GET:
				/*$context['http']['header'] = 'Accept: text/html' ;
				$context['http']['content'] = http_build_query($content);*/
				break;
			case RestServer::METHOD_POST:
				$context['http']['header'] = $header ? $header : 'Content-type: application/x-www-form-urlencoded' ;
				$context['http']['content'] = http_build_query($content);
				break;
			case RestServer::METHOD_PUT:
				//$context['http']['content'] = http_build_query($content);
				break;
			case RestServer::METHOD_DELETE:
				break;
		}
		return stream_context_create($context);
	}
	public function get($route,array $content = array()){
		foreach ($content as $key => $value){
			$route .= "/$value" ;
		}
		return $this->request($route,false,$this->createContext(RestServer::METHOD_GET));
	}
	public function post($route,array $content = array()){
		return $this->request($route,false,$this->createContext(RestServer::METHOD_POST,null,$content));
	}
	public function put($route,array $content = array()){
		return $this->request($route,false,$this->createContext(RestServer::METHOD_PUT,null,$content));
	}
}