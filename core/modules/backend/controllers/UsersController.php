<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\User\Users;
use Thunderhawk\API\Mvc\Model\User\UsersDetails;
use Thunderhawk\API\Mvc\Model\User\UsersStatus;
class UsersController extends Controller{
	protected function onInitialize(){
		$this->view->body_class = "page-user" ;
		$this->view->setTemplateAfter('index');
	}
	public function addAction(){
		$this->cssPlugins->addCss('vendor/jquery-wizard/jquery-wizard.css')
			->addCss('vendor/formvalidation/formValidation.css')
			->addCss('vendor/toastr/toastr.original.css');
		$this->jsPlugins->addJs('vendor/formvalidation/formValidation.js')
			->addJs('vendor/formvalidation/framework/bootstrap.js')
			->addJs('vendor/matchheight/jquery.matchHeight-min.js')
			->addJs('vendor/jquery-wizard/jquery-wizard.js')
			->addJs('vendor/asprogress/jquery-asProgress.js')
			->addJs('vendor/toastr/toastr.js');
		$this->jsComponents->addJs('js/components/jquery-wizard.js')
			->addJs('js/components/matchheight.js')
			->addJs('js/components/asprogress.js');
			//->addJs('js/components/toastr.js');
		$this->assets->renderInlineJs('js/controllers/userRegistration.js');
	}
	public function registerAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$email = $this->filter->sanitize($this->request->getPost('email'),'email');
				$user = Users::findFirstByEmail($email);
				if($user){
					$this->flash->error(_('There is already a user with this email'));
					return $this->forward('users','add');
				}
				$password = $this->request->getPost('password');
				$name = $this->request->getPost('name');
				$role = $this->request->getPost('role');
				$surname = $this->request->getPost('surname');
				$address = $this->request->getPost('address');
				$phone = $this->request->getPost('phone');
				//
				$user = new Users();
				$user->email = $email ;
				$user->password = $this->security->hash($this->auth->passwordHash($password));
				$user->status_id = UsersStatus::findFirstByName('active')->id;
				$user->role = $role ;
				$user->details = new UsersDetails();
				$user->details->name = $name ;
				$user->details->surname = $surname ;
				$user->details->address = $address ;
				$user->details->phone = $phone ;
				
				if($user->save() == false){
					foreach ($user->getMessages() as $message){
						$this->flash->error($message);
					}
				}else{
					$this->flash->success(_('User is added !'));
				}
				return $this->forward('users','add');
			}
		}else{
			$this->flash->warning(_('Use this form to register a new User'));
			return $this->forward('users','add');
		}
	}
	public function registerPreValidateAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$payload = array('error'=>0);
				$user = Users::findFirstByEmail($this->request->getPost('email'));
				if($user){
					$payload['error'] = 1;
					$payload['message'] = _('There is already a user with this email');
				}
				return $this->sendAjax($payload);
			}
		}
		
		return $this->forward('users','add');
	}
	public function listAction(){
		
	}
	public function forgotAction(){
		
	}
	public function failedAction(){
		
	}
	public function matrioskaAction(){
		$this->cssPlugins->addCss('vendor/bootstrap-treeview/bootstrap-treeview.css');
		$this->jsPlugins->addJs('vendor/bootstrap-treeview/bootstrap-treeview.min.js')
			->addJs('vendor/bootstrap-treeview/bootstrap-treeview.min.js');
		$this->jsComponents->addJs('js/components/bootstrap-treeview.js');
		$this->assets->renderInlineJs('js/controllers/tree.example.js');
	}
}