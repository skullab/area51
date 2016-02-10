<?php

namespace Vendor\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
class IndexController extends Controller{
	public function indexAction(){
		$this->view->setTemplateBefore('dashboard');
	}
}