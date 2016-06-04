<?php

namespace Thunderhawk\API\Mvc;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;
use Thunderhawk\API\Component\Settings;
use Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Engine;
abstract class Controller extends PhalconController{
	
	const USE_MODULE_VIEWS = 0 ;
	const USE_THEME_VIEWS = 1 ;
	protected $settings ;
	protected $cssPlugins ;
	protected $jsPlugins,$jsComponents,$jsControllers ;
	protected $engine ;
	
	public function initialize(){
		$this->session->start();
		$this->engine = Engine::getInstance();
		$this->tag->setDoctype(\Phalcon\Tag::HTML5);
		$this->tag->setTitleSeparator($this->config->app->titleSeparator);
		$this->tag->setTitle($this->config->app->title);
		$this->settings = Settings::findFirst(array(
				'namespace = ?0 AND controller = ?1',
				'bind' => array(
						$this->dispatcher->getNamespaceName(),
						$this->dispatcher->getControllerName()
				)
		));
		if($this->settings === false){
			$this->settings = new Settings();
		}
		$this->cssPlugins = $this->assets->collection('cssPlugins');
		$this->jsPlugins = $this->assets->collection('jsPlugins');
		$this->jsComponents = $this->assets->collection('jsComponents');
		$this->jsControllers = $this->assets->collection('jsControllers');
		$this->view->body_class = '' ;
		$this->onInitialize();
		
	}
	protected function getAssetsPackageTinycolor(){
		$this->jsPlugins->addJs('vendor/tinycolor/tinycolor.js');
	}
	protected function getAssetsPackageBootstrapMarkdown(){
		$this->cssPlugins->addCss('vendor/bootstrap-markdown/bootstrap-markdown.css');
		$this->jsPlugins->addJs('vendor/bootstrap-markdown/bootstrap-markdown.js');
		$this->jsPlugins->addJs('vendor/to-markdown/to-markdown.js');
	}
	protected function getAssetsPackageMultiSelect(){
		$this->cssPlugins->addCss('vendor/multi-select/multi-select.css');
		$this->jsPlugins->addJs('vendor/multi-select/jquery.multi-select.js');
		$this->jsComponents->addJs('js/components/multi-select.js');
	}
	protected function getAssetsPackageFullcalendar(){
		$this->cssPlugins->addCss('vendor/fullcalendar/fullcalendar.css');
		$this->jsPlugins->addJs('vendor/fullcalendar/fullcalendar.js');
	}
	protected function getAssetsPackageBootstrapTreeview(){
		$this->cssPlugins->addCss('vendor/bootstrap-treeview/bootstrap-treeview.css');
		$this->jsPlugins->addJs('vendor/bootstrap-treeview/bootstrap-treeview.min.js');
		$this->jsComponents->addJs('js/components/bootstrap-treeview.js');
	}
	protected function getAssetsPackageJqueryWizard(){
		$this->cssPlugins->addCss ( 'vendor/jquery-wizard/jquery-wizard.css' );
		$this->jsPlugins->addJs('vendor/jquery-wizard/jquery-wizard.js');
		$this->jsComponents->addJs('js/components/jquery-wizard.js');
	}
	protected function getAssetsPackageFormValidation(){
		$this->cssPlugins->addCss('vendor/formvalidation/formValidation.css');
		$this->jsPlugins->addJs('vendor/formvalidation/formValidation.js')
		->addJs('vendor/formvalidation/framework/bootstrap.js')
		->addJs('vendor/matchheight/jquery.matchHeight-min.js');
	}
	protected function getAssetsPackageSelect2(){
		$this->cssPlugins->addCss('vendor/select2/select2.min.css');
		$this->cssPlugins->addCss('vendor/bootstrap-select/bootstrap-select.css');
		$this->jsPlugins->addJs('vendor/bootstrap-select/bootstrap-select.js');
		$this->jsPlugins->addJs('vendor/select2/select2.min.js');
		$this->jsComponents->addJs('js/components/select2.js');
		$this->jsComponents->addJs('js/components/bootstrap-select.js');
	}
	protected function getAssetsPackageXEditable(){
		$this->cssPlugins->addCss('vendor/x-editable/x-editable.css');
		$this->jsPlugins->addJs('vendor/x-editable/bootstrap-editable.js');
	}
	protected function getAssetsPackageDatePicker(){
		$this->cssPlugins->addCss('vendor/fullcalendar/fullcalendar.css')
		->addCss('vendor/bootstrap-datepicker/bootstrap-datepicker.css')
		->addCss('vendor/bootstrap-touchspin/bootstrap-touchspin.css')
		->addCss('css/apps/calendar.css');
		$this->jsPlugins->addJs('vendor/moment/moment.min.js')
		->addJs('vendor/fullcalendar/fullcalendar.js')
		->addJs('vendor/jquery-selective/jquery-selective.min.js')
		->addJs('vendor/bootstrap-datepicker/bootstrap-datepicker.js')
		->addJs('vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js');
		$this->jsComponents->addJs('js/components/bootstrap-datepicker.js');
	}
	protected function getAssetsPackageEditableTable(){
		$this->cssPlugins->addCss('vendor/editable-table/editable-table.css');
		$this->jsPlugins->addJs('vendor/editable-table/mindmup-editabletable.js');
	}
	protected function getAssetsPackageSweetAlert(){
		$this->cssPlugins->addCss('vendor/bootstrap-sweetalert/sweet-alert.css');
		$this->jsPlugins->addJs('vendor/bootbox/bootbox.js')
		->addJs('vendor/bootstrap-sweetalert/sweet-alert.js');
		$this->jsComponents->addJs('js/components/bootbox.js')
		->addJs('js/components/bootstrap-sweetalert.js');
	}
	protected function getAssetsPackageToastr(){
		$this->cssPlugins->addCss('vendor/toastr/toastr.original.css');
		$this->jsPlugins->addJs('vendor/toastr/toastr.js');
	}
	protected function getAssetsPackageDataEditor(){
		$this->getAssetsPackageXEditable();
		$this->jsPlugins->addJs('vendor/dataEditor/dataEditor.js');
	}
	protected function getAssetsPackageDataTable(){
		$this->cssPlugins->addCss('vendor/datatables-bootstrap/dataTables.bootstrap.css')
		->addCss('vendor/datatables-fixedheader/dataTables.fixedHeader.css')
		->addCss('vendor/datatables-responsive/dataTables.responsive.css')
		->addCss('vendor/datatables-select/select.dataTables.min.css');
		$this->jsPlugins->addJs('vendor/datatables/jquery.dataTables.min.js')
		->addJs('vendor/datatables-fixedheader/dataTables.fixedHeader.js')
		->addJs('vendor/datatables-bootstrap/dataTables.bootstrap.js')
		->addJs('vendor/datatables-responsive/dataTables.responsive.js')
		->addJs('vendor/datatables-tabletools/dataTables.tableTools.js')
		->addJs('vendor/datatables-select/dataTables.select.min.js');
		$this->jsComponents->addJs('js/components/datatables.js');
	}
	protected function getAssetsPackageSpinner(){
		$this->cssPlugins->addCss('css/spinner.css');
		$this->jsPlugins->addJs('js/spinner.js');
	}
	public function assetsPackage($package){
		$func = "getAssetsPackage".\Phalcon\Text::camelize($package);
		return $this->{$func}() ;
	}
	public function getNamespaceName(){
		$this->dispatcher->getNamespaceName();
	}
	public function getModuleName(){
		return $this->dispatcher->getModuleName();
	}
	public function getControllerName(){
		return $this->dispatcher->getControllerName();
	}
	public function getActionName(){
		return $this->dispatcher->getActionName();
	}
	public function getResource(){
		return $this->getModuleName().':'.$this->getControllerName();
	}
	public function getModuleManifest(){
		return $this->manifestManager->getManifest($this->getModuleName());
	}
	public function switchViewsDir($mode){
		switch ($mode){
			case self::USE_THEME_VIEWS:
				$this->view->setViewsDir('themes/'.$this->theme->name.'/');
				$this->view->setPartialsDir($this->theme->partials.'/');
				//$this->view->setLayoutsDir($this->theme->layouts.'/');
				$this->view->setMainView($this->theme->main);
				break;
			case self::USE_MODULE_VIEWS:
				$this->view->setViewsDir('themes/'.$this->theme->name.'/'.
				$this->dispatcher->getModuleName().'/');
				$this->view->setPartialsDir('../'.$this->theme->partials.'/');
				//$this->view->setLayoutsDir($this->theme->layouts.'/');
				$this->view->setMainView('../'.$this->theme->main);
				
		}
	}
	public function pickModuleView($path){
		$this->switchViewsDir(self::USE_MODULE_VIEWS);
		$this->view->pick($path);
	}
	public function pickThemeView($path){
		$this->switchViewsDir(self::USE_THEME_VIEWS);
		$this->view->pick($path);
	}
	public function renderModuleView($controller,$action,array $params = array()){
		$this->switchViewsDir(self::USE_MODULE_VIEWS);
		$this->renderView($controller, $action,$params);
	}
	public function renderThemeView($controller,$action,array $params = array()){
		$this->switchViewsDir(self::USE_THEME_VIEWS);
		$this->renderView($controller, $action,$params);
	}
	public function renderView($controller,$action,array $params = array()){
		$view = clone $this->view;
		$view->render($controller,$action,$params);
	}
	public function startViewStream(){
		ob_start();
	}
	public function endViewStream($view){
		$content = ob_get_clean();
		$this->view->setContent($content);
		$this->view->partial($view);
	}
	public function show404Action(){
		if($this->auth->getIdentity()){
			$this->view->body_class = "page-error page-error-404 layout-full" ;
			$this->view->partial('errors/show404');
		}else{
			return $this->forward();
		}
	}
	public function forward($controller = 'index',$action = 'index',$params = array()){
		return $this->dispatcher->forward(array(
				'controller' => $controller,
				'action' => $action,
				'params' => $params
		));
	}
	public function redirect($uri,$static = true){
		$uri = $static ? $this->url->getStaticBaseUri().$uri : $uri ;
		return $this->response->redirect($uri);
	}
	public function route404Action(){}
	public function indexAction(){}
	public function beforeExecuteRoute(){
		$resource = $this->getResource();
		$action = $this->getActionName();
		$auth = $this->auth->getIdentity();
		if($auth){
			$role = $auth['role'] ;
			$this->view->display_name = $auth['display_name'];
			$this->view->identity = $auth ;
			$this->auth->updateOnlineUser();
		}else{
			$role = 'Guest' ;
		}
		
		try{
			$this->auth->checkIntegrity();
		}catch (Auth\Exception $e){
			$this->flashSession->error($e->getMessage());
			return $this->redirect();
		}
		return $this->perfomAcl($role, $resource, $action);
	}
	protected function perfomAcl($role,$resource,$action){
		if(!$this->acl->isAllowed($role,$resource,$action)){
			return $this->accessDenied($role, $resource, $action);
		}
	}
	protected function accessDenied($role,$resource,$action){
		if($this->auth->getIdentity()){
			$this->flash->error(_('You don\'t have permission to perform this operation'));
			return $this->forward();
		}
	}
	public function sendAjax($payload,$encode = true,array $headers = array()){
		$status      = 200;
		$description = 'OK';
		$contentType = 'application/json';
		$content     = $encode ? json_encode($payload) : $payload ;
		
		$response = new Response();
		
		$response->setStatusCode($status, $description);
		$response->setContentType($contentType, 'UTF-8');
		$response->setContent($content);
		
		// Set the additional headers
		foreach ($headers as $key => $value) {
			$response->setHeader($key, $value);
		}
		
		$this->view->disable();
		
		return $response;
	}
	protected function onInitialize(){}
}