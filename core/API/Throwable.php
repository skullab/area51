<?php

namespace Thunderhawk\API;

/**
 * Base Interface to throw an Exception
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *
 */
interface Throwable {
	/**
	 * Generate and throw an Exception
	 * 
	 * @param string $message The exception message
	 * @param int $code The Exception code.
	 * @param Exception $previous The previous exception used for the exception chaining.
	 * @return void
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Throwable::throwException()
	 */
	public function throwException($message = null , $code = 0 , \Exception $previous = null);
}