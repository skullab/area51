<?php

namespace Thunderhawk\API\Component;
use Phalcon\Mvc\User\Component;
use Thunderhawk\API\Component\Auth\AuthInterface;
use Thunderhawk\API\Mvc\Model\User\Users;
use Thunderhawk\API\Mvc\Model\User\UsersFailedAttempts;
use Thunderhawk\API\Mvc\Model\User\UsersRememberTokens;
use Thunderhawk\API\Component\Defuse\Crypto;
use Thunderhawk\API\Mvc\Model\User\UsersForgotPassword;
use Thunderhawk\API\Mvc\Model\User\UsersStatus;
use Thunderhawk\API\Mvc\Model\User\UsersLogin;
class Auth extends Component implements AuthInterface{
	
	const ROLE_SUPER	= 'super';
	const ROLE_ADMIN	= 'admin';
	const ROLE_AGENT	= 'sales agent';
	const ROLE_SUPPLY	= 'supply chain';
	const ROLE_PROMO	= 'promotions manager';
	const ROLE_BOOK		= 'bookkeeper';
	const ROLE_BUSINESS	= 'business consultant';
	const ROLE_USER		= 'user';
	const ROLE_GUEST	= 'guest';
		const STATUS_ACTIVE = 'active';
	const STATUS_LOCKED = 'locked';
	const STATUS_SUSPENDED = 'suspended' ;
	const STATUS_BANNED = 'banned';
	const STATUS_HACKED = 'hacked';
	
	const SESSION_AUTH = '#auth-identity#' ;
	const COOKIE_REMEMBER_SELECTOR 	= 'ZaytbI3CD17Ww95/ZGXtrA';
	const COOKIE_REMEMBER_TOKEN 	= '79mHhRU2e/yFX1VPpFuAKw';
	
	//const COOKIE_REMEMBER_SELECTOR 	= 'remember-selector';
	//const COOKIE_REMEMBER_TOKEN 	= 'remember-token';
 	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::checkAccess()
	 */
	public function checkAccess(array $credentials) {
		if(
				!isset($credentials['email']) || 
				!isset($credentials['password'])){
			throw new Auth\Exception(null,10);
		}
		if(Users::count() == 0){
			throw new Auth\Exception(null,800);
		}
		$user = Users::findFirstByEmail($credentials['email']);
		if($user == false){
			$this->userThrottling(0);
			throw new Auth\Exception(null,100);
		}
		if(!$this->security->checkHash(
				$this->passwordHash($credentials['password']),
				$user->password)
		){
			$this->userThrottling($user->id);
			throw new Auth\Exception(null,100);
		}
		
		$this->checkUserStatus($user);
		$this->registerIdentity($user);
		
		if((bool)$credentials['remember']){
			$this->createRememberEnvironment($user);
		}
		
		return true ;
	}
	public function hasUsers(){
		return (bool)Users::count();
	}
	public function passwordHash($plaintext){
		return base64_encode(hash('sha256', $plaintext,true));
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::createRememberEnvironment()
	 */
	public function createRememberEnvironment(Users $user) {
		//
		$userAgent = $this->request->getUserAgent();
		$selector = hash('sha256',$user->email.$userAgent,true) ;
		$token = openssl_random_pseudo_bytes(16);
		$expire = date("Y-m-d H:i:s",time()+TIME_ONE_WEEK);
		//
		$remember = UsersRememberTokens::findFirstBySelector($selector);
		if(!$remember){
			$remember = new UsersRememberTokens();
			$remember->users_id = $user->id ;
			$remember->selector = $selector ;
		}
		$remember->token = hash('sha256',$token,true);
		$remember->expires = $expire ;
		if($remember->save() != false){
			$this->cookies->set(self::COOKIE_REMEMBER_SELECTOR,$selector,time()+TIME_ONE_WEEK);
			$this->cookies->set(self::COOKIE_REMEMBER_TOKEN,$token,time()+TIME_ONE_WEEK);
			$this->cookies->send();
		}
	}
	public function loginWithRememberMe(){
		$selector = $this->cookies->get(self::COOKIE_REMEMBER_SELECTOR)->getValue();
		$token = $this->cookies->get(self::COOKIE_REMEMBER_TOKEN)->getValue();
		//
		$remember = UsersRememberTokens::findFirstBySelector($selector);
		if($remember != false){
			//expire test
			if($remember->expires < date("Y-m-d H:i:s")){
				$remember->delete();
				$this->deleteCookies();
				return false ;
			}
			if(!hash_equals($remember->token,hash('sha256',$token,true))){
				$this->deleteCookies();
				return false ;
			}
			$this->checkUserStatus($remember->user);
			$this->registerIdentity($remember->user);
			return true;
		}
		return false ;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::hasRememberMe()
	 */
	public function hasRememberMe() {
		return $this->cookies->has(self::COOKIE_REMEMBER_SELECTOR) && 
		$this->cookies->has(self::COOKIE_REMEMBER_TOKEN);
	}
	public function hasIdentity(){
		$identity = $this->getIdentity();
		return ($identity !== false && $identity != null) ;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::logout()
	 */
	public function logout() {
		$identity = $this->getIdentity();
		$user = Users::findFirstById($identity['id']);
		if(isset($user->login)){
			$user->login->online = 0;
			$user->save();
		}
		$this->deleteCookies();
		$this->session->remove(self::SESSION_AUTH);
	}

	public function deleteCookies(){
		if($this->cookies->has(self::COOKIE_REMEMBER_SELECTOR)){
			$this->cookies->get(self::COOKIE_REMEMBER_SELECTOR)->delete();
		}
		if($this->cookies->has(self::COOKIE_REMEMBER_TOKEN)){
			$this->cookies->get(self::COOKIE_REMEMBER_TOKEN)->delete();
		}
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::userThrottling()
	 */
	public function userThrottling($user_id) {
		
		$ip_address = ip2long($this->request->getClientAddress());
		
		$failed = new UsersFailedAttempts();
		$failed->users_id = $user_id;
		$failed->ip_address = $ip_address ;
		$failed->save();
		
		$attempts = UsersFailedAttempts::count(array(
				'ip_address = ?0 AND attempted >= ?1',
				'bind' => array(
						$ip_address,
						date("Y-m-d H:i:s",time()-TIME_ONE_HOUR)
				)
		));
		
		// prevent brute force
		switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::registerIdentity()
	 */
	public function registerIdentity(Users $user) {
		if(!$user->login){
			$login = new UsersLogin();
			$login->users_id = $user->id ;
			$login->last_access = date("Y-m-d H:i:s");
			$login->save();
		}
		$user->login->last_access = date("Y-m-d H:i:s");
		$user->login->online = 1 ;
		$user->login->busy = 0 ;
		$user->save();
		if($user->details && (trim($user->details->name) != '' || trim($user->details->surname) != '')){
			$display_name = $user->details->name.' '.$user->details->surname ;
		}else{
			$display_name = $user->email ;
		}
		if($user->details && (trim($user->details->portrait)) != ''){
			$portrait = $user->details->portrait ;
		}else{
			$portrait = 'user_blank.png' ;
		}
		$this->session->set(self::SESSION_AUTH,array(
				'id' => $user->id,
				'email' => $user->email,
				'status' => $user->status->name,
				'role' => $user->role,
				'display_name' => $display_name,
				'portrait' => $portrait
		));
	}

	public function updateOnlineUser($user_id = null,$online = 1,$busy = 0){
		if($user_id == null){
			$user_id = $this->getIdentity()['id'] ;
		}
		$user = Users::findFirstById($user_id);
		if($user && $user->login){
			$user->login->online = $online ;
			$user->login->busy = $busy ;
			$user->login->last_operation = date("Y-m-d H:i:s");
			$user->save();
		}
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::checkUserStatus()
	 */
	public function checkUserStatus(Users $user) {
		if($user->status->name != self::STATUS_ACTIVE){
			$this->logout();
			throw new Auth\Exception($user->status->description,200);
		}
	}
	public function isRoleOrInherits($role_name){
		return $this->isRole($role_name) || $this->isInherits($role_name) ;
	}
	public function isRole($role_name){
		$identity = $this->getIdentity();
		if(!$identity)return false ;
		if(strcasecmp($identity['role'],$role_name) == 0)return true ;
		return false ;
	}
	public function isInherits($role){
		$identity = $this->getIdentity();
		if(!$identity)return false ;
		$check = false ;
		//$role_name = null ;
		foreach ($this->acl->getRolesInherits() as $role_name => $inherits){
			foreach ($inherits as $inheritsRole){
				if(strcasecmp($inheritsRole,$role) == 0){
					$check = true ;
					break;
				}
			}
			if($check)break;
		}
		if(!$check)return false ;
		if(strcasecmp($identity['role'],$role_name) == 0){
			return true ;
		}else{
			return $this->isInherits($role_name);
		}
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Component\Auth\AuthInterface::getIdentity()
	 */
	public function getIdentity() {
		return $this->session->get(self::SESSION_AUTH);
	}
	
	public function checkIntegrity(){
		if(null !== $identity = $this->getIdentity()){
			$user = Users::findFirstById($identity['id']);
			if($user == false){
				$this->logout();
				throw new Auth\Exception(null,700);
			}
			$this->checkUserStatus($user);
			return true ;
		}
		return false ;
	}
	public function forgotPassword($email) {
		$user = Users::findFirstByEmail($email);
		if($user == false){
			throw new Auth\Exception(null,300);
		}
		$publicKey = Crypto::createNewRandomKey();
		$privateKey = Crypto::createNewRandomKey();
		$token = Crypto::encrypt($privateKey, $publicKey);
		
		$encodedPublicKey = rawurlencode($publicKey);
		$encodedToken = rawurlencode($token);
		
		$expire = date("Y-m-d H:i:s",time()+TIME_ONE_HOUR);
		//
		$forgot = new UsersForgotPassword();
		$forgot->users_id = $user->id ;
		$forgot->private_key = $privateKey ;
		$forgot->token = $token ;
		$forgot->expires = $expire ;
		if($forgot->save() == false){
			foreach ($forgot->getMessages() as $message){
				$this->flash->error($message);
			}
			return false ;
		}
		//
		$this->mail->setTo([$email]);
		$this->mail->setSubject('reset password');
		$this->mail->setBody('
				<div>
					<a href="http:'.
					$this->url->getStaticBaseUri().
					'reset-password?k='.$encodedPublicKey.'&t='.
					$encodedToken.'">reset your passsword here</a>
				</div>
		');
		$this->mail->send();
		//echo '<a href="'.$this->url->getStaticBaseUri().'reset-password?k='.$encodedPublicKey.'&t='.$encodedToken.'">reset your passsword here</a>' ;
		if(empty($this->mail->getFailedRecipients())){
			$this->flash->success('email sent. check your inbox');
		}else{
			$this->flash->error('an error occured');
		}
	}
	public function checkForgotPassword($publicKey,$token){
		$forgot = UsersForgotPassword::findFirstByToken(rawurldecode($token));
		if($forgot == false){
			throw new Auth\Exception(null,400);
		}
		if($forgot->expires < date("Y-m-d H:i:s")){
			$this->userThrottling($forgot->user->id);
			$forgot->delete();
			throw new Auth\Exception(null,500);
		}
		$privateKey = Crypto::decrypt(rawurldecode($token),rawurldecode($publicKey));
		if(!hash_equals($forgot->private_key, $privateKey)){
			$this->userThrottling($forgot->user->id);
			$forgot->delete();
			throw new Auth\Exception(null,600);
		}
		$locked = UsersStatus::findFirstByName(self::STATUS_LOCKED);
		$forgot->user->status_id = $locked->id ;
		$forgot->save();
	}
	public function resetPassword($publicKey,$token,$newPassword){
		$forgot = UsersForgotPassword::findFirstByToken(rawurldecode($token));
		if($forgot == false){
			throw new Auth\Exception(null,400);
		}
		$privateKey = Crypto::decrypt(rawurldecode($token),rawurldecode($publicKey));
		if($forgot->private_key != $privateKey){
			$this->userThrottling($forgot->user->id);
			$hacked = UsersStatus::findFirstByName(self::STATUS_HACKED);
			$forgot->user->status_id = $hacked->id ;
			$forgot->save();
			$forgot->delete();
			throw new Auth\Exception(null,600);
		}
		$active = UsersStatus::findFirstByName(self::STATUS_ACTIVE);
		$forgot->user->status_id = $active->id ;
		$forgot->user->password = $this->security->hash($this->passwordHash($newPassword));
		if($forgot->save() != false && $forgot->delete() != false){
			$this->flash->success('The new password is stored !');
		}else{
			foreach ($forgot->getMessages() as $message){
				$this->flash->error($message);
			}
		}
	}
}