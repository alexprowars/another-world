<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

include_once(APP_PATH."app/functions.php");

if (isset($application))
{
	$application->registerModules([
		'game' =>
		[
			'className'	=> 'App\Game\Module',
			'path'		=> APP_PATH.'app/modules/game/Module.php',
		],
	]);
}

$result = $di->get('cache')->get('app_config');

if ($result === null)
{
	$result = [];

	$loads = $di->get('db')->query("SELECT `key`, `value` FROM game_config");

	while ($load = $loads->fetch())
		$result[$load['key']] = $load['value'];

	$di->get('cache')->save('app_config', $result, 3600);
}

$di->get('config')->merge(new \Phalcon\Config(['app' => $result]));

if ($di->has('auth'))
{
	$auth = $di->getShared('auth');

	$auth->addAuthPlugin('\App\Auth\Plugins\Ulogin');
	$auth->addAuthPlugin('\App\Auth\Plugins\Vk');
	$auth->checkExtAuth();
}

define('VERSION', '0.1');
define('DB_PREFIX', 'game_');