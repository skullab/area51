<?php

namespace Thunderhawk\API\Di\Service;
use Thunderhawk\API\Engine;
/**
 * Service Manager class
 * @author Ivan Maruca - ivan[dot]maruca[at]gmail[dot]com
 *
 */
class Manager {
	const LOADER				= 'loader' ;
	const VIEW					= 'view' ;
	const URL					= 'url' ;
	const ROUTER				= 'router' ;
	const DISPATCHER			= 'dispatcher' ;
	const REQUEST				= 'request' ;
	const RESPONSE				= 'response';
	const ASSETS				= 'assets' ;
	const VOLT					= 'volt' ;
	const SESSION				= 'session' ;
	const COOKIES				= 'cookies';
	const FILTER				= 'filter';
	const FLASH					= 'flash';
	const FLASH_SESSION 		= 'flashSession';
	const EVENTS_MANAGER 		= 'eventsManager';
	const DB					= 'db';
	const SECURITY				= 'security';
	const CRYPT					= 'crypt';
	const TAG					= 'tag';
	const ESCAPER				= 'escaper';
	const ANNOTATIONS			= 'annotations';
	const MODELS_MANAGER		= 'modelsManager';
	const MODELS_METADATA		= 'modelsMetadata';
	const TRANSACTION_MANAGER	= 'transactionManager';
	const MODELS_CACHE			= 'modelsCache';
	const VIEWS_CACHE			= 'viewsCache';
	const MANIFEST_MANAGER		= 'manifestManager';
	const THEME					= 'theme' ;
	const CONFIG				= 'config';
	CONST ACL					= 'acl';
	CONST TOKEN					= 'token';
	const UI					= 'ui';
	const TRANSLATOR			= 'translator';
	const AUTH					= 'auth';
	const MAIL					= 'mail';
	
	public static function get($serviceName){
		return Engine::getInstance()->getService($serviceName);
	}
	public static function isService($serviceName){
		return Engine::getInstance()->isService($serviceName);
	}
}