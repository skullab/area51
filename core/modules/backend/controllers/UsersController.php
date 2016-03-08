<?php

namespace Thunderhawk\Modules\Backend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\User\Users;
use Thunderhawk\API\Mvc\Model\User\UsersDetails;
use Thunderhawk\API\Mvc\Model\User\UsersStatus;
use Phalcon\Mvc\View;
use Thunderhawk\API\Mvc\Model\User\UsersForgotPassword;
use Thunderhawk\API\Mvc\Model\User\UsersFailedAttempts;
use Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Mvc\Model\Acl\AclRoles;

class UsersController extends Controller {
	protected function onInitialize() {
		$this->view->body_class = "page-user";
		$this->view->setTemplateAfter ( 'index' );
		$this->cssPlugins->addCss('vendor/toastr/toastr.original.css');
		$this->jsPlugins->addJs('vendor/toastr/toastr.js');
	}
	public function addAction() {
		$this->cssPlugins->addCss ( 'vendor/jquery-wizard/jquery-wizard.css' )
		->addCss ( 'vendor/formvalidation/formValidation.css' )
		->addCss('css/spinner.css');
		$this->jsPlugins->addJs ( 'vendor/formvalidation/formValidation.js' )
		->addJs ( 'vendor/formvalidation/framework/bootstrap.js' )
		->addJs ( 'vendor/matchheight/jquery.matchHeight-min.js' )
		->addJs ( 'vendor/jquery-wizard/jquery-wizard.js' )
		->addJs ('js/spinner.js');
		$this->jsComponents->addJs ( 'js/components/jquery-wizard.js' )
		->addJs ( 'js/components/matchheight.js' );
		// ->addJs('js/components/toastr.js');
		$this->assets->renderInlineJs ( 'js/controllers/userRegistration.js' );
	}
	public function registerAction() {
		if ($this->request->isPost ()) {
			if ($this->token->check ()) {
				$email = $this->filter->sanitize ( $this->request->getPost ( 'email' ), 'email' );
				$user = Users::findFirstByEmail ( $email );
				if ($user) {
					$this->flash->error ( _ ( 'There is already a user with this email' ) );
					return $this->forward ( 'users', 'add' );
				}
				$password = $this->request->getPost ( 'password' );
				$name = $this->request->getPost ( 'name' );
				$role = $this->request->getPost ( 'role' );
				$surname = $this->request->getPost ( 'surname' );
				$address = $this->request->getPost ( 'address' );
				$phone = $this->request->getPost ( 'phone' );
				//
				$user = new Users ();
				$user->email = $email;
				$user->password = $this->security->hash ( $this->auth->passwordHash ( $password ) );
				$user->status_id = UsersStatus::findFirstByName ( 'active' )->id;
				$user->role = $role;
				$user->details = new UsersDetails ();
				$user->details->name = $name;
				$user->details->surname = $surname;
				$user->details->address = $address;
				$user->details->phone = $phone;
				
				if ($user->save () == false) {
					foreach ( $user->getMessages () as $message ) {
						$this->flash->error ( $message );
					}
				} else {
					$this->flash->success ( _ ( 'User is added !' ) );
				}
				return $this->forward ( 'users', 'add' );
			}
		} else {
			$this->flash->warning ( _ ( 'Use this form to register a new User' ) );
			return $this->forward ( 'users', 'add' );
		}
	}
	public function registerPreValidateAction() {
		if ($this->request->isPost ()) {
			if ($this->request->isAjax ()) {
				$payload = array (
						'error' => 0 
				);
				$user = Users::findFirstByEmail ( $this->request->getPost ( 'email' ) );
				if ($user) {
					$payload ['error'] = 1;
					$payload ['message'] = _ ( 'There is already a user with this email' );
				}
				return $this->sendAjax ( $payload );
			}
		}
		
		return $this->forward ( 'users', 'add' );
	}
	public function listAction() {
		$this->cssPlugins->addCss('css/pages/user.css');
		$this->jsPlugins->addJs('js/plugins/responsive-tabs.js');
		//->addJs('js/components/tabs.js');
		$this->cssPlugins->addCss('vendor/bootstrap-sweetalert/sweet-alert.css');
		$this->jsPlugins->addJs('vendor/bootbox/bootbox.js')
		->addJs('vendor/bootstrap-sweetalert/sweet-alert.js');
		$this->jsComponents->addJs('js/components/bootbox.js')
		->addJs('js/components/bootstrap-sweetalert.js');
		$this->assets->renderInlineJs('js/controllers/dropUser.js');
	}
	public function dropAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$id = $this->request->getPost('users_id');
				$user = Users::findFirstById($id);
				$payload = array('error'=>1);
				if($user){
					if($user->role == Auth::ROLE_ADMIN){
						try{
							$delete = $user->delete();
							if($delete){
								$payload['error'] = 0 ;
							}else{
								foreach ($user->getMessages() as $message){
									$payload['message'] .= $message.'<br>' ;
								}
							}
						}catch(\Exception $e){
							$payload = array('error'=>$e->getCode(),'message'=>$e->getMessage());
						}
					}else{
						$payload['message'] = _('You don\'t have permission to perform this operation');
					}
				}else{
					$payload['message'] = _('User not found');
				}
				return $this->sendAjax($payload);
			}
		}
		
		$this->redirect();
		
	}
	public function getForgotAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$records = UsersForgotPassword::find()->toArray() ;
					$table = array();
					$i = 0 ;
					foreach ($records as $record){
						$table[$i] = array();
						foreach ($record as $n => $v){
							$table[$i][$n] = $v ;
							if($n == 'token' || $n == 'private_key'){
								$table[$i][$n] = '***' ;
							}
						}
						$i++ ;
					}
					$records = null ;
					$data = array('data'=>$table);
					return $this->sendAjax($data);
				}
			}
		}
	}
	public function getFailedAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$records = UsersFailedAttempts::find()->toArray();
					$data = array('data'=>$records);
					return $this->sendAjax($data);
				}
			}
		}
	}
	public function forgotAction() {
		$this->cssPlugins->addCss('vendor/datatables-bootstrap/dataTables.bootstrap.css')
		->addCss('vendor/datatables-fixedheader/dataTables.fixedHeader.css')
		->addCss('vendor/datatables-responsive/dataTables.responsive.css');
		$this->jsPlugins->addJs('vendor/datatables/jquery.dataTables.min.js')
		->addJs('vendor/datatables-fixedheader/dataTables.fixedHeader.js')
		->addJs('vendor/datatables-bootstrap/dataTables.bootstrap.js')
		->addJs('vendor/datatables-responsive/dataTables.responsive.js')
		->addJs('vendor/datatables-tabletools/dataTables.tableTools.js');
		$this->jsComponents->addJs('js/components/datatables.js');
		$this->assets->renderInlineJs('js/controllers/userForgot.js');
	}
	public function failedAction() {
		$this->cssPlugins->addCss('vendor/datatables-bootstrap/dataTables.bootstrap.css')
		->addCss('vendor/datatables-fixedheader/dataTables.fixedHeader.css')
		->addCss('vendor/datatables-responsive/dataTables.responsive.css');
		$this->jsPlugins->addJs('vendor/datatables/jquery.dataTables.min.js')
		->addJs('vendor/datatables-fixedheader/dataTables.fixedHeader.js')
		->addJs('vendor/datatables-bootstrap/dataTables.bootstrap.js')
		->addJs('vendor/datatables-responsive/dataTables.responsive.js')
		->addJs('vendor/datatables-tabletools/dataTables.tableTools.js');
		$this->jsComponents->addJs('js/components/datatables.js');
		$this->assets->renderInlineJs('js/controllers/userFailed.js');
	}
	public function changeStatusAction($id,$status){
		$user = Users::findFirstById ( $id );
		if($user){
			$user->status_id = $status ;
			try {
				$user->save ();
				$this->flash->success(_('Status changed'));
				$this->forward('users','profile',array($id));
			} catch ( \Exception $e ) {
				$this->flash->error ( $e->getMessage () );
			}
		}
	}
	public function getRolesAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$roles = AclRoles::find()->toArray();
				return $this->sendAjax($roles);
			}
		}
	}
	public function profileAction($id, $change = null, $value = null) {
		if (is_numeric ( $id )) {
			$user = Users::findFirstById ( $id );
			if ($user) {
				
				if($this->request->isPost()){
					if($this->request->isAjax()){
						$payload = array('error'=>0);
						$field = $this->request->getPost('name');
						$value = $this->request->getPost('value');
						if(!$user->details){
							$user->details = new UsersDetails();
							$user->details->users_id = $user->id ;
						}
						if($field == 'role'){
							$user->role = $value ;
						}else{
							$user->details->{$field} = $value ;
						}
						try{
							$user->save();
						}catch(\Exception $e){
							$payload['error'] = $e->getCode();
							$payload['message'] = $e->getMessage();
						}
						return $this->sendAjax($payload);	
					}
				}
				
				$this->cssPlugins->addCss( 'css/pages/profile.css' )
				->addCss('vendor/x-editable/x-editable.css')
				
				->addCss('vendor/typeahead-js/typeahead.css')
				->addCss('vendor/select2/select2.css');
				
				$this->jsPlugins->addJs('vendor/x-editable/bootstrap-editable.js')
				->addJs('vendor/typeahead-js/bloodhound.min.js')
				->addJs('vendor/typeahead-js/typeahead.jquery.min.js')
				->addJs('vendor/x-editable/address.js')
				->addJs('vendor/select2/select2.min.js')
				->addJs('vendor/moment/moment.min.js');
				
				$this->assets->renderInlineJs('js/controllers/userProfile.js',true,array('user'=>$user));
				
				$this->view->body_class = 'page-profile';
				$this->view->user = $user;
				
			}else{
				$this->redirect();
			}
		}else{
			$this->redirect();
		}
	}
	public function matrioskaAction() {
		$this->cssPlugins->addCss ( 'vendor/bootstrap-treeview/bootstrap-treeview.css' );
		$this->jsPlugins->addJs ( 'vendor/bootstrap-treeview/bootstrap-treeview.min.js' )->addJs ( 'vendor/bootstrap-treeview/bootstrap-treeview.min.js' );
		$this->jsComponents->addJs ( 'js/components/bootstrap-treeview.js' );
		$this->assets->renderInlineJs ( 'js/controllers/tree.example.js' );
	}
}