<?php

namespace Sky\Core\Di;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Di;
use Phalcon\Di\Service;

class Zero extends Di
{
	public function __construct()
	{
		parent::__construct();

		$this->_services = [
			"request"	=> new Service("request", "Phalcon\\Http\\Request", true),
			"response"	=> new Service("response", "Phalcon\\Http\\Response", true),
			"filter"	=> new Service("filter", "Phalcon\\Filter", true),
			"escaper"	=> new Service("escaper", "Phalcon\\Escaper", true),
		];
	}
}