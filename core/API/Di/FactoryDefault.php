<?php

namespace Thunderhawk\API\Di;
use Phalcon\Di\FactoryDefault as PhalconFactoryDefault;
use Thunderhawk\API\Di\Service\Manager as ServiceManager;
use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Config as ConfigArray;

class FactoryDefault extends PhalconFactoryDefault{
	public function initializeServices(){
		$this->set(ServiceManager::CONFIG,function () {
			$files = array(
					'dirs' => CORE_PATH . 'config/dirs.ini.php',
					'db' => CORE_PATH . 'config/db.ini.php',
					'smtp' => CORE_PATH . 'config/smtp.ini.php'
			);
			$app = new ConfigIni ( CORE_PATH . 'config/app.ini.php');
			foreach ($files as $file){
				if(file_exists($file)){
					$c = new ConfigIni($file);
					$app->merge($c);
				}
			}
			//$dirs = new ConfigIni ( $files['dirs'] );
			//$db = new ConfigIni ( CORE_PATH . 'config/db.ini.php' );
			//$smtp = new ConfigIni ( CORE_PATH . 'config/smtp.ini.php' );
			
			
			if(file_exists(CORE_PATH.'config/modules.ser')){
				$modulesInstalled = unserialize(file_get_contents(CORE_PATH.'config/modules.ser'));
			}else{
				require (CORE_PATH . 'config/modules.php');
			}
			$modules = new ConfigArray ( $modulesInstalled );
			//file_put_contents(CORE_PATH.'config/modules.ser', serialize($modulesInstalled));
			//$app->merge ( $dirs );
			//$app->merge ( $db );
			$app->merge ( $modules );
			//$app->merge ( $smtp );
			return $app;
		},true);
		//require CORE_PATH . 'core/config/services.php';
		require CORE_PATH . 'config/services.php';
		foreach ($services as $service => $function){
			$this->set($service,$function,true);
		}
	}
}