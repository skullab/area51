<?php

namespace Thunderhawk\API\Dispatcher;

class Listener {
	public function beforeDispatchLoop($event,$dispatcher){}
	public function beforeDispatch($event,$dispatcher){
		echo '<table>
				<tr><td>Module</td><td>'.$dispatcher->getModuleName().'</td></tr>
				<tr><td>Namespace</td><td>'.$dispatcher->getNamespaceName().'</td></tr>
				<tr><td>Controller</td><td>'.$dispatcher->getControllerName().'</td></tr>
				<tr><td>Action</td><td>'.$dispatcher->getActionName().'</td></tr>
				</table>';
	}
	public function beforeExecuteRoute($event,$dispatcher){}
	public function beforeNotFoundAction($event,$dispatcher){}
	public function beforeException($event,$dispatcher,$exception){
		if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
		switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                	var_dump('controller not found');
                	$dispatcher->forward(array(
                		'controller' => 'index',
                		'action'	=> 'show404'
                	));
                	return false;
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                	var_dump('action not found');
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