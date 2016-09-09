<?php

namespace Sky\Core\Module;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Sky\Core\Modules;

abstract class Base
{
	public function __construct()
	{
		Modules::initialized($this->getModuleName());
	}

	abstract function getModuleName ();
}