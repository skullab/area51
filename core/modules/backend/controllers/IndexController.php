<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
class IndexController extends Controller {
	protected function onInitialize(){
		$this->view->body_class = "dashboard" ;
	}
	protected function accessDenied($role, $resource, $action){
		if($role == 'Guest'){
			return $this->redirect('login');
		}
	}
}