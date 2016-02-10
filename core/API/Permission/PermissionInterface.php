<?php

namespace Thunderhawk\API\Permission;
/**
 * Permission interface
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *
 */
interface PermissionInterface {
	/**
	 * Return the service name for this permission
	 */
	public function getServiceName();
	/**
	 * Set the service name for this permission
	 * @param string $serviceName : The service name
	 */
	public function setServiceName($serviceName);
}