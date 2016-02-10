<?php

namespace Thunderhawk\Modules\Frontend\Controllers;

use Thunderhawk\API\Mvc\Controller;
class LoginController extends Controller{
	public function indexAction(){
		$this->flash->error('ops');
	}
}