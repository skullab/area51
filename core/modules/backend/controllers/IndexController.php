<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Engine;
class IndexController extends Controller {
	protected function onInitialize(){
		$this->view->body_class = "dashboard" ;
	}
	public function indexAction(){
		echo 'Request time : '.$this->engine->getRequestTime().' seconds';
	}
	protected function accessDenied($role, $resource, $action){
		if($role == 'Guest'){
			return $this->redirect('login');
		}
	}
}