<?php

namespace Thunderhawk\Modules\Frontend\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Assets\Filters\Cssmin;
class IndexController extends Controller {
	public function onInitialize(){
		$this->assets->collection('css-header')
		->setPrefix($this->assets->getFullCssPath())
		//->setTargetPath('css-header.css')
		->addCss('gfonts.css')
		->addCss('bootstrap.css')
		->addCss('materialadmin.css')
		->addCss('font.awesome.min.css')
		->addCss('material-design-iconic-font.css');
		//->join(true)
		//->addFilter(new Cssmin());
		
		$jsPath = APP_PATH.'public/'.$this->assets->getAssetsDir().$this->assets->getJsDir(); 
		if ($handle = opendir($jsPath)) {
			echo "Directory handle: $handle\n";
			echo "Entries:\n";
		
			/* This is the correct way to loop over the directory. */
			while (false !== ($entry = readdir($handle))) {
				if($entry != '.' && $entry != '..'){
					var_dump($entry);
				}
			}
			closedir($handle);
		}
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