<?php

namespace Thunderhawk\Modules\Install\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Mvc\Model\User\Users;
use Thunderhawk\API\Mvc\Model\User\UsersStatus;
class IndexController extends Controller{
	protected function accessDenied($role, $resource, $action){}
	public function indexAction(){
		if(!$this->auth->hasUsers()){
			$this->view->body_class = 'page-login layout-full' ;
			$this->cssPlugins->addCss('css/pages/login.css');
			$this->assetsPackage('form-validation');
			$this->assets->renderInlineJs('js/controllers/install.js');
		}else{
			$this->redirect();
		}
	}
	public function registerAction(){
		if($this->request->isPost()){
			if($this->token->check()){
				$email = $this->request->getPost('email');
				$password = $this->request->getPost('password');
				$this->installRoles();
				$this->installResourcesAccess();
				$this->installUserStatus();
				$admin = new Users();
				$admin->email = $email;
				$admin->password = $this->security->hash($this->auth->passwordHash($password));
				$admin->status_id = UsersStatus::findFirstByName (Auth::STATUS_ACTIVE)->id;
				$admin->role = Auth::ROLE_ADMIN;
				
				if($admin->save() !== false){
					$this->flashSession->success(_('Administrator user is successfully created'));
					$this->redirect();
				}else{
					foreach ($admin->getMessages() as $message){
						$this->flash->error($message);
					}
				}
			}
		}
	}
	protected function installUserStatus(){
		try{
			$status = new UsersStatus();
			$status->name = Auth::STATUS_ACTIVE ;
			$status->save();
			
			$status = new UsersStatus();
			$status->name = Auth::STATUS_LOCKED;
			$status->save();
			
			$status = new UsersStatus();
			$status->name = Auth::STATUS_BANNED ;
			$status->save();
			
			$status = new UsersStatus();
			$status->name = Auth::STATUS_SUSPENDED ;
			$status->save();
			
			$status = new UsersStatus();
			$status->name = Auth::STATUS_HACKED ;
			$status->save();
		}catch (\Exception $e){
			$this->flash->error($e->getMessage());
		}
	}
	protected function installRoles(){
		
		$guest = new \Phalcon\Acl\Role(Auth::ROLE_GUEST,_('Guest user'));
		$this->acl->addRole($guest);
		
		$user = new \Phalcon\Acl\Role(Auth::ROLE_USER,_('Basic user'));
		$this->acl->addRole($user,$guest);
		
		$agent = new \Phalcon\Acl\Role(Auth::ROLE_AGENT,_('Sales Agent user'));
		$this->acl->addRole($agent,$user);
		
		$bookkepper = new \Phalcon\Acl\Role(Auth::ROLE_BOOK,_('Bookkeeper user'));
		$this->acl->addRole($bookkepper,$user);
		
		$business = new \Phalcon\Acl\Role(Auth::ROLE_BUSINESS,_('Business consultant user'));
		$this->acl->addRole($business,$bookkepper);
		
		$supply = new \Phalcon\Acl\Role(Auth::ROLE_SUPPLY,_('Supply Chain user'));
		$this->acl->addRole($supply,$user);
		
		$promo = new \Phalcon\Acl\Role(Auth::ROLE_PROMO,_('Promotions Manager user'));
		$this->acl->addRole($promo,$supply);
		
		$admin = new \Phalcon\Acl\Role(Auth::ROLE_ADMIN,_('Administrator user'));
		$this->acl->addRole($admin,$promo);
		
		$super = new \Phalcon\Acl\Role(Auth::ROLE_SUPER,_('SuperAdmin user'));
		$this->acl->addRole($super,$admin);
		
		
	}
	protected function installResourcesAccess(){
		$frontend = new \Phalcon\Acl\Resource('frontend:index',_('Frontend module index page'));

		$backend_index =  new \Phalcon\Acl\Resource('backend:index',_('Backend module index page'));
		$backend_acl = new \Phalcon\Acl\Resource('backend:acl',_('Backend nodule acl page'));
		$backend_order_entry = new \Phalcon\Acl\Resource('backend:orderEntry',_('Backend module order entry page'));
		$backend_users = new \Phalcon\Acl\Resource('backend:users',_('Backend module users page'));
		
		$this->acl->addResource($frontend,array('index','forgot','reset','sign','logout'));
		$this->acl->addResource($backend_index,array('index'));
		$this->acl->addResource($backend_acl,array('addRole','list','drop','getAccessList','getResource','registerPreValidate'));
		$this->acl->addResource($backend_order_entry,array('index'));
		$this->acl->addResource($backend_users,array('index',
				'add',
				'register',
				'registerPreValidate',
				'list',
				'drop',
				'getForgot',
				'getFailed',
				'forgot',
				'failed',
				'changeStatus',
				'getRoles',
				'profile'
		));
		
		$this->acl->allow(Auth::ROLE_ADMIN,'*','*');
	}
}