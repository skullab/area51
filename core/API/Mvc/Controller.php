<?php

namespace Thunderhawk\API\Mvc;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;
use Thunderhawk\API\Component\Settings;
abstract class Controller extends PhalconController{
	
	const USE_MODULE_VIEWS = 0 ;
	const USE_THEME_VIEWS = 1 ;
	protected $settings ;
	
	public function initialize(){
		$this->session->start();
		$this->tag->setDoctype(\Phalcon\Tag::HTML5);
		$this->tag->setTitleSeparator($this->config->app->titleSeparator);
		$this->tag->setTitle($this->config->app->title);
		$this->settings = Settings::findFirst(array(
				'namespace = ?0 AND controller = ?1',
				'bind' => array(
						$this->dispatcher->getNamespaceName(),
						$this->dispatcher->getControllerName()
				)
		));
		if($this->settings === false){
			$this->settings = new Settings();
		}
		$this->onInitialize();
		$this->prepareAssets();
	}
	public function getNamespaceName(){
		$this->dispatcher->getNamespaceName();
	}
	public function getModuleName(){
		return $this->dispatcher->getModuleName();
	}
	public function getModuleManifest(){
		return $this->manifestManager->getManifest($this->getModuleName());
	}
	public function switchViewsDir($mode){
		switch ($mode){
			case self::USE_THEME_VIEWS:
				$this->view->setViewsDir('themes/'.$this->theme->name.'/');
				$this->view->setPartialsDir($this->theme->partials.'/');
				$this->view->setLayoutsDir($this->theme->layouts.'/');
				$this->view->setMainView($this->theme->main);
				break;
			case self::USE_MODULE_VIEWS:
				$this->view->setViewsDir('themes/'.$this->theme->name.'/'.
				$this->dispatcher->getModuleName().'/');
				$this->view->setPartialsDir('../'.$this->theme->partials.'/');
				$this->view->setLayoutsDir('../'.$this->theme->layouts.'/');
				$this->view->setMainView('../'.$this->theme->main);
				
		}
	}
	public function pickModuleView($path){
		$this->switchViewsDir(self::USE_MODULE_VIEWS);
		$this->view->pick($path);
	}
	public function pickThemeView($path){
		$this->switchViewsDir(self::USE_THEME_VIEWS);
		$this->view->pick($path);
	}
	public function renderModuleView($controller,$action,array $params = array()){
		$this->switchViewsDir(self::USE_MODULE_VIEWS);
		$this->renderView($controller, $action,$params);
	}
	public function renderThemeView($controller,$action,array $params = array()){
		$this->switchViewsDir(self::USE_THEME_VIEWS);
		$this->renderView($controller, $action,$params);
	}
	public function renderView($controller,$action,array $params = array()){
		$view = clone $this->view;
		$view->render($controller,$action,$params);
	}
	public function show404Action(){
		//$this->renderThemeView('errors', 'show404');
		//$this->switchViewsDir(self::USE_THEME_VIEWS);
		//$view = clone $this->view ;
		//$this->renderModuleView('index', 'index');
		//$this->renderModuleView('index','index');
		$this->view->pick('index/index');
		$content = $this->view->getPartial('errors/show404');
		//$this->view->setContent($content);
		echo $content ;
	}
	public function route404Action(){}
	public function indexAction(){}
	public function beforeExecuteRoute(){
		/*$auth = $this->session->get('auth');
		if(!$auth){
			$role = 'Guest' ;
		}else{
			$role = 'Admin' ;
		}
		
		$resource = $this->dispatcher->getModuleName().':'.$this->dispatcher->getControllerName();
		$action = $this->dispatcher->getActionName();
		if(!$this->acl->isAllowed($role,$resource,$action)){
			$this->accessDenied($role, $resource, $action);
		}*/
	}
	protected function prepareAssets(){
		$themeDir = 'theme-default/' ; 
		$this->assets->
		addCss('http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900',false)->
		addCss($themeDir.'bootstrap.css')->
		addCss($themeDir.'materialadmin.css')->
		addCss($themeDir.'font-awesome.min.css')->
		addCss($themeDir.'material-design-iconic-font.min.css');
		/*****/
		$this->assets->collection('ie')->
		addJs('assets/js/libs/utils/html5shiv.js')->
		addJs('assets/js/libs/utils/respond.min.js');
		/*****/
		$this->assets->
		addJs('libs/jquery/jquery-1.11.2.min.js')->
		addJs('libs/jquery/jquery-migrate-1.2.1.min.js')->
		addJs('libs/bootstrap/bootstrap.min.js')->
		addJs('libs/spin.js/spin.min.js')->
		addJs('libs/autosize/jquery.autosize.min.js')->
		addJs('libs/nanoscroller/jquery.nanoscroller.min.js')->
		addJs('core/source/App.js')->
		addJs('core/source/AppNavigation.js')->
		addJs('core/source/AppOffcanvas.js')->
		addJs('core/source/AppCard.js')->
		addJs('core/source/AppForm.js')->
		addJs('core/source/AppNavSearch.js')->
		addJs('core/source/AppVendor.js')->
		addJs('core/demo/Demo.js');
		
	}
	protected function accessDenied($role,$resource,$action){
		var_dump('access denied for : '.$role.' '.$resource.' '.$action);
	}
	public function sendAjax($payload,array $headers = array()){
		$status      = 200;
		$description = 'OK';
		$contentType = 'application/json';
		$content     = json_encode($payload);
		
		$response = new Response();
		
		$response->setStatusCode($status, $description);
		$response->setContentType($contentType, 'UTF-8');
		$response->setContent($content);
		
		// Set the additional headers
		foreach ($headers as $key => $value) {
			$response->setHeader($key, $value);
		}
		
		$this->view->disable();
		
		return $response;
	}
	protected function onInitialize(){}
}