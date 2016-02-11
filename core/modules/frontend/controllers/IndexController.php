<?php

namespace Thunderhawk\Modules\Frontend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Phalcon\Mvc\View;

class IndexController extends Controller {
	public function onInitialize(){
		$this->assets->requireCss('gfonts.css');
		$this->assets->requireCss('bootstrap.css');
		$this->assets->requireCss('materialadmin.css');
		$this->assets->requireCss('font.awesome.min.css');
		$this->assets->requireCss('material-design-iconic-font.css');
	}
	public function signAction() {
		if($this->request->isPost()){
			//do user auth
			$this->flash->error('username or password is wrong !');
		}else{
			$this->dispatcher->forward(array(
					'action' => 'index'
			));
			return false ;
		}
	}
}