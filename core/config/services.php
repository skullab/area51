<?php
use Thunderhawk\API\Di\Service\Manager as ServiceManager ;
/********************************************************/
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use Thunderhawk\API\Manifest\Manager as ManifestManager;
use Thunderhawk\API\Assets\Manager as AssetsManager;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Thunderhawk\API\Component\Acl;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Thunderhawk\API\Component\Token;
use Thunderhawk\API\Component\Ui;
use Thunderhawk\API\Http\Response\Cookies;
use Phalcon\Crypt;
use Thunderhawk\API\Component\Translator;
use Thunderhawk\API\Component\Auth;
use Thunderhawk\API\Component\Mail;
use Thunderhawk\API\Mvc\View\Helper\Model as HelperModel;
use Thunderhawk\API\Component\Mail\SimpleMail;
/********************************************************/
$config = $this->get(ServiceManager::CONFIG);
$services = array(
		ServiceManager::ROUTER => function()use($config){
			$router = new Router();
			return $router;
		},
		ServiceManager::DISPATCHER => function()use($config){
			$dispatcher = new Dispatcher ();
			return $dispatcher;
		},
		ServiceManager::URL => function()use($config){
			$url = new UrlProvider ();
			$url->setBaseUri ( $config->app->base->uri );
			$url->setStaticBaseUri ( $config->app->base->staticUri );
			return $url;
		},
		ServiceManager::VIEW => function()use($config){
			$view = new View ();
			$view->setBasePath ( CORE_PATH);
			$view->model = new HelperModel();
			return $view;
		},
		ServiceManager::VOLT => function($view,$di)use($config){
			$volt = new Volt ( $view, $di );
			$options = array (
					'compiledPath' => $config->dirs->core->cache->volt,
					'compiledExtension' => $config->app->volt->compiledExtension,
					'compiledSeparator' => $config->app->volt->compiledSeparator,
					'stat' => ( bool ) $config->app->volt->stat,
					'compileAlways' => ( bool ) $config->app->volt->compileAlways,
					'prefix' => $config->app->volt->prefix,
					'autoescape' => ( bool ) $config->app->volt->autoescape
			);
			$volt->setOptions ( $options );
			require CORE_PATH.'config/volt.functions.php' ;
			foreach ($voltFunctions as $macro => $function){
				$volt->getCompiler()->addFunction($macro,$function);
			}
			/*$volt->getCompiler()->addFunction('_',function(){
				if(count(func_get_args()) == 3){
					return ngettext(func_get_arg(0), func_get_arg(1),func_get_arg(2));
				}else{
					return gettext(func_get_arg(0));
				}
			});
			$volt->getCompiler()->addFunction('gettext','gettext');*/
			return $volt;
		},
		ServiceManager::MANIFEST_MANAGER => function()use($config){
			$manifestManager = new ManifestManager ();
			return $manifestManager;
		},
		ServiceManager::THEME => function()use($config){
			return $config->app->theme;
		},
		ServiceManager::ASSETS => function()use($config){
			$assets = new AssetsManager ();
			$assets->setBasePath ( $config->app->base->staticUri );
			$assets->setAssetsDir ( 'assets/' );
			return $assets;
		},
		ServiceManager::FLASH => function()use($config){
			$flash = new FlashDirect ( array (
					'error' => 'alert alert-danger',
					'success' => 'alert alert-success',
					'notice' => 'alert alert-info',
					'warning' => 'alert alert-warning'
			) );
			return $flash;
		},
		ServiceManager::FLASH_SESSION => function()use($config){
			$flashSession = new FlashSession(array (
					'error' => 'alert alert-danger',
					'success' => 'alert alert-success',
					'notice' => 'alert alert-info',
					'warning' => 'alert alert-warning'
			) );
			return $flashSession ;
		},
		ServiceManager::ACL => function()use($config){
			$acl = new Acl();
			$acl->setDefaultAction(\Phalcon\Acl::DENY);
			return $acl;
		},
		ServiceManager::SESSION => function()use($config){
			$session = new SessionAdapter();
			$session->start();
			return $session ;
		},
		ServiceManager::TOKEN => function()use($config){
			$token = new Token();
			return $token ;
		},
		ServiceManager::UI => function()use($config){
			return new Ui();
		},
		ServiceManager::DB => function()use($config){
			$dbConfig = ( array ) $config->db;
			$dbAdapter = 'Phalcon\Db\Adapter\PDO\\' . $dbConfig ['adapter'];
			unset ( $dbConfig ['adapter'] );
			unset ( $dbConfig ['table'] );
			$db = new $dbAdapter ( $dbConfig );
			return $db;
		},
		ServiceManager::REMOTE_DB => function()use($config){
			$dbConfig = (array) $config->remotedb ;
			$dbAdapter = 'Phalcon\Db\Adapter\PDO\\' . $dbConfig['adapter'];
			unset ( $dbConfig ['adapter'] );
			unset ( $dbConfig ['table'] );
			$remotedb = new $dbAdapter($dbConfig);
			return $remotedb ;
		},
		ServiceManager::COOKIES => function()use($config){
			$cookies = new Cookies();
			$cookies->useEncryption(true);
			return $cookies ;
		},
		ServiceManager::CRYPT => function()use($config){
			$crypt = new Crypt();
			$crypt->setKey($config->app->crypt->salt);
			return $crypt;
		},
		ServiceManager::TRANSLATOR => function()use($config){
			$translator = new Translator();
			$translator->setLocalePath(APP_PATH.'core/locale');
			$translator->bindDomains(array('messages','errors'),'UTF-8');
			return $translator ;
		},
		ServiceManager::AUTH => function()use($config){
			return new Auth();
		},
		ServiceManager::MAIL => function()use($config){
			$mail = new SimpleMail((array)$config->smtp);
			return $mail ;
		}
		
);