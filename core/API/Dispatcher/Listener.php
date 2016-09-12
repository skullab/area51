<?php

namespace Thunderhawk\API\Dispatcher;

class Listener {
	public function beforeDispatchLoop($event,$dispatcher){
		$dispatcher->setActionName(lcfirst(\Phalcon\Text::camelize($dispatcher->getActionName())));
	}
	public function beforeDispatch($event,$dispatcher){
		$auth = $dispatcher->getDI()->get('auth');
		$moduleName = $dispatcher->getModuleName();
		if(!$auth->getIdentity() && $moduleName == 'backend'){
			$response = $dispatcher->getDI()->get('response');
			$response->redirect();
			return false;
		}
	}
	public function beforeExecuteRoute($event,$dispatcher){}
	public function beforeNotFoundAction($event,$dispatcher){}
	public function beforeException($event,$dispatcher,$exception){
		if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
		switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                	// controller not found
                	$dispatcher->forward(array(
                		'controller' => 'index',
                		'action'	=> 'show404'
                	));
                	return false;
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                	// action not found
                	var_dump($dispatcher->getActionName());
                    $dispatcher->forward(array(
       					'controller' => $dispatcher->getControllerName(),
                        'action'=>'show404'
                    ));
                    return false;
            }
		}
		return true;
	}
	public function afterInitialize($event,$dispatcher){}
	public function afterExecuteRoute($event,$dispatcher){}
	public function afterDispatch($event,$dispatcher){}
	public function afterDispatchLoop($event,$dispatcher){}
}