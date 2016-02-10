<?php
/***************************************************/
$time_start = microtime ( true );
function convert($size)
{
	$unit=array('b','kb','mb','gb','tb','pb');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
/***************************************************/
/*use Phalcon\Loader;
use Thunderhawk\API\Manifest\Manager as ManifestManager;
use Thunderhawk\API\Di\FactoryDefault;
use Phalcon\Config\Adapter\Ini as ConfigIni;
$loader = new Loader ();
$loader->registerNamespaces ( array (
		'Thunderhawk\API' => '../core/API' 
) )->register ();

define('APP_PATH',realpath('..').'/');
$di = new FactoryDefault();
$di->set('config',function(){
	$app = new ConfigIni(APP_PATH . 'core/config/app.ini.php');
	$dirs = new ConfigIni(APP_PATH.'core/config/dirs.ini.php');
	$db = new ConfigIni(APP_PATH . 'core/config/db.ini.php');
	$app->merge($dirs);
	$app->merge($db);
	return $app ;
},true);


$config = $di->get('config');
var_dump($config->db->adapter);*/
use Thunderhawk\API\Engine ;
use Thunderhawk\API\Adapter\Module;
use Phalcon\Events\Manager as EventsManager;
require '../core/API/Engine.php';
$engine = new Engine();
$ev = new EventsManager();
$engine->run();
/***************************************************/
$time_end = microtime ( true ) - $time_start;
echo '<div class="benchmark">';
echo "<h1>Time execution : $time_end seconds</h1>";
echo "<h1>Memory Usage : ".convert(memory_get_usage(true))."</h1>";
echo "<h1>Memory Peak : ".convert(memory_get_peak_usage(true))."</h1>";
echo '</div>';
/***************************************************/
