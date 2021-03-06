<?php

namespace Thunderhawk\API\Engine;

interface EngineInterface {
	public function run();
	public function getService($name);
	public function isRegisteredModule($moduleName);
	public function getModuleDefinition($moduleName);
	public function getDbPrefix();
	public function getBaseUri();
	public function getConfig();
	public function getVersion();
}