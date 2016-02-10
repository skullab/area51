<?php

namespace Thunderhawk\API\Mvc;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;
abstract class Controller extends PhalconController{
	
	const USE_MODULE_VIEWS = 0 ;
	const USE_THEME_VIEWS = 1 ;
	
	public function initialize(){
		$this->tag->setTitleSeparator($this->config->app->titleSeparator);
		$this->tag->setTitle($this->config->app->title);
		$this->onInitialize();
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
				break;
			case self::USE_MODULE_VIEWS:
				$this->view->setViewsDir('themes/'.$this->theme->name.'/'.
				$this->dispatcher->getModuleName().'/');
				$this->view->setPartialsDir('../'.$this->theme->partials.'/');
				$this->view->setLayoutsDir('../'.$this->theme->layouts.'/');
				
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
		$content = $this->view->getPartial('errors/show404');
		echo $content ;
	}
	public function route404Action(){}
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