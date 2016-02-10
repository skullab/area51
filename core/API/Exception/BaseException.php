<?php

namespace Thunderhawk\API\Exception;

/**
 * Thunderhawk BaseException
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *
 */
abstract class BaseException extends \Exception {
	
	const CODE_UNKNOWN = 0 ;
	const CODE_MISSING = 10 ;
	
	protected $extra = array ();
	protected $messages = array (
			0 => 'Unknown exception' 
	);
	
	/**
	 * Exception constructor
	 * @param mixed $params : a string or an array to sprintf the defined message for specific code
	 * @param number $code : the Exception code
	 * @param \Exception $previous : optional previous Exception
	 */
	public function __construct($params = null, $code = 0, \Exception $previous = null) {
		$this->extra = $this->defineExtra ( $this->extra );
		$this->messages = $this->defineMessages ( $this->messages, $this->extra );
		if (! isset ( $this->messages [$code] ))
			$this->messages [$code] = 'Unknown exception';
		if ($params != null) {
			if (is_string ( $params )) {
				$retMess = sprintf ( $this->messages [$code], $params );
			}
			if (is_array ( $params )) {
				array_unshift ( $params, $this->messages [$code] );
				$retMess = call_user_func_array ( 'sprintf', $params );
			}
		} else {
			$retMess = $this->messages [$code];
		}
		
		parent::__construct ( $retMess, $code, $previous );
	}
	/**
	 * User define extra content
	 * (Not used in this version)
	 * @param array $extra
	 * @return MUST return the $extra array
	 */
	abstract protected function defineExtra(array $extra);
	/**
	 * User define messages
	 * @param array $messages
	 * @param array $extra
	 * @return MUST return the messages array
	 */
	abstract protected function defineMessages(array $messages, array $extra);
	
	/*
	 * (non-PHPdoc)
	 * @see Exception::__toString()
	 */
	public function __toString() {
		// return parent::__toString();
		$ref = new \ReflectionClass ( $this );
		return '\'' . $ref->getName () . '\' with message \'' . $this->message . '\' and code \'' . $this->code . '\'';
	}
}