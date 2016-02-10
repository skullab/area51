<?php

namespace Thunderhawk\API\Manifest;

interface ManagerInterface {
	/**
	 * Load a Manifest file and returns an Thunderhawk\API\Manifest instance.<br>
	 * The manifest file name must be "Manifest.xml" and must be a valid xml file.<br>
	 * This file must be placed on the root directory of the module.
	 * @param string $fmoduleName : The name of the Manifest's Module to load
	 */
	public function load($moduleName);
	/**
	 * Check if the manifest is loaded
	 * @param string $moduleName : The module name
	 * @return bool
	 */
	public function isLoaded($moduleName);
	/**
	 * Get the Manifest loaded by filename<br>
	 * If the Manifest is not loaded throw an Exception
	 * @param string $moduleName : The module name
	 * @return Manifest 
	 */
	public function getManifest($moduleName);
}