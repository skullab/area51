<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Phalcon\Mvc\View;
class ChatController extends Controller{
	public function indexAction(){
		$this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
	}
}