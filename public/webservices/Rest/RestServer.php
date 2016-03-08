<?php
class RestServer {
	//
	const METHOD_POST = 'POST' ;
	const METHOD_GET = 'GET';
	const METHOD_PUT = 'PUT' ;
	const METHOD_DELETE = 'DELETE';
	const RET_TYPE_JSON = 'json';
	const RET_TYPE_RAW = 'raw';
	//
	protected $_baseUri ;
	protected $_uri ;
	protected $_routes = array();
	protected $_returnType = self::RET_TYPE_RAW ;
	protected $_rules = array(
			"/:controller" => "/([a-zA-Z0-9\\_\\-]+)",
			"/:action" => "/([a-zA-Z0-9\\_\\-]+)",
			"/:params" => "(/.*)*",
			"/:int" => "/([0-9]+)",
	);
	protected $_defaultHandler = array(
			'params' => array(),
			'method' => self::METHOD_GET,
			'returnType' => null,
	);
	protected $_macthes;
	protected $_activePattern ;
	protected $_activeRoute ;
	protected $_exceptions = array();
	protected $_persistant ;
	protected static $_persistantKey = '$REST$SERVER$PERSISTANT' ;
	
	public function __construct($persistant = false,$default = true){
		$this->persistantHandler($persistant);
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		if($default){
			$instance->addRoute('/:controller/:action/:params',array(
					'controller' => 1,
					'action' => 2,
					'params' => 3
			));
			/*$instance->addRoute('/:controller/:action',array(
					'controller' => 1,
					'action' => 2
			));
			$instance->addRoute('/:controller',array(
					'controller' => 1
			));*/
			$instance->addRoute('/errors',array(
					'controller' => $this->isPersistant() ? $this->getPersistant() : $this,
					'action' => 'getMessages'
			));
		}
	}
	public static function isPersistant(){
		return (isset($_SESSION[self::$_persistantKey]) && $_SESSION[self::$_persistantKey] != null);
	}
	public function persistantHandler($persistant){
		if($persistant){
			$_SESSION[self::$_persistantKey] = $this ;
		}
	}
	public function getPersistant(){
		return $_SESSION[self::$_persistantKey] ;
	}
	public function saveException($e){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		$instance->_exceptions[] = $e ;
	}
	public function getMessages(){
		$messages = array();
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		foreach ($instance->_exceptions as $e){
			$messages[] = $e->getMessage();
		}
		return $instance->returnJson($messages);
	}
	public function addRule($rule,$pattern){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		$instance->_rules[$rule] = $pattern ;
	}
	public function dropRule($rule){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		if(isset($instance->_rules[$rule])){
			unset($instance->_rules[$rule]);
		}
	}
	public function getRule($rule){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->_rules[$rule] ;
	}
	//
	public function setReturnType($type){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		$instance->_returnType = $type ;
	}
	public function getReturnType(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->_returnType ;
	}
	public function compileRoute($route){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		foreach ($instance->_rules as $rule => $pattern){
			$route = str_replace($rule, $pattern, $route);
		}
		$route = str_replace('/', '\/', $route);
		return '/^'.$route.'$/';
	}
	public function addRoute($route,array $handler){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		$route = $instance->compileRoute($route);
		$instance->_routes[$route] = array_merge($instance->_defaultHandler,$handler);
	}
	public function getRoutes(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->_routes ;
	}
	public function resolveRoute(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		foreach ($instance->_routes as $pattern => $route){
			$match = preg_match($pattern, $instance->_uri,$instance->_macthes) ;
			if($match){
				$instance->_activePattern = $pattern ;
				$instance->_activeRoute = $route ;
				return true ;
			}
		}
		return null ;
	}
	public function getActivePattern(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->_activePattern ;
	}
	public function getActiveRoute(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->_activeRoute ;
	}
	public function getActiveHandler($type){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		$route = $instance->getActiveRoute();
		if($route)return @$route[$type];
		return null ;
	}
	public function getController(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->getActiveHandler('controller');
	}
	public function getAction(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->getActiveHandler('action');
	}
	public function getParams(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->getActiveHandler('params');
	}
	public function getMethod(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->getActiveHandler('method');
	}
	public function getControllerReturnType(){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		return $instance->getActiveHandler('returnType');
	}
	public function matchMethod($method){
		return $_SERVER['REQUEST_METHOD'] == $method ;
	}
	public function returnJson($response){
		//header('Content-Type: application/json');
		echo json_encode($response);
	}
	public function returnRaw($response){
		echo $response ;
	}
	public function handle($uri = null){
		$instance = $this->isPersistant() ? $this->getPersistant() : $this ;
		$instance->_uri = $uri ? $uri : $_GET['_url'] ;
		$requestMethod = $_SERVER['REQUEST_METHOD'] ;
		$method = $instance->getMethod();
		if($method != null && !$instance->matchMethod($requestMethod)){
			throw new Exception('BAD METHOD');
		}
		if(!$instance->resolveRoute()){
			throw new Exception('BAD ROUTE');
		}
		$controller = is_int($instance->getController()) ? @$instance->_macthes[$instance->getController()] : $instance->getController();
		$action = is_int($instance->getAction()) ? @$instance->_macthes[$instance->getAction()] : $instance->getAction();
		if($requestMethod != self::METHOD_POST){
			$params = is_int($instance->getParams()) ? @$instance->_macthes[$instance->getParams()] : $instance->getParams();
			$params = explode('/',ltrim($params,'/')) ;
		}else{
			$params = $_POST ;
		}
		
		if(is_string($controller)){
			$controller = str_replace(' ', '',ucwords(str_replace('-',' ', $controller)));
		}
		if(is_string($action)){
			$action = lcfirst(str_replace(' ', '',ucwords(str_replace('-',' ', $action))));
		}
		
		$controllerReturnType = $instance->getControllerReturnType();
		
		if($controller){
			if(!is_object($controller) && !class_exists($controller)){
				throw new Exception('BAD CONTROLLER');
			}
			$controllerHandler = is_object($controller) ? $controller : new $controller();
			if(!method_exists($controllerHandler, $action)){
				throw new Exception('BAD ACTION');
			}
			$response = call_user_func_array(array($controllerHandler,$action), $params);
		}
		$returnType = $controllerReturnType ? $controllerReturnType : $instance->_returnType ;
		switch ($returnType){
			case self::RET_TYPE_JSON:
				return $instance->returnJson($response);
				break;
			case self::RET_TYPE_RAW:
				return $instance->returnRaw($response);
				break;
		}
	}
}