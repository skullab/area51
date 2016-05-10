<?php

namespace Thunderhawk\API\Component;
use Phalcon\Mvc\User\Component;

class Ui extends Component {
	public function getMenu() {
		// ADMINISTRATOR
		if ($this->auth->isRole ( Auth::ROLE_ADMIN )) {
			$this->view->partial ( 'menus/admin/category' );
			$this->view->partial ( 'menus/admin/users' );
			$this->view->partial ( 'menus/admin/acl' );
			//$this->view->partial ( 'menus/admin/languages' );
		}
		// COUNTRIES
		if($this->auth->isRoleOrInherits(Auth::ROLE_USER)){
			$this->view->partial('menus/countries/category');
			$this->view->partial('menus/countries/italy');
		}
		// PRODUCTS
		if($this->auth->isRoleOrInherits(Auth::ROLE_USER)){
			$this->view->partial('menus/products/category');
			$this->view->partial('menus/products/list');
			$this->view->partial('menus/products/price_list');
		}
		// ORDER ENTRY
		if($this->auth->isRoleOrInherits(Auth::ROLE_USER)){
			$this->view->partial ('menus/b2b/category');
			$this->view->partial ('menus/b2b/showcase');
			$this->view->partial ('menus/b2b/registry');
			$this->view->partial ('menus/b2b/promotions');
			$this->view->partial ('menus/b2b/orders');
		}
		
	}
	public function __get($name) {
		if ($this->getDI ()->has ( $name ))
			return parent::__get ( $name );
		return $this->{lcfirst ( \Phalcon\Text::camelize ( "get_$name" ) )} ();
	}
}