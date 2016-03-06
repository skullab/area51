<?php

namespace Thunderhawk\API\Component;

use Phalcon\Acl\Adapter;
use Phalcon\Di\InjectionAwareInterface;
use Thunderhawk\API\Mvc\Model\Acl\AclRoles;
use Thunderhawk\API\Mvc\Model\Acl\AclRolesInherits;
use Thunderhawk\API\Mvc\Model\Acl\AclResources;
use Thunderhawk\API\Mvc\Model\Acl\AclResourcesAccess;
use Thunderhawk\API\Mvc\Model\Acl\AclAccessList;

class Acl extends Adapter implements InjectionAwareInterface {
	protected $_dependencyInjector;
	public function setDI(\Phalcon\DiInterface $dependencyInjector) {
		$this->_dependencyInjector = $dependencyInjector;
	}
	public function getDI() {
		return $this->_dependencyInjector;
	}
	public function addRole($role, $accessInherits = null) {
		if ($role instanceof \Phalcon\Acl\Role) {
			$roleName = $role->getName ();
			$roleObject = $role;
		} else {
			$roleName = ( string ) $role;
			$roleObject = new \Phalcon\Acl\Role ( $roleName );
		}
		if ($this->isRole ( $roleName ))
			return false;
		
		$role = new AclRoles ();
		$role->name = $roleName;
		$role->description = $roleObject->getDescription ();
		$role->save ();
		
		if ($accessInherits != null) {
			return $this->addInherit ( $roleName, $accessInherits );
		}
		return true;
	}
	public function addInherit($roleName, $roleToInherit) {
		if (! $this->isRole ( $roleName )) {
			throw new \Exception ( "Role '$roleName' does not exist in the role list" );
		}
		if ($roleToInherit instanceof \Phalcon\Acl\Role) {
			$roleToInheritName = $roleToInherit->getName ();
		} else {
			$roleToInheritName = ( string ) $roleToInherit;
		}
		
		$inherits = $this->getRolesInherits ();
		if (isset ( $inherits [$roleToInheritName] )) {
			foreach ( $inherits [$roleToInheritName] as $deepInheritName ) {
				$this->addInherit ( $roleName, $deepInheritName );
			}
		}
		
		if (! $this->isRole ( $roleToInheritName )) {
			throw new \Exception ( "Role '$roleToInheritName' (to inherit) does not exist in the role list" );
		}
		if ($roleName == $roleToInheritName)
			return false;
		
		$rolesInherits = new AclRolesInherits ();
		$rolesInherits->roles_name = $roleName;
		$rolesInherits->roles_inherits = $roleToInheritName;
		return $rolesInherits->save ();
	}
	public function isRole($roleName) {
		return AclRoles::findFirstByName ( $roleName );
		// return is_object($role) ;
	}
	public function isResource($resourceName) {
		return AclResources::findFirstByName ( $resourceName );
		// return is_object($resource);
	}
	public function isResourceAccess($resourceName, $accessName) {
		return AclResourcesAccess::findFirst ( array (
				'resources_name = ?0 AND access_name = ?1',
				'bind' => array (
						$resourceName,
						$accessName 
				) 
		) );
		// return is_object($resourceAccess);
	}
	public function addResource($resourceObject, $accessList) {
		if ($resourceObject instanceof \Phalcon\Acl\Resource) {
			$resourceName = $resourceObject->getName ();
			$resourceInstance = $resourceObject;
		} else {
			$resourceName = ( string ) $resourceObject;
			$resourceInstance = new \Phalcon\Acl\Resource ( $resourceName );
		}
		if (! $this->isResource ( $resourceName )) {
			$resource = new AclResources ();
			$resource->name = $resourceName;
			$resource->description = $resourceInstance->getDescription ();
			$resource->save();
		}
		return $this->addResourceAccess ( $resourceName, $accessList );
	}
	public function addResourceAccess($resourceName, $accessList) {
		if (! $this->isResource ( $resourceName )) {
			throw new \Exception ( "Resource '$resourceName' does not exist in ACL" );
		}
		if (! is_array ( $accessList ) && ! is_string ( $accessList )) {
			throw new \Exception ( "Invalid value for accessList" );
		}
		if (! is_array ( $accessList )) {
			$accessList = array (
					$accessList 
			);
		}
		foreach ( $accessList as $accessName ) {
			if (! $this->isResourceAccess ( $resourceName, $accessName )) {
				$resourceAccess = new AclResourcesAccess ();
				$resourceAccess->resources_name = $resourceName;
				$resourceAccess->access_name = $accessName;
				$resourceAccess->save ();
			}
		}
		return true;
	}
	public function dropResourceAccess($resourceName, $accessList) {
		if (! is_array ( $accessList ) && ! is_string ( $accessList )) {
			throw new \Exception ( "Invalid value for accessList" );
		}
		if (! is_array ( $accessList )) {
			$accessList = array (
					$accessList 
			);
		}
		foreach ( $accessList as $accessName ) {
			if ($resourceAccess = $this->isResourceAccess ( $resourceName, $accessName )) {
				$resourceAccess->delete ();
			}
		}
	}
	public function dropAccessList($roleName,$resourceName,$accessName){
		if($access = $this->accessExists($roleName, $resourceName, $accessName)){
			return $access->delete();
		}
	}
	protected function accessExists($roleName, $resourceName, $accessName) {
		return AclAccessList::findFirst(array(
				'roles_name = ?0 AND resources_name = ?1 AND access_name = ?2',
				'bind' => array($roleName,$resourceName,$accessName)
		));
	}
	protected function _allowOrDeny($roleName, $resourceName, $access, $action) {
		if (! $this->isRole ( $roleName ) && $roleName != '*') {
			throw new \Exception ( "Role '$roleName' does not exist in ACL" );
		}
		if (! $this->isResource ( $resourceName ) && $resourceName != '*') {
			throw new \Exception ( "Resource '$resourceName' does not exist in ACL" );
		}
		if (! is_array ( $access )) {
			$access = array (
					$access 
			);
		}
		foreach ( $access as $accessName ) {
			if (! $this->isResourceAccess ( $resourceName, $accessName ) && $accessName != '*') {
				throw new \Exception ( "Access '$accessName' does not exist in resource '$resourceName'" );
			}
			
			if ($accessList = $this->accessExists ( $roleName, $resourceName, $accessName )) {
				// UPDATE
				$accessList->allowed = (int)$action ;
				$success = $accessList->update();
			} else {
				// INSERT
				$accessList = new AclAccessList();
				$accessList->roles_name = $roleName ;
				$accessList->resources_name = $resourceName;
				$accessList->access_name = $accessName;
				$accessList->allowed = (int)$action;
				$success = $accessList->save();
			}
			
			if ($accessName != '*' && ! $this->accessExists ( $roleName, $resourceName, '*' )) {
				$accessList = new AclAccessList();
				$accessList->roles_name = $roleName;
				$accessList->resources_name = $resourceName;
				$accessList->access_name = '*' ;
				$accessList->allowed = (int)$this->_defaultAccess;
			}
		}
	}
	public function allow($roleName, $resourceName, $access) {
		$this->_allowOrDeny ( $roleName, $resourceName, $access, \Phalcon\Acl::ALLOW );
	}
	public function deny($roleName, $resourceName, $access) {
		$this->_allowOrDeny ( $roleName, $resourceName, $access, \Phalcon\Acl::DENY );
	}
	public function isAllowed($roleName, $resourceName, $access) {
		$this->_activeRole = $roleName;
		$this->_activeResource = $resourceName;
		$this->_activeAccess = $access;
		if ($this->_eventsManager != null) {
			if ($this->_eventsManager->fire ( "acl:beforeCheckAccess", $this ) === false) {
				return false;
			}
		}
		// strict comparison
		/*
		 * if($this->strictResourceComparison()){
		 * if(!$this->isResourceAccess($resourceName, $access))return false ;
		 * }
		 */
		
		// if role doesn't exist return defaultAccess
		if (! $this->isRole ( $roleName ))
			return ($this->_defaultAccess == \Phalcon\Acl::ALLOW);
		$sql = "SELECT allowed FROM acl_access_list WHERE (roles_name = '*' OR roles_name IN(
				SELECT name FROM acl_roles WHERE name = ?
				UNION SELECT roles_inherits FROM acl_roles_inherits WHERE roles_name = ?))
				AND resources_name IN(?,'*') AND access_name IN (?,'*')
				ORDER BY allowed DESC LIMIT 1";
		$allowed = $this->getDI () -> get ( 'db' )->fetchOne ( $sql, \Phalcon\Db::FETCH_NUM, array (
				$roleName,
				$roleName,
				$resourceName,
				$access 
		) );
		
		$allowed = is_array ( $allowed ) ? (( int ) $allowed [0] == \Phalcon\Acl::ALLOW) : ($this->_defaultAccess == \Phalcon\Acl::ALLOW);
		$this->_accessGranted = $allowed;
		if ($this->_eventsManager != null) {
			$this->_eventsManager->fire ( "acl:afterCheckAccess", $this );
		}
		return $allowed;
	}
	public function getRoles() {
		return AclRoles::find ()->toArray ();
	}
	public function getResources() {
		return AclResources::find ()->toArray ();
	}
	public function getRolesInherits() {
		$rolesInherits = AclRolesInherits::find ();
		$inherits = array ();
		foreach ( $rolesInherits as $role ) {
			$inherits [$role->roles_name][] = $role->roles_inherits;
		}
		$rolesInherits = null;
		return $inherits;
	}
}