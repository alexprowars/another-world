<?php

namespace Game;

use Sky\Core\Module\Base;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module extends Base implements ModuleDefinitionInterface
{
	public function getModuleName()
	{
		return 'game';
	}

	public function registerAutoloaders(DiInterface $di = null)
	{
		$namespaces[__NAMESPACE__.'\Models'] = __DIR__.'/Models';

		$loader = $di->get('loader');

		$loader->registerNamespaces($namespaces, true);
		$loader->register();
	}

	public function registerServices(DiInterface $di)
	{
		if ($di->has('view'))
		{
			$view = $di->getShared('view');

			$viewDirs = $view->getViewsDir();

			if (is_null($viewDirs))
				$viewDirs = [];

			if (!is_array($viewDirs))
				$viewDirs = [$viewDirs];

			$config = $di->getShared('config');

			$viewDirs[] = ROOT_PATH.$config->application->baseDir.$config->application->modulesDir.'Game/Views';

			$view->setViewsDir($viewDirs);
		}

		$game = new Game();
		$game->loadGameVariables();

		$di->set('game', $game, true);
	}
}