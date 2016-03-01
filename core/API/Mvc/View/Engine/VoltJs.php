<?php

namespace Thunderhawk\API\Mvc\View\Engine;
use Phalcon\Mvc\View\Engine\Volt;
class VoltJs extends Volt{
	
	public function __construct(\Phalcon\Mvc\ViewBaseInterface $view, \Phalcon\DiInterface $di){
		parent::__construct($view,$di);
		require CORE_PATH . 'config/volt.functions.php';
		foreach ($voltFunctions as $macro => $function){
			$this->getCompiler()->addFunction($macro,$function);
		}
		$this->getCompiler()->setOptions(array(
				'stat' => true,
				'compileAlways' => true
		));
	}
}