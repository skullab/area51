<?php

namespace Thunderhawk\API;

use Thunderhawk\API\Permission\PermissionInterface;

/**
 * Permission class
 * 
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *        
 */
class Permission implements PermissionInterface,Throwable {
	protected $_serviceName;
	public function __construct($serviceName) {
		$this->setServiceName($serviceName);
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Permission\PermissionInterface::getServiceName()
	 */
	public function getServiceName() {
		return $this->_serviceName;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\API\Permission\PermissionInterface::setServiceName()
	 */
	public function setServiceName($serviceName) {
		if($this->_serviceName != null)$this->throwException(null,100);
		$this->_serviceName = ( string ) $serviceName;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\API\Throwable::throwException()
	 */
	public function throwException($message = null, $code = 0, Exception $previous = null) {
		throw new Permission\Exception($message,$code,$previous);

	}

}