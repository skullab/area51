<?php
/*************************************/
$debug = true ;
/*************************************/
if($debug){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
/*************************************/
require '../core/API/Engine.php';
use Thunderhawk\API\Engine;
$engine = new Engine ();
$engine->runBootstrap('boot.php');


