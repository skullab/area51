<?php

namespace Thunderhawk\API\Engine;

use Thunderhawk\API\Exception\BaseException;

/**
 * Engine Exception class
 * 
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *        
 */
class Exception extends BaseException {
	protected function defineExtra(array $extra) {
		return $extra;
	}
	protected function defineMessages(array $messages, array $extra) {
		$messages [10] = 'Engine is already instantiated';
		$messages [20] = 'Invalid module "%s" ! Module.php doesn\'t found';
		$messages [30] = 'Invalid manifest for module "%s" ! Manifest.xml doesn\'t found';
		$messages [40] = 'The controller "%s" MUST extend "Thunderhawk\API\Mvc\Controller"';
		$messages [50] = 'The module "%s" is already registered';
		$messages [55] = 'The module "%s" MUST extend "Thunderhawk\API\Adapters\Module"';
		return $messages;
	}
}