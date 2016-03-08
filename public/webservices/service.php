<?php
use Skullab\Rest\Server;
/*************************************/
$debug = true ;
/*************************************/
if($debug){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
/*************************************/
class User {
	public function get($id,$name){
		return array('id'=>$id,'name'=>$name);
	}
	public function set($id,$name){
		$this->name = $name ;
		return true ;
	}
}
require 'Rest/Server.php';
$server = new Server();
$server->addPattern('/user/:action/:params', array(
		'controller' => 'user',
		'action' => 1,
		'params' => 2
));
$server->handle()->sendResponse();