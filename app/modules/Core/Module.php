<?php

namespace Sky\Core;

use Sky\Core\Module\Base;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module extends Base implements ModuleDefinitionInterface
{
	public function getModuleName()
	{
		return 'core';
	}

	public function registerAutoloaders(DiInterface $di = null) {}

	public function registerServices(DiInterface $di) {}
}