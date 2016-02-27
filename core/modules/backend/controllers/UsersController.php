<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
class UsersController extends Controller{
	protected function onInitialize(){
		$this->view->body_class = "page-user" ;
	}
	public function addAction(){
		$this->view->setTemplateAfter('index');
		echo 'users add' ;
	}
	public function listAction(){
		$this->view->setTemplateAfter('index');
	}
	public function forgotAction(){
		$this->view->setTemplateAfter('index');
	}
	public function failedAction(){
		$this->view->setTemplateAfter('index');
	}
}