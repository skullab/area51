<?php

namespace Thunderhawk\API\Component;
use Phalcon\Mvc\User\Component;

class Ui extends Component {
	public function getMenu() {
		// ADMINISTRATOR
		if ($this->auth->isRoleOrInherits ( Auth::ROLE_ADMIN )) {
			$this->view->partial ( 'menus/admin/category' );
			$this->view->partial ( 'menus/admin/users' );
			$this->view->partial ( 'menus/admin/acl' );
			//$this->view->partial ( 'menus/admin/languages' );
		}
		// COUNTRIES
		if($this->auth->isRoleOrInherits(Auth::ROLE_USER)){
			$this->view->partial('menus/countries/category');
			$this->view->partial('menus/countries/country');
		}
		// LEAD TIME
		if($this->auth->isRoleOrInherits(Auth::ROLE_USER)){
			$this->view->partial('menus/lead_time/category');
			$this->view->partial('menus/lead_time/time');
		}
		// PRODUCTS
		if($this->auth->isRoleOrInherits(Auth::ROLE_USER)){
			$this->view->partial('menus/products/category');
			$this->view->partial('menus/products/price_lists');
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