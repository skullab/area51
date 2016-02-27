<?php

namespace Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Mvc\Model\User\Users;
interface AuthInterface {
	public function checkAccess(array $credentials);
	public function createRememberEnvironment(Users $user);
	public function hasRememberMe();
	public function logout();
	public function userThrottling($user_id);
	public function registerIdentity(Users $user);
	public function checkUserStatus(Users $user);
	public function getIdentity();
	public function loginWithRememberMe();
	public function forgotPassword($email);
}