<?php

namespace Vendor\Backend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Assets\Manager as AssetsManager;
use Phalcon\Assets\Filters\Cssmin;
class IndexController extends Controller {
	public function onInitialize() {
		$this->assets->requireCollection ( 'css-header', AssetsManager::RESOURCE_CSS, array (
				'gfonts.css',
				'bootstrap.css',
				'materialadmin.css',
				'font.awesome.min.css',
				'material-design-iconic-font.css',
				'morris.core.css'
		), true, array (
				new Cssmin ()
		) );
	}
}