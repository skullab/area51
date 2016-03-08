<?php

use Skullab\Rest\Client;
/*************************************/
$debug = true ;
/*************************************/
if($debug){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
/*************************************/
//require '../core/API/Engine.php';
//use Thunderhawk\API\Engine;
//$engine = new Engine ();
//$engine->runBootstrap('boot.php');

/*$config = array(
	'host' => '81.29.208.233',
	'dbname' => 'gc2110008_pr0v',
	'username' => 'gc2110008_pr0v',
	'password' => 'asAOYAN0Ti'
);

$db = new Phalcon\Db\Adapter\Pdo\Mysql($config);
$products = $db->fetchAll('SELECT * FROM ps_product');
//$f = fopen('product.csv','w');
foreach ($products as $product){
	var_dump($product);
}
//fclose($f);*/
require 'webservices/Rest/Client.php';
$client = new Client();
$client->setBaseUri('http://127.0.0.1/area51/webservices');
$client->sendGET('user/get/1/bob');


