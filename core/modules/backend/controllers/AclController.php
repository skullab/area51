<?php

namespace Thunderhawk\Modules\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Mvc\Model\Acl\AclRoles;
use Thunderhawk\API\Mvc\Model\Acl\AclAccessList;
use Thunderhawk\API\Mvc\Model\Acl\AclResources;
use Thunderhawk\API\Mvc\Model\Acl\AclResourcesAccess;
class AclController extends Controller{
	protected function onInitialize(){
		$this->view->setTemplateAfter('index');
		$this->cssPlugins->addCss('vendor/toastr/toastr.original.css');
		$this->jsPlugins->addJs('vendor/toastr/toastr.js');
	}
	public function registerPreValidateAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$role_name = $this->request->getPost('roleName');
				$find = AclRoles::findFirstByName($role_name);
				if($find === false){
					$payload = array('error'=>0);
				}else{
					$payload = array('error'=>1,'message'=>_('This name is already assigned'));
				}
				return $this->sendAjax($payload);
			}
		}
	}
	public function addRoleAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$role_name = $this->request->getPost('roleName');
				$role_description = $this->request->getPost('roleDescription');
				$role_inherits = $this->request->getPost('roleInherits');
				$role_inherits = $role_inherits ? $role_inherits : null ;
				$role = new \Phalcon\Acl\Role($role_name,$role_description);
				try{
					$check = $this->acl->addRole($role,$role_inherits);
					if($check){
						$this->flash->success(_('The Role name has been added'));
					}else{
						$this->flash->error(_('An error occurred'));
					}
				}catch(\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
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
		$this->assets->renderInlineJs('js/controllers/aclRegistration.js');
	}
	public function getResourceAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				$request = $this->request->getPost('resource');
				$resource = AclResources::findFirstByName($request);
				$payload = array('error'=>0);
				if($resource){
					$payload['error'] = 0 ;
					$payload['description'] = $resource->description ;
					if($resource->name != '*'){
						$resourceAccess = AclResourcesAccess::findByResourcesName($resource->name)->toArray();
					}else{
						$resourceAccess = AclResourcesAccess::find()->toArray();
					}
					$payload['data'] = $resourceAccess ;
				}else{
					$payload['error'] = 1 ;
					$payload['message'] = _('No ACL Resources found') ;
				}
				return $this->sendAjax($payload);
			}
		}
	}
	public function getAccessListAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$records = AclAccessList::find()->toArray();
					$data = array('data'=>$records);
					return $this->sendAjax($data);
				}
			}
		}
	}
	public function listAction(){
		$this->assetsPackage('toastr');
		$this->assetsPackage('sweet-alert');
		$this->assetsPackage('data-table');
		$this->assetsPackage('date-picker');
		$this->assets->renderInlineJs('js/controllers/aclList.js');
		$this->flash->warning('<b>'._('Pay close attention to the permissions change !').'</b><br>'._('A wrong operation could compromise access to users'));
		if($this->request->isPost()){
			if($this->token->check()){
				$allowed = (bool)$this->request->getPost('allowed') ;
				$role = $this->request->getPost('roles_name');
				$resource = $this->request->getPost('resources_name');
				$action = $this->request->getPost('access_name');
				try{
					if($allowed){
						$this->acl->allow($role,$resource,$action);
					}else{
						$this->acl->deny($role,$resource,$action);
					}
					$this->flash->success(_('ACL has been changed successfully'));
				}catch (\Exception $e){
					$this->flash->error($e->getMessage());
				}
			}
		}
	}
	function dropAction(){
		if($this->request->isPost()){
			if($this->request->isAjax()){
				if($this->token->check('token')){
					$dropList = $this->request->getPost('dropList');
					$payload = array('error'=>0);
					$check = true ;
					foreach ($dropList as $access){
						$roleName = $access['roles_name'] ;
						$resourceName = $access['resources_name'];
						$accessName = $access['access_name'];
						$check &= $this->acl->dropAccessList($roleName,$resourceName,$accessName);
					}
					if(!$check){
						$payload['error'] = 1 ;
						$payload['message'] = _('An error occurred');
					}
					return $this->sendAjax($payload);
				}
			}
		}
		
	}
}