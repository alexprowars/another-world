<?php

namespace Sky\Core;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Version;
use Sky\Core\Application\Services;
use Sky\Core\Di;
use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\Application as PhalconApplication;
use Phalcon\Config\Adapter\Ini as Config;
use Phalcon\Registry;
use Phalcon\Text;
use Phalcon\Events\Manager as EventsManager;
use Sky\Core\Models\Module;
use Sky\Core\Helpers\Cache as CacheHelper;

include_once(ROOT_PATH."/app/functions.php");

/**
 * Class Application
 * @package Sky\Core
 * @property \stdClass|\Phalcon\Config _config
 */
class Application extends PhalconApplication
{
	use Services;

	protected $_loaders =
	[
		'cache',
		'environment',
		'routers',
		'flash',
		'session',
		'view',
		'dispatcher'
	];

	public function __construct()
	{
		if (!class_exists('\Phalcon\Version'))
			throw new \Exception('Phalcon extensions not loaded');

		$version = Version::getPart(Version::VERSION_MAJOR);

		if ($version < 3)
			throw new \Exception('Required Phalcon 3.0.0 and above');

		if (file_exists(ROOT_PATH.'/.installed'))
			define('INSTALLED', true);

		if (defined('INSTALLED'))
			$this->_config = new Config(ROOT_PATH . '/app/config/core.ini');
		else
			$this->_config = new \Phalcon\Config([
				 'application' => ['baseDir' => '/app/']
			]);

		$namespaces = [];
		$namespaces[__NAMESPACE__] = ROOT_PATH.$this->_config->application->baseDir.'modules/Core/Classes';
		$namespaces[__NAMESPACE__.'\Models'] = ROOT_PATH.$this->_config->application->baseDir.'modules/Core/Models';

		$loader = new Loader();

		$loader->registerNamespaces($namespaces);
		$loader->register();

		$di = new Di\Zero();

		$registry = new Registry();

		/** @noinspection PhpUndefinedFieldInspection */
		$registry->directories = (object)[
			'modules' => ROOT_PATH.$this->_config->application->baseDir.'modules/',
		];

		$di->set('loader', $loader, true);
		$di->set('registry', $registry, true);
		$di->set('config', $this->_config, true);

		parent::__construct($di);
	}

	public function run ()
	{
		$di = $this->getDI();
		$di->set('app', $this, true);

		$eventsManager = new EventsManager();
		$this->setEventsManager($eventsManager);

		if (defined('INSTALLED'))
		{
			if (file_exists(ROOT_PATH.$this->_config->application->baseDir.'globals.php'))
				/** @noinspection PhpIncludeInspection */
				include_once(ROOT_PATH.$this->_config->application->baseDir.'globals.php');

			$this->initProfiler($di, $eventsManager);

			if ($this->_config->application->prophiler)
				$appBenchmark = $di->getShared('profiler')->start(__CLASS__.'::run', [], 'Application');

			$this->initDatabase($di, $eventsManager);

			$registry = $di->get('registry');

			/** @noinspection PhpUndefinedFieldInspection */
			$registry->modules = Module::find()->toArray();

			if ($this->request->hasQuery('clear_cache'))
				CacheHelper::clearAll();

			$this->initModules($di);
			$this->initLoaders($di);

			foreach ($this->_loaders as $service)
			{
				$serviceName = ucfirst($service);

				if ($this->_config->application->prophiler)
					$benchmark = $di->getShared('profiler')->start(__CLASS__.'::init'.$serviceName, [], 'Application');

				$eventsManager->fire('init:before'.$serviceName, null);
				$result = $this->{'init'.$serviceName}($di, $eventsManager);
				$eventsManager->fire('init:after'.$serviceName, $result);

				if ($this->_config->application->prophiler && isset($benchmark))
					$di->getShared('profiler')->stop($benchmark);
			}

			if ($this->_config->application->prophiler && isset($appBenchmark))
				$di->getShared('profiler')->stop($appBenchmark);
		}
		else
		{
			$installer = new Installer($di);
			$installer->run();
		}

		$di->set('eventsManager', $eventsManager, true);
	}

	public function getOutput()
	{
		$di = $this->getDI();

		if (defined('INSTALLED') && $this->_config->application->prophiler && $di->has('profiler'))
		{
			$benchmark = $di->getShared('profiler')->start(__CLASS__.'::getOutput', [], 'Application');

			$this->assets->addCss('//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		}

		$handle = $this->handle();

		if (isset($benchmark))
			$di->getShared('profiler')->stop($benchmark);

		if (defined('INSTALLED') && $this->_config->application->prophiler && !$this->request->isAjax() && !$this->view->isDisabled())
		{
			$controller = $this->router->getControllerName();

			if ($controller !== '')
			{
				$toolbar = new Prophiler\Toolbar($this->getDI()->getShared('profiler'));
				$toolbar->addDataCollector(new Prophiler\DataCollector\Request());
				$toolbar->addDataCollector(new Prophiler\DataCollector\Files());
			}
		}

		if ($this->request->isAjax() && $this->dispatcher->getModuleName() !== 'admin')
		{
			/** @noinspection PhpUndefinedFieldInspection */
			$this->response->setJsonContent(
			[
				'status' 	=> $this->game->getRequestStatus(),
				'message' 	=> $this->game->getRequestMessage(),
				'html' 		=> str_replace("\t", ' ', $handle->getContent()),
				'data' 		=> $this->game->getRequestData()
			]);
			$this->response->setContentType('text/json', 'utf8');
			$this->response->send();
			die();
		}

		return $handle->getContent().(isset($toolbar) ? $toolbar->render() : '');
	}

	/**
	 * @param $di DiInterface
	 */
	protected function initModules ($di)
	{
		$registry = $di->get('registry');

		$modules = [];

		if (!empty($registry->modules))
		{
			foreach ($registry->modules as $module)
			{
				if ((int) $module['active'] != 1)
					continue;

				$namespace = ($module['namespace'] != '' ? $module['namespace'] : ucfirst($module['code']));

				$modules[mb_strtolower($module['code'])] = [
					'className'	=> $namespace.'\Module',
					'path' 		=> $registry->directories->modules.ucfirst($module['code']).'/Module.php'
				];
			}
		}

		parent::registerModules($modules);
	}

	public function hasModule ($name)
	{
		return (isset($this->_modules[Text::lower($name)]));
	}
}