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

/*$to      = 'ivan.maruca@gmail.com';
$subject = 'request password change';
$message = '<a href="http://areariservata.iprovenzali.it/area51">click here</a>';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: assistenza@areariservata.iprovenzali.it' . "\r\n" .
			'Reply-To: assistenza@areariservata.iprovenzali.it' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

$esito = mail($to, $subject, $message, $headers);
var_dump($esito);*/




