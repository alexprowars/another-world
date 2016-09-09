<?php

namespace Sky\Core;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use Sky\Core\Assets\Manager as AssetManager;
use Phalcon\Cache\Frontend\Data as FrontendCache;
use Phalcon\Cache\Backend\Memory as BackendCache;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Model\Manager as ModelsManager;

class Installer
{
	private $_di;

	public function __construct (Di $di)
	{
		$this->_di = $di;
	}

	public function run ()
	{
		$this->initServices();
	}

	private function initServices ()
	{
		$loader = $this->_di->get('loader');

		$namespaces = [
			__NAMESPACE__.'\Controllers' => ROOT_PATH.'/app/modules/Core/Controllers'
		];

		$loader->registerNamespaces($namespaces, true);
		$loader->register();

		$router = new Router(false);
		$router->removeExtraSlashes(true);

		$router->add('/', [
			'controller' 	=> 'install',
			'action' 		=> 'index',
		]);

		$router->add('/:action/:params', [
			'controller' 	=> 'install',
			'action' 		=> 1,
			'params' 		=> 2
		]);

		$this->_di->set('router', $router, true);

		$view = new View();
		$view->setMainView('install');
		$view->setViewsDir(ROOT_PATH.'/app/modules/Core/Views');

		$view->registerEngines([".volt" => function ($view, $di)
		{
			$volt = new Volt($view, $di);

			$volt->setOptions([
				'compiledPath'		=> ROOT_PATH.'/app/cache/views/',
				'compiledSeparator'	=> '-',
				'compiledExtension'	=> '.cache'
			]);

			return $volt;
		}]);

		$this->_di->set('view', $view, true);

		$dispatcher = new Dispatcher();
		$dispatcher->setDefaultNamespace('Sky\Core\Controllers');

		$this->_di->set('dispatcher', $dispatcher, true);

		$this->_di->set('assets', new AssetManager(), true);

		$frontCache = new FrontendCache();
		$cache = new BackendCache($frontCache);

		$this->_di->set('cache', $cache, true);

		$url = new UrlResolver();
		$url->setStaticBaseUri('/');
		$url->setBaseUri('/');

		$this->_di->set('url', $url, true);

		$session = new \Phalcon\Session\Adapter\Files();
		$session->start();

		$this->_di->set('session', $session, true);

		$flash = new FlashSession([
			'error' 	=> 'alert alert-danger',
			'success' 	=> 'alert alert-success',
			'warning' 	=> 'alert alert-warning',
			'notice' 	=> 'alert alert-info',
		]);

		$this->_di->set('flashSession', $flash, true);

		$modelsManager = new ModelsManager();

		$this->_di->set('modelsManager', $modelsManager, true);

		$metaData = new \Phalcon\Mvc\Model\MetaData\Memory();

		$this->_di->set('modelsMetadata', $metaData, true);
	}
}