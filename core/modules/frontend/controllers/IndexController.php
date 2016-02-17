<?php

namespace Thunderhawk\Modules\Frontend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Assets\Manager as AssetsManager;
use Phalcon\Mvc\View;
use Phalcon\Assets\Filters\Cssmin;
use Thunderhawk\API\Mvc\Model\Users;
use Thunderhawk\API\Mvc\Model\UsersStatus;
use Thunderhawk\API\Component\Acl;

class IndexController extends Controller {
	public function indexAction(){
		if($this->session->get('auth')){
			//return $this->response->redirect('//127.0.0.1/panel');
		}
	}
	public function signAction() {
		if ($this->request->isPost ()) {
			if($this->token->checkToken()){
				$username = $this->request->getPost('username');
				$password = $this->request->getPost('password');
				$remember = $this->request->getPost('remember');
				return $this->response->redirect($this->url->getStaticBaseUri().'panel');
			}
		} else {
			$this->dispatcher->forward ( array (
					'controller' => 'index',
					'action' => 'index' 
			) );
			return false;
		}
	}
}