<?php

namespace Sky\Core\Application;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use DirectoryIterator;
use PDO;
use Phalcon\Crypt;
use Phalcon\DiInterface;
use Phalcon\Exception;
use Phalcon\Http\Response\Cookies;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Tag;
use Phalcon\Text;
use Sky\Core\Access\Auth;
use Sky\Core\Access\Security;
use Sky\Core\Lang;
use Sky\Core\Prophiler;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Cache\Frontend\Data as FrontendCache;
use Sky\Core\Prophiler\Plugin\Phalcon\Mvc\RouterPlugin;
use Sky\Core\Router\Annotations as Router;
use Phalcon\Mvc\Url as UrlResolver;
use Sky\Core\Assets\Manager as AssetManager;
use Phalcon\Flash\Session as FlashSession;

trait Services
{
	/**
	 * @var \stdClass|\Phalcon\Config
	 */
	private $_config;

	/**
	 * @param $di DiInterface
	 * @return \Phalcon\Loader
	 */
	protected function initLoaders ($di)
	{
		$namespaces = [];

		$modules = $this->getModules();

		foreach ($modules as $code => $data)
		{
			$moduleName = ucfirst($code);

			$namespace = str_replace('\Module', '', $data['className']);

			$namespaces[$namespace] = ROOT_PATH.$this->_config->application->baseDir.'modules/'.$moduleName.'/Classes';
			$namespaces[$namespace.'\Controllers'] = ROOT_PATH.$this->_config->application->baseDir.'modules/'.$moduleName.'/Controllers';
		}

		$loader = $di->get('loader');

		$loader->registerNamespaces($namespaces, true);
		$loader->register();

		return $loader;
	}

	/**
	 * @param $di DiInterface
	 * @param $eventsManager
	 * @throws Exception
	 */
	protected function initDatabase ($di, $eventsManager)
	{
		if ($di->has('db'))
			return;

		if (!$this->_config->offsetExists('database'))
			throw new Exception('No configuration for a database');

		if (!defined('DB_PREFIX'))
			define('DB_PREFIX', $this->_config->database->prefix);

		if (strpos($this->_config->database->adapter, '\\') === false)
			$adapter = '\Phalcon\Db\Adapter\Pdo\\'.ucfirst($this->_config->database->adapter);
		else
			$adapter = trim($this->_config->database->adapter);

		$connection = new $adapter([
			'host'			=> $this->_config->database->host,
			'username'		=> $this->_config->database->username,
			'password' 		=> $this->_config->database->password,
			'dbname'		=> $this->_config->database->dbname,
			'options'		=> [PDO::ATTR_PERSISTENT => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
		]);

		$di->set('db', $connection, true);

		$modelsManager = new ModelsManager();
		$modelsManager->setEventsManager($eventsManager);

		$di->set('modelsManager', $modelsManager, true);

		if ($this->_config->offsetExists('metadata') && $this->_config->metadata->offsetExists('adapter'))
			$metadataAdapter = '\Phalcon\Mvc\Model\Metadata\\' . $this->_config->metadata->adapter;
		else
			$metadataAdapter = '\Phalcon\Mvc\Model\MetaData\Memory';

		$metaData = new $metadataAdapter($this->_config->metadata->toArray());

		$di->set('modelsMetadata', $metaData, true);

		Model::setup([
			'events' 			 => true,
			'columnRenaming' 	 => false,
			'notNullValidations' => false,
			'virtualForeignKeys' => true
		]);
	}

	/**
	 * @param $di DiInterface
	 * @throws Exception
	 */
	public function initCache ($di)
	{
		if ($di->has('cache'))
			return;

		if (!$this->_config->offsetExists('cache') || !$this->_config->cache->offsetExists('adapter'))
			throw new Exception('No configuration for a cache');

		$frontCache = new FrontendCache(["lifetime" => $this->_config->cache->lifetime]);

		$cacheAdapter = '\Phalcon\Cache\Backend\\'.ucfirst($this->_config->cache->adapter);

		$cache = new $cacheAdapter($frontCache, $this->_config->cache->toArray());

		if ($di->has('profiler'))
		{
			$profiler = $di->get('profiler');

			$cache = new Prophiler\Decorator\Phalcon\Cache\BackendDecorator($cache, $profiler);
		}

		$di->set('cache', $cache, true);
	}

	/**
	 * @param $di DiInterface
	 * @param $eventsManager EventsManager
	 * @return Router
	 */
	protected function initRouters ($di, $eventsManager)
	{
		$registry = $di->get('registry');

		$router = new Router(true);

		$router->setDI($di);
		$router->removeExtraSlashes(false);
		$router->setDefaultModule('core');
		$router->setDefaultController("index");
		$router->setDefaultController("index");
		$router->setEventsManager($eventsManager);

		if ($di->has('profiler'))
			$eventsManager->attach('router', RouterPlugin::getInstance($di->getShared('profiler')));

		foreach ($registry->modules as $module)
		{
			if ((int) $module['active'] != 1)
				continue;

			$moduleName = ucfirst($module['code']);

			if (file_exists($registry->directories->modules.$moduleName."/routes.php"))
				/** @noinspection PhpIncludeInspection */
				include_once($registry->directories->modules.$moduleName."/routes.php");
		}

		$di->set('router', $router, true);

		return $router;
	}

	/**
	 * @param $di DiInterface
	 * @param $eventManager EventsManager
	 * @return null|Prophiler\Profiler
	 */
	protected function initProfiler ($di, $eventManager)
	{
		$profiler = null;

		if ($this->_config->application->prophiler && $di->has('view'))
		{
			$profiler = new Prophiler\Profiler();
			$profiler->addAggregator(new Prophiler\Aggregator\Database\QueryAggregator());

			if ($di->has('cache'))
				$profiler->addAggregator(new Prophiler\Aggregator\Cache\CacheAggregator());

			$di->set('profiler', $profiler, true);

			/** @noinspection PhpUnusedParameterInspection */
			$eventManager->attach('application:afterStartModule', function ($event, $application) use ($profiler)
			{
				$pluginManager = new Prophiler\Plugin\Manager\Phalcon($profiler);
				$pluginManager->register();
			});
		}

		return $profiler;
	}

	/**
	 * @param $di DiInterface
	 * @param $eventManager EventsManager
	 */
	protected function initDispatcher ($di, $eventManager)
	{
		$eventManager->attach('dispatch:beforeExecuteRoute', new Security);

		$dispatcher = new Dispatcher();
		$dispatcher->setEventsManager($eventManager);

		$di->set('dispatcher', $dispatcher, true);
	}

	/**
	 * @param $di DiInterface
	 * @return mixed|null
	 */
	protected function initFlash ($di)
	{
		$flash = new FlashSession([
			'error' 	=> 'alert alert-danger',
			'success' 	=> 'alert alert-success',
			'warning' 	=> 'alert alert-warning',
			'notice' 	=> 'alert alert-info',
		]);

		$di->set('flashSession', $flash, true);

		return $flash;
	}

	/**
	 * @param $di DiInterface
	 * @return \Phalcon\Session\Adapter
	 */
	protected function initSession ($di)
	{
		if (!$this->_config->offsetExists('session') || !$this->_config->session->offsetExists('adapter'))
			$sessionAdapter = 'Phalcon\Session\Adapter\Files';
		else
			$sessionAdapter = 'Phalcon\Session\Adapter\\'.ucfirst($this->_config->session->adapter);

		/**
		 * @var $session \Phalcon\Session\Adapter
		 */
		$session = new $sessionAdapter($this->_config->session->toArray());
		$session->start();

		$di->set('session', $session, true);

		return $session;
	}

	/**
	 * @param $di DiInterface
	 * @param $eventManager EventsManager
	 * @return View
	 */
	protected function initView ($di, $eventManager)
	{
		$url = new UrlResolver();
		$url->setStaticBaseUri('/');
		$url->setBaseUri($this->_config->application->baseUri);

		$di->set('url', $url, true);
		$di->set('assets', new AssetManager(), true);

		$view = new View();
		$view->setEventsManager($eventManager);

		if (!is_dir(ROOT_PATH.$this->_config->application->baseDir.$this->_config->application->cacheDir.'views'))
			mkdir(ROOT_PATH.$this->_config->application->baseDir.$this->_config->application->cacheDir.'views');

		$config = $this->_config;

		$view->registerEngines([".volt" => function ($view, $di) use ($config, $eventManager)
		{
			$volt = new Volt($view, $di);

			$volt->setOptions([
				'compiledPath'		=> ROOT_PATH.$config->application->baseDir.$config->application->cacheDir.'views/',
				'compiledSeparator'	=> '-',
				'compiledExtension'	=> '.cache'
			]);

			$compiler = $volt->getCompiler();

			$compiler->addFunction('_text', '\Sky\Core\Lang::getText');
			$compiler->addFunction('plural', '\Sky\Core\Helpers::getPlural');
			$compiler->addFunction('option', '\Sky\Core\Options::get');

			$eventManager->fire('view:afterEngineRegister', $volt);

			return $volt;
		}]);

		$di->set('view', $view, true);

		return $view;
	}

	/**
	 * @param $di DiInterface
	 * @param $eventManager EventsManager
	 */
	protected function initEnvironment ($di, /** @noinspection PhpUnusedParameterInspection */$eventManager)
	{
		/*set_error_handler(function ($errorCode, $errorMessage, $errorFile, $errorLine)
		{
			throw new \ErrorException($errorMessage, $errorCode, 1, $errorFile, $errorLine);
		});*/

		if ($this->_config->application->debug)
		{
			ini_set('log_errors', 'On');
			ini_set('display_errors', 1);
			error_reporting(E_ALL);

			ini_set('error_log', ROOT_PATH.'/php_errors.log');
		}

		Lang::setLang($this->_config->app->language);

		$crypt = new Crypt();
		$crypt->setKey('fsdgdghrdfhgasdfsdqqwedf');

		$di->set('crypt', $crypt, true);

		if ($this->_config->offsetExists('annotations') && $this->_config->annotations->offsetExists('adapter'))
			$annotationsAdapter = '\Phalcon\Annotations\Adapter\\' . $this->_config->annotations->adapter;
		else
			$annotationsAdapter = '\Phalcon\Annotations\Adapter\Memory';

		$di->set('annotations', new $annotationsAdapter($this->_config->annotations->toArray()), true);

		$cookies = new Cookies();
		$cookies->useEncryption(false);

		$di->set('cookies', $cookies, true);
		$di->set('auth', new Auth(), true);
		$di->set('tag', new Tag(), true);

		$registry = $di->getShared('registry');

		if ($di->has('cache'))
			$cache = $di->getShared('cache');

		if (isset($cache))
			$resources = $cache->get('CORE_CONTROLLERS');
		else
			$resources = null;

		if (!is_array($resources))
		{
			$resources = [];

			foreach ($registry->modules as $module)
			{
				if ((int) $module['active'] != 1)
					continue;

				$moduleName = ucfirst($module['code']);

				if (!file_exists($registry->directories->modules.$moduleName.'/Controllers'))
					continue;

				$files = new DirectoryIterator($registry->directories->modules.$moduleName.'/Controllers');

				foreach ($files as $file)
				{
					if (!$file->isFile() || strpos($file->getFilename(), 'Controller.php') === false)
						continue;

					$resources[] = [
						'module'	=> strtolower($module['code']),
						'class'		=> ($module['namespace'] != '' ? $module['namespace'] : $moduleName).'\Controllers\\'.ucfirst(Text::lower(str_replace('Controller.php', '', $file->getFilename())))
					];
				}
			}

			if (isset($cache))
				$cache->save('CORE_CONTROLLERS', $resources, 3600);
		}

		$registry->controllers = $resources;
	}
}