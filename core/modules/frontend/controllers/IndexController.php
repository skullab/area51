<?php

namespace Thunderhawk\Modules\Frontend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Assets\Manager as AssetsManager;
use Phalcon\Mvc\View;
use Phalcon\Assets\Filters\Cssmin;
class IndexController extends Controller {
	public function onInitialize(){
		if(!isset($this->settings['test'])){
			var_dump('settings test doesnt exists');
			$this->settings->test = 'hello world' ;
			$this->settings->save();
		}
		var_dump($this->settings->test);
		/*if(!file_exists(APP_PATH.'public/assets/css/collections/css-header.css')){
			$this->assets->collection('css-header')
			//->setPrefix($this->assets->getFullCssPath())
			//->setPrefix(APP_PATH.'public/assets/css/')
			->setTargetPath(APP_PATH.'public/assets/css/collections/css-header.css')
			->setTargetUri('public/assets/css/collections/css-header.css')
			->addCss(APP_PATH.'public/assets/css/gfonts.css')
			->addCss(APP_PATH.'public/assets/css/bootstrap.css')
			->addCss(APP_PATH.'public/assets/css/materialadmin.css')
			->addCss(APP_PATH.'public/assets/css/font.awesome.min.css')
			->addCss(APP_PATH.'public/assets/css/material-design-iconic-font.css')
			->join(true)
			->addFilter(new Cssmin());
		}else{
			$this->assets->collection('css-header')
			->setPrefix($this->assets->getFullCssPath().'collections/')
			->addCss('css-header.css');
		}*/
		$this->assets->requireCollection('css-header',
				AssetsManager::RESOURCE_CSS,
				array(
						'gfonts.css',
						'bootstrap.css',
						'materialadmin.css',
						'font.awesome.min.css',
						'material-design-iconic-font.css'
						
				),true,
				array(new Cssmin()));
		/*$this->assets->requireCss('gfonts.css');
		$this->assets->requireCss('bootstrap.css');
		$this->assets->requireCss('materialadmin.css');
		$this->assets->requireCss('font.awesome.min.css');
		$this->assets->requireCss('material-design-iconic-font.css');*/
	}
	public function signAction() {
		if($this->request->isPost()){
			//do user auth
			$this->flash->error('username or password is wrong !');
		}else{
			$this->dispatcher->forward(array(
					'action' => 'index'
			));
			return false ;
		}
	}
}