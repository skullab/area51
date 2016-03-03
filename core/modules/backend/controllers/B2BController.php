<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
class B2BController extends Controller{
	protected function onInitialize(){
		$this->view->setTemplateAfter('index');
	}
}