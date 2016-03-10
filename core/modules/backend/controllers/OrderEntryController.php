<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
class OrderEntryController extends Controller{
	protected function onInitialize(){
		$this->view->setTemplateAfter('index');
	}
}