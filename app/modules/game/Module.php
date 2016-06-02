<?php

namespace App\Game;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use App\Auth\Security;
use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\View\Engine\Volt;

class Module implements ModuleDefinitionInterface
{
	public function registerAutoloaders(DiInterface $di = null)
	{
		$loader = new Loader();

		$config = $di->getShared('config');

		/** @noinspection PhpUndefinedFieldInspection */
		$loader->registerNamespaces(array
		(
		    'App\Game\Controllers' => APP_PATH.$config->application->baseDir.$config->application->modulesDir.'game/'.$config->application->controllersDir,
		));

		$loader->register();
	}

	public function registerServices(DiInterface $di)
	{
		$config = $di->getShared('config');

		$di->setShared('view', function() use ($config)
		{
			$view = new View();
			$view->setViewsDir(APP_PATH.$config->application->baseDir.$config->application->modulesDir.'game/'.$config->application->viewsDir);

			if (!is_dir(APP_PATH.$config->application->baseDir.$config->application->cacheDir.'views'))
				mkdir(APP_PATH.$config->application->baseDir.$config->application->cacheDir.'views');

			$view->registerEngines([".volt" => function ($view, $di) use ($config)
			{
				$volt = new Volt($view, $di);

				$volt->setOptions([
					'compiledPath'		=> APP_PATH.$config->application->baseDir.$config->application->cacheDir.'views/',
					'compiledSeparator'	=> '|',
					'compiledExtension'	=> '.cache'
				]);

				$compiler = $volt->getCompiler();

				$compiler->addFunction('_text', function($arguments)
				{
					return '_getText(' . $arguments . ')';
				});

				$compiler->addFilter('round', 'round');

				return $volt;
			}]);

			return $view;
		});

		$di->set('dispatcher', function () use ($di)
		{
			$eventsManager = new EventsManager;
			$eventsManager->attach('dispatch:beforeExecuteRoute', new Security);
			/** @noinspection PhpUnusedParameterInspection */
			$eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception)
			{
				/**
				 * @var \Phalcon\Mvc\Dispatcher $dispatcher
				 * @var \Phalcon\Mvc\Dispatcher\Exception $exception
				 */
				switch ($exception->getCode())
				{
					case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
					case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
						$dispatcher->forward([
							'controller' => 'error',
							'action'	 => 'notFound',
						]);
						return false;
				}

				return true;
			});

			$dispatcher = new Dispatcher();
			$dispatcher->setDefaultNamespace('App\Game\Controllers');
			$dispatcher->setEventsManager($eventsManager);
			return $dispatcher;
		});
	}
}

?>