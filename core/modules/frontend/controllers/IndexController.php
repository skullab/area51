<?php

namespace Thunderhawk\Modules\Frontend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Component\Auth;

class IndexController extends Controller {
	protected function onInitialize(){
		$this->view->body_class = "page-login layout-full" ;
	}
	public function indexAction(){
		if($this->auth->hasRememberMe()){
			var_dump('remember');
			try{
				if($this->auth->loginWithRememberMe()){
					return $this->redirect('dashboard');
				}
			}catch(Auth\Exception $e){
				$this->flash->error($e->getMessage());
			}
		}
	}
	public function signAction() {
		if ($this->request->isPost ()) {
			if($this->token->check()){
				$email = $this->request->getPost('email');
				$password = $this->request->getPost('password');
				$remember = $this->request->getPost('remember');
				$credentials = array(
						'email' => $email,
						'password' => $password,
						'remember' => $remember
				);
				try{
					if($this->auth->checkAccess($credentials)){
						return $this->redirect('dashboard');
					}
				}catch (Auth\Exception $e){
					$this->flash->error($e->getMessage());
					return $this->forward('index', 'index');
				}
			}
		} else {
			return $this->forward('index', 'index');
		}
	}
	public function logoutAction(){
		$this->auth->logout();
		return $this->redirect('login');
	}
	public function forgotAction(){
		$this->view->body_class = "page-forgot-password layout-full" ;
		if($this->request->isPost()){
			if($this->token->check()){
				try{
					$this->auth->forgotPassword($this->request->getPost('email'));
					return $this->forward('index', 'index');
				}catch(Auth\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
	}
	public function resetAction(){
		$this->view->body_class = "page-forgot-password layout-full" ;
		if($this->request->isPost()){
			if($this->token->check()){
				$publicKey = base64_decode($this->request->getPost('k'));
				$token = base64_decode($this->request->getPost('t'));
				$password = $this->request->getPost('password');
				$password2 = $this->request->getPost('password2');
				if($password != $password2){
					return $this->flash->error('The passwords must be equals');
				}
				try{
					$this->auth->resetPassword($publicKey,$token,$password);
					return $this->forward('index','index');
				}catch (\Exception $e){
					$this->view->public_key = base64_encode($publicKey);
					$this->view->token = base64_encode($token);
					$this->flash->error($e->getMessage());
				}
			}
		}else{
			$publicKey = $this->request->getQuery('k');
			$token = $this->request->getQuery('t');
			if($publicKey == null && $token == null){
				return $this->forward('index', 'index');
			}
			$this->view->public_key = base64_encode($publicKey);
			$this->view->token = base64_encode($token);
			try{
				$this->auth->checkForgotPassword($publicKey,$token);
			}catch(\Exception $e){
				$this->flash->error($e->getMessage());
				return $this->forward('index', 'index');
			}
			
		}
	}
}