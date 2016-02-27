<?php

namespace Thunderhawk\API\Component;
use Phalcon\Mvc\User\Component;
class Ui extends Component {
	
	public function getMenu(){
		$identity = $this->auth->getIdentity();
		if($identity){
			if($identity['role'] == Auth::ROLE_ADMIN){
				$this->view->partial('menus/admin/menubar');
			}
		}
	}
	
	public function __get($name){
		if($this->getDI()->has($name))return parent::__get($name);
		return $this->{'get'.\Phalcon\Text::camelize($name)}() ;
	}
}